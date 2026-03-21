<?php

namespace App\Http\Controllers;

use App\CoolerAsset;
use App\CoolerComplianceLog;
use App\CoolerDealer;
use App\CoolerDocument;
use App\CoolerRetrieval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoolerComplianceController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!auth()->user()->can('cooler.compliance.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Asset summary counts
        $assetStats = CoolerAsset::forBusiness($business_id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Dealer compliance overview
        $dealerStats = [
            'total'        => CoolerDealer::forBusiness($business_id)->count(),
            'active'       => CoolerDealer::forBusiness($business_id)->active()->count(),
            'low_score'    => CoolerDealer::forBusiness($business_id)->where('compliance_score', '<', 80)->count(),
            'critical'     => CoolerDealer::forBusiness($business_id)->where('compliance_score', '<', 50)->count(),
        ];

        // Recent non-compliant logs
        $nonCompliantLogs = CoolerComplianceLog::with(['dealer', 'cooler'])
            ->nonCompliant()
            ->whereHas('dealer', fn($q) => $q->where('business_id', $business_id))
            ->latest('checked_at')
            ->take(10)
            ->get();

        // Documents expiring within 30 days
        $expiringDocs = CoolerDocument::expiringSoon(30)
            ->whereHasMorph('documentable', [\App\CoolerDealer::class], fn($q) => $q->where('business_id', $business_id))
            ->with('documentable')
            ->orderBy('expires_at')
            ->take(20)
            ->get();

        // Expired documents
        $expiredDocs = CoolerDocument::whereNotNull('expires_at')
            ->whereDate('expires_at', '<', now())
            ->whereHasMorph('documentable', [\App\CoolerDealer::class], fn($q) => $q->where('business_id', $business_id))
            ->with('documentable')
            ->count();

        // Recent retrievals
        $recentRetrievals = CoolerRetrieval::forBusiness($business_id)
            ->with(['dealer', 'cooler'])
            ->latest()
            ->take(5)
            ->get();

        // Retrieval reasons breakdown
        $retrievalReasons = CoolerRetrieval::forBusiness($business_id)
            ->selectRaw('reason, count(*) as total')
            ->groupBy('reason')
            ->pluck('total', 'reason');

        return view('cooler.compliance.dashboard', compact(
            'assetStats', 'dealerStats', 'nonCompliantLogs',
            'expiringDocs', 'expiredDocs', 'recentRetrievals', 'retrievalReasons'
        ));
    }

    public function reports(Request $request)
    {
        if (!auth()->user()->can('cooler.report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $dealers = CoolerDealer::forBusiness($business_id)
            ->withCount(['coolers', 'agreements'])
            ->with(['complianceLogs' => fn($q) => $q->latest()->take(1)])
            ->get();

        $assetStatusBreakdown = CoolerAsset::forBusiness($business_id)
            ->selectRaw('status, asset_type, count(*) as total')
            ->groupBy('status', 'asset_type')
            ->get();

        $retrievalsByReason = CoolerRetrieval::forBusiness($business_id)
            ->selectRaw('reason, count(*) as total')
            ->groupBy('reason')
            ->get();

        return view('cooler.compliance.reports', compact('dealers', 'assetStatusBreakdown', 'retrievalsByReason'));
    }

    /**
     * Recalculate compliance score for a dealer (called manually or via scheduler).
     */
    public function recalculateScore(Request $request, int $dealerId)
    {
        if (!auth()->user()->can('cooler.compliance.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $dealer      = CoolerDealer::forBusiness($business_id)->findOrFail($dealerId);

        // Simplified scoring: start at 100, deduct for each non-compliant log in last 90 days
        $recentNonCompliant = CoolerComplianceLog::where('dealer_id', $dealerId)
            ->where('status', 'non_compliant')
            ->where('checked_at', '>=', now()->subDays(90))
            ->count();

        $score = max(0, 100 - ($recentNonCompliant * 10));
        $dealer->update(['compliance_score' => $score]);

        return response()->json(['success' => true, 'score' => $score]);
    }

    /**
     * Manually flag a dealer as non-compliant.
     */
    public function flagDealer(Request $request, int $dealerId)
    {
        if (!auth()->user()->can('cooler.compliance.view')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'check_type' => 'required|in:' . implode(',', array_keys(CoolerComplianceLog::$checkTypes)),
            'details'    => 'nullable|string',
        ]);

        $business_id = $request->session()->get('user.business_id');
        $dealer      = CoolerDealer::forBusiness($business_id)->findOrFail($dealerId);

        CoolerComplianceLog::create([
            'dealer_id'  => $dealerId,
            'cooler_id'  => $dealer->coolers()->first()?->id,
            'check_type' => $request->check_type,
            'status'     => 'non_compliant',
            'details'    => $request->details ? ['note' => $request->details] : null,
            'checked_at' => now(),
            'checked_by' => auth()->id(),
        ]);

        // Update score
        $this->recalculateScore($request, $dealerId);

        return response()->json(['success' => true]);
    }
}
