<?php

namespace App\Http\Controllers;

use App\SaasFeature;
use App\SaasBundle;
use App\SaasSubscription;
use App\SaasInvoice;
use App\SaasHostedAccount;
use App\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaasAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'SetSessionData', 'superadmin']);
    }

    // ─── Dashboard ───────────────────────────────────────────────
    public function dashboard()
    {
        $stats = [
            'active'      => SaasSubscription::where('status', 'active')->count(),
            'grace'       => SaasSubscription::where('status', 'grace')->count(),
            'suspended'   => SaasSubscription::where('status', 'suspended')->count(),
            'mrr'         => SaasSubscription::where('status', 'active')->where('billing_cycle', 'monthly')->sum('total_amount'),
            'arr'         => SaasSubscription::where('status', 'active')->where('billing_cycle', 'yearly')->sum('total_amount'),
            'revenue'     => SaasInvoice::where('status', 'paid')->sum('amount'),
            'features'    => SaasFeature::count(),
            'bundles'     => SaasBundle::count(),
            'enquiries'   => DB::table('saas_enquiries')->where('status', 'pending')->count(),
        ];
        $recent = SaasSubscription::with('business')->latest()->limit(10)->get();
        return view('saas.admin.dashboard', compact('stats', 'recent'));
    }

    // ─── Features ────────────────────────────────────────────────
    public function featuresIndex()
    {
        $features = SaasFeature::orderBy('sort_order')->orderBy('name')->get()->groupBy('category');
        return view('saas.admin.features.index', compact('features'));
    }

    public function featuresCreate()
    {
        return view('saas.admin.features.create');
    }

    public function featuresStore(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'key'             => 'required|string|unique:saas_features,key|max:100',
            'category'        => 'required|string',
            'price_monthly'   => 'required|numeric|min:0',
            'price_quarterly' => 'required|numeric|min:0',
            'price_yearly'    => 'required|numeric|min:0',
            'price_once'      => 'required|numeric|min:0',
        ]);

        SaasFeature::create($request->except('_token'));
        return redirect()->route('saas.admin.features')->with('success', 'Feature created.');
    }

    public function featuresEdit(SaasFeature $feature)
    {
        return view('saas.admin.features.edit', compact('feature'));
    }

    public function featuresUpdate(Request $request, SaasFeature $feature)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'price_monthly'   => 'required|numeric|min:0',
            'price_quarterly' => 'required|numeric|min:0',
            'price_yearly'    => 'required|numeric|min:0',
            'price_once'      => 'required|numeric|min:0',
        ]);

        $feature->update($request->except(['_token', '_method', 'key']));
        return redirect()->route('saas.admin.features')->with('success', 'Feature updated.');
    }

    public function featuresDestroy(SaasFeature $feature)
    {
        $feature->delete();
        return back()->with('success', 'Feature deleted.');
    }

    // ─── Feature AJAX: Toggle Active ─────────────────────────────
    public function featuresToggle(Request $request, SaasFeature $feature)
    {
        $feature->update(['is_active' => (bool) $request->is_active]);
        return response()->json(['success' => true, 'is_active' => $feature->is_active]);
    }

    // ─── Feature AJAX: Quick Price Update ────────────────────────
    public function featuresUpdatePrice(Request $request, SaasFeature $feature)
    {
        $request->validate([
            'cycle' => 'required|in:monthly,quarterly,yearly,once',
            'price' => 'required|numeric|min:0',
        ]);

        $col = 'price_' . $request->cycle;
        $feature->update([$col => $request->price]);
        return response()->json(['success' => true, 'price' => $feature->$col]);
    }

    // ─── Bundles ─────────────────────────────────────────────────
    public function bundlesIndex()
    {
        $bundles = SaasBundle::with('features')->orderBy('sort_order')->get();
        return view('saas.admin.bundles.index', compact('bundles'));
    }

    public function bundlesCreate()
    {
        $features = SaasFeature::where('is_active', true)->orderBy('sort_order')->get()->groupBy('category');
        return view('saas.admin.bundles.create', compact('features'));
    }

    public function bundlesStore(Request $request)
    {
        $request->validate(['name' => 'required', 'slug' => 'required|unique:saas_bundles,slug']);
        $bundle = SaasBundle::create($request->except(['_token', 'feature_ids']));
        $bundle->features()->sync($request->feature_ids ?? []);
        return redirect()->route('saas.admin.bundles')->with('success', 'Bundle created.');
    }

    public function bundlesEdit(SaasBundle $bundle)
    {
        $bundle->load('features');
        $features = SaasFeature::where('is_active', true)->orderBy('sort_order')->get()->groupBy('category');
        $selected = $bundle->features->pluck('id')->toArray();
        return view('saas.admin.bundles.edit', compact('bundle', 'features', 'selected'));
    }

    public function bundlesUpdate(Request $request, SaasBundle $bundle)
    {
        $bundle->update($request->except(['_token', '_method', 'feature_ids']));
        $bundle->features()->sync($request->feature_ids ?? []);
        return redirect()->route('saas.admin.bundles')->with('success', 'Bundle updated.');
    }

    public function bundlesDestroy(SaasBundle $bundle)
    {
        $bundle->delete();
        return back()->with('success', 'Bundle deleted.');
    }

    // ─── Subscriptions ───────────────────────────────────────────
    public function subscriptionsIndex(Request $request)
    {
        $query = SaasSubscription::with(['business', 'features'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->cycle,  fn($q) => $q->where('billing_cycle', $request->cycle))
            ->latest();

        $subscriptions = $query->paginate(20);
        return view('saas.admin.subscriptions.index', compact('subscriptions'));
    }

    public function subscriptionsShow(SaasSubscription $subscription)
    {
        $subscription->load(['business', 'features', 'invoices']);
        return view('saas.admin.subscriptions.show', compact('subscription'));
    }

    public function subscriptionsUpdateStatus(Request $request, SaasSubscription $subscription)
    {
        $subscription->update(['status' => $request->status]);
        return back()->with('success', 'Subscription status updated.');
    }

    public function subscriptionsExtend(Request $request, SaasSubscription $subscription)
    {
        $request->validate(['days' => 'required|integer|min:1']);
        $subscription->update(['ends_at' => $subscription->ends_at->addDays($request->days)]);
        return back()->with('success', "Extended by {$request->days} days.");
    }

    // ─── Invoices ────────────────────────────────────────────────
    public function invoicesIndex()
    {
        $invoices = SaasInvoice::with('business')->latest()->paginate(25);
        return view('saas.admin.invoices.index', compact('invoices'));
    }

    public function invoicesMarkPaid(Request $request, SaasInvoice $invoice)
    {
        $invoice->update([
            'status'            => 'paid',
            'paid_at'           => now(),
            'payment_method'    => $request->payment_method ?? 'manual',
            'payment_reference' => $request->reference,
        ]);

        // Activate subscription if pending
        if ($invoice->subscription && $invoice->subscription->status === 'pending') {
            $invoice->subscription->update(['status' => 'active', 'starts_at' => now()]);
        }

        return back()->with('success', 'Invoice marked as paid and subscription activated.');
    }

    // ─── Enquiries ───────────────────────────────────────────────
    public function enquiriesIndex(Request $request)
    {
        $query = DB::table('saas_enquiries')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at');

        $enquiries = $query->paginate(20);
        return view('saas.admin.enquiries.index', compact('enquiries'));
    }

    public function enquiriesUpdateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,contacted,converted,cancelled']);
        DB::table('saas_enquiries')->where('id', $id)->update([
            'status'     => $request->status,
            'notes'      => $request->notes,
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Enquiry updated.');
    }
}
