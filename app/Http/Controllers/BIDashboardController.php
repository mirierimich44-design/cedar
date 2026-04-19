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
        return response()->json($this->getDetailedContext(request()->session()->get('user.business_id')));
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
        return redirect()->back()->with('status', ['success' => 1, 'msg' => 'AI Configuration Updated']);
    }

    private function getDetailedContext($business_id)
    {
        $summary = [
            'general' => [
                'revenue_6m' => DB::table('transactions')->where('business_id', $business_id)->where('type', 'sell')->where('transaction_date', '>=', Carbon::now()->subMonths(6))->select(DB::raw('MONTHNAME(transaction_date) as month'), DB::raw('SUM(final_total) as total'))->groupBy('month')->get(),
                'payment_split' => DB::table('transaction_payments')->join('transactions', 'transactions.id', '=', 'transaction_payments.transaction_id')->where('transactions.business_id', $business_id)->select('method', DB::raw('SUM(amount) as total'))->groupBy('method')->get(),
            ],
            'inventory' => [
                'stock_value' => DB::table('variation_location_details')
                    ->join('products', 'products.id', '=', 'variation_location_details.product_id')
                    ->join('variations', 'variations.product_id', '=', 'products.id')
                    ->where('products.business_id', $business_id)
                    ->select(DB::raw('SUM(variation_location_details.qty_available * variations.default_sell_price) as total_value'))
                    ->first(),
                'low_stock' => DB::table('products')->where('business_id', $business_id)->whereRaw('alert_quantity > 0')->limit(5)->get()
            ]
        ];

        // Hospital Intelligence
        if ($this->moduleUtil->isModuleEnabled('hospital_module', $business_id)) {
            $summary['hospital'] = [
                'total_patients' => DB::table('contacts')->where('business_id', $business_id)->where('type', 'customer')->count(),
                'recent_consultations' => DB::table('hospital_consultations')->where('created_at', '>=', Carbon::now()->subDays(30))->count(),
                'unbilled_services' => [
                    'labs' => DB::table('hospital_lab_requests')->where('business_id', $business_id)->whereNull('transaction_id')->count(),
                    'imaging' => DB::table('hospital_radiography_requests')->where('business_id', $business_id)->whereNull('transaction_id')->count()
                ],
                'bed_occupancy' => DB::table('hospital_beds')->join('hospital_wards', 'hospital_wards.id', '=', 'hospital_beds.ward_id')->where('hospital_wards.business_id', $business_id)->select(DB::raw('SUM(CASE WHEN is_available=0 THEN 1 ELSE 0 END) as occupied'), DB::raw('COUNT(*) as total'))->first()
            ];
        }

        // Logistics/Parcel Intelligence
        if ($this->moduleUtil->isModuleEnabled('parcel', $business_id)) {
            $summary['logistics'] = [
                'total_parcels_30d' => DB::table('parcels')->where('business_id', $business_id)->where('created_at', '>=', Carbon::now()->subDays(30))->count(),
                'failure_rate' => DB::table('parcels')->where('business_id', $business_id)->select(DB::raw('SUM(CASE WHEN status="failed" THEN 1 ELSE 0 END) as failed'), DB::raw('COUNT(*) as total'))->first(),
                'top_routes' => DB::table('parcels')->where('business_id', $business_id)->leftJoin('parcel_stations as s1', 's1.id', '=', 'parcels.origin_station_id')->leftJoin('parcel_stations as s2', 's2.id', '=', 'parcels.destination_station_id')->select(DB::raw('CONCAT(s1.name, " to ", s2.name) as route'), DB::raw('COUNT(*) as volume'), DB::raw('SUM(charge_amount) as revenue'))->groupBy('route')->orderBy('volume', 'desc')->limit(5)->get()
            ];
        }

        // Pharmacy/DDA Intelligence
        if ($this->moduleUtil->isModuleEnabled('dda_module', $business_id)) {
            $summary['pharmacy'] = [
                'controlled_drugs_dispensed_30d' => DB::table('dda_dispense_logs')->where('created_at', '>=', Carbon::now()->subDays(30))->count(),
                'expiring_soon' => DB::table('purchase_lines')->join('transactions', 'transactions.id', '=', 'purchase_lines.transaction_id')->where('transactions.business_id', $business_id)->whereNotNull('exp_date')->whereBetween('exp_date', [now(), now()->addMonths(3)])->count()
            ];
        }

        return $summary;
    }

    private function callGemini($prompt, $isJson = false)
    {
        try {
            $business = \App\Business::where('id', request()->session()->get('user.business_id'))->first();
            $apiKey = ($business->common_settings ?? [])['gemini_api_key'] ?? config('services.gemini.key');
            if (empty($apiKey)) return $isJson ? json_encode(['error' => 'API Key missing']) : "Please set your Gemini API Key in the settings tab.";

            $response = Http::withHeaders(['Content-Type' => 'application/json'])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-pro-preview:generateContent?key=" . $apiKey, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.5, 'maxOutputTokens' => 2048, 'responseMimeType' => $isJson ? "application/json" : "text/plain"]
            ]);
            return $response->json('candidates.0.content.parts.0.text');
        } catch (\Exception $e) { return "AI Advisor Offline: " . $e->getMessage(); }
    }
}
