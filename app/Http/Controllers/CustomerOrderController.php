<?php

namespace App\Http\Controllers;

use App\Business;
use App\Contact;
use App\MpesaTransaction;
use App\PosOrder;
use App\PosOrderLine;
use App\Product;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\Utils\MpesaService;
use App\VariationLocationDetails;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerOrderController extends Controller
{
    protected $productUtil;

    public function __construct(\App\Utils\ProductUtil $productUtil)
    {
        $this->productUtil = $productUtil;
    }
    // ── Public: show order form for customer ────────────────────────────────

    public function show($token)
    {
        try {
            // Support both 'customer' and 'both' type contacts
            $contact = Contact::where('order_token', $token)
                ->whereIn('type', ['customer', 'both'])
                ->first();

            if (!$contact) {
                return response()->view('errors.404', [
                    'message' => 'This order link is invalid or has expired. Please ask the store for a new link.'
                ], 404);
            }

            $business = Business::find($contact->business_id);
            if (!$business) {
                return response()->view('errors.404', ['message' => 'Store not found.'], 404);
            }

            // Get active products — simple join without location requirement
            // (location details may not exist for all products, causing empty list)
            $products = DB::table('products as p')
                ->join('variations as v', 'p.id', '=', 'v.product_id')
                ->where('p.business_id', $contact->business_id)
                ->where('p.is_inactive', 0)
                ->whereNotIn('p.type', ['modifier'])
                ->select(
                    'p.id as product_id',
                    'p.name as product_name',
                    'p.image as product_image',
                    'v.id as variation_id',
                    'v.name as variation_name',
                    'v.default_sell_price as price'
                )
                ->orderBy('p.name')
                ->get();

            // Check MPESA enabled — safe fallback if table missing
            try {
                $mpesaEnabled = DB::table('mpesa_settings')
                    ->where('business_id', $contact->business_id)
                    ->where('is_active', 1)
                    ->exists();
            } catch (\Exception $e) {
                $mpesaEnabled = false;
            }

            return view('customer_order.show', compact('contact', 'business', 'products', 'token', 'mpesaEnabled'));

        } catch (\Exception $e) {
            Log::error('CustomerOrder show error: ' . $e->getMessage() . ' | token: ' . $token);
            return response('<div style="font-family:sans-serif;padding:40px;text-align:center;">'
                . '<h2>Something went wrong</h2>'
                . '<p>Unable to load the order page. Please contact the store.</p>'
                . '<small style="color:#aaa;">Ref: ' . substr($token, 0, 8) . '</small>'
                . '</div>', 500);
        }
    }

    // ── Public: AJAX product search for customer order page ──────────────────

    public function search(Request $request, $token)
    {
        $contact = Contact::where('order_token', $token)
            ->whereIn('type', ['customer', 'both'])
            ->first();

        if (!$contact) {
            return response()->json([]);
        }

        $q = trim($request->get('q', ''));

        $query = DB::table('products as p')
            ->join('variations as v', 'p.id', '=', 'v.product_id')
            ->where('p.business_id', $contact->business_id)
            ->where('p.is_inactive', 0)
            ->whereNotIn('p.type', ['modifier'])
            ->select(
                'p.id as product_id',
                'p.name as product_name',
                'p.image as product_image',
                'v.id as variation_id',
                'v.name as variation_name',
                'v.default_sell_price as price'
            )
            ->orderBy('p.name');

        if ($q !== '') {
            $query->where(function ($qb) use ($q) {
                $qb->where('p.name', 'like', '%' . $q . '%')
                   ->orWhere('v.name', 'like', '%' . $q . '%');
            });
        }

        return response()->json($query->limit(20)->get());
    }

    // ── Public: submit order ─────────────────────────────────────────────────

    public function store(Request $request, $token)
    {
        $contact = Contact::where('order_token', $token)
            ->whereIn('type', ['customer', 'both'])
            ->first();

        if (!$contact) {
            return response()->json(['success' => false, 'message' => 'Invalid order link.']);
        }

        $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.variation_id' => 'required|integer',
            'items.*.qty'    => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:mpesa,cash',
            'notes'          => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Get default location
            $location = DB::table('business_locations')
                ->where('business_id', $contact->business_id)
                ->first();

            // Calculate total
            $total = 0;
            $lines = [];
            foreach ($request->items as $item) {
                $variation = DB::table('variations')->where('id', $item['variation_id'])->first();
                if (!$variation) continue;
                $qty   = (float) $item['qty'];
                $price = (float) $variation->default_sell_price;
                $total += $qty * $price;
                $lines[] = [
                    'variation_id' => $variation->id,
                    'product_id'   => $variation->product_id,
                    'qty'          => $qty,
                    'price'        => $price,
                ];
            }

            if (empty($lines)) {
                return response()->json(['success' => false, 'message' => 'No valid items in order.']);
            }

            // Create the order
            $order = PosOrder::create([
                'business_id'    => $contact->business_id,
                'location_id'    => $location->id,
                'contact_id'     => $contact->id,
                'created_by'     => DB::table('users')
                    ->where('business_id', $contact->business_id)
                    ->value('id'),
                'ref_no'         => 'CO-' . strtoupper(Str::random(6)),
                'status'         => 'pending',
                'notes'          => $request->notes,
                'total_amount'   => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'mpesa_phone'    => $request->mpesa_phone ?? null,
            ]);

            foreach ($lines as $line) {
                PosOrderLine::create([
                    'order_id'     => $order->id,
                    'product_id'   => $line['product_id'],
                    'variation_id' => $line['variation_id'],
                    'quantity'     => $line['qty'],
                    'unit_price'   => $line['price'],
                ]);
            }

            DB::commit();

            // If MPESA payment, initiate STK push
            if ($request->payment_method === 'mpesa' && $request->mpesa_phone) {
                $stkResult = $this->initiateMpesaPayment($order, $contact->business_id, $request->mpesa_phone);
                return response()->json([
                    'success'             => true,
                    'order_id'            => $order->id,
                    'ref_no'              => $order->ref_no,
                    'total'               => $total,
                    'mpesa_initiated'     => $stkResult['success'],
                    'mpesa_transaction_id'=> $stkResult['mpesa_transaction_id'] ?? null,
                    'message'             => $stkResult['success']
                        ? 'Order placed! Check your phone for MPESA payment prompt.'
                        : 'Order placed but MPESA prompt failed: ' . ($stkResult['message'] ?? ''),
                ]);
            }

            return response()->json([
                'success' => true,
                'order_id'=> $order->id,
                'ref_no'  => $order->ref_no,
                'total'   => $total,
                'message' => 'Order placed successfully!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CustomerOrder store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error placing order.']);
        }
    }

    // ── Public: poll MPESA payment status for customer order ─────────────────

    public function checkPayment(Request $request)
    {
        $mpesaTransactionId = $request->mpesa_transaction_id;
        $orderId            = $request->order_id;

        if (!$mpesaTransactionId) {
            return response()->json(['success' => false, 'message' => 'Missing transaction ID']);
        }

        $tx = MpesaTransaction::find($mpesaTransactionId);
        if (!$tx) {
            return response()->json(['success' => false, 'message' => 'Transaction not found']);
        }

        // Query Safaricom directly if still pending
        if ($tx->isPending() && $tx->checkout_request_id) {
            $ageSeconds = now()->diffInSeconds($tx->created_at);
            if ($ageSeconds >= 8) {
                try {
                    $mpesaService = new MpesaService($tx->business_id);
                    $result = $mpesaService->querySTKPushStatus($tx->checkout_request_id);
                    if ($result['success'] && isset($result['result_code'])) {
                        $rc = (string) $result['result_code'];
                        if ($rc === '0') {
                            $receipt = $tx->mpesa_receipt_number ?? ('QRY-' . now()->format('YmdHis'));
                            $tx->markAsPaid($receipt, '0', 'Confirmed via query');
                            // Update order
                            if ($orderId) {
                                PosOrder::where('id', $orderId)->update([
                                    'payment_status'      => 'paid',
                                    'mpesa_receipt'       => $receipt,
                                    'mpesa_transaction_id'=> $tx->id,
                                    'status'              => 'processing',
                                ]);
                            }
                            $tx->refresh();
                        } elseif (!in_array($rc, ['500.001.1001', '500.001.1000', ''])) {
                            $tx->markAsFailed($rc, $result['result_desc'] ?? '');
                            $tx->refresh();
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('CustomerOrder MPESA query failed', ['error' => $e->getMessage()]);
                }
            }
        }

        // If paid, also mark order
        if ($tx->isPaid() && $orderId) {
            PosOrder::where('id', $orderId)
                ->where('payment_status', '!=', 'paid')
                ->update([
                    'payment_status'      => 'paid',
                    'mpesa_receipt'       => $tx->mpesa_receipt_number,
                    'mpesa_transaction_id'=> $tx->id,
                    'status'              => 'processing',
                ]);
        }

        return response()->json([
            'success'        => true,
            'status'         => $tx->status,
            'is_paid'        => $tx->isPaid(),
            'receipt_number' => $tx->mpesa_receipt_number,
        ]);
    }

    // ── Owner: generate / get order token for a contact ──────────────────────

    public function generateToken(Request $request, $contact_id)
    {
        $business_id = $request->session()->get('user.business_id');

        $contact = Contact::where('id', $contact_id)
            ->where('business_id', $business_id)
            ->whereIn('type', ['customer', 'both'])
            ->firstOrFail();

        if (empty($contact->order_token)) {
            $contact->order_token = Str::random(40);
            $contact->save();
        }

        $url = url('/customer-order/' . $contact->order_token);

        return response()->json([
            'success'     => true,
            'token'       => $contact->order_token,
            'order_url'   => $url,
            'customer_name' => $contact->name,
        ]);
    }

    // ── Owner: regenerate token ───────────────────────────────────────────────

    public function regenerateToken(Request $request, $contact_id)
    {
        $business_id = $request->session()->get('user.business_id');

        $contact = Contact::where('id', $contact_id)
            ->where('business_id', $business_id)
            ->whereIn('type', ['customer', 'both'])
            ->firstOrFail();

        $contact->order_token = Str::random(40);
        $contact->save();

        return response()->json([
            'success'   => true,
            'order_url' => url('/customer-order/' . $contact->order_token),
        ]);
    }

    // ── Private helper: initiate MPESA STK for customer order ────────────────

    private function initiateMpesaPayment(PosOrder $order, $business_id, $phone)
    {
        try {
            $mpesaService = new MpesaService($business_id);

            // Create pending transaction record
            $tx = MpesaTransaction::create([
                'business_id'  => $business_id,
                'amount'       => $order->total_amount,
                'phone_number' => $phone,
                'reference'    => $order->ref_no,
                'description'  => 'Customer order ' . $order->ref_no,
                'status'       => MpesaTransaction::STATUS_PENDING,
                'initiated_by' => null,
            ]);

            $result = $mpesaService->initiateSTKPush(
                $phone,
                $order->total_amount,
                $order->ref_no,
                'Order ' . $order->ref_no
            );

            if ($result['success']) {
                $tx->update([
                    'checkout_request_id'  => $result['checkout_request_id'] ?? null,
                    'merchant_request_id'  => $result['merchant_request_id'] ?? null,
                ]);
                $order->update(['mpesa_transaction_id' => $tx->id]);
            }

            return [
                'success'              => $result['success'],
                'mpesa_transaction_id' => $tx->id,
                'message'              => $result['message'] ?? '',
            ];
        } catch (\Exception $e) {
            Log::error('CustomerOrder MPESA initiate error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ── Authenticated: Order Links management page ──────────────────────────

    public function index(Request $request)
    {
        $business_id = session('user.business_id');

        // All customer contacts for this business
        $contacts = Contact::where('business_id', $business_id)
            ->whereIn('type', ['customer', 'both'])
            ->where('contact_status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'mobile', 'email', 'order_token', 'type']);

        // Order counts per contact
        $orderCounts = PosOrder::where('business_id', $business_id)
            ->selectRaw('contact_id, COUNT(*) as total_orders, SUM(total_amount) as total_value')
            ->groupBy('contact_id')
            ->pluck('total_orders', 'contact_id')
            ->toArray();

        return view('customer_order.index', compact('contacts', 'orderCounts'));
    }
}
