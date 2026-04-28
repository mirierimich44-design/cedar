<?php

namespace Modules\BusinessIntelligence\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BiAnalyzer
{
    protected $dataProcessor;
    protected $businessId;

    public function __construct($businessId = null)
    {
        $this->businessId = $this->getBusinessId($businessId);
        $this->dataProcessor = new DataProcessor($this->businessId);
    }

    /**
     * Get business ID safely
     */
    protected function getBusinessId($businessId = null)
    {
        if ($businessId) {
            return $businessId;
        }

        // Try to get from session if available
        try {
            if (request()->hasSession() && session()->has('user.business_id')) {
                return session()->get('user.business_id');
            }
        } catch (\Exception $e) {
            // Session not available
        }

        // Fallback to auth user's business
        if (auth()->check() && auth()->user()->business_id) {
            return auth()->user()->business_id;
        }

        return null;
    }

    /**
     * Get comprehensive KPI metrics
     */
    public function getKPIMetrics($startDate, $endDate)
    {
        // Disable caching temporarily to ensure fresh data
        // $cacheKey = "bi_kpi_metrics_{$this->businessId}_{$startDate}_{$endDate}";
        
        // return Cache::remember($cacheKey, config('businessintelligence.dashboard.cache_ttl', 600), function() use ($startDate, $endDate) {
        
        // Get fresh data without caching
        $profitData = $this->dataProcessor->calculateProfit($startDate, $endDate);
        $salesData = $this->dataProcessor->getSalesData($startDate, $endDate);
        $purchaseData = $this->dataProcessor->getPurchaseData($startDate, $endDate);
        $inventory = $this->dataProcessor->getInventoryData();
        $customerDues = $this->dataProcessor->getCustomerDues();
        $supplierDues = $this->dataProcessor->getSupplierDues();

        return [
                'revenue' => [
                    'value' => $profitData['sales'],
                    'label' => 'Total Revenue',
                    'icon' => 'fas fa-dollar-sign',
                    'color' => 'success',
                    'trend' => $this->calculateTrend($salesData, 'total_sales'),
                ],
                'profit' => [
                    'value' => $profitData['net_profit'],
                    'label' => 'Net Profit',
                    'icon' => 'fas fa-chart-line',
                    'color' => 'primary',
                    'trend' => null, // Would need previous period data
                    'percentage' => $profitData['profit_margin'],
                ],
                'expenses' => [
                    'value' => $profitData['expenses'],
                    'label' => 'Total Expenses',
                    'icon' => 'fas fa-money-bill-wave',
                    'color' => 'danger',
                    'trend' => null,
                ],
                'inventory_value' => [
                    'value' => $inventory->sum('stock_value'),
                    'label' => 'Inventory Value',
                    'icon' => 'fas fa-boxes',
                    'color' => 'info',
                    'count' => $inventory->count(),
                ],
                'customer_dues' => [
                    'value' => $customerDues->sum('total_due'),
                    'label' => 'Accounts Receivable',
                    'icon' => 'fas fa-users',
                    'color' => 'warning',
                    'count' => $customerDues->count(),
                ],
                'supplier_dues' => [
                    'value' => $supplierDues->sum('total_due'),
                    'label' => 'Accounts Payable',
                    'icon' => 'fas fa-truck',
                    'color' => 'danger',
                    'count' => $supplierDues->count(),
                ],
                'transactions' => [
                    'value' => $salesData->sum('transaction_count'),
                    'label' => 'Total Transactions',
                    'icon' => 'fas fa-receipt',
                    'color' => 'secondary',
                ],
                'average_sale' => [
                    'value' => $salesData->avg('average_sale') ?? 0,
                    'label' => 'Average Sale Value',
                    'icon' => 'fas fa-calculator',
                    'color' => 'info',
                ],
            ];
    }

    /**
     * Calculate trend percentage
     */
    protected function calculateTrend($data, $column)
    {
        $values = $data->pluck($column)->toArray();
        
        if (count($values) < 2) {
            return null;
        }

        $halfPoint = floor(count($values) / 2);
        $firstHalf = array_slice($values, 0, $halfPoint);
        $secondHalf = array_slice($values, $halfPoint);

        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);

        if ($firstAvg == 0) {
            return null;
        }

        $trend = (($secondAvg - $firstAvg) / $firstAvg) * 100;

        return [
            'value' => round($trend, 2),
            'direction' => $trend >= 0 ? 'up' : 'down',
        ];
    }

    /**
     * Get sales trend chart data
     */
    public function getSalesTrendChartData($startDate, $endDate, $groupBy = 'day')
    {
        $businessId = $this->getBusinessId();
        if (!$businessId) {
            return ['categories' => [], 'sales' => []];
        }

        // Format dates for DATETIME field comparison
        $startDateFormatted = Carbon::parse($startDate)->format('Y-m-d 00:00:00');
        $endDateFormatted = Carbon::parse($endDate)->format('Y-m-d 23:59:59');

        // Get actual sales data
        $salesData = DB::table('transactions')
            ->where('business_id', $businessId)
            ->where('type', 'sell')
            ->where('status', '!=', 'draft')
            ->where('transaction_date', '>=', $startDateFormatted)
            ->where('transaction_date', '<=', $endDateFormatted)
            ->selectRaw('DATE(transaction_date) as date, SUM(final_total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Debug logging (remove after testing)
        \Log::info('Sales Trend Query Debug', [
            'business_id' => $businessId,
            'start_date' => $startDateFormatted,
            'end_date' => $endDateFormatted,
            'sales_count' => $salesData->count(),
            'today_date' => Carbon::now()->format('Y-m-d'),
            'today_sales' => isset($salesData[Carbon::now()->format('Y-m-d')]) ? $salesData[Carbon::now()->format('Y-m-d')]->total : 'NO DATA'
        ]);

        // Generate all dates in range
        $categories = [];
        $sales = [];
        $currentDate = Carbon::parse($startDate)->startOfDay();
        $endDateCarbon = Carbon::parse($endDate)->endOfDay();

        while ($currentDate->lte($endDateCarbon)) {
            $dateKey = $currentDate->format('Y-m-d');
            $categories[] = $currentDate->format('M d');
            
            // Use actual sales data if exists, otherwise 0
            $sales[] = isset($salesData[$dateKey]) ? (float)$salesData[$dateKey]->total : 0;
            
            $currentDate->addDay();
        }

        return [
            'categories' => $categories,
            'series' => $sales, // Changed from 'sales' to 'series' for ApexCharts
            'sales' => $sales   // Keep both for backward compatibility
        ];
    }

    /**
     * Get profit comparison chart data (monthly profit vs expenses for last 6 months)
     * Uses the same calculation as the profit-loss report
     */
    public function getProfitComparisonChartData($startDate, $endDate)
    {
        $businessId = $this->getBusinessId();
        if (!$businessId) {
            return ['categories' => [], 'profit' => [], 'expenses' => []];
        }

        $months = [];
        $profits = [];
        $expenses = [];

        // Use the same TransactionUtil as the profit-loss report
        $transactionUtil = new \App\Utils\TransactionUtil();
        $permittedLocations = auth()->user()->permitted_locations();

        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth()->format('Y-m-d');
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth()->format('Y-m-d');
            
            // Use the same method as profit-loss report
            $data = $transactionUtil->getProfitLossDetails(
                $businessId,
                null, // location_id
                $monthStart,
                $monthEnd,
                null, // user_id
                $permittedLocations
            );
            
            $months[] = Carbon::parse($monthStart)->format('M');
            $profits[] = round($data['net_profit'] ?? 0, 2);
            $expenses[] = round($data['total_expense'] ?? 0, 2);
        }

        return [
            'categories' => $months,
            'profit' => $profits,
            'expenses' => $expenses,
        ];
    }

    /**
     * Get top products chart data
     */
    public function getTopProductsChartData($startDate, $endDate, $limit = 10)
    {
        $topProducts = $this->dataProcessor->getTopSellingProducts($startDate, $endDate, $limit);

        // If no products, return empty data
        if ($topProducts->isEmpty()) {
            return [
                'categories' => ['No sales data'],
                'data' => [0],
            ];
        }

        // Return format for ApexCharts horizontal bar
        return [
            'categories' => $topProducts->pluck('name')->toArray(),
            'data' => $topProducts->pluck('total_revenue')->map(function($value) {
                return round($value, 2);
            })->toArray(),
        ];
    }

    /**
     * Get expense breakdown chart data
     */
    public function getExpenseBreakdownChartData($startDate, $endDate)
    {
        $businessId = $this->getBusinessId();
        if (!$businessId) {
            return ['labels' => [], 'series' => []];
        }

        $expenses = DB::table('transactions as t')
            ->leftJoin('expense_categories as ec', 't.expense_category_id', '=', 'ec.id')
            ->where('t.business_id', $businessId)
            ->where('t.type', 'expense')
            ->whereBetween('t.transaction_date', [$startDate, $endDate])
            ->selectRaw('COALESCE(ec.name, "Other") as category, SUM(t.final_total) as total')
            ->groupBy('category')
            ->get();

        // If no data, return default categories
        if ($expenses->isEmpty()) {
            return [
                'labels' => ['Salaries', 'Rent', 'Utilities', 'Marketing', 'Others'],
                'series' => [0, 0, 0, 0, 0],
            ];
        }

        return [
            'labels' => $expenses->pluck('category')->toArray(),
            'series' => $expenses->pluck('total')->map(function($value) {
                return round($value, 2);
            })->toArray(),
        ];
    }

    /**
     * Get inventory status chart data
     */
    public function getInventoryStatusChartData()
    {
        $inventory = $this->dataProcessor->getInventoryData();
        
        $inStock = $inventory->where('qty_available', '>', config('businessintelligence.alerts.low_stock_threshold', 10))->count();
        $lowStock = $inventory->where('qty_available', '>', 0)->where('qty_available', '<=', config('businessintelligence.alerts.low_stock_threshold', 10))->count();
        $outOfStock = $inventory->where('qty_available', '<=', 0)->count();
        
        return [
            'labels' => ['In Stock', 'Low Stock', 'Out of Stock'],
            'series' => [$inStock, $lowStock, $outOfStock],
        ];
    }

    /**
     * Get cash flow chart data
     */
    public function getCashFlowChartData($startDate, $endDate)
    {
        $businessId = $this->getBusinessId();
        if (!$businessId) {
            return ['categories' => [], 'series' => []];
        }

        $dailyCashFlow = DB::table('transactions')
            ->where('business_id', $businessId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('DATE(transaction_date) as date, 
                          SUM(CASE WHEN type = "sell" AND status != "draft" THEN final_total ELSE 0 END) as inflow,
                          SUM(CASE WHEN type IN ("purchase", "expense") THEN final_total ELSE 0 END) as outflow')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'categories' => $dailyCashFlow->pluck('date')->map(function($date) {
                return date('M d', strtotime($date));
            })->toArray(),
            'series' => [
                [
                    'name' => 'Cash Inflow',
                    'data' => $dailyCashFlow->pluck('inflow')->toArray(),
                ],
                [
                    'name' => 'Cash Outflow',
                    'data' => $dailyCashFlow->pluck('outflow')->toArray(),
                ],
            ],
        ];
    }

    /**
     * Get performance summary
     */
    public function getPerformanceSummary($startDate, $endDate)
    {
        return [
            'sales_performance' => $this->analyzeSalesPerformance($startDate, $endDate),
            'inventory_health' => $this->analyzeInventoryHealth(),
            'financial_health' => $this->analyzeFinancialHealth($startDate, $endDate),
            'customer_metrics' => $this->analyzeCustomerMetrics(),
        ];
    }

    /**
     * Analyze sales performance
     */
    protected function analyzeSalesPerformance($startDate, $endDate)
    {
        $salesData = $this->dataProcessor->getSalesData($startDate, $endDate);
        $totalSales = $salesData->sum('total_sales');
        $totalTransactions = $salesData->sum('transaction_count');

        return [
            'total_sales' => $totalSales,
            'total_transactions' => $totalTransactions,
            'average_transaction' => $totalTransactions > 0 ? $totalSales / $totalTransactions : 0,
            'trend' => $this->calculateTrend($salesData, 'total_sales'),
        ];
    }

    /**
     * Analyze inventory health
     */
    protected function analyzeInventoryHealth()
    {
        $inventory = $this->dataProcessor->getInventoryData();
        $lowStockThreshold = config('businessintelligence.alerts.low_stock_threshold', 10);

        $totalValue = $inventory->sum('stock_value');
        $lowStockCount = $inventory->filter(fn($item) => $item->qty_available <= $lowStockThreshold)->count();
        $outOfStockCount = $inventory->where('qty_available', 0)->count();

        return [
            'total_value' => $totalValue,
            'total_products' => $inventory->count(),
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'health_score' => $this->calculateInventoryHealthScore($inventory->count(), $lowStockCount, $outOfStockCount),
        ];
    }

    /**
     * Calculate inventory health score
     */
    protected function calculateInventoryHealthScore($total, $lowStock, $outOfStock)
    {
        if ($total == 0) return 100;

        $lowStockPenalty = ($lowStock / $total) * 30;
        $outOfStockPenalty = ($outOfStock / $total) * 50;

        return max(0, 100 - $lowStockPenalty - $outOfStockPenalty);
    }

    /**
     * Analyze financial health
     */
    protected function analyzeFinancialHealth($startDate, $endDate)
    {
        $profitData = $this->dataProcessor->calculateProfit($startDate, $endDate);

        return [
            'profit_margin' => $profitData['profit_margin'],
            'net_profit' => $profitData['net_profit'],
            'health_score' => $this->calculateFinancialHealthScore($profitData),
        ];
    }

    /**
     * Calculate financial health score
     */
    protected function calculateFinancialHealthScore($profitData)
    {
        $score = 50; // Base score

        // Profit margin contribution (0-30 points)
        if ($profitData['profit_margin'] >= 30) {
            $score += 30;
        } elseif ($profitData['profit_margin'] >= 20) {
            $score += 20;
        } elseif ($profitData['profit_margin'] >= 10) {
            $score += 10;
        }

        // Profitability contribution (0-20 points)
        if ($profitData['net_profit'] > 0) {
            $score += 20;
        } elseif ($profitData['net_profit'] >= -1000) {
            $score += 10;
        }

        return min(100, $score);
    }

    /**
     * Analyze customer metrics
     */
    protected function analyzeCustomerMetrics()
    {
        $customerDues = $this->dataProcessor->getCustomerDues();

        return [
            'total_customers' => $customerDues->count(),
            'total_receivables' => $customerDues->sum('total_due'),
            'average_due' => $customerDues->count() > 0 ? $customerDues->avg('total_due') : 0,
        ];
    }

    /**
     * Get total customers count
     */
    public function getTotalCustomers()
    {
        return DB::table('contacts')
            ->where('business_id', $this->businessId)
            ->where('type', 'customer')
            ->count();
    }

    /**
     * Get total orders count
     */
    public function getTotalOrders($startDate, $endDate)
    {
        return DB::table('transactions')
            ->where('business_id', $this->businessId)
            ->where('type', 'sell')
            ->where('status', '!=', 'draft')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->count();
    }

    /**
     * Get total products count
     */
    public function getTotalProducts()
    {
        return DB::table('products')
            ->where('business_id', $this->businessId)
            ->where('type', '!=', 'modifier')
            ->count();
    }

    /**
     * Get profit margin percentage
     */
    public function getProfitMargin($startDate, $endDate)
    {
        $profitData = $this->dataProcessor->calculateProfit($startDate, $endDate);
        
        if ($profitData['sales'] > 0) {
            return round(($profitData['net_profit'] / $profitData['sales']) * 100, 2);
        }
        
        return 0;
    }

    /**
     * Get revenue sources chart data
     */
    public function getRevenueSourcesChartData($startDate, $endDate)
    {
        $businessId = $this->getBusinessId();
        if (!$businessId) {
            return ['labels' => [], 'series' => []];
        }

        // Try to get revenue by source field first
        $revenueBySource = DB::table('transactions')
            ->where('business_id', $businessId)
            ->where('type', 'sell')
            ->where('status', '!=', 'draft')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('COALESCE(source, "Direct Sales") as source_name, SUM(final_total) as total')
            ->groupBy('source_name')
            ->get();

        // If no source data, group by location
        if ($revenueBySource->isEmpty() || $revenueBySource->count() == 1) {
            $revenueByLocation = DB::table('transactions as t')
                ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
                ->where('t.business_id', $businessId)
                ->where('t.type', 'sell')
                ->where('t.status', '!=', 'draft')
                ->whereBetween('t.transaction_date', [$startDate, $endDate])
                ->selectRaw('COALESCE(bl.name, "Main Store") as location_name, SUM(t.final_total) as total')
                ->groupBy('location_name')
                ->get();

            // If still only one location, group by payment method
            if ($revenueByLocation->isEmpty() || $revenueByLocation->count() == 1) {
                $revenueByPayment = DB::table('transaction_payments as tp')
                    ->join('transactions as t', 'tp.transaction_id', '=', 't.id')
                    ->where('t.business_id', $businessId)
                    ->where('t.type', 'sell')
                    ->where('t.status', '!=', 'draft')
                    ->whereBetween('t.transaction_date', [$startDate, $endDate])
                    ->selectRaw('tp.method as payment_method, SUM(tp.amount) as total')
                    ->groupBy('payment_method')
                    ->get();

                if (!$revenueByPayment->isEmpty()) {
                    return [
                        'labels' => $revenueByPayment->pluck('payment_method')->map(function($method) {
                            return ucfirst(str_replace('_', ' ', $method));
                        })->toArray(),
                        'series' => $revenueByPayment->pluck('total')->map(function($value) {
                            return round($value, 2);
                        })->toArray(),
                    ];
                }
            }

            return [
                'labels' => $revenueByLocation->pluck('location_name')->toArray(),
                'series' => $revenueByLocation->pluck('total')->map(function($value) {
                    return round($value, 2);
                })->toArray(),
            ];
        }

        return [
            'labels' => $revenueBySource->pluck('source_name')->toArray(),
            'series' => $revenueBySource->pluck('total')->map(function($value) {
                return round($value, 2);
            })->toArray(),
        ];
    }

    /**
     * Get profit vs expense chart data
     */
    public function getProfitExpenseChartData($startDate, $endDate)
    {
        return $this->getProfitComparisonChartData($startDate, $endDate);
    }

    /**
     * Get customer growth chart data
     */
    public function getCustomerGrowthChartData($startDate, $endDate)
    {
        $months = [];
        $customers = [];

        for ($i = 8; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $newCustomers = DB::table('contacts')
                ->where('business_id', $this->businessId)
                ->where('type', 'customer')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            
            $months[] = $monthStart->format('M');
            $customers[] = $newCustomers;
        }

        return [
            'categories' => $months,
            'data' => $customers,
        ];
    }

    /**
     * Get Comprehensive Profit & Loss Chart Data
     * Returns complete P&L breakdown with all components
     */
    public function getProfitLossChartData($dateRange = 30)
    {
        $business_id = $this->getBusinessId();
        $endDate = Carbon::now()->endOfDay()->format('Y-m-d');
        $startDate = Carbon::now()->subDays($dateRange - 1)->startOfDay()->format('Y-m-d');
        
        // Get profit loss details using the same method as the existing report
        $transactionUtil = new \App\Utils\TransactionUtil();
        $permitted_locations = auth()->user()->permitted_locations();
        $location_id = null;
        $user_id = null;
        
        $data = $transactionUtil->getProfitLossDetails(
            $business_id,
            $location_id,
            $startDate,
            $endDate,
            $user_id,
            $permitted_locations
        );
        
        // Format data for waterfall/breakdown chart
        return [
            'categories' => [
                'Revenue',
                'COGS',
                'Gross Profit',
                'Expenses',
                'Net Profit'
            ],
            'data' => [
                [
                    'name' => 'Total Sales',
                    'value' => round($data['total_sell'], 2),
                    'color' => '#00E396'
                ],
                [
                    'name' => 'Cost of Goods Sold',
                    'value' => round($data['total_purchase'] - $data['total_purchase_discount'] + $data['opening_stock'] - $data['closing_stock'], 2),
                    'color' => '#FF4560'
                ],
                [
                    'name' => 'Gross Profit',
                    'value' => round(($data['total_sell'] - ($data['total_purchase'] - $data['total_purchase_discount'] + $data['opening_stock'] - $data['closing_stock'])), 2),
                    'color' => '#008FFB'
                ],
                [
                    'name' => 'Total Expenses',
                    'value' => round($data['total_expense'] + $data['total_sell_discount'] + $data['total_reward_amount'], 2),
                    'color' => '#FEB019'
                ],
                [
                    'name' => 'Net Profit',
                    'value' => round($data['net_profit'], 2),
                    'color' => ($data['net_profit'] >= 0) ? '#26de81' : '#fc5c65'
                ]
            ],
            'detailed_breakdown' => [
                'revenue' => [
                    'total_sales' => round($data['total_sell'], 2),
                    'sales_returns' => round($data['total_sell_return'], 2),
                    'discounts' => round($data['total_sell_discount'], 2),
                    'net_revenue' => round($data['total_sell'] - $data['total_sell_return'] - $data['total_sell_discount'], 2)
                ],
                'cogs' => [
                    'opening_stock' => round($data['opening_stock'], 2),
                    'purchases' => round($data['total_purchase'], 2),
                    'purchase_returns' => round($data['total_purchase_return'], 2),
                    'purchase_discounts' => round($data['total_purchase_discount'], 2),
                    'closing_stock' => round($data['closing_stock'], 2),
                    'total_cogs' => round($data['total_purchase'] - $data['total_purchase_discount'] + $data['opening_stock'] - $data['closing_stock'], 2)
                ],
                'expenses' => [
                    'total_expenses' => round($data['total_expense'], 2),
                    'adjustments' => round($data['total_adjustment'], 2),
                    'rewards' => round($data['total_reward_amount'], 2),
                    'shipping' => round($data['total_purchase_shipping_charge'] + $data['total_transfer_shipping_charges'], 2)
                ],
                'profit' => [
                    'gross_profit' => round(($data['total_sell'] - ($data['total_purchase'] - $data['total_purchase_discount'] + $data['opening_stock'] - $data['closing_stock'])), 2),
                    'net_profit' => round($data['net_profit'], 2),
                    'profit_margin' => $data['total_sell'] > 0 ? round(($data['net_profit'] / $data['total_sell']) * 100, 2) : 0
                ]
            ]
        ];
    }

    /**
     * Get Sales, Purchase & Expense Analytics Chart Data
     * Returns monthly comparison data for the last 6 months
     */
    public function getSalesPurchaseExpenseAnalyticsData($dateRange = 30)
    {
        $business_id = $this->getBusinessId();
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();

        // Get monthly data
        $months = [];
        $salesData = [];
        $purchaseData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $months[] = $monthStart->format('M Y');

            // Sales data
            $sales = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->whereIn('status', ['final', 'delivered'])
                ->whereBetween('transaction_date', [
                    $monthStart->format('Y-m-d 00:00:00'),
                    $monthEnd->format('Y-m-d 23:59:59')
                ])
                ->sum('final_total');
            $salesData[] = round($sales, 2);

            // Purchase data
            $purchases = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'purchase')
                ->whereIn('status', ['received', 'ordered'])
                ->whereBetween('transaction_date', [
                    $monthStart->format('Y-m-d 00:00:00'),
                    $monthEnd->format('Y-m-d 23:59:59')
                ])
                ->sum('final_total');
            $purchaseData[] = round($purchases, 2);

            // Expense data
            $expenses = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [
                    $monthStart->format('Y-m-d 00:00:00'),
                    $monthEnd->format('Y-m-d 23:59:59')
                ])
                ->sum('final_total');
            $expenseData[] = round($expenses, 2);
        }

        return [
            'categories' => $months,
            'sales' => $salesData,
            'purchases' => $purchaseData,
            'expenses' => $expenseData
        ];
    }

    /**
     * Get Payment Methods Distribution
     */
    public function getPaymentMethodsData($startDate, $endDate)
    {
        $businessId = $this->getBusinessId();
        if (!$businessId) {
            return ['labels' => [], 'series' => []];
        }

        $payments = DB::table('transaction_payments as tp')
            ->join('transactions as t', 'tp.transaction_id', '=', 't.id')
            ->where('t.business_id', $businessId)
            ->where('t.type', 'sell')
            ->where('t.status', '!=', 'draft')
            ->whereBetween('tp.paid_on', [
                Carbon::parse($startDate)->format('Y-m-d 00:00:00'),
                Carbon::parse($endDate)->format('Y-m-d 23:59:59')
            ])
            ->select(
                'tp.method',
                DB::raw('SUM(tp.amount) as total_amount'),
                DB::raw('COUNT(tp.id) as transaction_count')
            )
            ->groupBy('tp.method')
            ->get();

        if ($payments->isEmpty()) {
            return [
                'labels' => ['No payment data'],
                'series' => [0]
            ];
        }

        $labels = [];
        $series = [];

        foreach ($payments as $payment) {
            $labels[] = ucfirst(str_replace('_', ' ', $payment->method ?: 'Other'));
            $series[] = (float) $payment->total_amount;
        }

        return [
            'labels' => $labels,
            'series' => $series
        ];
    }

    /**
     * Get Customer Types (New vs Returning)
     */
    public function getCustomerTypesData($startDate, $endDate)
    {
        $businessId = $this->getBusinessId();
        if (!$businessId) {
            return ['returning' => 0, 'new' => 0];
        }

        // Get all customers who made purchases in this period
        $customersInPeriod = DB::table('transactions')
            ->where('business_id', $businessId)
            ->where('type', 'sell')
            ->where('status', '!=', 'draft')
            ->whereBetween('transaction_date', [
                Carbon::parse($startDate)->format('Y-m-d 00:00:00'),
                Carbon::parse($endDate)->format('Y-m-d 23:59:59')
            ])
            ->distinct()
            ->pluck('contact_id');

        if ($customersInPeriod->isEmpty()) {
            return ['returning' => 0, 'new' => 0];
        }

        $newCustomers = 0;
        $returningCustomers = 0;

        foreach ($customersInPeriod as $contactId) {
            // Check if customer had purchases before this period
            $previousPurchases = DB::table('transactions')
                ->where('business_id', $businessId)
                ->where('type', 'sell')
                ->where('status', '!=', 'draft')
                ->where('contact_id', $contactId)
                ->where('transaction_date', '<', Carbon::parse($startDate)->format('Y-m-d 00:00:00'))
                ->count();

            if ($previousPurchases > 0) {
                $returningCustomers++;
            } else {
                $newCustomers++;
            }
        }

        $total = $newCustomers + $returningCustomers;

        return [
            'returning' => $total > 0 ? round(($returningCustomers / $total) * 100, 1) : 0,
            'new' => $total > 0 ? round(($newCustomers / $total) * 100, 1) : 0,
            'returning_count' => $returningCustomers,
            'new_count' => $newCustomers
        ];
    }

    /**
     * Get Conversion Rate (Transactions with payments / Total transactions)
     */
    public function getConversionRateData($startDate, $endDate)
    {
        $businessId = $this->getBusinessId();
        if (!$businessId) {
            return ['rate' => 0, 'paid_count' => 0, 'total_count' => 0];
        }

        // Total sell transactions
        $totalTransactions = DB::table('transactions')
            ->where('business_id', $businessId)
            ->where('type', 'sell')
            ->where('status', '!=', 'draft')
            ->whereBetween('transaction_date', [
                Carbon::parse($startDate)->format('Y-m-d 00:00:00'),
                Carbon::parse($endDate)->format('Y-m-d 23:59:59')
            ])
            ->count();

        if ($totalTransactions == 0) {
            return ['rate' => 0, 'paid_count' => 0, 'total_count' => 0];
        }

        // Transactions that have received full or partial payment
        $paidTransactions = DB::table('transactions as t')
            ->join('transaction_payments as tp', 't.id', '=', 'tp.transaction_id')
            ->where('t.business_id', $businessId)
            ->where('t.type', 'sell')
            ->where('t.status', '!=', 'draft')
            ->whereBetween('t.transaction_date', [
                Carbon::parse($startDate)->format('Y-m-d 00:00:00'),
                Carbon::parse($endDate)->format('Y-m-d 23:59:59')
            ])
            ->distinct('t.id')
            ->count('t.id');

        $conversionRate = round(($paidTransactions / $totalTransactions) * 100, 1);

        return [
            'rate' => $conversionRate,
            'paid_count' => $paidTransactions,
            'total_count' => $totalTransactions
        ];
    }
}

