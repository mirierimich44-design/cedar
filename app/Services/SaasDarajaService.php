<?php

namespace App\Services;

use App\SaasInvoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Platform-level Daraja STK Push for SaaS subscription payments.
 *
 * Kept separate from App\Http\Controllers\MpesaController (which is the
 * per-tenant M-Pesa integration). This service uses ApexPOS's own
 * Safaricom credentials from config/services.php `saas_daraja`.
 */
class SaasDarajaService
{
    protected array $cfg;

    public function __construct()
    {
        $this->cfg = config('services.saas_daraja');
    }

    protected function baseUrl(): string
    {
        return ($this->cfg['env'] ?? 'sandbox') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    /**
     * Obtain a short-lived OAuth access token.
     */
    public function getAccessToken(): ?string
    {
        $key    = $this->cfg['consumer_key']    ?? null;
        $secret = $this->cfg['consumer_secret'] ?? null;
        if (!$key || !$secret) {
            Log::warning('SaasDaraja: missing consumer key/secret');
            return null;
        }

        $resp = Http::withBasicAuth($key, $secret)
            ->timeout(15)
            ->get($this->baseUrl() . '/oauth/v1/generate', ['grant_type' => 'client_credentials']);

        if (!$resp->ok()) {
            Log::error('SaasDaraja: oauth failed', ['body' => $resp->body()]);
            return null;
        }

        return $resp->json('access_token');
    }

    /**
     * Kick off an STK Push for a SaaS invoice.
     * Returns the Daraja response array or null on failure.
     */
    public function initiateStkPush(SaasInvoice $invoice, string $phone): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $shortcode = $this->cfg['shortcode']    ?? null;
        $passkey   = $this->cfg['passkey']      ?? null;
        $callback  = $this->cfg['callback_url'] ?? null;

        if (!$shortcode || !$passkey || !$callback) {
            Log::warning('SaasDaraja: shortcode/passkey/callback missing');
            return null;
        }

        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($shortcode . $passkey . $timestamp);
        $phone     = $this->normalizePhone($phone);
        $amount    = (int) round($invoice->amount); // Daraja requires whole KES

        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => $this->cfg['transaction_type'] ?? 'CustomerPayBillOnline',
            'Amount'            => max(1, $amount),
            'PartyA'            => $phone,
            'PartyB'            => $shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => rtrim($callback, '/') . '/' . $invoice->id,
            'AccountReference'  => $invoice->invoice_no,
            'TransactionDesc'   => 'ApexPOS ' . $invoice->invoice_no,
        ];

        $resp = Http::withToken($token)
            ->timeout(20)
            ->post($this->baseUrl() . '/mpesa/stkpush/v1/processrequest', $payload);

        $json = $resp->json();
        Log::info('SaasDaraja: stk push', ['invoice' => $invoice->id, 'response' => $json]);

        if ($resp->ok() && ($json['ResponseCode'] ?? null) === '0') {
            $invoice->update([
                'payment_reference' => $json['CheckoutRequestID'] ?? null,
                'payment_method'    => 'mpesa_stk',
            ]);
        }

        return $json;
    }

    /**
     * Handle Safaricom's async callback. Marks the invoice + subscription
     * paid/active when ResultCode === 0, otherwise logs and leaves unpaid.
     */
    public function handleCallback(SaasInvoice $invoice, array $payload): void
    {
        $stk = $payload['Body']['stkCallback'] ?? [];
        $resultCode = $stk['ResultCode'] ?? null;

        if ((int) $resultCode !== 0) {
            Log::info('SaasDaraja callback: payment failed', [
                'invoice' => $invoice->id,
                'desc'    => $stk['ResultDesc'] ?? 'unknown',
            ]);
            $invoice->update(['status' => 'failed']);
            return;
        }

        // Extract MpesaReceiptNumber from CallbackMetadata
        $receipt = null;
        foreach ($stk['CallbackMetadata']['Item'] ?? [] as $item) {
            if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
                $receipt = $item['Value'] ?? null;
                break;
            }
        }

        $invoice->update([
            'status'            => 'paid',
            'paid_at'           => now(),
            'payment_method'    => 'mpesa_stk',
            'payment_reference' => $receipt ?? $invoice->payment_reference,
        ]);

        // Activate the subscription
        if ($sub = $invoice->subscription) {
            $months = match($sub->billing_cycle) {
                'quarterly' => 3,
                'yearly'    => 12,
                'once'      => 999 * 12,
                default     => 1,
            };
            $sub->update([
                'status'        => 'active',
                'starts_at'     => $sub->starts_at ?? now(),
                'ends_at'       => now()->addMonths($months),
                'grace_ends_at' => now()->addMonths($months)->addDays(7),
            ]);
        }

        Log::info('SaasDaraja callback: payment confirmed', [
            'invoice' => $invoice->id,
            'receipt' => $receipt,
        ]);
    }

    /**
     * Normalize KE phone numbers to 2547XXXXXXXX / 2541XXXXXXXX.
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '254' . substr($phone, 1);
        }
        if (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            return '254' . $phone;
        }
        if (str_starts_with($phone, '+254')) {
            return substr($phone, 1);
        }
        return $phone;
    }
}
