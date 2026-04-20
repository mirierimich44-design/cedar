<?php

namespace App\Http\Controllers;

use App\Business;
use App\SaasSetting;
use App\SaasSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    // ── Show activation choice page (trial vs pay) ────────────────────────────
    public function showActivate(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user     = auth()->user();
        $business = Business::find($user->business_id);

        if (! $business) {
            return redirect()->route('login');
        }

        // Already has a live subscription — skip ahead
        $existing = SaasSubscription::where('business_id', $business->id)
            ->whereIn('status', ['trial', 'active', 'grace'])
            ->latest()->first();

        if ($existing && $existing->isAccessible()) {
            return redirect('/home');
        }

        $trialEnabled  = SaasSetting::trialEnabled();
        $trialDays     = SaasSetting::trialDays();
        $mpesaEnabled  = (bool) SaasSetting::get('mpesa_enabled', true);
        $mpesaPaybill  = SaasSetting::mpesaPaybill();
        $monthlyPrice  = (int) SaasSetting::get('onboarding_monthly_price', 2999);

        return view('onboarding.activate', compact(
            'business', 'user',
            'trialEnabled', 'trialDays',
            'mpesaEnabled', 'mpesaPaybill', 'monthlyPrice'
        ));
    }

    // ── Start free trial ──────────────────────────────────────────────────────
    public function startTrial(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user      = auth()->user();
        $business  = Business::findOrFail($user->business_id);
        $trialDays = SaasSetting::trialDays();
        $graceDays = SaasSetting::trialGraceDays();

        // Prevent double-activation
        $existing = SaasSubscription::where('business_id', $business->id)
            ->whereIn('status', ['trial', 'active'])
            ->exists();

        if ($existing) {
            return redirect('/home');
        }

        DB::beginTransaction();
        try {
            SaasSubscription::create([
                'business_id'   => $business->id,
                'billing_cycle' => 'trial',
                'hosting_type'  => 'cloud',
                'total_amount'  => 0,
                'status'        => 'trial',
                'starts_at'     => now(),
                'ends_at'       => now()->addDays($trialDays),
                'grace_ends_at' => now()->addDays($trialDays + $graceDays),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trial activation failed', ['error' => $e->getMessage(), 'business' => $business->id]);
            return back()->with('error', 'Could not start trial. Please try again or contact support.');
        }

        return redirect('/home')->with('status', [
            'success' => 1,
            'msg'     => "Your {$trialDays}-day free trial has started. Welcome to Apex POS!",
        ]);
    }

    // ── Initiate M-Pesa STK push ──────────────────────────────────────────────
    public function initiateStkPush(Request $request)
    {
        $request->validate(['phone' => 'required|string|min:9|max:15']);

        if (! auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Not authenticated.']);
        }

        $user     = auth()->user();
        $business = Business::findOrFail($user->business_id);
        $amount   = (int) SaasSetting::get('onboarding_monthly_price', 2999);

        // Normalise phone to 254XXXXXXXXX
        $phone = preg_replace('/\s+/', '', $request->phone);
        $phone = preg_replace('/^\+/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }
        if (! str_starts_with($phone, '254')) {
            $phone = '254' . $phone;
        }

        // SaaS-level Daraja credentials (set in SaaS Admin → Settings)
        $consumerKey    = SaasSetting::get('mpesa_consumer_key');
        $consumerSecret = SaasSetting::get('mpesa_consumer_secret');
        $shortcode      = SaasSetting::get('mpesa_shortcode') ?: SaasSetting::mpesaPaybill();
        $passkey        = SaasSetting::get('mpesa_passkey');
        $environment    = SaasSetting::get('mpesa_env', 'sandbox');

        // If Daraja credentials not configured, return manual payment instructions
        if (! $consumerKey || ! $consumerSecret || ! $passkey) {
            return response()->json([
                'success' => false,
                'manual'  => true,
                'paybill' => SaasSetting::mpesaPaybill(),
                'account' => 'APEX-' . $business->id,
                'amount'  => $amount,
                'message' => 'Use the Paybill details below to pay manually.',
            ]);
        }

        try {
            $baseUrl = $environment === 'production'
                ? 'https://api.safaricom.co.ke'
                : 'https://sandbox.safaricom.co.ke';

            // Step 1: OAuth token
            $tokenResp = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->timeout(15)
                ->get("{$baseUrl}/oauth/v1/generate?grant_type=client_credentials");

            if (! $tokenResp->ok()) {
                throw new \Exception('M-Pesa token error: ' . $tokenResp->body());
            }
            $accessToken = $tokenResp->json('access_token');

            // Step 2: STK push
            $timestamp   = now()->format('YmdHis');
            $password    = base64_encode($shortcode . $passkey . $timestamp);
            $ref         = 'APEX-' . $business->id . '-' . time();
            $callbackUrl = SaasSetting::get('mpesa_callback_url', url('/onboarding/mpesa/callback'));

            $stkResp = Http::withToken($accessToken)
                ->timeout(20)
                ->post("{$baseUrl}/mpesa/stkpush/v1/processrequest", [
                    'BusinessShortCode' => $shortcode,
                    'Password'          => $password,
                    'Timestamp'         => $timestamp,
                    'TransactionType'   => 'CustomerPayBillOnline',
                    'Amount'            => $amount,
                    'PartyA'            => $phone,
                    'PartyB'            => $shortcode,
                    'PhoneNumber'       => $phone,
                    'CallBackURL'       => $callbackUrl,
                    'AccountReference'  => $ref,
                    'TransactionDesc'   => 'Apex POS Subscription',
                ]);

            if ($stkResp->ok() && $stkResp->json('ResponseCode') === '0') {
                $checkoutId = $stkResp->json('CheckoutRequestID');

                // Create pending subscription record
                SaasSubscription::create([
                    'business_id'   => $business->id,
                    'billing_cycle' => 'monthly',
                    'hosting_type'  => 'cloud',
                    'total_amount'  => $amount,
                    'status'        => 'pending',
                    'starts_at'     => null,
                    'ends_at'       => null,
                    'notes'         => "ref:{$ref}|checkout:{$checkoutId}",
                ]);

                session(['saas_checkout_id' => $checkoutId]);

                return response()->json([
                    'success'     => true,
                    'checkout_id' => $checkoutId,
                    'message'     => 'Check your phone and enter your M-Pesa PIN to complete payment.',
                ]);
            }

            throw new \Exception($stkResp->json('errorMessage') ?? 'STK push failed: ' . $stkResp->body());

        } catch (\Exception $e) {
            Log::error('Onboarding STK push error', ['error' => $e->getMessage(), 'business' => $business->id]);

            return response()->json([
                'success' => false,
                'manual'  => true,
                'paybill' => SaasSetting::mpesaPaybill(),
                'account' => 'APEX-' . $business->id,
                'amount'  => $amount,
                'message' => 'Auto payment failed. Pay manually using the Paybill details below.',
            ]);
        }
    }

    // ── AJAX: poll whether payment has been confirmed ─────────────────────────
    public function checkPaymentStatus(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['paid' => false]);
        }

        $businessId = auth()->user()->business_id;
        $sub = SaasSubscription::where('business_id', $businessId)
            ->whereIn('status', ['active', 'trial'])
            ->latest()->first();

        if ($sub && $sub->isAccessible()) {
            return response()->json(['paid' => true, 'redirect' => url('/home')]);
        }

        return response()->json(['paid' => false]);
    }

    // ── Safaricom STK callback (no auth, no CSRF) ─────────────────────────────
    public function mpesaCallback(Request $request)
    {
        $data = $request->json()->all();
        Log::info('Onboarding M-Pesa STK callback', $data);

        try {
            $body       = $data['Body']['stkCallback'] ?? null;
            $resultCode = $body['ResultCode'] ?? -1;
            $checkoutId = $body['CheckoutRequestID'] ?? null;

            if ($resultCode == 0 && $checkoutId) {
                $items     = $body['CallbackMetadata']['Item'] ?? [];
                $mpesaCode = null;
                $ref       = null;

                foreach ($items as $item) {
                    if ($item['Name'] === 'MpesaReceiptNumber') $mpesaCode = $item['Value'];
                    if ($item['Name'] === 'AccountReference')   $ref       = $item['Value'] ?? null;
                }

                // Find business from ref (APEX-{id}-{timestamp})
                $businessId = null;
                if ($ref && preg_match('/APEX-(\d+)-/', $ref, $m)) {
                    $businessId = (int) $m[1];
                }

                if (! $businessId) {
                    // Try matching by checkout id stored in notes
                    $sub = SaasSubscription::where('notes', 'like', "%checkout:{$checkoutId}%")
                        ->where('status', 'pending')
                        ->latest()->first();
                    if ($sub) $businessId = $sub->business_id;
                }

                if ($businessId) {
                    $sub = SaasSubscription::where('business_id', $businessId)
                        ->where('status', 'pending')
                        ->latest()->first();

                    if ($sub) {
                        $graceDays = SaasSetting::trialGraceDays();
                        $sub->update([
                            'status'        => 'active',
                            'starts_at'     => now(),
                            'ends_at'       => now()->addMonth(),
                            'grace_ends_at' => now()->addMonth()->addDays($graceDays),
                            'notes'         => ($sub->notes ?? '') . '|mpesa:' . $mpesaCode,
                        ]);
                        Log::info("Onboarding subscription activated for business {$businessId}, code: {$mpesaCode}");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Onboarding callback error', ['error' => $e->getMessage()]);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}
