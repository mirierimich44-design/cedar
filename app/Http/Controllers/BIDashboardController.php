<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Utils\ModuleUtil;

class BIDashboardController extends Controller
{
    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Dashboard Home - Initial Data Load
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $business = \App\Business::find($business_id);
        
        // Basic Stats
        $this_month = [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        $last_month = [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];

        $revenue_now = DB::table('transactions')->where('business_id', $business_id)->where('type', 'sell')->where('status', 'final')->whereBetween('transaction_date', $this_month)->sum('final_total');
        $revenue_last = DB::table('transactions')->where('business_id', $business_id)->where('type', 'sell')->where('status', 'final')->whereBetween('transaction_date', $last_month)->sum('final_total');

        $customers_now = DB::table('contacts')->where('business_id', $business_id)->where('type', 'customer')->whereBetween('created_at', $this_month)->count();
        $customers_last = DB::table('contacts')->where('business_id', $business_id)->where('type', 'customer')->whereBetween('created_at', $last_month)->count();

        $low_stock_count = DB::table('variation_location_details')->join('products', 'products.id', '=', 'variation_location_details.product_id')->where('products.business_id', $business_id)->whereRaw('qty_available <= alert_quantity')->count();

        // 12 Month Trend
        $months = []; $revenue_chart = []; $parcel_chart = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $months[] = $m->format('M Y');
            $revenue_chart[] = (float)DB::table('transactions')->where('business_id', $business_id)->where('type', 'sell')->whereMonth('transaction_date', $m->month)->whereYear('transaction_date', $m->year)->sum('final_total');
            $parcel_chart[] = DB::table('parcels')->where('business_id', $business_id)->whereMonth('created_at', $m->month)->whereYear('created_at', $m->year)->count();
        }

        $payment_methods = DB::table('transaction_payments')->join('transactions', 'transactions.id', '=', 'transaction_payments.transaction_id')->where('transactions.business_id', $business_id)->select('method', DB::raw('SUM(amount) as total'))->groupBy('method')->get();
        $top_routes = DB::table('parcels')->where('parcels.business_id', $business_id)->leftJoin('parcel_stations as s1', 's1.id', '=', 'parcels.origin_station_id')->leftJoin('parcel_stations as s2', 's2.id', '=', 'parcels.destination_station_id')->select(DB::raw('CONCAT(s1.name, " ➔ ", s2.name) as route'), DB::raw('SUM(charge_amount) as total'))->groupBy('route')->orderBy('total', 'desc')->limit(8)->get();
        $heatmap_raw = DB::table('transactions')->where('business_id', $business_id)->where('type', 'sell')->select(DB::raw('DAYOFWEEK(transaction_date) as day'), DB::raw('HOUR(transaction_date) as hour'), DB::raw('COUNT(*) as count'))->groupBy('day', 'hour')->get();

        $parcels_now = DB::table('parcels')->where('business_id', $business_id)->whereBetween('created_at', $this_month)->count();
        $parcels_last = DB::table('parcels')->where('business_id', $business_id)->whereBetween('created_at', $last_month)->count();

        $data = compact('revenue_now', 'revenue_last', 'parcels_now', 'parcels_last', 'customers_now', 'customers_last', 'low_stock_count', 'months', 'revenue_chart', 'parcel_chart', 'payment_methods', 'top_routes', 'heatmap_raw', 'business');

        return view('dashboard.bi', $data);
    }

    public function getInsights()
    {
        $business_id = request()->session()->get('user.business_id');
        $business = \App\Business::find($business_id);
        $type = $business->business_type ?? 'retail';
        
        $cache_key = "bi_insights_{$business_id}_{$type}";
        if (request()->has('refresh')) Cache::forget($cache_key);

        return Cache::remember($cache_key, 1800, function() use ($business_id, $type) {
            $context = $this->getDetailedContext($business_id);
            
            $specialist_role = match($type) {
                'hospital' => 'Hospital Director and Medical Auditor',
                'pharmacy' => 'Pharmacy Chain Manager and Compliance Officer',
                'restaurant' => 'Executive Chef and Restaurant Operations Consultant',
                'logistics' => 'Logistics Fleet Manager and Supply Chain Analyst',
                default => 'Business Intelligence Strategist'
            };

            $prompt = "You are a senior {$specialist_role} in Kenya. Analyze this comprehensive business data and return exactly 6 deep-dive bullet points. 
            Crucial: Be industry-specific. If hospital data is present, talk about patients and labs. If logistics, talk about routes and failures.
            Format with: [OPPORTUNITY], [WARNING], [ANOMALY], [GROWTH], [ACTION], [FINANCIAL].
            Data Context: " . json_encode($context);

            $result = $this->callGemini($prompt);
            $insights = array_filter(explode("\n", $result), fn($line) => !empty(trim($line)) && strpos($line, '[') !== false);
            return response()->json(['insights' => array_values($insights)]);
        });
    }

    public function askQuestion(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = \App\Business::find($business_id);
        $context = $this->getDetailedContext($business_id);
        
        $prompt = "You are an expert AI Business Advisor for a Kenyan company typed as '{$business->business_type}'. 
        Using the context below, answer the user question. Focus on operational excellence and profit maximization.
        Context: " . json_encode($context) . "\nQuestion: " . $request->question;

        return response()->json(['answer' => $this->callGemini($prompt)]);
    }

    public function getPredictions()
    {
        $business_id = request()->session()->get('user.business_id');
        $context = $this->getDetailedContext($business_id);
        $prompt = "Analyze this data and return a JSON object with 3 prediction keys: 'revenue_forecast', 'operational_risk', and 'inventory_strategy'. 
        Be extremely specific to the business type. Context: " . json_encode($context);

        $result = $this->callGemini($prompt, true);
        return response()->json(json_decode(preg_replace('/```json|```/', '', $result)));
    }

    public function getDeepIntelligence()
    {
        $business_id = request()->session()->get('user.business_id');

        // Revenue leakage: unbilled hospital services
        $unbilled_labs    = DB::table('hospital_lab_requests')->where('business_id', $business_id)->whereNull('transaction_id')->count();
        $unbilled_imaging = DB::table('hospital_radiography_requests')->where('business_id', $business_id)->whereNull('transaction_id')->count();
        $avg_lab_price    = DB::table('hospital_lab_tests')->where('business_id', $business_id)->avg('price') ?? 500;
        $estimated_loss   = round(($unbilled_labs + $unbilled_imaging) * $avg_lab_price, 2);

        // Hospital efficiency
        $bed_data = DB::table('hospital_beds')
            ->join('hospital_wards', 'hospital_wards.id', '=', 'hospital_beds.ward_id')
            ->where('hospital_wards.business_id', $business_id)
            ->select(DB::raw("SUM(CASE WHEN status='occupied' THEN 1 ELSE 0 END) as occupied"), DB::raw('COUNT(*) as total'))
            ->first();
        $occupancy = ($bed_data && $bed_data->total > 0)
            ? round($bed_data->occupied / $bed_data->total * 100, 1)
            : 0;
        $avg_consultation = DB::table('hospital_consultations')
            ->where('business_id', $business_id)
            ->where('status', 'completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_mins')
            ->value('avg_mins');

        // Logistics risk by route
        $route_risk = DB::table('parcels')
            ->where('parcels.business_id', $business_id)
            ->leftJoin('parcel_stations as s1', 's1.id', '=', 'parcels.origin_station_id')
            ->leftJoin('parcel_stations as s2', 's2.id', '=', 'parcels.destination_station_id')
            ->select(
                DB::raw('CONCAT(COALESCE(s1.name,"?"), " → ", COALESCE(s2.name,"?")) as route'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status="failed" THEN 1 ELSE 0 END) as failures')
            )
            ->groupBy('route')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'revenue_leakage' => [
                'estimated_loss'  => $estimated_loss,
                'unbilled_labs'   => $unbilled_labs,
                'unbilled_imaging'=> $unbilled_imaging,
            ],
            'hospital_efficiency' => [
                'bed_occupancy_percent'   => $occupancy,
                'avg_consultation_mins'   => round($avg_consultation ?? 0, 1),
            ],
            'logistics_risk' => [
                'route_risk' => $route_risk,
            ],
        ]);
    }

    /**
     * Run Auto-Procurement manually from Dashboard
     */
    public function runAutoProcurement()
    {
        try {
            $result = (new \App\Services\AutoProcurementService())->run();

            if ($result['count'] === 0) {
                return response()->json(['success' => true, 'msg' => 'All stock levels are healthy. No reorders needed.', 'items' => []]);
            }

            $msg = "Found <strong>{$result['count']}</strong> products needing reorder. "
                 . "Estimated restock cost: <strong>KES " . number_format($result['total_value'], 2) . "</strong>. "
                 . "Scanned at {$result['scanned_at']}.";

            return response()->json(['success' => true, 'msg' => $msg, 'items' => $result['items']]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function generateCampaign(Request $request)
    {
        $prompt = "Write a professional, concise WhatsApp message for a customer based on this business insight: '{$request->insight}'. 
        Keep it friendly, Kenyan-market appropriate, and actionable. Add a placeholder [Customer Name].";
        return response()->json(['message' => $this->callGemini($prompt)]);
    }

    public function saveSettings(Request $request)
    {
        $business = \App\Business::findOrFail($request->session()->get('user.business_id'));
        $settings = $business->common_settings ?? [];
        $settings['gemini_api_key'] = $request->gemini_api_key;
        $business->common_settings = $settings;
        $business->save();

        // Refresh the business object in session so form shows saved key immediately
        $request->session()->put('business', $business->fresh());

        // Bust the AI insight cache so new key takes effect instantly
        \Cache::forget("bi_insights_{$business->id}_{$business->business_type}");

        return redirect()->back()->with('status', ['success' => 1, 'msg' => 'AI Configuration Updated. AI insights will reload with your new key.']);
    }

    private function getDetailedContext($business_id)
    {
        $summary = [
            'general' => [
                'revenue_6m' => DB::table('transactions')
                    ->where('transactions.business_id', $business_id)
                    ->where('type', 'sell')
                    ->where('transaction_date', '>=', Carbon::now()->subMonths(6))
                    ->select(DB::raw('MONTHNAME(transaction_date) as month'), DB::raw('SUM(final_total) as total'))
                    ->groupBy('month')
                    ->get(),
                'payment_split' => DB::table('transaction_payments')
                    ->join('transactions', 'transactions.id', '=', 'transaction_payments.transaction_id')
                    ->where('transactions.business_id', $business_id)
                    ->select('method', DB::raw('SUM(amount) as total'))
                    ->groupBy('method')
                    ->get(),
            ],
            'inventory' => [
                'stock_value' => DB::table('variation_location_details')
                    ->join('products', 'products.id', '=', 'variation_location_details.product_id')
                    ->join('variations', 'variations.product_id', '=', 'products.id')
                    ->where('products.business_id', $business_id)
                    ->select(DB::raw('SUM(variation_location_details.qty_available * variations.default_sell_price) as total_value'))
                    ->first(),
                'low_stock' => DB::table('products')
                    ->where('products.business_id', $business_id)
                    ->whereRaw('alert_quantity > 0')
                    ->limit(5)
                    ->get()
            ]
        ];

        // Hospital Intelligence
        if ($this->moduleUtil->isModuleEnabled('hospital_module', $business_id)) {
            $summary['hospital'] = [
                'total_patients' => DB::table('contacts')->where('contacts.business_id', $business_id)->where('type', 'customer')->count(),
                'recent_consultations' => DB::table('hospital_consultations')->where('hospital_consultations.business_id', $business_id)->where('created_at', '>=', Carbon::now()->subDays(30))->count(),
                'unbilled_services' => [
                    'labs' => DB::table('hospital_lab_requests')->where('hospital_lab_requests.business_id', $business_id)->whereNull('transaction_id')->count(),
                    'imaging' => DB::table('hospital_radiography_requests')->where('hospital_radiography_requests.business_id', $business_id)->whereNull('transaction_id')->count()
                ],
                'bed_occupancy' => DB::table('hospital_beds')
                    ->join('hospital_wards', 'hospital_wards.id', '=', 'hospital_beds.ward_id')
                    ->where('hospital_wards.business_id', $business_id)
                    ->select(DB::raw("SUM(CASE WHEN status='occupied' THEN 1 ELSE 0 END) as occupied"), DB::raw('COUNT(*) as total'))
                    ->first()
            ];
        }

        // Logistics/Parcel Intelligence
        if ($this->moduleUtil->isModuleEnabled('parcel', $business_id)) {
            $summary['logistics'] = [
                'total_parcels_30d' => DB::table('parcels')->where('parcels.business_id', $business_id)->where('created_at', '>=', Carbon::now()->subDays(30))->count(),
                'failure_rate' => DB::table('parcels')->where('parcels.business_id', $business_id)->select(DB::raw('SUM(CASE WHEN status="failed" THEN 1 ELSE 0 END) as failed'), DB::raw('COUNT(*) as total'))->first(),
                'top_routes' => DB::table('parcels')
                    ->where('parcels.business_id', $business_id)
                    ->leftJoin('parcel_stations as s1', 's1.id', '=', 'parcels.origin_station_id')
                    ->leftJoin('parcel_stations as s2', 's2.id', '=', 'parcels.destination_station_id')
                    ->select(DB::raw('CONCAT(s1.name, " to ", s2.name) as route'), DB::raw('COUNT(*) as volume'), DB::raw('SUM(charge_amount) as revenue'))
                    ->groupBy('route')
                    ->orderBy('volume', 'desc')
                    ->limit(5)
                    ->get()
            ];
        }

        // Pharmacy/DDA Intelligence
        if ($this->moduleUtil->isModuleEnabled('dda_module', $business_id)) {
            $summary['pharmacy'] = [
                'controlled_drugs_dispensed_30d' => DB::table('dda_dispense_logs')->where('dda_dispense_logs.business_id', $business_id)->where('created_at', '>=', Carbon::now()->subDays(30))->count(),
                'expiring_soon' => DB::table('purchase_lines')
                    ->join('transactions', 'transactions.id', '=', 'purchase_lines.transaction_id')
                    ->where('transactions.business_id', $business_id)
                    ->whereNotNull('exp_date')
                    ->whereBetween('exp_date', [now(), now()->addMonths(3)])
                    ->count()
            ];
        }

        return $summary;
    }

    // ─── Business Intelligence: Sales Analytics ──────────────────
    public function getSalesAnalytics()
    {
        $business_id = request()->session()->get('user.business_id');
        $days = (int) request()->get('days', 30);
        $from = Carbon::now()->subDays($days)->startOfDay();

        $top_products = DB::table('transaction_lines')
            ->join('transactions', 'transactions.id', '=', 'transaction_lines.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_lines.product_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->where('transactions.transaction_date', '>=', $from)
            ->select('products.name', DB::raw('SUM(transaction_lines.quantity) as qty_sold'), DB::raw('SUM(transaction_lines.unit_price_inc_tax * transaction_lines.quantity) as revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $top_categories = DB::table('transaction_lines')
            ->join('transactions', 'transactions.id', '=', 'transaction_lines.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_lines.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->where('transactions.transaction_date', '>=', $from)
            ->select(DB::raw('COALESCE(categories.name, "Uncategorised") as category'), DB::raw('SUM(transaction_lines.unit_price_inc_tax * transaction_lines.quantity) as revenue'), DB::raw('COUNT(DISTINCT transactions.id) as txn_count'))
            ->groupBy('products.category_id', 'categories.name')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        $avg_basket = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('transaction_date', '>=', $from)
            ->avg('final_total');

        $daily_sales = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('transaction_date', '>=', $from)
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(final_total) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date')
            ->get();

        $hourly_sales = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('transaction_date', '>=', $from)
            ->select(DB::raw('HOUR(transaction_date) as hour'), DB::raw('COUNT(*) as count'), DB::raw('SUM(final_total) as total'))
            ->groupBy(DB::raw('HOUR(transaction_date)'))
            ->orderBy('hour')
            ->get();

        return response()->json(compact('top_products', 'top_categories', 'avg_basket', 'daily_sales', 'hourly_sales'));
    }

    // ─── Business Intelligence: Customer Analytics ────────────────
    public function getCustomerAnalytics()
    {
        $business_id = request()->session()->get('user.business_id');
        $days = (int) request()->get('days', 30);
        $from = Carbon::now()->subDays($days)->startOfDay();

        $top_customers = DB::table('transactions')
            ->join('contacts', 'contacts.id', '=', 'transactions.contact_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->where('transactions.transaction_date', '>=', $from)
            ->select('contacts.name', 'contacts.mobile', DB::raw('SUM(transactions.final_total) as spent'), DB::raw('COUNT(transactions.id) as visits'))
            ->groupBy('contacts.id', 'contacts.name', 'contacts.mobile')
            ->orderByDesc('spent')
            ->limit(10)
            ->get();

        $new_customers = DB::table('contacts')
            ->where('business_id', $business_id)
            ->where('type', 'customer')
            ->where('created_at', '>=', $from)
            ->count();

        $returning = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('transaction_date', '>=', $from)
            ->whereNotNull('contact_id')
            ->select('contact_id', DB::raw('COUNT(*) as visits'))
            ->groupBy('contact_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()->count();

        $total_buying = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('transaction_date', '>=', $from)
            ->whereNotNull('contact_id')
            ->distinct('contact_id')
            ->count('contact_id');

        $customer_growth = DB::table('contacts')
            ->where('business_id', $business_id)
            ->where('type', 'customer')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->select(DB::raw('DATE_FORMAT(created_at, "%b %Y") as month'), DB::raw('COUNT(*) as new_customers'))
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get();

        return response()->json(compact('top_customers', 'new_customers', 'returning', 'total_buying', 'customer_growth'));
    }

    // ─── Business Intelligence: Financial KPIs ────────────────────
    public function getFinancialKpis()
    {
        $business_id = request()->session()->get('user.business_id');
        $year = Carbon::now()->year;

        $monthly = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereYear('transaction_date', $year)
            ->select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(final_total) as revenue'),
                DB::raw('SUM(total_before_tax) as subtotal'),
                DB::raw('COUNT(*) as txn_count')
            )
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $purchase_monthly = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'purchase')
            ->where('status', 'received')
            ->whereYear('transaction_date', $year)
            ->select(DB::raw('MONTH(transaction_date) as month'), DB::raw('SUM(final_total) as cost'))
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $expenses_monthly = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'expense')
            ->whereYear('transaction_date', $year)
            ->select(DB::raw('MONTH(transaction_date) as month'), DB::raw('SUM(final_total) as expenses'))
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chart = [];
        for ($m = 1; $m <= 12; $m++) {
            $rev  = $monthly[$m]->revenue ?? 0;
            $cost = $purchase_monthly[$m]->cost ?? 0;
            $exp  = $expenses_monthly[$m]->expenses ?? 0;
            $chart[] = [
                'month'       => Carbon::create($year, $m)->format('M'),
                'revenue'     => round($rev, 2),
                'gross_profit'=> round($rev - $cost, 2),
                'net_profit'  => round($rev - $cost - $exp, 2),
                'expenses'    => round($exp, 2),
            ];
        }

        $ytd_revenue  = array_sum(array_column($chart, 'revenue'));
        $ytd_gp       = array_sum(array_column($chart, 'gross_profit'));
        $ytd_net      = array_sum(array_column($chart, 'net_profit'));
        $gp_margin    = $ytd_revenue > 0 ? round($ytd_gp / $ytd_revenue * 100, 1) : 0;
        $net_margin   = $ytd_revenue > 0 ? round($ytd_net / $ytd_revenue * 100, 1) : 0;

        $expense_breakdown = DB::table('transactions')
            ->join('expense_categories', 'expense_categories.id', '=', 'transactions.expense_category_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'expense')
            ->whereYear('transactions.transaction_date', $year)
            ->select('expense_categories.name', DB::raw('SUM(transactions.final_total) as total'))
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('total')
            ->get();

        return response()->json(compact('chart', 'ytd_revenue', 'ytd_gp', 'ytd_net', 'gp_margin', 'net_margin', 'expense_breakdown'));
    }

    // ─── Business Intelligence: Inventory Analytics ───────────────
    public function getInventoryAnalytics()
    {
        $business_id = request()->session()->get('user.business_id');

        $slow_movers = DB::table('products')
            ->leftJoin('transaction_lines', function ($join) {
                $join->on('transaction_lines.product_id', '=', 'products.id')
                     ->whereExists(function ($q) {
                         $q->from('transactions')
                           ->whereColumn('transactions.id', 'transaction_lines.transaction_id')
                           ->where('transactions.type', 'sell')
                           ->where('transactions.transaction_date', '>=', Carbon::now()->subDays(30));
                     });
            })
            ->join('variation_location_details as vld', 'vld.product_id', '=', 'products.id')
            ->where('products.business_id', $business_id)
            ->whereNull('transaction_lines.id')
            ->where('vld.qty_available', '>', 0)
            ->select('products.name', DB::raw('SUM(vld.qty_available) as stock'), DB::raw('MAX(products.alert_quantity) as alert_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('stock')
            ->limit(15)
            ->get();

        $dead_stock = DB::table('products')
            ->leftJoin('transaction_lines', function ($join) {
                $join->on('transaction_lines.product_id', '=', 'products.id')
                     ->whereExists(function ($q) {
                         $q->from('transactions')
                           ->whereColumn('transactions.id', 'transaction_lines.transaction_id')
                           ->where('transactions.type', 'sell')
                           ->where('transactions.transaction_date', '>=', Carbon::now()->subDays(90));
                     });
            })
            ->join('variation_location_details as vld', 'vld.product_id', '=', 'products.id')
            ->where('products.business_id', $business_id)
            ->whereNull('transaction_lines.id')
            ->where('vld.qty_available', '>', 0)
            ->select('products.name', DB::raw('SUM(vld.qty_available) as stock'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('stock')
            ->limit(10)
            ->get();

        $turnover = DB::table('transaction_lines')
            ->join('transactions', 'transactions.id', '=', 'transaction_lines.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_lines.product_id')
            ->join('variation_location_details as vld', 'vld.product_id', '=', 'products.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->where('transactions.transaction_date', '>=', Carbon::now()->subDays(30))
            ->where('vld.qty_available', '>', 0)
            ->select('products.name', DB::raw('SUM(transaction_lines.quantity) as sold'), DB::raw('AVG(vld.qty_available) as avg_stock'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold')
            ->limit(10)
            ->get()
            ->map(fn($p) => array_merge((array)$p, ['turnover_rate' => $p->avg_stock > 0 ? round($p->sold / $p->avg_stock, 2) : 0]));

        $low_stock = DB::table('variation_location_details')
            ->join('products', 'products.id', '=', 'variation_location_details.product_id')
            ->where('products.business_id', $business_id)
            ->whereRaw('variation_location_details.qty_available <= products.alert_quantity')
            ->where('products.alert_quantity', '>', 0)
            ->select('products.name', 'variation_location_details.qty_available', 'products.alert_quantity')
            ->orderBy('variation_location_details.qty_available')
            ->limit(15)
            ->get();

        return response()->json(compact('slow_movers', 'dead_stock', 'turnover', 'low_stock'));
    }

    // ─── AI: Procurement & Restocking Recommendations ────────────
    public function getAIProcurement()
    {
        $business_id = request()->session()->get('user.business_id');
        $cache_key   = "bi_ai_procurement_{$business_id}";
        if (request()->has('refresh')) Cache::forget($cache_key);

        return Cache::remember($cache_key, 1800, function () use ($business_id) {

            // Products below alert quantity
            $low_stock = DB::table('variation_location_details as vld')
                ->join('products', 'products.id', '=', 'vld.product_id')
                ->leftJoin('variations', 'variations.product_id', '=', 'products.id')
                ->where('products.business_id', $business_id)
                ->where('products.alert_quantity', '>', 0)
                ->whereRaw('vld.qty_available <= products.alert_quantity')
                ->select('products.name', 'vld.qty_available', 'products.alert_quantity',
                    DB::raw('COALESCE(variations.default_purchase_price, 0) as unit_cost'))
                ->limit(20)->get();

            // Sales velocity for those products (units/day last 30d)
            $velocity = DB::table('transaction_lines as tl')
                ->join('transactions as t', 't.id', '=', 'tl.transaction_id')
                ->join('products', 'products.id', '=', 'tl.product_id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')->where('t.status', 'final')
                ->where('t.transaction_date', '>=', Carbon::now()->subDays(30))
                ->whereIn('products.name', $low_stock->pluck('name'))
                ->select('products.name', DB::raw('SUM(tl.quantity) as sold_30d'))
                ->groupBy('products.id', 'products.name')
                ->get()->keyBy('name');

            // Slow movers with tied-up capital
            $slow = DB::table('products as p')
                ->join('variation_location_details as vld', 'vld.product_id', '=', 'p.id')
                ->leftJoin('variations as v', 'v.product_id', '=', 'p.id')
                ->where('p.business_id', $business_id)
                ->where('vld.qty_available', '>', 0)
                ->whereNotExists(function ($q) {
                    $q->from('transaction_lines as tl2')
                      ->join('transactions as t2', 't2.id', '=', 'tl2.transaction_id')
                      ->whereColumn('tl2.product_id', 'p.id')
                      ->where('t2.type', 'sell')
                      ->where('t2.transaction_date', '>=', Carbon::now()->subDays(60));
                })
                ->select('p.name',
                    DB::raw('SUM(vld.qty_available) as stock'),
                    DB::raw('COALESCE(AVG(v.default_purchase_price), 0) as unit_cost'),
                    DB::raw('SUM(vld.qty_available * COALESCE(v.default_purchase_price, 0)) as tied_capital'))
                ->groupBy('p.id', 'p.name')
                ->orderByDesc('tied_capital')
                ->limit(10)->get();

            $context = [
                'low_stock_items'    => $low_stock,
                'sales_velocity_30d' => $velocity->values(),
                'slow_movers_60d'    => $slow,
            ];

            $prompt = "You are an expert Procurement & Inventory Manager for a Kenyan business.
Analyze the data below and return a JSON object with these keys:
- reorder_recommendations: array of objects {product, current_stock, suggested_order_qty, urgency (high/medium/low), reason}
- dead_stock_actions: array of objects {product, stock, tied_capital, suggested_action}
- procurement_summary: one-paragraph executive summary

Return ONLY valid JSON, no markdown.
Data: " . json_encode($context);

            $raw = $this->callGemini($prompt, true);
            $decoded = json_decode(preg_replace('/```json|```/', '', $raw), true);
            return response()->json($decoded ?? ['error' => 'AI response parse failed', 'raw' => $raw]);
        });
    }

    // ─── AI: Customer Intelligence ────────────────────────────────
    public function getAICustomerInsights()
    {
        $business_id = request()->session()->get('user.business_id');
        $cache_key   = "bi_ai_customers_{$business_id}";
        if (request()->has('refresh')) Cache::forget($cache_key);

        return Cache::remember($cache_key, 1800, function () use ($business_id) {

            // Customers who bought before but not in last 45 days
            $churn_risk = DB::table('contacts as c')
                ->join('transactions as t', 't.contact_id', '=', 'c.id')
                ->where('c.business_id', $business_id)
                ->where('c.type', 'customer')
                ->where('t.type', 'sell')->where('t.status', 'final')
                ->where('t.transaction_date', '<', Carbon::now()->subDays(45))
                ->whereNotExists(function ($q) {
                    $q->from('transactions as t2')
                      ->whereColumn('t2.contact_id', 'c.id')
                      ->where('t2.type', 'sell')->where('t2.status', 'final')
                      ->where('t2.transaction_date', '>=', Carbon::now()->subDays(45));
                })
                ->select('c.name', 'c.mobile',
                    DB::raw('MAX(t.transaction_date) as last_purchase'),
                    DB::raw('SUM(t.final_total) as lifetime_value'),
                    DB::raw('COUNT(t.id) as total_orders'))
                ->groupBy('c.id', 'c.name', 'c.mobile')
                ->orderByDesc('lifetime_value')
                ->limit(15)->get();

            // VIP customers (top 10% by spend last 90 days)
            $vip = DB::table('contacts as c')
                ->join('transactions as t', 't.contact_id', '=', 'c.id')
                ->where('c.business_id', $business_id)
                ->where('c.type', 'customer')
                ->where('t.type', 'sell')->where('t.status', 'final')
                ->where('t.transaction_date', '>=', Carbon::now()->subDays(90))
                ->select('c.name', DB::raw('SUM(t.final_total) as spend'), DB::raw('COUNT(t.id) as orders'))
                ->groupBy('c.id', 'c.name')
                ->orderByDesc('spend')
                ->limit(10)->get();

            // Product affinity: what customers frequently buy together
            $top_products = DB::table('transaction_lines as tl')
                ->join('transactions as t', 't.id', '=', 'tl.transaction_id')
                ->join('products as p', 'p.id', '=', 'tl.product_id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')->where('t.status', 'final')
                ->where('t.transaction_date', '>=', Carbon::now()->subDays(90))
                ->select('p.name', DB::raw('COUNT(DISTINCT t.id) as txn_count'),
                    DB::raw('SUM(tl.quantity) as qty_sold'))
                ->groupBy('p.id', 'p.name')
                ->orderByDesc('txn_count')
                ->limit(10)->get();

            $context = [
                'churn_risk_customers' => $churn_risk,
                'vip_customers'        => $vip,
                'top_products_90d'     => $top_products,
                'total_churn_risk'     => $churn_risk->count(),
            ];

            $prompt = "You are a Customer Success and Retention expert for a Kenyan business.
Analyze the data below and return a JSON object with these keys:
- churn_actions: array of objects {customer_name, days_since_purchase, lifetime_value, recommended_action, message_template}
- segments: object with keys vip (count+description), regular (count+description), at_risk (count+description), lost (count+description)
- upsell_opportunities: array of objects {product, insight, suggested_bundle_or_action}
- retention_summary: one-paragraph executive summary with specific actions

Return ONLY valid JSON, no markdown.
Data: " . json_encode($context);

            $raw = $this->callGemini($prompt, true);
            $decoded = json_decode(preg_replace('/```json|```/', '', $raw), true);
            return response()->json($decoded ?? ['error' => 'AI response parse failed', 'raw' => $raw]);
        });
    }

    // ─── AI: Sales Intelligence ───────────────────────────────────
    public function getAISalesInsights()
    {
        $business_id = request()->session()->get('user.business_id');
        $cache_key   = "bi_ai_sales_{$business_id}";
        if (request()->has('refresh')) Cache::forget($cache_key);

        return Cache::remember($cache_key, 1800, function () use ($business_id) {

            // Daily sales last 30 days for anomaly detection
            $daily = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')->where('status', 'final')
                ->where('transaction_date', '>=', Carbon::now()->subDays(30))
                ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(final_total) as revenue'), DB::raw('COUNT(*) as orders'))
                ->groupBy(DB::raw('DATE(transaction_date)'))
                ->orderBy('date')->get();

            // Margin analysis: revenue vs cost of goods
            $margin_analysis = DB::table('transaction_lines as tl')
                ->join('transactions as t', 't.id', '=', 'tl.transaction_id')
                ->join('products as p', 'p.id', '=', 'tl.product_id')
                ->leftJoin('variations as v', 'v.product_id', '=', 'p.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')->where('t.status', 'final')
                ->where('t.transaction_date', '>=', Carbon::now()->subDays(30))
                ->select('p.name',
                    DB::raw('SUM(tl.quantity * tl.unit_price_inc_tax) as revenue'),
                    DB::raw('SUM(tl.quantity * COALESCE(tl.purchase_price, v.default_purchase_price, 0)) as cost'),
                    DB::raw('SUM(tl.quantity) as units_sold'))
                ->groupBy('p.id', 'p.name')
                ->orderByDesc('revenue')
                ->limit(15)->get()
                ->map(fn($p) => array_merge((array)$p, [
                    'margin_pct' => $p->revenue > 0 ? round(($p->revenue - $p->cost) / $p->revenue * 100, 1) : 0
                ]));

            // Best day-of-week performance
            $dow_performance = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')->where('status', 'final')
                ->where('transaction_date', '>=', Carbon::now()->subDays(90))
                ->select(DB::raw('DAYNAME(transaction_date) as day_name'), DB::raw('AVG(daily_total) as avg_revenue'))
                ->fromSub(
                    DB::table('transactions')
                        ->where('business_id', $business_id)
                        ->where('type', 'sell')->where('status', 'final')
                        ->where('transaction_date', '>=', Carbon::now()->subDays(90))
                        ->select(DB::raw('DATE(transaction_date) as sale_date'), DB::raw('DAYNAME(transaction_date) as day_name'), DB::raw('SUM(final_total) as daily_total'))
                        ->groupBy(DB::raw('DATE(transaction_date)')),
                    'daily_agg'
                )
                ->groupBy('day_name')
                ->get();

            $avg_daily = $daily->avg('revenue') ?? 0;
            $anomalies = $daily->filter(fn($d) => $avg_daily > 0 && abs($d->revenue - $avg_daily) / $avg_daily > 0.4)->values();

            $context = [
                'daily_sales_30d'    => $daily,
                'avg_daily_revenue'  => round($avg_daily, 2),
                'anomaly_days'       => $anomalies,
                'margin_by_product'  => $margin_analysis,
                'best_days_of_week'  => $dow_performance,
            ];

            $prompt = "You are a Sales Intelligence Analyst for a Kenyan business.
Analyze the data below and return a JSON object with these keys:
- anomalies: array of objects {date, revenue, deviation_pct, likely_cause, recommendation}
- top_margin_products: array of objects {product, margin_pct, insight, pricing_suggestion}
- low_margin_warnings: array of objects {product, margin_pct, risk, action}
- best_selling_day: string (day name) with reason
- sales_summary: one-paragraph executive summary with 3 specific action points

Return ONLY valid JSON, no markdown.
Data: " . json_encode($context);

            $raw = $this->callGemini($prompt, true);
            $decoded = json_decode(preg_replace('/```json|```/', '', $raw), true);
            return response()->json($decoded ?? ['error' => 'AI response parse failed', 'raw' => $raw]);
        });
    }

    // ─── AI: Financial Advisor ────────────────────────────────────
    public function getAIFinancialAdvice()
    {
        $business_id = request()->session()->get('user.business_id');
        $cache_key   = "bi_ai_financial_{$business_id}";
        if (request()->has('refresh')) Cache::forget($cache_key);

        return Cache::remember($cache_key, 1800, function () use ($business_id) {

            $year = Carbon::now()->year;

            // Monthly P&L
            $pnl = [];
            for ($m = 1; $m <= Carbon::now()->month; $m++) {
                $rev  = DB::table('transactions')->where('business_id', $business_id)->where('type', 'sell')->where('status', 'final')->whereYear('transaction_date', $year)->whereMonth('transaction_date', $m)->sum('final_total');
                $cost = DB::table('transactions')->where('business_id', $business_id)->where('type', 'purchase')->where('status', 'received')->whereYear('transaction_date', $year)->whereMonth('transaction_date', $m)->sum('final_total');
                $exp  = DB::table('transactions')->where('business_id', $business_id)->where('type', 'expense')->whereYear('transaction_date', $year)->whereMonth('transaction_date', $m)->sum('final_total');
                $pnl[] = ['month' => Carbon::create($year, $m)->format('M Y'), 'revenue' => round($rev, 2), 'cogs' => round($cost, 2), 'expenses' => round($exp, 2), 'net_profit' => round($rev - $cost - $exp, 2)];
            }

            // Expense categories trend
            $expense_trend = DB::table('transactions as t')
                ->join('expense_categories as ec', 'ec.id', '=', 't.expense_category_id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'expense')
                ->where('t.transaction_date', '>=', Carbon::now()->subMonths(3))
                ->select('ec.name', DB::raw('MONTH(t.transaction_date) as month'), DB::raw('SUM(t.final_total) as total'))
                ->groupBy('ec.id', 'ec.name', DB::raw('MONTH(t.transaction_date)'))
                ->orderBy('total', 'desc')->get();

            // Unpaid invoices / receivables
            $unpaid = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')->where('status', 'final')
                ->where('payment_status', '!=', 'paid')
                ->select(DB::raw('SUM(final_total - (SELECT COALESCE(SUM(amount),0) FROM transaction_payments WHERE transaction_id = transactions.id)) as outstanding'), DB::raw('COUNT(*) as count'))
                ->first();

            $context = [
                'monthly_pnl'        => $pnl,
                'expense_trend_3m'   => $expense_trend,
                'unpaid_receivables' => $unpaid,
                'current_month'      => Carbon::now()->format('F Y'),
            ];

            $prompt = "You are a CFO and Financial Advisor for a Kenyan SME.
Analyze the data below and return a JSON object with these keys:
- cash_flow_forecast: object {next_30_days_estimate, confidence, key_assumptions, risks}
- expense_alerts: array of objects {category, observation, recommended_action}
- margin_trend: object {direction (improving/declining/stable), insight, action}
- receivables_risk: object {outstanding_amount, risk_level, recommended_action}
- financial_summary: one-paragraph executive summary with top 3 priority actions

Return ONLY valid JSON, no markdown.
Data: " . json_encode($context);

            $raw = $this->callGemini($prompt, true);
            $decoded = json_decode(preg_replace('/```json|```/', '', $raw), true);
            return response()->json($decoded ?? ['error' => 'AI response parse failed', 'raw' => $raw]);
        });
    }

    // ─── AI: Inventory Health ─────────────────────────────────────
    public function getAIInventoryHealth()
    {
        $business_id = request()->session()->get('user.business_id');
        $cache_key   = "bi_ai_inventory_{$business_id}";
        if (request()->has('refresh')) Cache::forget($cache_key);

        return Cache::remember($cache_key, 1800, function () use ($business_id) {

            // Dead stock with tied capital
            $dead = DB::table('products as p')
                ->join('variation_location_details as vld', 'vld.product_id', '=', 'p.id')
                ->leftJoin('variations as v', 'v.product_id', '=', 'p.id')
                ->where('p.business_id', $business_id)
                ->where('vld.qty_available', '>', 0)
                ->whereNotExists(function ($q) {
                    $q->from('transaction_lines as tl')
                      ->join('transactions as t', 't.id', '=', 'tl.transaction_id')
                      ->whereColumn('tl.product_id', 'p.id')
                      ->where('t.type', 'sell')
                      ->where('t.transaction_date', '>=', Carbon::now()->subDays(90));
                })
                ->select('p.name',
                    DB::raw('SUM(vld.qty_available) as stock'),
                    DB::raw('COALESCE(AVG(v.default_sell_price), 0) as sell_price'),
                    DB::raw('COALESCE(AVG(v.default_purchase_price), 0) as cost_price'),
                    DB::raw('SUM(vld.qty_available * COALESCE(v.default_purchase_price, 0)) as tied_capital'))
                ->groupBy('p.id', 'p.name')
                ->orderByDesc('tied_capital')->limit(15)->get();

            // Expiry risk (if exp_date tracked)
            $expiry = DB::table('purchase_lines as pl')
                ->join('transactions as t', 't.id', '=', 'pl.transaction_id')
                ->join('products as p', 'p.id', '=', 'pl.product_id')
                ->where('t.business_id', $business_id)
                ->whereNotNull('pl.exp_date')
                ->whereBetween('pl.exp_date', [now(), now()->addDays(60)])
                ->where('pl.quantity_remaining', '>', 0)
                ->select('p.name', 'pl.exp_date', 'pl.quantity_remaining',
                    DB::raw('DATEDIFF(pl.exp_date, NOW()) as days_left'))
                ->orderBy('pl.exp_date')->limit(15)->get();

            // Over-ordered products (high stock, low turnover)
            $over_stocked = DB::table('products as p')
                ->join('variation_location_details as vld', 'vld.product_id', '=', 'p.id')
                ->leftJoin('variations as v', 'v.product_id', '=', 'p.id')
                ->where('p.business_id', $business_id)
                ->where('vld.qty_available', '>', DB::raw('p.alert_quantity * 5'))
                ->select('p.name',
                    DB::raw('SUM(vld.qty_available) as stock'),
                    DB::raw('p.alert_quantity as alert_qty'),
                    DB::raw('SUM(vld.qty_available * COALESCE(v.default_purchase_price,0)) as tied_capital'))
                ->groupBy('p.id', 'p.name', 'p.alert_quantity')
                ->orderByDesc('tied_capital')->limit(10)->get();

            $context = [
                'dead_stock_90d'  => $dead,
                'expiry_risk_60d' => $expiry,
                'over_stocked'    => $over_stocked,
                'total_tied_capital' => $dead->sum('tied_capital'),
            ];

            $prompt = "You are an Inventory Optimisation expert for a Kenyan business.
Analyze the data below and return a JSON object with these keys:
- clearance_plan: array of objects {product, stock, tied_capital, suggested_discount_pct, expected_recovery, timeline}
- expiry_actions: array of objects {product, days_left, qty, urgent_action}
- over_stock_warnings: array of objects {product, excess_stock, recommendation}
- capital_recovery_estimate: number (total KES recoverable)
- inventory_health_score: number 0-100
- inventory_summary: one-paragraph executive summary with top 3 actions

Return ONLY valid JSON, no markdown.
Data: " . json_encode($context);

            $raw = $this->callGemini($prompt, true);
            $decoded = json_decode(preg_replace('/```json|```/', '', $raw), true);
            return response()->json($decoded ?? ['error' => 'AI response parse failed', 'raw' => $raw]);
        });
    }

    private function callGemini($prompt, $isJson = false)
    {
        try {
            $business = \App\Business::where('id', request()->session()->get('user.business_id'))->first();
            $apiKey = ($business->common_settings ?? [])['gemini_api_key'] ?? config('services.gemini.key');
            if (empty($apiKey)) return $isJson ? json_encode(['error' => 'API Key missing']) : "Please set your Gemini API Key in the settings tab.";

            $response = Http::withHeaders(['Content-Type' => 'application/json'])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.5, 'maxOutputTokens' => 2048, 'responseMimeType' => $isJson ? "application/json" : "text/plain"]
            ]);
            return $response->json('candidates.0.content.parts.0.text');
        } catch (\Exception $e) { return "AI Advisor Offline: " . $e->getMessage(); }
    }
}
