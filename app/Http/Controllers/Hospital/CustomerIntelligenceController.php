<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Contact;
use App\Business;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CustomerIntelligenceController extends Controller
{
    /**
     * RFM Segmentation
     */
    public function getSegments()
    {
        $business_id = request()->session()->get('user.business_id');
        
        // Simple RFM Calculation
        $data = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->select(
                'contacts.name',
                DB::raw('DATEDIFF(NOW(), MAX(transaction_date)) as recency'),
                DB::raw('COUNT(transactions.id) as frequency'),
                DB::raw('SUM(final_total) as monetary')
            )
            ->groupBy('contacts.id')
            ->get();

        $prompt = "Categorize these customers into segments (Champions, At-Risk, Lapsed) based on RFM scores: " . json_encode($data);
        $result = $this->callGemini($prompt, true);
        
        return response()->json(json_decode(preg_replace('/```json|```/', '', $result)));
    }

    /**
     * Hospital Volume Forecasting
     */
    public function getVolumeForecast()
    {
        $business_id = request()->session()->get('user.business_id');
        
        $history = DB::table('hospital_consultations')
            ->where('business_id', $business_id)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as volume'))
            ->groupBy('date')
            ->get();

        $prompt = "You are a hospital operations analyst. Analyze the following daily patient volume history and forecast the next 7 days of patient volume. Suggest staffing levels (Nurse/Doctor counts). Format as JSON: {'forecast': [{'date': '...', 'volume': '...'}], 'staffing_advice': '...'} \n\n History: " . json_encode($history);
        
        $result = $this->callGemini($prompt, true);
        return response()->json(json_decode(preg_replace('/```json|```/', '', $result)));
    }

    private function callGemini($prompt, $isJson = false)
    {
        try {
            $business = \App\Business::find(session('user.business_id'));
            $settings = $business->common_settings ?? [];

            $anthropicKey = $settings['anthropic_api_key'] ?? null;
            $openaiKey    = $settings['openai_api_key'] ?? config('openai.api_key');
            $geminiKey    = $settings['gemini_api_key'] ?? config('services.gemini.key');

            if (!empty($anthropicKey)) {
                $response = Http::timeout(60)->withHeaders([
                    'x-api-key' => $anthropicKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ])->post('https://api.anthropic.com/v1/messages', [
                    'model' => 'claude-sonnet-4-20250514',
                    'max_tokens' => 2048,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                ]);
                return $response->json('content.0.text') ?? '[]';
            } elseif (!empty($openaiKey)) {
                $response = Http::timeout(60)->withHeaders([
                    'Authorization' => 'Bearer ' . $openaiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'max_tokens' => 2048,
                    'temperature' => 0.5,
                ]);
                return $response->json('choices.0.message.content') ?? '[]';
            } elseif (!empty($geminiKey)) {
                $response = Http::timeout(30)->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $geminiKey, [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.5, 'maxOutputTokens' => 2048, 'responseMimeType' => $isJson ? "application/json" : "text/plain"]
                    ]);
                return $response->json('candidates.0.content.parts.0.text') ?? '[]';
            }

            return '[]';
        } catch (\Exception $e) { return '[]'; }
    }
}
