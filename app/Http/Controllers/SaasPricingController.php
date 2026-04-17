<?php

namespace App\Http\Controllers;

use App\SaasFeature;
use App\SaasBundle;
use App\SaasSubscription;
use App\SaasInvoice;
use App\Business;
use Illuminate\Http\Request;

class SaasPricingController extends Controller
{
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

    // Submit order (creates pending subscription + invoice)
    public function submitOrder(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'email'         => 'required|email',
            'phone'         => 'required|string',
            'cycle'         => 'required|in:monthly,quarterly,yearly,once',
            'feature_ids'   => 'required|array|min:1',
            'hosting'       => 'required|in:cloud,self_hosted',
        ]);

        // Find business by checking the owner user's email
        $user = \App\User::where('email', $request->email)->first();
        $business = $user ? \App\Business::find($user->business_id) : null;

        if (!$business) {
            // Store enquiry for admin to action
            \DB::table('saas_enquiries')->insert([
                'business_name' => $request->business_name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'cycle'         => $request->cycle,
                'hosting'       => $request->hosting,
                'feature_ids'   => json_encode($request->feature_ids),
                'total'         => $request->total,
                'status'        => 'pending',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            return redirect()->route('saas.pricing')->with('success',
                'Your order has been received! We will contact you at ' . $request->email . ' within 24 hours to complete setup.'
            );
        }

        $features = SaasFeature::whereIn('id', $request->feature_ids)->get();
        $total    = $features->sum(fn($f) => $f->priceFor($request->cycle));

        $sub = SaasSubscription::create([
            'business_id'   => $business->id,
            'billing_cycle' => $request->cycle,
            'hosting_type'  => $request->hosting,
            'total_amount'  => $total,
            'status'        => 'pending',
        ]);

        foreach ($features as $f) {
            $sub->features()->attach($f->id, ['price_locked' => $f->priceFor($request->cycle)]);
        }

        $invoice = SaasInvoice::create([
            'invoice_no'  => SaasInvoice::generateNumber(),
            'business_id' => $business->id,
            'subscription_id' => $sub->id,
            'amount'      => $total,
            'currency'    => 'KES',
            'status'      => 'unpaid',
            'type'        => 'subscription',
            'due_at'      => now()->addDays(3),
        ]);

        return view('saas.order_confirmation', compact('sub', 'invoice', 'features', 'total'));
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
