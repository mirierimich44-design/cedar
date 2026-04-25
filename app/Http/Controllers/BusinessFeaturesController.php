<?php

namespace App\Http\Controllers;

use App\Models\BusinessFeatureSetting;
use App\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Per-business feature/module management.
 *
 * Admin superusers can enable or disable any listed feature for any business.
 * The enabled/disabled state is cached for 5 minutes per feature × business.
 *
 * Routes:
 *   GET  /business-features              → index  (pick a business, see toggles)
 *   POST /business-features/toggle       → toggle a single feature on/off
 *   POST /business-features/enable-all   → enable every feature for a business
 *   POST /business-features/disable-all  → disable every feature for a business
 */
class BusinessFeaturesController extends Controller
{
    public function __construct()
    {
        // Superadmin (no business_id / Superadmin role) OR users with manage_modules permission.
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $isSuperadmin = $user && (
                $user->hasRole('Superadmin')
                || empty($user->business_id)
                || $user->username === 'saas_admin'
                || strtolower($user->email) === strtolower(env('SAAS_ADMIN_EMAIL', 'admin@apexpos.co.ke'))
            );
            if (! ($user && ($isSuperadmin || $user->can('manage_modules')))) {
                abort(403, 'You do not have permission to manage business features.');
            }
            return $next($request);
        });
    }

    /**
     * Show the feature management dashboard.
     * ?business_id=N selects a specific business.
     */
    public function index(Request $request)
    {
        $businesses = Business::orderBy('name')->get();

        // Default to the first business or whatever is selected
        $selectedId  = (int) $request->get('business_id', optional($businesses->first())->id ?? 0);
        $selectedBiz = $businesses->firstWhere('id', $selectedId);

        $featureList = BusinessFeatureSetting::featureList();

        // Build current state: feature_key → bool
        $states = [];
        if ($selectedId) {
            $rows = BusinessFeatureSetting::where('business_id', $selectedId)->get()->keyBy('feature_key');
            foreach ($featureList as $key => $meta) {
                $states[$key] = isset($rows[$key]) ? (bool) $rows[$key]->is_enabled : true; // default on
            }
        }

        // Group by category for display
        $grouped = [];
        foreach ($featureList as $key => $meta) {
            $grouped[$meta['category']][$key] = array_merge($meta, ['enabled' => $states[$key] ?? true]);
        }

        return view('business_features.index', compact('businesses', 'selectedId', 'selectedBiz', 'grouped'));
    }

    /**
     * Toggle a single feature for a business.
     */
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'business_id' => 'required|integer|exists:business,id',
            'feature_key' => 'required|string|max:100',
            'is_enabled'  => 'required|boolean',
        ]);

        BusinessFeatureSetting::updateOrCreate(
            ['business_id' => $data['business_id'], 'feature_key' => $data['feature_key']],
            ['is_enabled'  => $data['is_enabled']]
        );

        // Clear the cache so the change takes effect immediately
        BusinessFeatureSetting::clearCache((int) $data['business_id'], $data['feature_key']);

        return response()->json(['success' => true]);
    }

    /**
     * Enable ALL features for a business.
     */
    public function enableAll(Request $request)
    {
        $bid = (int) $request->validate(['business_id' => 'required|integer|exists:business,id'])['business_id'];
        $this->setAll($bid, true);
        return response()->json(['success' => true, 'message' => 'All features enabled.']);
    }

    /**
     * Disable ALL features for a business.
     */
    public function disableAll(Request $request)
    {
        $bid = (int) $request->validate(['business_id' => 'required|integer|exists:business,id'])['business_id'];
        $this->setAll($bid, false);
        return response()->json(['success' => true, 'message' => 'All features disabled.']);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function setAll(int $businessId, bool $enabled): void
    {
        foreach (array_keys(BusinessFeatureSetting::featureList()) as $key) {
            BusinessFeatureSetting::updateOrCreate(
                ['business_id' => $businessId, 'feature_key' => $key],
                ['is_enabled'  => $enabled]
            );
            BusinessFeatureSetting::clearCache($businessId, $key);
        }
    }
}
