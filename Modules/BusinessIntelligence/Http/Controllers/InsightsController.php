<?php

namespace Modules\BusinessIntelligence\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\BusinessIntelligence\Entities\BiInsight;
use Modules\BusinessIntelligence\Utils\InsightGenerator;
use Carbon\Carbon;

class InsightsController extends Controller
{
    protected $insightGenerator;
    protected $businessId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->businessId = $request->session()->get('user.business_id');
            $this->insightGenerator = new InsightGenerator($this->businessId);
            return $next($request);
        });
    }

    /**
     * Display insights page
     */
    public function index(Request $request)
    {
        $businessId = $request->session()->get('user.business_id');
        
        $insights = BiInsight::where('business_id', $businessId)
            ->orderByDesc('priority')
            ->orderByDesc('confidence_score')
            ->orderByDesc('insight_date')
            ->paginate(20);

        return view('businessintelligence::insights.index', compact('insights'));
    }

    /**
     * Get insights data via AJAX
     */
    public function getInsights(Request $request)
    {
        $businessId = $request->session()->get('user.business_id');
        $type = $request->get('type');
        $status = $request->get('status', 'active');
        $priority = $request->get('priority');

        \Log::info('Getting insights', [
            'business_id' => $businessId,
            'type' => $type,
            'status' => $status,
            'priority' => $priority
        ]);

        $query = BiInsight::where('business_id', $businessId);

        if ($type) {
            $query->where('insight_type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        $insights = $query->orderByDesc('priority')
            ->orderByDesc('confidence_score')
            ->orderByDesc('insight_date')
            ->get();

        \Log::info('Found insights', ['count' => $insights->count()]);

        return response()->json([
            'success' => true,
            'data' => $insights
        ]);
    }

    /**
     * Generate new insights
     */
    public function generateInsights(Request $request)
    {
        $dateRange = $request->get('date_range', 30);
        
        \Log::info('Generating insights', [
            'business_id' => $this->businessId,
            'date_range' => $dateRange
        ]);
        
        try {
            $insights = $this->insightGenerator->generateAllInsights($dateRange);

            \Log::info('Insights generated', ['count' => count($insights)]);

            return response()->json([
                'success' => true,
                'message' => 'Insights generated successfully',
                'count' => count($insights),
                'data' => $insights
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to generate insights', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate insights: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show single insight
     */
    public function show($id, Request $request)
    {
        $businessId = $request->session()->get('user.business_id');
        
        $insight = BiInsight::where('business_id', $businessId)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $insight
        ]);
    }

    /**
     * Acknowledge an insight
     */
    public function acknowledge($id, Request $request)
    {
        $businessId = $request->session()->get('user.business_id');
        $userId = $request->session()->get('user.id');
        
        $insight = BiInsight::where('business_id', $businessId)
            ->findOrFail($id);

        $insight->acknowledge($userId, $request->input('note'));

        return response()->json([
            'success' => true,
            'message' => 'Insight acknowledged successfully',
            'data' => $insight
        ]);
    }

    /**
     * Dismiss an insight
     */
    public function dismiss($id, Request $request)
    {
        $businessId = $request->session()->get('user.business_id');
        
        $insight = BiInsight::where('business_id', $businessId)
            ->findOrFail($id);

        $insight->update(['status' => 'dismissed']);

        return response()->json([
            'success' => true,
            'message' => 'Insight dismissed successfully'
        ]);
    }

    /**
     * Resolve an insight
     */
    public function resolve($id, Request $request)
    {
        $businessId = $request->session()->get('user.business_id');
        
        $insight = BiInsight::where('business_id', $businessId)
            ->findOrFail($id);

        $insight->update([
            'status' => 'resolved',
            'acknowledgement_note' => $request->input('resolution_note')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Insight resolved successfully'
        ]);
    }

    /**
     * Get insights by type
     */
    public function getByType($type, Request $request)
    {
        $insights = $this->insightGenerator->getInsightsByType($type, 10);

        return response()->json([
            'success' => true,
            'data' => $insights
        ]);
    }

    /**
     * Get critical insights
     */
    public function getCritical(Request $request)
    {
        $insights = $this->insightGenerator->getCriticalInsights();

        return response()->json([
            'success' => true,
            'data' => $insights
        ]);
    }
}

