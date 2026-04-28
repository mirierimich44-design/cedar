<?php

namespace Modules\BusinessIntelligence\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\BusinessIntelligence\Utils\BiAnalyzer;
use Modules\BusinessIntelligence\Utils\DataProcessor;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $biAnalyzer;
    protected $dataProcessor;
    protected $businessId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->businessId = $request->session()->get('user.business_id');
            $this->biAnalyzer = new BiAnalyzer($this->businessId);
            $this->dataProcessor = new DataProcessor($this->businessId);
            return $next($request);
        });
    }

    /**
     * Get sales analytics (Visual Dashboard)
     */
    public function getSalesAnalytics(Request $request)
    {
        $dateRange = $request->get('date_range', 30);

        if ($dateRange === 'custom') {
            $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
            $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        } elseif ($dateRange == 1) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays($dateRange - 1)->startOfDay();
        }
        $locationId = $request->get('location_id');

        $salesData = $this->dataProcessor->getSalesData($startDate, $endDate, $locationId);
        $topProducts = $this->dataProcessor->getTopSellingProducts($startDate, $endDate);
        
        // Calculate metrics
        $totalSales = $salesData->sum('total_sales');
        $totalTransactions = $salesData->sum('transaction_count');
        $averageSale = $salesData->avg('average_sale');
        
        // Return view with data
        return view('businessintelligence::analytics.sales', compact(
            'salesData',
            'topProducts',
            'totalSales',
            'totalTransactions',
            'averageSale',
            'dateRange'
        ));
    }

    /**
     * Get inventory analytics
     */
    public function getInventoryAnalytics(Request $request)
    {
        $locationId = $request->get('location_id');

        $inventory = $this->dataProcessor->getInventoryData($locationId);
        $inventoryChart = $this->biAnalyzer->getInventoryStatusChartData();
        $lowStockThreshold = config('businessintelligence.alerts.low_stock_threshold', 10);

        $lowStockItems = $inventory->filter(fn($item) => $item->qty_available <= $lowStockThreshold && $item->qty_available > 0);
        $outOfStockItems = $inventory->filter(fn($item) => $item->qty_available == 0);

        return response()->json([
            'success' => true,
            'data' => [
                'total_products' => $inventory->count(),
                'total_value' => $inventory->sum('stock_value'),
                'low_stock_items' => $lowStockItems->values(),
                'out_of_stock_items' => $outOfStockItems->values(),
                'chart_data' => $inventoryChart,
            ]
        ]);
    }

    /**
     * Get financial analytics
     */
    public function getFinancialAnalytics(Request $request)
    {
        $dateRange = $request->get('date_range', 30);

        if ($dateRange === 'custom') {
            $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
            $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        } elseif ($dateRange == 1) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays($dateRange - 1)->startOfDay();
        }

        $profitData = $this->dataProcessor->calculateProfit($startDate, $endDate);
        $profitChart = $this->biAnalyzer->getProfitComparisonChartData($startDate, $endDate);
        $expenseChart = $this->biAnalyzer->getExpenseBreakdownChartData($startDate, $endDate);
        $cashFlowChart = $this->biAnalyzer->getCashFlowChartData($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => [
                'profit_data' => $profitData,
                'profit_chart' => $profitChart,
                'expense_chart' => $expenseChart,
                'cash_flow_chart' => $cashFlowChart,
            ]
        ]);
    }

    /**
     * Get customer analytics
     */
    public function getCustomerAnalytics(Request $request)
    {
        $customerDues = $this->dataProcessor->getCustomerDues();
        $overdueThreshold = config('businessintelligence.alerts.overdue_days_threshold', 30);

        $overdueCustomers = $customerDues->filter(function($customer) use ($overdueThreshold) {
            return Carbon::parse($customer->last_transaction_date)->diffInDays(Carbon::now()) > $overdueThreshold;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'total_customers' => $customerDues->count(),
                'total_receivables' => $customerDues->sum('total_due'),
                'overdue_customers' => $overdueCustomers->values(),
                'average_due' => $customerDues->avg('total_due'),
            ]
        ]);
    }

    /**
     * Get supplier analytics
     */
    public function getSupplierAnalytics(Request $request)
    {
        $supplierDues = $this->dataProcessor->getSupplierDues();

        return response()->json([
            'success' => true,
            'data' => [
                'total_suppliers' => $supplierDues->count(),
                'total_payables' => $supplierDues->sum('total_due'),
                'suppliers_with_dues' => $supplierDues->values(),
                'average_due' => $supplierDues->avg('total_due'),
            ]
        ]);
    }

    /**
     * Get comprehensive analytics
     */
    public function getComprehensiveAnalytics(Request $request)
    {
        $dateRange = $request->get('date_range', 30);

        if ($dateRange === 'custom') {
            $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
            $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        } elseif ($dateRange == 1) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
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
     * Export analytics data
     */
    public function exportAnalytics(Request $request)
    {
        $type = $request->get('type', 'comprehensive');
        $dateRange = $request->get('date_range', 30);

        if ($dateRange === 'custom') {
            $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
            $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        } elseif ($dateRange == 1) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays($dateRange - 1)->startOfDay();
        }

        // Generate export data based on type
        $data = $this->biAnalyzer->getPerformanceSummary($startDate, $endDate);

        // In a real implementation, you would create an Excel/CSV file here
        // For now, return JSON
        return response()->json([
            'success' => true,
            'message' => 'Export feature coming soon',
            'data' => $data
        ]);
    }
}

