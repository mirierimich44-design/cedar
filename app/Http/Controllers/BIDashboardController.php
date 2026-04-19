<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BIDashboardController extends Controller
{
    /**
     * Dashboard Home - Initial Data Load
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        
        // --- KPI DATA ---
        $this_month = [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        $last_month = [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];

        // Revenue
        $revenue_now = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween('transaction_date', $this_month)
            ->sum('final_total');
        
        $revenue_last = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween('transaction_date', $last_month)
            ->sum('final_total');

        // Parcels
        $parcels_now = DB::table('parcels')
            ->where('business_id', $business_id)
            ->whereBetween('created_at', $this_month)
            ->count();
        
        $parcels_last = DB::table('parcels')
            ->where('business_id', $business_id)
            ->whereBetween('created_at', $last_month)
            ->count();

        // New Customers
        $customers_now = DB::table('contacts')
            ->where('business_id', $business_id)
            ->where('type', 'customer')
            ->whereBetween('created_at', $this_month)
            ->count();
        
        $customers_last = DB::table('contacts')
            ->where('business_id', $business_id)
            ->where('type', 'customer')
            ->whereBetween('created_at', $last_month)
            ->count();

        // Low Stock (using variation_location_details)
        $low_stock_count = DB::table('variation_location_details')
            ->join('products', 'products.id', '=', 'variation_location_details.product_id')
            ->where('products.business_id', $business_id)
            ->whereRaw('qty_available <= alert_quantity')
            ->count();

        // Sparklines (7 day trends)
        $seven_days_ago = Carbon::now()->subDays(7);
        $revenue_trend = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('transaction_date', '>=', $seven_days_ago)
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(final_total) as total'))
            ->groupBy('date')
            ->pluck('total')->toArray();

        // --- CHART DATA (12 Months) ---
        $months = [];
        $revenue_chart = [];
        $parcel_chart = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $months[] = $m->format('M Y');
            
            $rev = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->whereMonth('transaction_date', $m->month)
                ->whereYear('transaction_date', $m->year)
                ->sum('final_total');
            $revenue_chart[] = (float)$rev;

            $prc = DB::table('parcels')
                ->where('business_id', $business_id)
                ->whereMonth('created_at', $m->month)
                ->whereYear('created_at', $m->year)
                ->count();
            $parcel_chart[] = $prc;
        }

        // --- PAYMENT METHODS ---
        $payment_methods = DB::table('transaction_payments')
            ->join('transactions', 'transactions.id', '=', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->select('method', DB::raw('SUM(amount) as total'))
            ->groupBy('method')
            ->get();

        // --- TOP ROUTES ---
        $top_routes = DB::table('parcels')
            ->where('parcels.business_id', $business_id)
            ->leftJoin('parcel_stations as s1', 's1.id', '=', 'parcels.origin_station_id')
            ->leftJoin('parcel_stations as s2', 's2.id', '=', 'parcels.destination_station_id')
            ->select(DB::raw('CONCAT(s1.name, " ➔ ", s2.name) as route'), DB::raw('SUM(charge_amount) as total'))
            ->groupBy('route')
            ->orderBy('total', 'desc')
            ->limit(8)
            ->get();

        // --- SALES HEATMAP (Day of Week vs Hour) ---
        $heatmap_raw = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->select(DB::raw('DAYOFWEEK(transaction_date) as day'), DB::raw('HOUR(transaction_date) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('day', 'hour')
            ->get();

        $data = compact(
            'revenue_now', 'revenue_last', 'parcels_now', 'parcels_last', 
            'customers_now', 'customers_last', 'low_stock_count', 'revenue_trend',
            'months', 'revenue_chart', 'parcel_chart', 'payment_methods', 'top_routes', 'heatmap_raw'
        );

        return view('dashboard.bi', $data);
    }

    /**
     * AI Insights Generation
     */
    public function getInsights()
    {
        $business_id = request()->session()->get('user.business_id');
        $cache_key = "bi_insights_" . $business_id;

        if (request()->has('refresh')) {
            Cache::forget($cache_key);
        }

        return Cache::remember($cache_key, 1800, function() use ($business_id) {
            $context = $this->getBusinessSummary($business_id);
            $prompt = "You are a business intelligence analyst for a Kenyan POS and parcel logistics company. Analyze this data and return exactly 5 bullet-point insights in plain text. Focus on anomalies, growth opportunities, operational warnings, and revenue optimization. Be specific with numbers. Format each insight starting with one of: [OPPORTUNITY], [WARNING], [ANOMALY], [GROWTH], [ACTION]. \n\n Data Context: " . json_encode($context);

            $result = $this->callGemini($prompt);
            
            // Parse bullets
            $insights = array_filter(explode("\n", $result), function($line) {
                return !empty(trim($line)) && (strpos($line, '[') !== false);
            });

            return response()->json(['insights' => array_values($insights)]);
        });
    }

    /**
     * AI Q&A
     */
    public function askQuestion(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $question = $request->input('question');
        
        $context = $this->getBusinessSummary($business_id);
        $prompt = "You are an expert BI Assistant for a Kenyan business. Using the following data context, answer the user's question concisely. If the data doesn't contain the answer, say so based on your best judgment. \n\n Context: " . json_encode($context) . "\n\n Question: " . $question;

        $answer = $this->callGemini($prompt);
        return response()->json(['answer' => $answer]);
    }

    /**
     * AI Predictive Recommendations
     */
    public function getPredictions()
    {
        $business_id = request()->session()->get('user.business_id');
        $context = $this->getBusinessSummary($business_id);

        $prompt = "Analyze the provided business data and return a JSON object with 3 keys: 'revenue_forecast', 'churn_risk', and 'inventory_suggestions'. 
        For each, provide: 'value' (string), 'confidence' (int 0-100), and 'reasoning' (one short sentence). 
        Context: " . json_encode($context);

        $result = $this->callGemini($prompt, true); // True for JSON mode hint
        
        // Clean markdown if Gemini returns it
        $json = preg_replace('/```json|```/', '', $result);
        
        return response()->json(json_decode($json));
    }

    private function getBusinessSummary($business_id)
    {
        // Aggregated summary for Gemini context
        return [
            'revenue_last_6_months' => DB::table('transactions')->where('business_id', $business_id)->where('type', 'sell')->where('transaction_date', '>=', Carbon::now()->subMonths(6))->select(DB::raw('MONTHNAME(transaction_date) as month'), DB::raw('SUM(final_total) as total'))->groupBy('month')->get(),
            'top_products_stock' => DB::table('products')->where('business_id', $business_id)->select('name', 'alert_quantity')->limit(10)->get(),
            'payment_split' => DB::table('transaction_payments')->join('transactions', 'transactions.id', '=', 'transaction_payments.transaction_id')->where('transactions.business_id', $business_id)->select('method', DB::raw('SUM(amount) as total'))->groupBy('method')->get(),
            'parcel_stats' => DB::table('parcels')->where('business_id', $business_id)->select('status', DB::raw('count(*) as count'))->groupBy('status')->get(),
            'customer_count' => DB::table('contacts')->where('business_id', $business_id)->where('type', 'customer')->count(),
            'currency' => 'KES'
        ];
    }

    private function callGemini($prompt, $isJson = false)
    {
        try {
            $apiKey = config('services.gemini.key');
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 1024,
                    'responseMimeType' => $isJson ? "application/json" : "text/plain"
                ]
            ]);

            if ($response->failed()) {
                throw new \Exception("Gemini API Error: " . $response->body());
            }

            return $response->json('candidates.0.content.parts.0.text');
        } catch (\Exception $e) {
            \Log::error("Gemini Call Failed: " . $e->getMessage());
            return "AI service temporarily unavailable. Please try again later.";
        }
    }
}
