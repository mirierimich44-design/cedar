<?php

namespace Modules\Pesapal\Utils;

use Modules\Pesapal\Entities\PesapalSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesapalService
{
    protected $settings;
    protected $business_id;

    const CACHE_PREFIX = 'pesapal_token_';

    // Pesapal tokens last 5 minutes; cache for 4 to avoid expiry mid-request
    const TOKEN_CACHE_SECONDS = 240;

    public function __construct($business_id)
    {
        $this->business_id = $business_id;
        $this->settings = PesapalSetting::getForBusiness($business_id);
    }

    public function isConfigured()
    {
        return $this->settings && $this->settings->isConfigured();
    }

    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Get a fresh or cached Bearer token from Pesapal.
     */
    public function getAccessToken()
    {
        if (!$this->settings) {
            throw new \Exception('Pesapal settings not found for this business.');
        }

        $cacheKey = self::CACHE_PREFIX . $this->business_id;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $key    = $this->settings->decrypted_consumer_key;
        $secret = $this->settings->decrypted_consumer_secret;

        if (!$key || !$secret) {
            throw new \Exception('Pesapal consumer key/secret not configured.');
        }

        $url = $this->settings->getBaseUrl() . '/api/Auth/RequestToken';

        try {
            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, [
                'consumer_key'    => $key,
                'consumer_secret' => $secret,
            ]);

            $data = $response->json();

            Log::info('Pesapal auth response', ['status' => $response->status(), 'data' => $data]);

            if ($response->successful() && !empty($data['token'])) {
                Cache::put($cacheKey, $data['token'], now()->addSeconds(self::TOKEN_CACHE_SECONDS));
                return $data['token'];
            }

            throw new \Exception('Failed to get Pesapal token: ' . ($data['message'] ?? $response->body()));

        } catch (\Exception $e) {
            Log::error('Pesapal token error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Force-clear the cached token and fetch a fresh one.
     */
    public function testConnection()
    {
        try {
            Cache::forget(self::CACHE_PREFIX . $this->business_id);
            $token = $this->getAccessToken();

            if ($token) {
                $this->settings->update(['last_tested_at' => now()]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Register an IPN URL with Pesapal and save the returned ipn_id.
     */
    public function registerIpnUrl($ipn_url = null)
    {
        $token  = $this->getAccessToken();
        $url    = $this->settings->getBaseUrl() . '/api/URLSetup/RegisterIPN';
        $ipn_url = $ipn_url ?: url('/pesapal/ipn');

        try {
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->post($url, [
                'url'                    => $ipn_url,
                'ipn_notification_type'  => 'GET',
            ]);

            $data = $response->json();

            Log::info('Pesapal IPN registration response', ['data' => $data]);

            if ($response->successful() && !empty($data['ipn_id'])) {
                $this->settings->update([
                    'ipn_id'  => $data['ipn_id'],
                    'ipn_url' => $ipn_url,
                ]);

                return [
                    'success' => true,
                    'ipn_id'  => $data['ipn_id'],
                    'message' => 'IPN URL registered successfully.',
                ];
            }

            return [
                'success' => false,
                'message' => $data['error']['message'] ?? $data['message'] ?? 'IPN registration failed.',
            ];

        } catch (\Exception $e) {
            Log::error('Pesapal IPN registration error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get list of registered IPN URLs.
     */
    public function getIpnList()
    {
        $token = $this->getAccessToken();
        $url   = $this->settings->getBaseUrl() . '/api/URLSetup/GetIpnList';

        try {
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->get($url);

            return $response->json() ?? [];

        } catch (\Exception $e) {
            Log::error('Pesapal GetIpnList error', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Submit an order request to Pesapal.
     *
     * Returns ['success', 'redirect_url', 'order_tracking_id', 'merchant_reference'] on success.
     */
    public function submitOrderRequest($amount, $description, $merchant_reference, $billing = [], $callback_url = null)
    {
        if (!$this->settings || !$this->settings->ipn_id) {
            throw new \Exception('Pesapal IPN not registered. Please register your IPN URL first.');
        }

        $token        = $this->getAccessToken();
        $url          = $this->settings->getBaseUrl() . '/api/Transactions/SubmitOrderRequest';
        $callback_url = $callback_url ?: ($this->settings->callback_url ?: url('/pesapal/callback'));
        $currency     = $this->settings->currency ?: 'KES';

        // Pesapal merchant_reference: alphanumeric, dashes, underscores, dots, colons — max 50 chars
        $merchant_reference = substr(preg_replace('/[^a-zA-Z0-9\-_.:]/','', $merchant_reference), 0, 50);

        $payload = [
            'id'              => $merchant_reference,
            'currency'        => $currency,
            'amount'          => round((float) $amount, 2),
            'description'     => substr($description, 0, 100),
            'callback_url'    => $callback_url,
            'notification_id' => $this->settings->ipn_id,
            'billing_address' => $billing ?: new \stdClass(), // empty object if no billing info
        ];

        try {
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->post($url, $payload);

            $data = $response->json();

            Log::info('Pesapal SubmitOrderRequest response', ['ref' => $merchant_reference, 'data' => $data]);

            if ($response->successful() && !empty($data['order_tracking_id'])) {
                return [
                    'success'           => true,
                    'order_tracking_id' => $data['order_tracking_id'],
                    'merchant_reference' => $data['merchant_reference'] ?? $merchant_reference,
                    'redirect_url'      => $data['redirect_url'],
                ];
            }

            return [
                'success' => false,
                'message' => $data['error']['message'] ?? $data['message'] ?? 'Order submission failed.',
            ];

        } catch (\Exception $e) {
            Log::error('Pesapal SubmitOrderRequest error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get transaction status from Pesapal by order_tracking_id.
     *
     * status_code: 0=INVALID, 1=COMPLETED, 2=FAILED, 3=REVERSED
     */
    public function getTransactionStatus($order_tracking_id)
    {
        $token = $this->getAccessToken();
        $url   = $this->settings->getBaseUrl()
               . '/api/Transactions/GetTransactionStatus?orderTrackingId='
               . urlencode($order_tracking_id);

        try {
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->get($url);

            $data = $response->json();

            Log::info('Pesapal GetTransactionStatus', ['tracking_id' => $order_tracking_id, 'data' => $data]);

            if ($response->successful()) {
                return [
                    'success'                    => true,
                    'status_code'                => $data['status_code'] ?? null,
                    'payment_status_description' => $data['payment_status_description'] ?? null,
                    'payment_method'             => $data['payment_method'] ?? null,
                    'amount'                     => $data['amount'] ?? null,
                    'currency'                   => $data['currency'] ?? null,
                    'confirmation_code'          => $data['confirmation_code'] ?? null,
                    'payment_account'            => $data['payment_account'] ?? null,
                    'merchant_reference'         => $data['merchant_reference'] ?? null,
                    'description'                => $data['description'] ?? null,
                    'raw'                        => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $data['error']['message'] ?? 'Status check failed.',
            ];

        } catch (\Exception $e) {
            Log::error('Pesapal GetTransactionStatus error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Map Pesapal status_code integer to our transaction status string.
     */
    public static function mapStatusCode($status_code)
    {
        return match((int) $status_code) {
            1  => 'completed',
            2  => 'failed',
            3  => 'reversed',
            default => 'invalid',
        };
    }
}
