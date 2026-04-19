<?php

namespace App\Http\Controllers;

use App\SaasFeature;
use App\SaasBundle;
use App\SaasSubscription;
use App\SaasInvoice;
use App\SaasSetting;
use App\Business;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Services\SaasDarajaService;
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

        $business_types = [
            'hospital'   => ['label' => 'Hospital / Clinic', 'icon' => 'fa-hospital-alt', 'desc' => 'Medical record, wards, and labs.'],
            'pharmacy'   => ['label' => 'Pharmacy',          'icon' => 'fa-pills',        'desc' => 'DDA register and prescriptions.'],
            'restaurant' => ['label' => 'Restaurant / Cafe', 'icon' => 'fa-utensils',     'desc' => 'Table management and kitchen orders.'],
            'retail'     => ['label' => 'Retail / Shop',     'icon' => 'fa-shopping-bag', 'desc' => 'General POS and inventory.'],
            'mixed'      => ['label' => 'Mixed Business',    'icon' => 'fa-random',       'desc' => 'A bit of everything.'],
        ];

        return view('saas.pricing', compact('featuresByCategory', 'bundles', 'categories', 'business_types'));
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
        $biz_type   = $request->biz ?? 'retail';

        if (empty($featureIds)) {
            return redirect()->route('saas.pricing')->with('error', 'Please select at least one feature.');
        }

        $features = SaasFeature::whereIn('id', $featureIds)->where('is_active', true)->get();
        $total    = $features->sum(fn($f) => $f->priceFor($cycle));
        $hosting  = $request->hosting ?? 'cloud';

        return view('saas.checkout', compact('features', 'featureIds', 'cycle', 'total', 'hosting', 'biz_type'));
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

        // Apply campaign discount (admin-controlled)
        if (SaasSetting::campaignActive() && SaasSetting::campaignDiscount() > 0) {
            $total = round($total * (1 - SaasSetting::campaignDiscount() / 100), 2);
        }

        // Enforce trial-on/off + dynamic trial length from settings
        $trialEnabled = SaasSetting::trialEnabled();
        $trialDays    = SaasSetting::trialDays();
        $graceDays    = SaasSetting::trialGraceDays();

        $action = $request->action;
        if ($action === 'trial' && (!$trialEnabled || $trialDays <= 0)) {
            return back()->withInput()->with('error', 'Free trial is not currently available. Please choose Activate Now.');
        }

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

            // 1. Create owner user via User::create_user (handles hashing + defaults)
            $user = User::create_user([
                'surname'    => '',
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name ?? '',
                'username'   => $username,
                'email'      => $request->email,
                'password'   => $request->password,
                'language'   => 'en',
            ]);

            // 2. Create business with Kenyan defaults
            $kesId = DB::table('currencies')->where('code', 'KES')->value('id')
                  ?? DB::table('currencies')->value('id');
            
            $enabled_modules = ['purchases', 'add_sale', 'pos_sale', 'stock_transfers', 'stock_adjustment', 'expenses'];
            
            // Preset module mapping based on business type
            if ($request->biz_type === 'hospital' || $request->biz_type === 'clinic') {
                $enabled_modules[] = 'hospital_module';
            } elseif ($request->biz_type === 'pharmacy') {
                $enabled_modules[] = 'dda_module';
            } elseif ($request->biz_type === 'restaurant') {
                $enabled_modules[] = 'restaurant_module';
            }

            $business = $this->businessUtil->createNewBusiness([
                'name'              => $request->business_name,
                'currency_id'       => $kesId,
                'start_date'        => now()->toDateString(),
                'time_zone'         => 'Africa/Nairobi',
                'fy_start_month'    => 1,
                'accounting_method' => 'fifo',
                'owner_id'          => $user->id,
                'enabled_modules'   => $enabled_modules,
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

            // 5b. Fire module hooks (Superadmin, Essentials, etc.) — matches BusinessController::postRegister
            if (config('app.env') != 'demo') {
                try {
                    $this->moduleUtil->getModuleData('after_business_created', ['business' => $business]);
                } catch (\Throwable $e) {
                    \Log::warning('after_business_created hook failed: ' . $e->getMessage());
                }
            }

            // 6. Create subscription based on chosen action
            if ($request->action === 'trial') {
                $sub = SaasSubscription::create([
                    'business_id'   => $business->id,
                    'billing_cycle' => $request->cycle,
                    'hosting_type'  => $request->hosting,
                    'total_amount'  => $total,
                    'status'        => 'trial',
                    'starts_at'     => now(),
                    'ends_at'       => now()->addDays($trialDays),
                    'grace_ends_at' => now()->addDays($trialDays + $graceDays),
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
                'due_at'          => $request->action === 'trial' ? now()->addDays($trialDays) : now()->addHours(1),
            ]);

            DB::commit();

            // 8. Log the user in
            Auth::login($user);

            // 9. Branch by action
            if ($request->action === 'trial') {
                return redirect('/home')->with('status', [
                    'success' => 1,
                    'msg'     => 'Welcome to ' . config('app.name') . "! Your {$trialDays}-day free trial is active. Activate anytime from the menu.",
                ]);
            }

            // pay_now — fire Daraja STK push, then show pending page that polls status
            try {
                app(SaasDarajaService::class)->initiateStkPush($invoice, $request->phone);
            } catch (\Throwable $e) {
                \Log::warning('SaaS STK push failed: ' . $e->getMessage());
                // Don't block the user — invoice stays unpaid, they can retry from pending page
            }
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

    // Daraja STK async callback — public, no auth (Safaricom posts here)
    public function mpesaCallback(Request $request, SaasInvoice $invoice)
    {
        \Log::info('SaaS M-Pesa callback received', [
            'invoice' => $invoice->id,
            'payload' => $request->all(),
        ]);
        app(SaasDarajaService::class)->handleCallback($invoice, $request->all());
        // Safaricom expects a JSON ack
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    // M-Pesa STK pending page
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
