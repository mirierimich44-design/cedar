<?php

namespace Modules\Pesapal\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Pesapal\Entities\PesapalSetting;
use Modules\Pesapal\Entities\PesapalTransaction;
use Modules\Pesapal\Utils\PesapalService;
use App\Transaction;
use App\TransactionPayment;
use App\Events\TransactionPaymentAdded;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PesapalGatewayController extends Controller
{
    protected $transactionUtil;

    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * After a Pesapal payment is confirmed, create a TransactionPayment record
     * in the main ERP and update the transaction's payment status.
     * Safe to call multiple times — guards against duplicate records.
     */
    private function applyPaymentToTransaction(PesapalTransaction $pesapalTx): void
    {
        if (!$pesapalTx->transaction_id) {
            return; // No ERP transaction linked — standalone Pesapal payment
        }

        // Prevent duplicate payment records for the same Pesapal confirmation
        $alreadyRecorded = TransactionPayment::where('transaction_id', $pesapalTx->transaction_id)
            ->where('transaction_no', $pesapalTx->confirmation_code)
            ->exists();

        if ($alreadyRecorded) {
            return;
        }

        $transaction = Transaction::find($pesapalTx->transaction_id);
        if (!$transaction) {
            return;
        }

        $prefix_type = $transaction->type === 'purchase' ? 'purchase_payment' : 'sell_payment';
        $ref_count   = $this->transactionUtil->setAndGetReferenceCount($prefix_type, $transaction->business_id);
        $payment_ref = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count, $transaction->business_id);

        $tp = TransactionPayment::create([
            'transaction_id' => $transaction->id,
            'business_id'    => $transaction->business_id,
            'method'         => 'pesapal',
            'amount'         => $pesapalTx->amount,
            'transaction_no' => $pesapalTx->confirmation_code,
            'payment_ref_no' => $payment_ref,
            'paid_on'        => now()->toDateTimeString(),
            'created_by'     => $pesapalTx->initiated_by ?? auth()->id(),
            'payment_for'    => $transaction->contact_id,
            'note'           => 'Pesapal — ' . $pesapalTx->confirmation_code,
        ]);

        $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

        event(new TransactionPaymentAdded($tp, $transaction->toArray()));
    }

    // -----------------------------------------------------------------------
    // Settings
    // -----------------------------------------------------------------------

    public function settings()
    {
        if (!auth()->user()->can('pesapal.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $settings    = PesapalSetting::getForBusiness($business_id);

        return view('pesapal::settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        if (!auth()->user()->can('pesapal.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $validator = Validator::make($request->all(), [
            'consumer_key'    => 'nullable|string|max:255',
            'consumer_secret' => 'nullable|string|max:255',
            'environment'     => 'required|in:sandbox,production',
            'currency'        => 'nullable|string|max:10',
            'is_active'       => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $settings = PesapalSetting::firstOrNew(['business_id' => $business_id]);

            if ($request->filled('consumer_key')) {
                $settings->consumer_key = $request->consumer_key;
            }
            if ($request->filled('consumer_secret')) {
                $settings->consumer_secret = $request->consumer_secret;
            }

            $settings->environment  = $request->environment;
            $settings->currency     = $request->input('currency', 'KES');
            $settings->callback_url = url('/pesapal/callback');
            $settings->ipn_url      = $settings->ipn_url ?: url('/pesapal/ipn');
            $settings->is_active    = $request->boolean('is_active');
            $settings->save();

            return response()->json(['success' => true, 'message' => 'Pesapal settings saved successfully.']);

        } catch (\Exception $e) {
            Log::error('Pesapal settings save error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => __('messages.something_went_wrong')]);
        }
    }

    // -----------------------------------------------------------------------
    // Test connection
    // -----------------------------------------------------------------------

    public function testConnection()
    {
        if (!auth()->user()->can('pesapal.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $service = new PesapalService($business_id);

            if (!$service->getSettings()) {
                return response()->json(['success' => false, 'message' => 'Pesapal not configured.']);
            }

            $ok = $service->testConnection();

            if ($ok) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'Connected to Pesapal successfully.',
                    'tested_at' => now()->format('Y-m-d H:i:s'),
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Connection failed. Check your credentials.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // -----------------------------------------------------------------------
    // IPN Registration
    // -----------------------------------------------------------------------

    public function registerIpn()
    {
        if (!auth()->user()->can('pesapal.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $service = new PesapalService($business_id);
            $result  = $service->registerIpnUrl(url('/pesapal/ipn'));

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // -----------------------------------------------------------------------
    // Initiate Payment (POS)
    // -----------------------------------------------------------------------

    public function initiatePayment(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id     = Auth::id();

        $validator = Validator::make($request->all(), [
            'amount'         => 'required|numeric|min:1',
            'reference'      => 'nullable|string|max:50',
            'description'    => 'nullable|string|max:100',
            'transaction_id' => 'nullable|integer',
            'email'          => 'nullable|email',
            'phone'          => 'nullable|string|max:20',
            'first_name'     => 'nullable|string|max:100',
            'last_name'      => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $service = new PesapalService($business_id);

            if (!$service->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesapal is not configured. Please set up your API keys and register the IPN URL first.',
                ]);
            }

            $reference = $request->filled('reference')
                ? $request->reference
                : 'POS-' . strtoupper(Str::random(12));

            $description = $request->input('description', 'POS Payment');

            $billing = [];
            if ($request->filled('email'))      $billing['email_address'] = $request->email;
            if ($request->filled('phone')) {
                $billing['phone_number']  = $request->phone;
                $billing['country_code']  = 'KE';
            }
            if ($request->filled('first_name')) $billing['first_name']    = $request->first_name;
            if ($request->filled('last_name'))  $billing['last_name']     = $request->last_name;

            // Create pending local record before API call
            $pesapalTx = PesapalTransaction::create([
                'business_id'        => $business_id,
                'transaction_id'     => $request->transaction_id,
                'merchant_reference' => $reference,
                'status'             => PesapalTransaction::STATUS_PENDING,
                'amount'             => $request->amount,
                'currency'           => $service->getSettings()->currency ?: 'KES',
                'description'        => $description,
                'initiated_by'       => $user_id,
                'metadata'           => ['initiated_at' => now()->toDateTimeString()],
            ]);

            $result = $service->submitOrderRequest($request->amount, $description, $reference, $billing);

            if ($result['success']) {
                $pesapalTx->update([
                    'order_tracking_id'  => $result['order_tracking_id'],
                    'merchant_reference' => $result['merchant_reference'],
                ]);

                return response()->json([
                    'success'            => true,
                    'message'            => 'Payment order created. Complete payment via mobile prompt or interface.',
                    'pesapal_tx_id'      => $pesapalTx->id,
                    'order_tracking_id'  => $result['order_tracking_id'],
                    'merchant_reference' => $result['merchant_reference'],
                    'redirect_url'       => $result['redirect_url'],
                ]);
            }

            $pesapalTx->markAsFailed('failed', $result['message'] ?? 'Order submission failed.');

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to create payment order.',
            ]);

        } catch (\Exception $e) {
            Log::error('Pesapal initiatePayment error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => __('messages.something_went_wrong')]);
        }
    }

    // -----------------------------------------------------------------------
    // Poll payment status (POS JS every 3 seconds)
    // -----------------------------------------------------------------------

    public function checkPaymentStatus(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            $validator = Validator::make($request->all(), [
                'pesapal_tx_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $pesapalTx = PesapalTransaction::where('id', $request->pesapal_tx_id)
                ->where('business_id', $business_id)
                ->first();

            if (!$pesapalTx) {
                return response()->json(['success' => false, 'message' => 'Transaction not found.']);
            }

            // Already resolved — return cached result
            if (!$pesapalTx->isPending()) {
                return response()->json([
                    'success'            => true,
                    'status'             => $pesapalTx->status,
                    'is_completed'       => $pesapalTx->isCompleted(),
                    'confirmation_code'  => $pesapalTx->confirmation_code,
                    'payment_method'     => $pesapalTx->payment_method,
                    'status_description' => $pesapalTx->status_description,
                ]);
            }

            // Query Pesapal directly if we have an order_tracking_id
            if ($pesapalTx->order_tracking_id) {
                $service = new PesapalService($business_id);
                $result  = $service->getTransactionStatus($pesapalTx->order_tracking_id);

                if ($result['success'] && $result['status_code'] !== null) {
                    $statusCode = (int) $result['status_code'];

                    if ($statusCode === 1) {
                        // COMPLETED
                        $pesapalTx->markAsCompleted(
                            $result['confirmation_code'],
                            $result['payment_method'],
                            $result['payment_account'],
                            $result['payment_status_description']
                        );
                        $pesapalTx->refresh();
                        // Apply payment to ERP transaction
                        try {
                            $this->applyPaymentToTransaction($pesapalTx);
                        } catch (\Exception $payEx) {
                            Log::error('Pesapal: failed to apply payment to transaction', ['error' => $payEx->getMessage()]);
                        }
                    } elseif (in_array($statusCode, [2, 3])) {
                        // FAILED or REVERSED
                        $pesapalTx->markAsFailed(
                            PesapalService::mapStatusCode($statusCode),
                            $result['payment_status_description']
                        );
                        $pesapalTx->refresh();
                    }
                    // status_code 0 = not yet paid — keep polling
                }
            }

            return response()->json([
                'success'            => true,
                'status'             => $pesapalTx->status,
                'is_completed'       => $pesapalTx->isCompleted(),
                'confirmation_code'  => $pesapalTx->confirmation_code,
                'payment_method'     => $pesapalTx->payment_method,
                'status_description' => $pesapalTx->status_description,
            ]);

        } catch (\Exception $e) {
            Log::error('Pesapal checkPaymentStatus error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Server error.']);
        }
    }

    // -----------------------------------------------------------------------
    // IPN callback — public, called by Pesapal via GET
    // -----------------------------------------------------------------------

    public function ipnCallback(Request $request)
    {
        Log::info('Pesapal IPN received', ['params' => $request->all()]);

        try {
            $orderTrackingId   = $request->input('OrderTrackingId');
            $merchantReference = $request->input('OrderMerchantReference');

            if (!$orderTrackingId) {
                Log::warning('Pesapal IPN: missing OrderTrackingId');
                return response()->json(['orderNotificationType' => 'IPNCHANGE', 'status' => 200]);
            }

            $pesapalTx = PesapalTransaction::where('order_tracking_id', $orderTrackingId)->first()
                ?? PesapalTransaction::where('merchant_reference', $merchantReference)->first();

            if (!$pesapalTx || !$pesapalTx->isPending()) {
                return response()->json(['orderNotificationType' => 'IPNCHANGE', 'status' => 200]);
            }

            $service = new PesapalService($pesapalTx->business_id);
            $result  = $service->getTransactionStatus($orderTrackingId);

            if ($result['success'] && $result['status_code'] !== null) {
                $statusCode = (int) $result['status_code'];

                if ($statusCode === 1) {
                    $pesapalTx->markAsCompleted(
                        $result['confirmation_code'],
                        $result['payment_method'],
                        $result['payment_account'],
                        $result['payment_status_description']
                    );
                    if (!$pesapalTx->order_tracking_id) {
                        $pesapalTx->update(['order_tracking_id' => $orderTrackingId]);
                    }
                    $pesapalTx->refresh();
                    // Apply payment to ERP transaction
                    try {
                        $this->applyPaymentToTransaction($pesapalTx);
                    } catch (\Exception $payEx) {
                        Log::error('Pesapal IPN: failed to apply payment to transaction', ['error' => $payEx->getMessage()]);
                    }
                    Log::info('Pesapal IPN: payment completed', [
                        'confirmation_code' => $result['confirmation_code'],
                        'method'            => $result['payment_method'],
                        'amount'            => $result['amount'],
                    ]);
                } elseif (in_array($statusCode, [2, 3])) {
                    $pesapalTx->markAsFailed(
                        PesapalService::mapStatusCode($statusCode),
                        $result['payment_status_description']
                    );
                }
            }

            // Pesapal requires this exact acknowledgement
            return response()->json(['orderNotificationType' => 'IPNCHANGE', 'status' => 200]);

        } catch (\Exception $e) {
            Log::error('Pesapal IPN error', ['error' => $e->getMessage()]);
            return response()->json(['orderNotificationType' => 'IPNCHANGE', 'status' => 200]);
        }
    }

    // -----------------------------------------------------------------------
    // Customer redirect after payment (landing page for QR scans)
    // -----------------------------------------------------------------------

    public function paymentCallback(Request $request)
    {
        Log::info('Pesapal customer callback', ['params' => $request->all()]);

        $orderTrackingId   = $request->input('OrderTrackingId');
        $merchantReference = $request->input('OrderMerchantReference');

        return view('pesapal::payment_complete', compact('orderTrackingId', 'merchantReference'));
    }

    // -----------------------------------------------------------------------
    // Transactions list
    // -----------------------------------------------------------------------

    public function transactions(Request $request)
    {
        if (!auth()->user()->can('pesapal.view_transactions')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $txs = PesapalTransaction::byBusiness($business_id)
                ->with('initiatedBy')
                ->orderBy('created_at', 'desc');

            if ($request->filled('status') && $request->status !== 'all') {
                $txs->where('status', $request->status);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $txs->whereBetween('created_at', [$request->start_date, $request->end_date . ' 23:59:59']);
            }

            return DataTables::of($txs)
                ->editColumn('amount', fn($row) => number_format($row->amount, 2))
                ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i:s'))
                ->addColumn('initiated_by_name', fn($row) => $row->initiatedBy ? $row->initiatedBy->user_full_name : 'N/A')
                ->addColumn('status_label', fn($row) =>
                    '<span class="badge ' . $row->getStatusBadgeClass() . '">' . $row->getStatusLabel() . '</span>'
                )
                ->rawColumns(['status_label'])
                ->make(true);
        }

        return view('pesapal::transactions');
    }

    // -----------------------------------------------------------------------
    // Daily summary for POS badge
    // -----------------------------------------------------------------------

    public function dailySummary()
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $today    = now()->startOfDay();
            $todayEnd = now()->endOfDay();

            $paidTotal = PesapalTransaction::where('business_id', $business_id)
                ->where('status', PesapalTransaction::STATUS_COMPLETED)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->sum('amount');

            $paidCount = PesapalTransaction::where('business_id', $business_id)
                ->where('status', PesapalTransaction::STATUS_COMPLETED)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->count();

            $pendingCount = PesapalTransaction::where('business_id', $business_id)
                ->where('status', PesapalTransaction::STATUS_PENDING)
                ->whereBetween('created_at', [$today, $todayEnd])
                ->count();

            return response()->json([
                'success' => true,
                'count'   => $paidCount,
                'total'   => round($paidTotal, 2),
                'pending' => $pendingCount,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'count' => 0, 'total' => 0, 'pending' => 0]);
        }
    }
}
