<?php

namespace App\Http\Controllers;

use App\MpesaSetting;
use App\MpesaTransaction;
use App\MpesaC2bPayment;
use App\Utils\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MpesaController extends Controller
{
    /**
     * Display M-Pesa settings form.
     */
    public function settings()
    {
        if (!auth()->user()->can('mpesa.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $settings = MpesaSetting::getForBusiness($business_id);

        return view('mpesa.settings', compact('settings'));
    }

    /**
     * Save M-Pesa settings.
     */
    public function saveSettings(Request $request)
    {
        if (!auth()->user()->can('mpesa.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $validator = Validator::make($request->all(), [
            'consumer_key' => 'nullable|string|max:255',
            'consumer_secret' => 'nullable|string|max:255',
            'passkey' => 'nullable|string|max:255',
            'shortcode' => 'nullable|string|max:20',
            'shortcode_type' => 'required|in:paybill,till',
            'till_number' => 'nullable|string|max:20',
            'account_number' => 'nullable|string|max:50',
            'environment' => 'required|in:sandbox,production',
            'is_active' => 'nullable|boolean',
            'initiator_name' => 'nullable|string|max:255',
            'security_credential' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            $settings = MpesaSetting::firstOrNew(['business_id' => $business_id]);

            // Only update credentials if provided (not empty)
            if ($request->filled('consumer_key')) {
                $settings->consumer_key = $request->consumer_key;
            }
            if ($request->filled('consumer_secret')) {
                $settings->consumer_secret = $request->consumer_secret;
            }
            if ($request->filled('passkey')) {
                $settings->passkey = $request->passkey;
            }
            if ($request->filled('initiator_name')) {
                $settings->initiator_name = $request->initiator_name;
            }
            if ($request->filled('security_credential')) {
                $settings->security_credential = $request->security_credential;
            }

            $settings->shortcode = $request->shortcode;
            $settings->shortcode_type = $request->shortcode_type;
            $settings->till_number = $request->till_number;
            $settings->account_number = $request->account_number;
            $settings->environment = $request->environment;
            $settings->is_active = $request->boolean('is_active');

            // Set default callback URLs
            $settings->callback_url = url('/mobile-money/webhook/callback');
            $settings->validation_url = url('/mobile-money/webhook/validation');
            $settings->confirmation_url = url('/mobile-money/webhook/confirmation');

            $settings->save();

            return response()->json([
                'success' => true,
                'message' => __('lang_v1.mpesa_settings_saved'),
            ]);

        } catch (\Exception $e) {
            Log::error('M-Pesa settings save error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong'),
            ]);
        }
    }

    /**
     * Test M-Pesa connection.
     */
    public function testConnection()
    {
        if (!auth()->user()->can('mpesa.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $mpesaService = new MpesaService($business_id);
            
            if (!$mpesaService->getSettings()) {
                return response()->json([
                    'success' => false,
                    'message' => __('lang_v1.mpesa_not_configured'),
                ]);
            }

            $result = $mpesaService->testConnection();

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => __('lang_v1.mpesa_connection_success'),
                    'tested_at' => now()->format('Y-m-d H:i:s'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('lang_v1.mpesa_connection_failed'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Initiate STK Push payment.
     */
    public function stkPush(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id = Auth::id();

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:9|max:15',
            'amount' => 'required|numeric|min:1',
            'reference' => 'nullable|string|max:12',
            'transaction_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            $mpesaService = new MpesaService($business_id);

            if (!$mpesaService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => __('lang_v1.mpesa_not_configured'),
                ]);
            }

            $phone = $request->phone;
            $amount = $request->amount;
            $reference = $request->reference ?: 'Payment';

            // Create pending transaction record
            $mpesaTransaction = MpesaTransaction::create([
                'business_id' => $business_id,
                'transaction_id' => $request->transaction_id,
                'status' => MpesaTransaction::STATUS_PENDING,
                'phone' => $mpesaService->formatPhoneNumber($phone),
                'amount' => $amount,
                'account_reference' => $reference,
                'transaction_type' => 'stk_push',
                'initiated_by' => $user_id,
                'metadata' => [
                    'original_phone' => $phone,
                    'initiated_at' => now()->toDateTimeString(),
                ],
            ]);

            // Initiate STK Push
            $result = $mpesaService->initiateSTKPush($phone, $amount, $reference);

            if ($result['success']) {
                // Update transaction with merchant and checkout request IDs
            $mpesaTransaction->update([
                'merchant_request_id' => $result['merchant_request_id'] ?? null,
                'checkout_request_id' => $result['checkout_request_id'] ?? null,
            ]);

            // Debug Log: What callback URL did we actually send?
            Log::info('M-Pesa STK Push initiated.', [
                'checkout_id' => $result['checkout_request_id'] ?? 'N/A',
                'callback_url' => $mpesaService->getCallbackUrl()
            ]);

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Request accepted for processing',
                'checkout_request_id' => $result['checkout_request_id'] ?? null,
                'mpesa_transaction_id' => $mpesaTransaction->id,
            ]);
            }

            // Mark as failed
            $mpesaTransaction->markAsFailed(
                $result['error_code'] ?? 'UNKNOWN',
                $result['message']
            );

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ]);

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong'),
            ]);
        }
    }

    /**
     * Query STK Push status.
     */
    public function queryStatus(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $validator = Validator::make($request->all(), [
            'checkout_request_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            // First check local database
            $mpesaTransaction = MpesaTransaction::where('checkout_request_id', $request->checkout_request_id)
                ->where('business_id', $business_id)
                ->first();

            if ($mpesaTransaction && $mpesaTransaction->status !== MpesaTransaction::STATUS_PENDING) {
                return response()->json([
                    'success' => true,
                    'status' => $mpesaTransaction->status,
                    'receipt_number' => $mpesaTransaction->mpesa_receipt_number,
                    'result_description' => $mpesaTransaction->result_description,
                ]);
            }

            // Query Safaricom API
            $mpesaService = new MpesaService($business_id);
            $result = $mpesaService->querySTKPushStatus($request->checkout_request_id);

            if ($result['success'] && isset($result['result_code'])) {
                $resultCode = $result['result_code'];
                $resultDesc = $result['result_desc'] ?? '';

                if ($resultCode === '0' || $resultCode === 0) {
                    // Payment successful - but we should wait for callback for receipt number
                    return response()->json([
                        'success' => true,
                        'status' => 'processing',
                        'result_description' => $resultDesc,
                    ]);
                } else {
                    // Payment failed
                    if ($mpesaTransaction) {
                        $mpesaTransaction->markAsFailed($resultCode, $resultDesc);
                    }

                    return response()->json([
                        'success' => true,
                        'status' => 'failed',
                        'result_code' => $resultCode,
                        'result_description' => $resultDesc,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'status' => 'pending',
            ]);

        } catch (\Exception $e) {
            Log::error('M-Pesa query status error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle STK Push callback from Safaricom.
     */
    public function callback(Request $request)
    {
        Log::info('M-Pesa STK Callback received', ['payload' => $request->all()]);

        try {
            $data = $request->all();
            
            // Extract callback data
            $body = $data['Body']['stkCallback'] ?? null;
            
            if (!$body) {
                Log::error('M-Pesa callback: Invalid payload structure');
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
            }

            $merchantRequestId = $body['MerchantRequestID'] ?? null;
            $checkoutRequestId = $body['CheckoutRequestID'] ?? null;
            $resultCode = $body['ResultCode'] ?? null;
            $resultDesc = $body['ResultDesc'] ?? '';

            // Find the transaction
            $mpesaTransaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

            if (!$mpesaTransaction) {
                Log::warning('M-Pesa callback: Transaction not found', ['checkout_id' => $checkoutRequestId]);
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
            }

            if ($resultCode === 0 || $resultCode === '0') {
                // Payment successful
                $callbackMetadata = $body['CallbackMetadata']['Item'] ?? [];
                $receiptNumber = null;
                $amount = null;
                $phone = null;

                foreach ($callbackMetadata as $item) {
                    $name = $item['Name'] ?? '';
                    $value = $item['Value'] ?? null;

                    if ($name === 'MpesaReceiptNumber') {
                        $receiptNumber = $value;
                    } elseif ($name === 'Amount') {
                        $amount = $value;
                    } elseif ($name === 'PhoneNumber') {
                        $phone = $value;
                    }
                }

                $mpesaTransaction->markAsPaid($receiptNumber, $resultCode, $resultDesc);

                Log::info('M-Pesa payment successful', [
                    'receipt' => $receiptNumber,
                    'amount' => $amount,
                    'phone' => $phone,
                ]);

            } else {
                // Payment failed
                $mpesaTransaction->markAsFailed($resultCode, $resultDesc);

                Log::info('M-Pesa payment failed', [
                    'result_code' => $resultCode,
                    'result_desc' => $resultDesc,
                ]);
            }

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);

        } catch (\Exception $e) {
            Log::error('M-Pesa callback error', ['error' => $e->getMessage()]);
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }
    }

    /**
     * Register C2B URLs with Safaricom.
     */
    public function registerUrls()
    {
        if (!auth()->user()->can('mpesa.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $mpesaService = new MpesaService($business_id);
            $result = $mpesaService->registerC2BUrls();

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * C2B Validation webhook.
     */
    public function validation(Request $request)
    {
        Log::info('M-Pesa C2B Validation received', ['payload' => $request->all()]);

        // Accept all payments by default
        // You can add custom validation logic here
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * C2B Confirmation webhook.
     */
    public function confirmation(Request $request)
    {
        Log::info('M-Pesa C2B Confirmation received', ['payload' => $request->all()]);

        try {
            $data = $request->all();

            // Get shortcode from payload to find business
            $shortcode = $data['BusinessShortCode'] ?? null;
            
            $settings = MpesaSetting::where('shortcode', $shortcode)
                ->orWhere('till_number', $shortcode)
                ->first();

            if (!$settings) {
                Log::warning('M-Pesa C2B: Business not found for shortcode', ['shortcode' => $shortcode]);
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
            }

            // Check for duplicate
            $existingPayment = MpesaC2bPayment::where('trans_id', $data['TransID'] ?? '')->first();
            if ($existingPayment) {
                Log::info('M-Pesa C2B: Duplicate transaction', ['trans_id' => $data['TransID']]);
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
            }

            // Create C2B payment record
            MpesaC2bPayment::createFromCallback($settings->business_id, $data);

            Log::info('M-Pesa C2B payment recorded', [
                'trans_id' => $data['TransID'] ?? null,
                'amount' => $data['TransAmount'] ?? null,
            ]);

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);

        } catch (\Exception $e) {
            Log::error('M-Pesa C2B confirmation error', ['error' => $e->getMessage()]);
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }
    }

    /**
     * List C2B payments.
     */
    public function c2bPayments(Request $request)
    {
        if (!auth()->user()->can('mpesa.view_transactions')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $payments = MpesaC2bPayment::byBusiness($business_id)
                ->orderBy('created_at', 'desc');

            if ($request->has('status') && $request->status !== 'all') {
                $payments->where('status', $request->status);
            }

            return DataTables::of($payments)
                ->addColumn('action', function ($row) {
                    if ($row->status !== MpesaC2bPayment::STATUS_USED) {
                        return '<button class="btn btn-xs btn-primary use-payment-btn" data-id="' . $row->id . '">' . 
                               __('lang_v1.mpesa_use_payment') . '</button>';
                    }
                    return '<span class="label label-success">' . __('lang_v1.mpesa_c2b_used') . '</span>';
                })
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 2);
                })
                ->editColumn('trans_time', function ($row) {
                    return $row->trans_time ? $row->trans_time->format('Y-m-d H:i:s') : 'N/A';
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->full_name;
                })
                ->addColumn('status_label', function ($row) {
                    return '<span class="badge ' . $row->getStatusBadgeClass() . '">' . $row->getStatusLabel() . '</span>';
                })
                ->rawColumns(['action', 'status_label'])
                ->make(true);
        }

        return view('mpesa.c2b_payments');
    }

    /**
     * Match C2B payment to transaction.
     */
    public function matchPayment(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $validator = Validator::make($request->all(), [
            'c2b_payment_id' => 'required|integer',
            'transaction_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            $payment = MpesaC2bPayment::where('id', $request->c2b_payment_id)
                ->where('business_id', $business_id)
                ->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => __('lang_v1.mpesa_payment_not_found'),
                ]);
            }

            if ($payment->status === MpesaC2bPayment::STATUS_USED) {
                return response()->json([
                    'success' => false,
                    'message' => __('lang_v1.mpesa_payment_already_used'),
                ]);
            }

            $payment->matchTo('transaction', $request->transaction_id);
            $payment->markAsUsed();

            return response()->json([
                'success' => true,
                'message' => __('lang_v1.mpesa_payment_matched'),
                'amount' => $payment->amount,
                'receipt' => $payment->trans_id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Transaction history and reports.
     */
    public function transactions(Request $request)
    {
        if (!auth()->user()->can('mpesa.view_transactions')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $transactions = MpesaTransaction::byBusiness($business_id)
                ->with('initiatedBy')
                ->orderBy('created_at', 'desc');

            if ($request->has('status') && $request->status !== 'all') {
                $transactions->where('status', $request->status);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $transactions->whereBetween('created_at', [$request->start_date, $request->end_date . ' 23:59:59']);
            }

            return DataTables::of($transactions)
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 2);
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('initiated_by_name', function ($row) {
                    return $row->initiatedBy ? $row->initiatedBy->user_full_name : 'N/A';
                })
                ->addColumn('status_label', function ($row) {
                    return '<span class="badge ' . $row->getStatusBadgeClass() . '">' . $row->getStatusLabel() . '</span>';
                })
                ->rawColumns(['status_label'])
                ->make(true);
        }

        return view('mpesa.transactions');
    }

    /**
     * Check payment status by mpesa transaction ID (for polling).
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            if (!$request->mpesa_transaction_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction ID is required',
                ]);
            }

            $mpesaTransaction = MpesaTransaction::where('id', $request->mpesa_transaction_id)
                ->where('business_id', $business_id)
                ->first();

            if (!$mpesaTransaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found',
                ]);
            }

            // If still pending, don't wait for callback — query Safaricom directly.
            // Only query if transaction is at least 8 seconds old (user needs time to respond to STK prompt).
            if ($mpesaTransaction->isPending() && $mpesaTransaction->checkout_request_id) {
                $ageSeconds = now()->diffInSeconds($mpesaTransaction->created_at);
                if ($ageSeconds >= 8) {
                    try {
                        $mpesaService = new MpesaService($business_id);
                        $result = $mpesaService->querySTKPushStatus($mpesaTransaction->checkout_request_id);

                        if ($result['success'] && isset($result['result_code'])) {
                            $resultCode = (string) $result['result_code'];
                            $resultDesc  = $result['result_desc'] ?? '';

                            // "500.001.1001" = still being processed — keep polling
                            if ($resultCode === '0') {
                                // Payment confirmed by Safaricom query
                                $receipt = $mpesaTransaction->mpesa_receipt_number
                                    ?? ($result['receipt_number'] ?? ('QRY-' . now()->format('YmdHis')));
                                $mpesaTransaction->markAsPaid($receipt, '0', 'Payment confirmed via status query');
                                $mpesaTransaction->refresh();
                            } elseif (!in_array($resultCode, ['500.001.1001', '500.001.1000', ''])) {
                                // Definitive failure (not "still processing")
                                $mpesaTransaction->markAsFailed($resultCode, $resultDesc);
                                $mpesaTransaction->refresh();
                            }
                        }
                    } catch (\Exception $e) {
                        // Query failed — silently continue; callback may still arrive
                        Log::warning('M-Pesa direct status query failed', ['error' => $e->getMessage()]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'status' => $mpesaTransaction->status,
                'is_paid' => $mpesaTransaction->isPaid(),
                'receipt_number' => $mpesaTransaction->mpesa_receipt_number,
                'result_description' => $mpesaTransaction->result_description,
            ]);
        } catch (\Exception $e) {
            Log::error('M-Pesa checkPaymentStatus error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get daily M-Pesa summary for POS modal.
     */
    public function dailySummary()
    {
        $business_id = request()->session()->get('user.business_id');
        
        try {
            $today = now()->startOfDay();
            $todayEnd = now()->endOfDay();
            
            // Get today's paid transactions
            $paidCount = MpesaTransaction::where('business_id', $business_id)
                ->where('status', MpesaTransaction::STATUS_PAID)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->count();
            
            $paidTotal = MpesaTransaction::where('business_id', $business_id)
                ->where('status', MpesaTransaction::STATUS_PAID)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->sum('amount');
            
            $pendingCount = MpesaTransaction::where('business_id', $business_id)
                ->where('status', MpesaTransaction::STATUS_PENDING)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->count();
            
            return response()->json([
                'success' => true,
                'count' => $paidCount,
                'total' => round($paidTotal, 2),
                'pending' => $pendingCount,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'total' => 0,
                'pending' => 0,
            ]);
        }
    }

    /**
     * Check account balance.
     */
    public function checkBalance()
    {
        if (!auth()->user()->can('mpesa.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $mpesaService = new MpesaService($business_id);
            $result = $mpesaService->checkAccountBalance();

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Initiate advanced M-Pesa payment (B2C/B2B/Pochi).
     */
    public function initiatePayment(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|integer',
            'transaction_type' => 'required|in:purchase,expense',
            'payment_mode' => 'required|in:send_money,buy_goods,paybill,pochi',
            'amount' => 'required|numeric|min:1',
            'phone' => 'nullable|required_if:payment_mode,send_money,pochi|string',
            'b2b_shortcode' => 'nullable|required_if:payment_mode,buy_goods,paybill|string',
            'account_number' => 'nullable|required_if:payment_mode,paybill|string',
            'command_id' => 'nullable|string',
            'remarks' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        // Security check: permissions
        if ($request->transaction_type === 'expense' && !auth()->user()->can('expense.add')) {
            abort(403, 'Unauthorized action.');
        }
        if ($request->transaction_type === 'purchase' && !auth()->user()->can('purchase.payments')) {
            abort(403, 'Unauthorized action.');
        }

        // Security check: business isolation
        $transaction = \App\Transaction::where('business_id', $business_id)
            ->where('id', $request->transaction_id)
            ->where('type', $request->transaction_type)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ]);
        }

        try {
            $mpesaService = new MpesaService($business_id);
            $paymentMode = $request->payment_mode;
            $result = ['success' => false, 'message' => 'Invalid payment mode.'];

            if ($paymentMode === 'send_money' || $paymentMode === 'pochi') {
                $commandId = $request->command_id ?: 'BusinessPayment';
                $result = $mpesaService->b2cPayment(
                    $request->phone,
                    $request->amount,
                    $commandId,
                    $request->remarks
                );
            } elseif ($paymentMode === 'buy_goods') {
                $result = $mpesaService->b2bPayment(
                    $request->b2b_shortcode,
                    $request->amount,
                    'BusinessBuyGoods',
                    'Payment',
                    $request->remarks
                );
            } elseif ($paymentMode === 'paybill') {
                $result = $mpesaService->b2bPayment(
                    $request->b2b_shortcode,
                    $request->amount,
                    'BusinessPayBill',
                    $request->account_number,
                    $request->remarks
                );
            }

            if ($result['success']) {
                // Record the transaction
                MpesaTransaction::create([
                    'business_id' => $business_id,
                    'transaction_id' => $request->transaction_id,
                    'status' => MpesaTransaction::STATUS_PENDING,
                    'phone' => ($paymentMode === 'send_money' || $paymentMode === 'pochi') ? $mpesaService->formatPhoneNumber($request->phone) : null,
                    'amount' => $request->amount,
                    'account_reference' => $request->account_number ?: ($request->transaction_type === 'purchase' ? 'Purchase' : 'Expense'),
                    'transaction_type' => ($paymentMode === 'send_money' || $paymentMode === 'pochi') ? 'b2c' : 'b2b',
                    'initiated_by' => Auth::id(),
                    'conversation_id' => $result['conversation_id'] ?? null,
                    'originator_conversation_id' => $result['originator_conversation_id'] ?? null,
                    'metadata' => [
                        'payment_mode' => $paymentMode,
                        'b2b_shortcode' => $request->b2b_shortcode,
                        'command_id' => $request->command_id,
                    ],
                ]);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('M-Pesa initiatePayment error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Pay expense via M-Pesa B2C.
     * @deprecated Use initiatePayment instead.
     */
    /**
     * Pay expense via M-Pesa B2C.
     * @deprecated Use initiatePayment instead.
     */
    public function payExpense(Request $request)
    {
        return $this->initiatePayment($request->merge(['transaction_type' => 'expense', 'payment_mode' => 'send_money']));
    }

    /**
     * Show advanced payment modal.
     */
    public function getPaymentModal(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $transaction_id = $request->input('transaction_id');
        $transaction_type = $request->input('transaction_type');

        if ($transaction_type === 'expense') {
            $transaction = \App\Transaction::where('business_id', $business_id)
                ->where('id', $transaction_id)
                ->where('type', 'expense')
                ->firstOrFail();
            $phone = $transaction->contact->mobile ?? '';
        } else {
            $transaction = \App\Transaction::where('business_id', $business_id)
                ->where('id', $transaction_id)
                ->where('type', 'purchase')
                ->firstOrFail();
            $phone = $transaction->contact->mobile ?? '';
        }

        $amount = $transaction->final_total - $transaction->payment_status !== 'paid' ? ($transaction->final_total - $transaction->transaction_payments->sum('amount')) : 0;
        if ($amount <= 0) {
            $amount = $transaction->final_total;
        }

        return view('mpesa.partials.payment_modal', compact('transaction', 'transaction_type', 'amount', 'phone'));
    }

    /**
     * Get unassigned C2B payments for POS matching.
     */
    public function getUnassignedPayments(Request $request)
    {
        if (!auth()->user()->can('mpesa.view_transactions')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $query = MpesaC2bPayment::where('business_id', $business_id)
            ->whereIn('status', [MpesaC2bPayment::STATUS_RECEIVED, MpesaC2bPayment::STATUS_UNMATCHED])
            ->orderBy('created_at', 'desc')
            ->limit(15);

        if ($request->has('amount') && $request->amount > 0) {
            $query->where('amount', $request->amount);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('trans_id', 'like', "%{$search}%")
                  ->orWhere('msisdn', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $payments = $query->get();

        return response()->json([
            'success' => true,
            'payments' => $payments
        ]);
    }

    /**
     * Check for a matching C2B payment based on amount.
     */
    public function checkMatchingPayment(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $amount = $request->get('amount');

        if (empty($amount)) {
            return response()->json([
                'success' => false,
                'message' => 'Amount is required'
            ]);
        }

        // Search for unmatched C2B payments with the same amount in the last 15 minutes
        $payment = MpesaC2bPayment::where('business_id', $business_id)
            ->where('amount', $amount)
            ->whereIn('status', [MpesaC2bPayment::STATUS_RECEIVED, MpesaC2bPayment::STATUS_UNMATCHED])
            ->where('created_at', '>=', now()->subMinutes(15))
            ->orderBy('created_at', 'desc')
            ->first();

        if ($payment) {
            return response()->json([
                'success' => true,
                'payment' => [
                    'id' => $payment->id,
                    'trans_id' => $payment->trans_id,
                    'amount' => $payment->amount,
                    'phone' => $payment->msisdn,
                    'name' => $payment->first_name . ' ' . $payment->last_name,
                    'time' => $payment->created_at->diffForHumans()
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No matching payment found yet'
        ]);
    }
}
