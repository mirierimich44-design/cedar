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
