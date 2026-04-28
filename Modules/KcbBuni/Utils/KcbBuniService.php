<?php

namespace Modules\KcbBuni\Utils;

use Modules\KcbBuni\Entities\KcbBuniSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KcbBuniService
{
    protected $settings;
    protected $business_id;
    const CACHE_PREFIX = 'kcb_buni_token_';

    public function __construct($business_id)
    {
        $this->business_id = $business_id;
        $this->settings = KcbBuniSetting::getForBusiness($business_id);
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function isConfigured()
    {
        return $this->settings && $this->settings->isConfigured();
    }

    protected function getBaseUrl()
    {
        return ($this->settings->environment === 'production')
            ? 'https://api.kcbgroup.com'
            : 'https://uat.buni.kcbgroup.com';
    }

    public function getAccessToken()
    {
        $cacheKey = self::CACHE_PREFIX . $this->business_id;
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $appKey = $this->settings->decrypted_app_key;
        $appSecret = $this->settings->decrypted_app_secret;

        $response = Http::asForm()
            ->withBasicAuth($appKey, $appSecret)
            ->post($this->getBaseUrl() . '/token', [
                'grant_type' => 'client_credentials'
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $token = $data['access_token'];
            // Cache slightly less than expires_in (usually 3600s)
            $expiresIn = $data['expires_in'] ?? 3600;
            Cache::put($cacheKey, $token, $expiresIn - 60);
            return $token;
        }

        Log::error('KCB Buni Auth Error', [
            'business_id' => $this->business_id,
            'response' => $response->body()
        ]);

        throw new \Exception('Failed to authenticate with KCB Buni API.');
    }

    /**
     * Business to Business (B2B) Transfer
     */
    public function b2bTransfer($amount, $receiver_shortcode, $receiver_type = 'Paybill', $remarks = 'B2B Transfer')
    {
        $token = $this->getAccessToken();
        $reference = 'B2B' . time() . rand(100, 999);

        $payload = [
            'Header' => [
                'messageID' => uniqid(),
                'featureCode' => 'B2B',
                'featureName' => 'B2B Transfer',
                'serviceCode' => 'B2B001',
                'serviceName' => 'B2B Transfer',
                'channelCode' => 'WEB',
                'channelName' => 'WEB',
                'routeCode' => 'KCB',
                'timeStamp' => now()->format('YmdHis')
            ],
            'Primary' => [
                'amount' => (string) $amount,
                'currency' => $this->settings->currency ?: 'KES',
                'senderShortCode' => $this->settings->b2b_shortcode,
                'receiverShortCode' => $receiver_shortcode,
                'receiverType' => $receiver_type, // Paybill, Till
                'remarks' => $remarks,
                'accountReference' => $reference
            ]
        ];

        $response = Http::withToken($token)
            ->post($this->getBaseUrl() . '/v1/b2b/transfer', $payload);

        return $this->processResponse($response, 'B2B Transfer');
    }

    /**
     * Business to Consumer (B2C) Transfer
     */
    public function b2cTransfer($amount, $phone, $remarks = 'B2C Transfer')
    {
        $token = $this->getAccessToken();
        
        $payload = [
            'Header' => [
                'messageID' => uniqid(),
                'featureCode' => 'B2C',
                'serviceCode' => 'B2C001',
                'timeStamp' => now()->format('YmdHis')
            ],
            'Primary' => [
                'amount' => (string) $amount,
                'currency' => $this->settings->currency ?: 'KES',
                'phone' => $phone,
                'shortCode' => $this->settings->b2c_shortcode,
                'remarks' => $remarks
            ]
        ];

        $response = Http::withToken($token)
            ->post($this->getBaseUrl() . '/v1/b2c/transfer', $payload);

        return $this->processResponse($response, 'B2C Transfer');
    }

    /**
     * Account Balance Query
     */
    public function getBalance($shortcode = null)
    {
        $token = $this->getAccessToken();
        $shortcode = $shortcode ?: $this->settings->b2b_shortcode;

        $payload = [
            'Header' => [
                'messageID' => uniqid(),
                'timeStamp' => now()->format('YmdHis')
            ],
            'Primary' => [
                'shortCode' => $shortcode
            ]
        ];

        $response = Http::withToken($token)
            ->post($this->getBaseUrl() . '/v1/account/balance', $payload);

        return $this->processResponse($response, 'Balance Query');
    }

    protected function processResponse($response, $action)
    {
        $data = $response->json();
        
        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $data,
                'message' => 'Request processed successfully'
            ];
        }

        Log::error("KCB Buni $action Error", [
            'business_id' => $this->business_id,
            'response' => $response->body()
        ]);

        return [
            'success' => false,
            'message' => $data['message'] ?? 'API request failed',
            'error' => $data
        ];
    }
}
