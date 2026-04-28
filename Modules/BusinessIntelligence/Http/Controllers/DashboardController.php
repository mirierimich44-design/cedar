<?php

namespace Modules\BusinessIntelligence\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\BusinessIntelligence\Utils\BiAnalyzer;
use Modules\BusinessIntelligence\Utils\InsightGenerator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $biAnalyzer;
    protected $insightGenerator;
    protected $businessId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->businessId = $request->session()->get('user.business_id');
            $this->biAnalyzer = new BiAnalyzer($this->businessId);
            $this->insightGenerator = new InsightGenerator($this->businessId);
            return $next($request);
        });
    }

    /**
     * Display the main BI dashboard
     */
    public function index(Request $request)
    {
        $businessId = $request->session()->get('user.business_id');

        // Handle date range selection
        $dateRange = $request->get('date_range', 30);

        if ($dateRange === 'custom') {
            $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
            $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        } elseif ($dateRange == 1) {
            // Today
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            // Default date range: last N days
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays($dateRange - 1)->startOfDay();
        }

        // Get KPI metrics
        $kpis = $this->biAnalyzer->getKPIMetrics($startDate, $endDate);
        
        // Add additional KPIs for the modern dashboard
        $kpis = array_merge($kpis, [
            'customers' => [
                'label' => 'Total Customers',
                'value' => $this->biAnalyzer->getTotalCustomers(),
                'icon' => 'fa fa-users',
                'color' => 'primary'
            ],
            'orders' => [
                'label' => 'Total Orders',
                'value' => $this->biAnalyzer->getTotalOrders($startDate, $endDate),
                'icon' => 'fa fa-shopping-cart',
                'color' => 'success'
            ],
            'products' => [
                'label' => 'Total Products',
                'value' => $this->biAnalyzer->getTotalProducts(),
                'icon' => 'fa fa-cube',
                'color' => 'info'
            ],
            'profit_margin' => [
                'label' => 'Profit Margin',
                'value' => $this->biAnalyzer->getProfitMargin($startDate, $endDate),
                'icon' => 'fa fa-percent',
                'color' => 'warning',
                'percentage' => true
            ]
        ]);

        // Get recent insights
        $insights = $this->insightGenerator->getActiveInsights(5);

        // Auto-generate insights if none exist (first-time load)
        if ($insights->count() == 0) {
            try {
                Log::info('Auto-generating insights for business: ' . $businessId);
                $this->insightGenerator->generateAllInsights($dateRange);
                $insights = $this->insightGenerator->getActiveInsights(5);
                Log::info('Auto-generated ' . $insights->count() . ' insights');
            } catch (\Exception $e) {
                Log::error('Failed to auto-generate insights: ' . $e->getMessage());
                // Continue anyway, will show empty state with generate button
            }
        }

        // Get critical insights
        $criticalInsights = $this->insightGenerator->getCriticalInsights();

        // Get currency settings from session (Ultimate POS standard)
        $currencySymbol = session('currency.symbol', '৳');
        $currencyPrecision = session('business.currency_precision', 2);
        $currencyDecimalSeparator = session('currency.decimal_separator', '.');
        $currencyThousandSeparator = session('currency.thousand_separator', ',');

        return view('businessintelligence::dashboard.index', compact(
            'kpis',
            'insights',
            'criticalInsights',
            'dateRange',
            'currencySymbol',
            'currencyPrecision',
            'currencyDecimalSeparator',
            'currencyThousandSeparator'
        ));
    }

    /**
     * Get KPI data via AJAX
     */
    public function getKPIs(Request $request)
    {
        $dateRange = $request->get('date_range', 30);

        if ($dateRange === 'custom') {
            $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
            $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        } elseif ($dateRange == 1) {
            // Today
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            // Default date range: last N days
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays($dateRange - 1)->startOfDay();
        }

        $kpis = $this->biAnalyzer->getKPIMetrics($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $kpis
        ]);
    }

    /**
     * Get chart data
     */
    public function getChartData(Request $request)
    {
        $chartType = $request->get('chart_type');
        $dateRange = $request->get('date_range', 30);

        if ($dateRange === 'custom') {
            $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
            $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        } elseif ($dateRange == 1) {
            // Today
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            // Default date range: last N days
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays($dateRange - 1)->startOfDay();
        }

        $chartData = [];

        switch ($chartType) {
            case 'sales_trend':
                $chartData = $this->biAnalyzer->getSalesTrendChartData($startDate, $endDate);
                break;
            case 'revenue_sources':
                $chartData = $this->biAnalyzer->getRevenueSourcesChartData($startDate, $endDate);
                break;
            case 'profit_expense':
                $chartData = $this->biAnalyzer->getProfitExpenseChartData($startDate, $endDate);
                break;
            case 'cash_flow':
                $chartData = $this->biAnalyzer->getCashFlowChartData($startDate, $endDate);
                break;
            case 'top_products':
                $chartData = $this->biAnalyzer->getTopProductsChartData($startDate, $endDate);
                break;
            case 'inventory_status':
                $chartData = $this->biAnalyzer->getInventoryStatusChartData();
                break;
            case 'expense_breakdown':
                $chartData = $this->biAnalyzer->getExpenseBreakdownChartData($startDate, $endDate);
                break;
            case 'customer_growth':
                $chartData = $this->biAnalyzer->getCustomerGrowthChartData($startDate, $endDate);
                break;
            case 'sales_purchase_expense_analytics':
                $chartData = $this->biAnalyzer->getSalesPurchaseExpenseAnalyticsData($dateRange);
                break;
            case 'profit_loss_complete':
                $chartData = $this->biAnalyzer->getProfitLossChartData($dateRange);
                break;
            case 'payment_methods':
                $chartData = $this->biAnalyzer->getPaymentMethodsData($startDate, $endDate);
                break;
            case 'customer_types':
                $chartData = $this->biAnalyzer->getCustomerTypesData($startDate, $endDate);
                break;
            case 'conversion_rate':
                $chartData = $this->biAnalyzer->getConversionRateData($startDate, $endDate);
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid chart type'], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $chartData
        ]);
    }

    /**
     * Get performance summary
     */
    public function getPerformanceSummary(Request $request)
    {
        $dateRange = $request->get('date_range', 30);

        if ($dateRange === 'custom') {
            $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
            $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        } elseif ($dateRange == 1) {
            // Today
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            // Default date range: last N days
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays($dateRange - 1)->startOfDay();
        }

        $summary = $this->biAnalyzer->getPerformanceSummary($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Refresh dashboard data
     */
    public function refreshData(Request $request)
    {
        $dateRange = $request->get('date_range', 30);

        if ($dateRange === 'custom') {
            $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
            $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        } elseif ($dateRange == 1) {
            // Today
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            // Default date range: last N days
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays($dateRange - 1)->startOfDay();
        }

        // Clear cache - use Cache facade properly
        try {
            Cache::forget("bi_kpi_metrics_{$request->session()->get('user.business_id')}_{$startDate}_{$endDate}");
        } catch (\Exception $e) {
            // Cache might not be available, continue anyway
            Log::warning('Cache forget failed: ' . $e->getMessage());
        }

        // Get fresh data
        $kpis = $this->biAnalyzer->getKPIMetrics($startDate, $endDate);

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data refreshed successfully',
            'data' => $kpis
        ]);
    }

    /**
     * Export dashboard data
     */
    public function exportDashboard(Request $request)
    {
        $dateRange = $request->get('date_range', 30);

        if ($dateRange === 'custom') {
            $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
            $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        } elseif ($dateRange == 1) {
            // Today
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            // Default date range: last N days
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays($dateRange - 1)->startOfDay();
        }

        // Get all dashboard data
        $kpis = $this->biAnalyzer->getKPIMetrics($startDate, $endDate);
        
        // For now, return JSON. Can be enhanced to PDF/Excel later
        return response()->json([
            'success' => true,
            'data' => [
                'date_range' => $dateRange,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'kpis' => $kpis,
            ],
            'message' => 'Dashboard data exported successfully'
        ]);
    }
}

