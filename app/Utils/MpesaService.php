<?php

namespace App\Utils;

use App\MpesaSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    /**
     * @var MpesaSetting
     */
    protected $settings;

    /**
     * @var int Business ID
     */
    protected $business_id;

    /**
     * Cache key prefix
     */
    const CACHE_PREFIX = 'mpesa_token_';

    /**
     * Token cache duration in minutes (50 mins, tokens expire in 60)
     */
    const TOKEN_CACHE_MINUTES = 50;

    /**
     * Constructor
     */
    public function __construct($business_id)
    {
        $this->business_id = $business_id;
        $this->settings = MpesaSetting::getForBusiness($business_id);
    }

    /**
     * Check if M-Pesa is configured.
     */
    public function isConfigured()
    {
        return $this->settings && 
               $this->settings->is_active &&
               $this->settings->decrypted_consumer_key &&
               $this->settings->decrypted_consumer_secret;
    }

    /**
     * Get access token from Safaricom.
     */
    public function getAccessToken()
    {
        if (!$this->settings) {
            throw new \Exception('M-Pesa settings not found for this business.');
        }

        $cacheKey = self::CACHE_PREFIX . $this->business_id;

        // Return cached token if exists
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $consumerKey = $this->settings->decrypted_consumer_key;
        $consumerSecret = $this->settings->decrypted_consumer_secret;

        if (!$consumerKey || !$consumerSecret) {
            throw new \Exception('M-Pesa credentials not configured.');
        }

        $url = $this->settings->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials';
        
        $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials,
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;

                if ($token) {
                    Cache::put($cacheKey, $token, now()->addMinutes(self::TOKEN_CACHE_MINUTES));
                    return $token;
                }
            }

            Log::error('M-Pesa token generation failed', [
                'response' => $response->body(),
                'status' => $response->status(),
            ]);

            throw new \Exception('Failed to generate M-Pesa access token: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('M-Pesa token exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Test connection by fetching access token.
     */
    public function testConnection()
    {
        try {
            // Clear cached token to force fresh request
            Cache::forget(self::CACHE_PREFIX . $this->business_id);
            
            $token = $this->getAccessToken();
            
            if ($token) {
                // Update last tested timestamp
                $this->settings->update([
                    'last_tested_at' => now(),
                ]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Format phone number to 254XXXXXXXXX format.
     */
    public function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Handle different formats
        if (strlen($phone) === 9) {
            // 712345678 -> 254712345678
            $phone = '254' . $phone;
        } elseif (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            // 0712345678 -> 254712345678
            $phone = '254' . substr($phone, 1);
        } elseif (strlen($phone) === 12 && substr($phone, 0, 3) === '254') {
            // Already correct format
        } elseif (strlen($phone) === 13 && substr($phone, 0, 4) === '+254') {
            // +254712345678 -> 254712345678
            $phone = substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Generate STK Push password.
     */
    public function generatePassword($timestamp)
    {
        $shortcode = $this->settings->shortcode;
        $passkey = $this->settings->decrypted_passkey;

        return base64_encode($shortcode . $passkey . $timestamp);
    }

    /**
     * Get current timestamp in required format.
     */
    public function getTimestamp()
    {
        return date('YmdHis');
    }

    public function getCallbackUrl()
    {
        return $this->settings->callback_url ?: url('/mobile-money/webhook/callback');
    }

    /**
     * Initiate STK Push request.
     */
    public function initiateSTKPush($phone, $amount, $reference = null, $description = null)
    {
        if (!$this->isConfigured()) {
            throw new \Exception('M-Pesa is not properly configured.');
        }

        $token = $this->getAccessToken();
        $timestamp = $this->getTimestamp();
        $password = $this->generatePassword($timestamp);

        $phone = $this->formatPhoneNumber($phone);
        $reference = $reference ?: 'Payment';
        $description = $description ?: 'Payment for goods/services';

        $shortcode = $this->settings->shortcode;
        $partyB = $this->settings->shortcode_type === 'till' 
            ? $this->settings->till_number 
            : $this->settings->shortcode;

        $callbackUrl = $this->settings->callback_url ?: url('/mobile-money/webhook/callback');

        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $this->settings->getTransactionType(),
            'Amount' => (int) $amount,
            'PartyA' => $phone,
            'PartyB' => $partyB,
            'PhoneNumber' => $phone,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => substr($reference, 0, 12), // Max 12 chars
            'TransactionDesc' => substr($description, 0, 13), // Max 13 chars
        ];

        $url = $this->settings->getBaseUrl() . '/mpesa/stkpush/v1/processrequest';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $data = $response->json();

            Log::info('M-Pesa STK Push response', [
                'request' => array_merge($payload, ['Password' => '[REDACTED]']),
                'response' => $data,
            ]);

            if ($response->successful() && isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                return [
                    'success' => true,
                    'message' => $data['ResponseDescription'] ?? 'STK Push initiated',
                    'merchant_request_id' => $data['MerchantRequestID'] ?? null,
                    'checkout_request_id' => $data['CheckoutRequestID'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $data['errorMessage'] ?? $data['ResponseDescription'] ?? 'STK Push failed',
                'error_code' => $data['errorCode'] ?? $data['ResponseCode'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Failed to initiate M-Pesa payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Query STK Push status.
     */
    public function querySTKPushStatus($checkoutRequestId)
    {
        if (!$this->isConfigured()) {
            throw new \Exception('M-Pesa is not properly configured.');
        }

        $token = $this->getAccessToken();
        $timestamp = $this->getTimestamp();
        $password = $this->generatePassword($timestamp);

        $payload = [
            'BusinessShortCode' => $this->settings->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        $url = $this->settings->getBaseUrl() . '/mpesa/stkpushquery/v1/query';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $data = $response->json();

            Log::info('M-Pesa STK Query response', ['response' => $data]);

            return [
                'success' => $response->successful(),
                'result_code' => $data['ResultCode'] ?? null,
                'result_desc' => $data['ResultDesc'] ?? null,
                'data' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Query exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Register C2B URLs.
     */
    public function registerC2BUrls()
    {
        if (!$this->isConfigured()) {
            throw new \Exception('M-Pesa is not properly configured.');
        }

        $token = $this->getAccessToken();

        $validationUrl = $this->settings->validation_url ?: url('/mobile-money/webhook/validation');
        $confirmationUrl = $this->settings->confirmation_url ?: url('/mobile-money/webhook/confirmation');

        $payload = [
            'ShortCode' => $this->settings->shortcode,
            'ResponseType' => 'Completed', // or 'Cancelled'
            'ConfirmationURL' => $confirmationUrl,
            'ValidationURL' => $validationUrl,
        ];

        $url = $this->settings->getBaseUrl() . '/mpesa/c2b/v2/registerurl';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $data = $response->json();

            Log::info('M-Pesa C2B URL registration response', ['response' => $data]);

            if ($response->successful() && isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                return [
                    'success' => true,
                    'message' => $data['ResponseDescription'] ?? 'URLs registered successfully',
                ];
            }

            return [
                'success' => false,
                'message' => $data['errorMessage'] ?? $data['ResponseDescription'] ?? 'URL registration failed',
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa C2B registration exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get settings instance.
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Check M-Pesa account balance.
     */
    public function checkAccountBalance()
    {
        if (!$this->isConfigured()) {
            throw new \Exception('M-Pesa is not properly configured.');
        }

        $token = $this->getAccessToken();
        $initiator = $this->settings->initiator_name;
        $securityCredential = $this->settings->decrypted_security_credential;

        if (!$initiator || !$securityCredential) {
            throw new \Exception('Initiator Name and Security Credential are required for balance check.');
        }

        $payload = [
            'Initiator' => $initiator,
            'SecurityCredential' => $securityCredential,
            'CommandID' => 'AccountBalance',
            'PartyA' => $this->settings->shortcode,
            'IdentifierType' => '4', // Shortcode
            'Remarks' => 'Balance Query',
            'QueueTimeOutURL' => $this->settings->callback_url ?: url('/mobile-money/webhook/timeout'),
            'ResultURL' => $this->settings->callback_url ?: url('/mobile-money/webhook/result'),
        ];

        $url = $this->settings->getBaseUrl() . '/mpesa/accountbalance/v1/query';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $data = $response->json();

            Log::info('M-Pesa Account Balance response', ['response' => $data]);

            if ($response->successful() && isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                return [
                    'success' => true,
                    'message' => $data['ResponseDescription'] ?? 'Balance query initiated',
                    'conversation_id' => $data['ConversationID'] ?? null,
                    'originator_conversation_id' => $data['OriginatorConversationID'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $data['errorMessage'] ?? $data['ResponseDescription'] ?? 'Balance query failed',
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa Account Balance exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Initiate B2C Payment (Business to Customer).
     * Used for Send Money and Pochi.
     */
    public function b2cPayment($phone, $amount, $commandId = 'BusinessPayment', $remarks = null, $occasion = null)
    {
        if (!$this->isConfigured()) {
            throw new \Exception('M-Pesa is not properly configured.');
        }

        $token = $this->getAccessToken();
        $initiator = $this->settings->initiator_name;
        $securityCredential = $this->settings->decrypted_security_credential;

        if (!$initiator || !$securityCredential) {
            throw new \Exception('Initiator Name and Security Credential are required for B2C payments.');
        }

        $phone = $this->formatPhoneNumber($phone);

        $payload = [
            'InitiatorName' => $initiator,
            'SecurityCredential' => $securityCredential,
            'CommandID' => $commandId, // BusinessPayment, SalaryPayment, PromotionPayment
            'Amount' => (int) $amount,
            'PartyA' => $this->settings->shortcode,
            'PartyB' => $phone,
            'Remarks' => substr($remarks ?: 'Expense Payment', 0, 100),
            'QueueTimeOutURL' => $this->settings->callback_url ?: url('/mobile-money/webhook/timeout'),
            'ResultURL' => $this->settings->callback_url ?: url('/mobile-money/webhook/result'),
            'Occasion' => substr($occasion ?: 'Payment', 0, 100),
        ];

        $url = $this->settings->getBaseUrl() . '/mpesa/b2c/v1/paymentrequest';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $data = $response->json();

            Log::info('M-Pesa B2C Payment response', ['response' => $data]);

            if ($response->successful() && isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                return [
                    'success' => true,
                    'message' => $data['ResponseDescription'] ?? 'B2C payment initiated',
                    'conversation_id' => $data['ConversationID'] ?? null,
                    'originator_conversation_id' => $data['OriginatorConversationID'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $data['errorMessage'] ?? $data['ResponseDescription'] ?? 'B2C payment failed',
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa B2C Payment exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Initiate B2B Payment (Business to Business).
     * Used for Buy Goods and Paybill.
     */
    public function b2bPayment($partyB, $amount, $commandId, $accountReference = null, $remarks = null)
    {
        if (!$this->isConfigured()) {
            throw new \Exception('M-Pesa is not properly configured.');
        }

        $token = $this->getAccessToken();
        $initiator = $this->settings->initiator_name;
        $securityCredential = $this->settings->decrypted_security_credential;

        if (!$initiator || !$securityCredential) {
            throw new \Exception('Initiator Name and Security Credential are required for B2B payments.');
        }

        $payload = [
            'Initiator' => $initiator,
            'SecurityCredential' => $securityCredential,
            'CommandID' => $commandId, // BusinessPayBill, BusinessBuyGoods, etc.
            'SenderIdentifierType' => '4', // Shortcode
            'RecieverIdentifierType' => ($commandId === 'BusinessBuyGoods') ? '2' : '4', // 2 for Till, 4 for Shortcode
            'Amount' => (int) $amount,
            'PartyA' => $this->settings->shortcode,
            'PartyB' => $partyB,
            'AccountReference' => substr($accountReference ?: 'Payment', 0, 12),
            'Remarks' => substr($remarks ?: 'Business Payment', 0, 100),
            'QueueTimeOutURL' => $this->settings->callback_url ?: url('/mobile-money/webhook/timeout'),
            'ResultURL' => $this->settings->callback_url ?: url('/mobile-money/webhook/result'),
        ];

        $url = $this->settings->getBaseUrl() . '/mpesa/b2b/v1/paymentrequest';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $data = $response->json();

            Log::info('M-Pesa B2B Payment response', ['response' => $data]);

            if ($response->successful() && isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                return [
                    'success' => true,
                    'message' => $data['ResponseDescription'] ?? 'B2B payment initiated',
                    'conversation_id' => $data['ConversationID'] ?? null,
                    'originator_conversation_id' => $data['OriginatorConversationID'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $data['errorMessage'] ?? $data['ResponseDescription'] ?? 'B2B payment failed',
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa B2B Payment exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
