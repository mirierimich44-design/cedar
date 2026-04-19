<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\BIDashboardController;
use App\Utils\Util;
use App\Business;
use Carbon\Carbon;

class BiDetectAnomalies extends Command
{
    protected $signature = 'bi:detect-anomalies';
    protected $description = 'AI-driven automated detection of business anomalies (fraud, leaks, stock-outs).';

    public function handle()
    {
        $businesses = Business::all();
        $util = new Util();

        foreach ($businesses as $business) {
            $this->info("Scanning anomalies for: {$business->name}");
            
            // Re-use logic from BIDashboardController (or similar)
            $context = $this->getBusinessContext($business->id);
            
            $prompt = "You are a Chief AI Auditor for a business typed as '{$business->business_type}'. 
            Analyze this context for:
            1. Revenue Leaks (Unbilled medical services).
            2. Operational Fraud/Anomalies (Unusual activity).
            3. Critical Stock-Outs.
            Return ONLY a JSON array of objects: [{\"type\": \"...\", \"severity\": \"(high/medium/low)\", \"message\": \"...\"}].
            Context: " . json_encode($context);

            $result = $this->callGemini($prompt, $business);
            $anomalies = json_decode(preg_replace('/```json|```/', '', $result), true);

            if (is_array($anomalies)) {
                foreach ($anomalies as $a) {
                    if ($a['severity'] === 'high') {
                        $this->sendAlert($business, $a['message'], $util);
                    }
                }
            }
        }
    }

    private function getBusinessContext($business_id)
    {
        // Similar to getDetailedContext in BIDashboardController
        return [
            'unbilled' => [
                'labs' => DB::table('hospital_lab_requests')->where('business_id', $business_id)->whereNull('transaction_id')->count(),
                'imaging' => DB::table('hospital_radiography_requests')->where('business_id', $business_id)->whereNull('transaction_id')->count()
            ],
            'stock_alerts' => DB::table('products')->where('business_id', $business_id)->whereRaw('qty_available <= alert_quantity')->count(),
            'recent_transactions' => DB::table('transactions')->where('business_id', $business_id)->where('transaction_date', '>=', Carbon::now()->subHours(6))->count()
        ];
    }

    private function callGemini($prompt, $business)
    {
        $apiKey = ($business->common_settings ?? [])['gemini_api_key'] ?? config('services.gemini.key');
        if (empty($apiKey)) return '[]';

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-pro-preview:generateContent?key=" . $apiKey, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => 1024, 'responseMimeType' => "application/json"]
            ]);
            return $response->json('candidates.0.content.parts.0.text');
        } catch (\Exception $e) { return '[]'; }
    }

    private function sendAlert($business, $message, $util)
    {
        $phone = $business->mobile; // Assuming business mobile is the owner's phone
        if (!$phone) return;

        $msg = "🚨 Apex AI Alert: {$message}";
        $data = [
            'sms_settings' => $business->sms_settings ?? [],
            'mobile_number' => $phone,
            'sms_body' => $msg
        ];
        $util->sendSms($data);
        $this->warn("Sent alert to {$phone}: {$msg}");
    }
}
