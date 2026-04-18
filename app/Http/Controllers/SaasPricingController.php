<?php

namespace App\Http\Controllers;

use App\SaasFeature;
use App\SaasBundle;
use App\SaasSubscription;
use App\SaasInvoice;
use App\Business;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class SaasPricingController extends Controller
{
    protected $businessUtil;
    protected $moduleUtil;

    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil   = $moduleUtil;
    }

    // Public pricing page
    public function index()
    {
        $featuresByCategory = SaasFeature::activeByCategory();
        $bundles = SaasBundle::with('features')->where('is_active', true)->orderBy('sort_order')->get();

        $categories = [
            'core'          => ['label' => 'Core (Always Included)', 'icon' => 'fa-star'],
            'inventory'     => ['label' => 'Inventory & Stock',       'icon' => 'fa-boxes'],
            'pharmacy'      => ['label' => 'Pharmacy / DDA',          'icon' => 'fa-pills'],
            'reporting'     => ['label' => 'Reports & Analytics',     'icon' => 'fa-chart-bar'],
            'communication' => ['label' => 'Communication',           'icon' => 'fa-comment-dots'],
            'restaurant'    => ['label' => 'Restaurant',              'icon' => 'fa-utensils'],
        ];

        return view('saas.pricing', compact('featuresByCategory', 'bundles', 'categories'));
    }

    // Calculate price via AJAX
    public function calculate(Request $request)
    {
        $featureIds = $request->feature_ids ?? [];
        $cycle      = $request->cycle ?? 'monthly';

        $features = SaasFeature::whereIn('id', $featureIds)->where('is_active', true)->get();
        $total    = $features->sum(fn($f) => $f->priceFor($cycle));

        return response()->json([
            'total'    => $total,
            'currency' => 'KES',
            'features' => $features->map(fn($f) => [
                'id'    => $f->id,
                'name'  => $f->name,
                'price' => $f->priceFor($cycle),
            ]),
        ]);
    }

    // Checkout page
    public function checkout(Request $request)
    {
        $featureIds = $request->feature_ids ? explode(',', $request->feature_ids) : [];
        $cycle      = $request->cycle ?? 'monthly';

        if (empty($featureIds)) {
            return redirect()->route('saas.pricing')->with('error', 'Please select at least one feature.');
        }

        $features = SaasFeature::whereIn('id', $featureIds)->where('is_active', true)->get();
        $total    = $features->sum(fn($f) => $f->priceFor($cycle));
        $hosting  = $request->hosting ?? 'cloud';

        return view('saas.checkout', compact('features', 'featureIds', 'cycle', 'total', 'hosting'));
    }

    // Submit order — creates user + business + subscription, logs in, routes to trial or M-Pesa
    public function submitOrder(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'phone'         => 'required|string|max:30',
            'password'      => 'required|string|min:6|max:255',
            'cycle'         => 'required|in:monthly,quarterly,yearly,once',
            'feature_ids'   => 'required|array|min:1',
            'hosting'       => 'required|in:cloud,self_hosted',
            'action'        => 'required|in:trial,pay_now',
            'biz_type'      => 'nullable|string|max:50',
        ], [
            'email.unique' => 'An account with this email already exists. Please sign in instead.',
        ]);

        $features = SaasFeature::whereIn('id', $request->feature_ids)->where('is_active', true)->get();
        if ($features->isEmpty()) {
            return redirect()->route('saas.pricing')->with('error', 'Please select at least one feature.');
        }
        $total = $features->sum(fn($f) => $f->priceFor($request->cycle));

        DB::beginTransaction();
        try {
            // Derive a unique username from email
            $baseUsername = strtolower(preg_replace('/[^a-z0-9]/i', '', explode('@', $request->email)[0]));
            if (strlen($baseUsername) < 4) $baseUsername .= rand(100, 999);
            $username = $baseUsername;
            $i = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $i;
                $i++;
            }

            // 1. Create owner user
            $user = User::create([
                'surname'     => '',
                'first_name'  => $request->first_name,
                'last_name'   => $request->last_name ?? '',
                'username'    => $username,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'language'    => 'en',
                'user_type'   => 'user',
                'status'      => 'active',
                'business_id' => null, // set below
            ]);

            // 2. Create business with Kenyan defaults
            $business = $this->businessUtil->createNewBusiness([
                'name'              => $request->business_name,
                'currency_id'       => 133, // KES
                'start_date'        => now()->toDateString(),
                'time_zone'         => 'Africa/Nairobi',
                'fy_start_month'    => 1,
                'accounting_method' => 'fifo',
                'owner_id'          => $user->id,
                'enabled_modules'   => ['purchases', 'add_sale', 'pos_sale', 'stock_transfers', 'stock_adjustment', 'expenses'],
            ]);

            // 3. Link user ↔ business
            $user->business_id = $business->id;
            $user->save();

            // 4. Default roles, walk-in customer, invoice scheme/layout
            $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);

            // 5. Default location
            $location = $this->businessUtil->addLocation($business->id, [
                'name'     => $request->business_name,
                'country'  => 'Kenya',
                'state'    => 'Nairobi',
                'city'     => 'Nairobi',
                'zip_code' => '00100',
                'landmark' => 'Nairobi',
                'mobile'   => $request->phone,
            ]);
            Permission::firstOrCreate(['name' => 'location.' . $location->id]);

            // 6. Create subscription based on chosen action
            if ($request->action === 'trial') {
                $sub = SaasSubscription::create([
                    'business_id'   => $business->id,
                    'billing_cycle' => $request->cycle,
                    'hosting_type'  => $request->hosting,
                    'total_amount'  => $total,
                    'status'        => 'trial',
                    'starts_at'     => now(),
                    'ends_at'       => now()->addDays(3),
                    'grace_ends_at' => now()->addDays(3),
                ]);
            } else {
                // pay_now — pending until STK callback confirms payment
                $sub = SaasSubscription::create([
                    'business_id'   => $business->id,
                    'billing_cycle' => $request->cycle,
                    'hosting_type'  => $request->hosting,
                    'total_amount'  => $total,
                    'status'        => 'pending',
                ]);
            }

            foreach ($features as $f) {
                $sub->features()->attach($f->id, ['price_locked' => $f->priceFor($request->cycle)]);
            }

            // 7. Invoice (unpaid until STK confirms OR trial converts)
            $invoice = SaasInvoice::create([
                'invoice_no'      => SaasInvoice::generateNumber(),
                'business_id'     => $business->id,
                'subscription_id' => $sub->id,
                'amount'          => $total,
                'currency'        => 'KES',
                'status'          => 'unpaid',
                'type'            => 'subscription',
                'due_at'          => $request->action === 'trial' ? now()->addDays(3) : now()->addHours(1),
            ]);

            DB::commit();

            // 8. Log the user in
            Auth::login($user);

            // 9. Branch by action
            if ($request->action === 'trial') {
                return redirect('/home')->with('status', [
                    'success' => 1,
                    'msg'     => 'Welcome to ' . config('app.name') . '! Your 3-day free trial is active. Activate anytime from the menu.',
                ]);
            }

            // pay_now — show M-Pesa STK pending page (stub polls until admin marks paid)
            return redirect()->route('saas.mpesa.pending', ['invoice' => $invoice->id]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('SaaS signup failed', [
                'msg'  => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);
            return back()->withInput()->with('error',
                'We could not create your account: ' . $e->getMessage() . '. Please try again or contact support.'
            );
        }
    }

    // M-Pesa STK pending page (stub — real STK push wired separately)
    public function mpesaPending(SaasInvoice $invoice)
    {
        // Only the account owner can see this
        if (!Auth::check() || Auth::user()->business_id !== $invoice->business_id) {
            return redirect('/login');
        }
        $invoice->load('subscription.features');
        return view('saas.mpesa_pending', compact('invoice'));
    }

    // Poll endpoint: has the invoice been paid yet?
    public function mpesaStatus(SaasInvoice $invoice)
    {
        if (!Auth::check() || Auth::user()->business_id !== $invoice->business_id) {
            return response()->json(['status' => 'denied'], 403);
        }
        return response()->json([
            'status'  => $invoice->status,
            'paid'    => $invoice->status === 'paid',
            'sub'     => $invoice->subscription ? $invoice->subscription->status : null,
        ]);
    }

    // Customer portal (own subscription)
    public function portal()
    {
        $business = auth()->user()->business_id
            ? \App\Business::find(auth()->user()->business_id)
            : null;

        $subscription = $business
            ? SaasSubscription::with('features')
                ->where('business_id', $business->id)
                ->latest()
                ->first()
            : null;

        $invoices = $business
            ? SaasInvoice::where('business_id', $business->id)->latest()->limit(10)->get()
            : collect();

        return view('saas.portal', compact('subscription', 'invoices', 'business'));
    }
}
