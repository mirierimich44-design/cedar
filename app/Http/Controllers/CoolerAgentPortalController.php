<?php

namespace App\Http\Controllers;

use App\CoolerAgreement;
use App\CoolerAsset;
use App\CoolerDealer;
use App\CoolerDocument;
use App\CoolerRetrieval;
use App\MpesaTransaction;
use App\PosOrder;
use App\PosOrderLine;
use App\Utils\CoolerDocumentService;
use App\Utils\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CoolerAgentPortalController extends Controller
{
    protected CoolerDocumentService $docService;

    public function __construct(CoolerDocumentService $docService)
    {
        $this->docService = $docService;
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->can('cooler.agent.portal')) {
                abort(403, 'Agent portal access required.');
            }
            return $next($request);
        });
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        $agentId    = auth()->id();
        $businessId = $request->session()->get('user.business_id');

        $myCustomers = CoolerDealer::forBusiness($businessId)->forAgent($agentId);

        $stats = [
            'total_customers'    => (clone $myCustomers)->count(),
            'active_customers'   => (clone $myCustomers)->active()->count(),
            'pending_retrievals' => CoolerRetrieval::where('business_id', $businessId)
                ->where('created_by', $agentId)
                ->whereIn('status', ['initiated', 'in_progress'])
                ->count(),
            'orders_today'       => PosOrder::where('business_id', $businessId)
                ->where('created_by', $agentId)
                ->whereDate('created_at', today())
                ->count(),
        ];

        $recentCustomers = (clone $myCustomers)->latest()->limit(5)->get();
        $recentRetrievals = CoolerRetrieval::where('business_id', $businessId)
            ->where('created_by', $agentId)
            ->with(['dealer', 'cooler'])
            ->latest()
            ->limit(5)
            ->get();
        $recentOrders = PosOrder::where('business_id', $businessId)
            ->where('created_by', $agentId)
            ->latest()
            ->limit(5)
            ->get();

        return view('cooler.agent.dashboard', compact('stats', 'recentCustomers', 'recentRetrievals', 'recentOrders'));
    }

    // ── My Customers ──────────────────────────────────────────────────────────

    public function customers(Request $request)
    {
        $agentId    = auth()->id();
        $businessId = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $customers = CoolerDealer::forBusiness($businessId)
                ->forAgent($agentId)
                ->select(['id', 'name', 'outlet_name', 'phone', 'channel', 'area', 'status', 'compliance_score', 'created_at']);

            return \Yajra\DataTables\Facades\DataTables::of($customers)
                ->addColumn('status_badge', fn ($row) => (new CoolerDealer(['status' => $row->status]))->status_badge)
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('cooler.agent.customers.show', $row->id) . '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a> '
                        . '<a href="' . route('cooler.agent.orders.create', ['customer_id' => $row->id]) . '" class="btn btn-xs btn-success" title="Place Order"><i class="fa fa-shopping-cart"></i></a>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('cooler.agent.customers');
    }

    public function showCustomer(Request $request, int $id)
    {
        $dealer = $this->findMyCustomer($id, $request->session()->get('user.business_id'));
        $dealer->load(['coolers', 'activeAgreement', 'retrievals', 'documents']);
        return view('cooler.agent.customer_show', compact('dealer'));
    }

    public function createCustomer(Request $request)
    {
        return view('cooler.agent.customer_create');
    }

    public function storeCustomer(Request $request)
    {
        $businessId = $request->session()->get('user.business_id');

        $request->validate([
            'name'             => 'required|string|max:255',
            'outlet_name'      => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'id_number'        => 'required|string|max:50',
            'kra_pin'          => 'nullable|string|max:20',
            'channel'          => 'required|in:' . implode(',', CoolerDealer::$channels),
            'area'             => 'nullable|string|max:255',
            'building'         => 'nullable|string|max:255',
            'road'             => 'nullable|string|max:255',
            'years_in_business'=> 'nullable|integer|min:0',
            'brands_stocked'   => 'nullable|array',
            // Documents
            'id_copy'          => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'kra_pin_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'passport_photo'   => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $dealer = CoolerDealer::create([
                'business_id'      => $businessId,
                'name'             => $request->name,
                'outlet_name'      => $request->outlet_name,
                'phone'            => $request->phone,
                'id_number'        => $request->id_number,
                'kra_pin'          => $request->kra_pin,
                'channel'          => $request->channel,
                'area'             => $request->area,
                'building'         => $request->building,
                'road'             => $request->road,
                'years_in_business'=> $request->years_in_business,
                'brands_stocked'   => $request->brands_stocked ?? [],
                'status'           => 'active',
                'compliance_score' => 100,
                'created_by'       => auth()->id(),
                'agent_id'         => auth()->id(),
            ]);

            // Upload required documents
            foreach (['id_copy', 'kra_pin_certificate', 'passport_photo'] as $field) {
                if ($request->hasFile($field)) {
                    $this->docService->upload($request->file($field), $dealer, $field);
                }
            }

            // Optional legal documents
            foreach (['county_business_permit', 'certificate_of_registration', 'certificate_of_incorporation', 'tax_certificate'] as $field) {
                if ($request->hasFile($field)) {
                    $this->docService->upload($request->file($field), $dealer, $field);
                }
            }

            DB::commit();

            return redirect()->route('cooler.agent.customers')
                ->with('status', ['success' => 1, 'msg' => "Customer {$dealer->outlet_name} registered successfully."]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent storeCustomer error: ' . $e->getMessage());
            return back()->withInput()->with('notification', ['success' => 0, 'msg' => 'Failed to register customer: ' . $e->getMessage()]);
        }
    }

    // ── Retrievals ────────────────────────────────────────────────────────────

    public function retrievals(Request $request)
    {
        $agentId    = auth()->id();
        $businessId = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $retrievals = CoolerRetrieval::where('cooler_retrievals.business_id', $businessId)
                ->where('cooler_retrievals.created_by', $agentId)
                ->join('cooler_dealers as cd', 'cooler_retrievals.dealer_id', '=', 'cd.id')
                ->join('cooler_assets as ca', 'cooler_retrievals.cooler_id', '=', 'ca.id')
                ->select([
                    'cooler_retrievals.id', 'cooler_retrievals.retrieval_date',
                    'cooler_retrievals.reason', 'cooler_retrievals.status',
                    'cd.name as dealer_name', 'cd.outlet_name',
                    'ca.asset_number',
                ]);

            return \Yajra\DataTables\Facades\DataTables::of($retrievals)
                ->addColumn('status_badge', fn ($row) => (new CoolerRetrieval(['status' => $row->status]))->status_badge)
                ->addColumn('action', function ($row) {
                    $html = '<a href="' . route('cooler.retrievals.show', $row->id) . '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a> ';
                    if ($row->status !== 'completed') {
                        $html .= '<a href="' . route('cooler.retrievals.execute', $row->id) . '" class="btn btn-xs btn-warning"><i class="fa fa-truck"></i> Execute</a>';
                    }
                    return $html;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        $agentId    = auth()->id();
        $businessId = $request->session()->get('user.business_id');
        $customers  = CoolerDealer::forBusiness($businessId)->forAgent($agentId)->active()->pluck('outlet_name', 'id');
        $reasons    = CoolerRetrieval::$reasons;
        $user       = auth()->user();

        return view('cooler.agent.retrievals', compact('customers', 'reasons', 'user'));
    }

    public function storeRetrieval(Request $request)
    {
        $businessId = $request->session()->get('user.business_id');

        $request->validate([
            'dealer_id'              => 'required|integer|exists:cooler_dealers,id',
            'cooler_id'              => 'required|integer|exists:cooler_assets,id',
            'retrieval_date'         => 'required|date',
            'reason'                 => 'required|in:' . implode(',', array_keys(CoolerRetrieval::$reasons)),
            'reason_notes'           => 'nullable|string',
            'authorized_staff_name'  => 'required|string|max:255',
            'authorized_staff_id_no' => 'nullable|string|max:50',
            'authorized_staff_tel'   => 'nullable|string|max:20',
        ]);

        // Ensure the customer belongs to this agent
        $this->findMyCustomer($request->dealer_id, $businessId);

        $agreement = CoolerAgreement::where('dealer_id', $request->dealer_id)
            ->where('cooler_id', $request->cooler_id)
            ->where('status', 'active')
            ->first();

        $retrieval = CoolerRetrieval::create([
            'business_id'            => $businessId,
            'agreement_id'           => $agreement?->id,
            'dealer_id'              => $request->dealer_id,
            'cooler_id'              => $request->cooler_id,
            'retrieval_date'         => $request->retrieval_date,
            'reason'                 => $request->reason,
            'reason_notes'           => $request->reason_notes,
            'authorized_staff_name'  => $request->authorized_staff_name,
            'authorized_staff_id_no' => $request->authorized_staff_id_no,
            'authorized_staff_tel'   => $request->authorized_staff_tel,
            'status'                 => 'initiated',
            'created_by'             => auth()->id(),
        ]);

        return redirect()->route('cooler.retrievals.execute', $retrieval->id)
            ->with('success', 'Retrieval initiated. Proceed with field execution.');
    }

    // ── Orders (with MPESA) ───────────────────────────────────────────────────

    public function createOrder(Request $request)
    {
        $agentId    = auth()->id();
        $businessId = $request->session()->get('user.business_id');

        $customers = CoolerDealer::forBusiness($businessId)->forAgent($agentId)->active()
            ->get(['id', 'outlet_name', 'name', 'phone', 'contact_id']);

        $selectedCustomerId = $request->get('customer_id');

        $products = DB::table('products as p')
            ->join('variations as v', 'p.id', '=', 'v.product_id')
            ->where('p.business_id', $businessId)
            ->where('p.is_inactive', 0)
            ->whereNotIn('p.type', ['modifier'])
            ->select('p.id as product_id', 'p.name as product_name', 'p.image as product_image',
                     'v.id as variation_id', 'v.name as variation_name', 'v.default_sell_price as price')
            ->orderBy('p.name')
            ->get();

        try {
            $mpesaEnabled = DB::table('mpesa_settings')
                ->where('business_id', $businessId)
                ->where('is_active', 1)
                ->exists();
        } catch (\Exception $e) {
            $mpesaEnabled = false;
        }

        return view('cooler.agent.order_create', compact('customers', 'products', 'mpesaEnabled', 'selectedCustomerId'));
    }

    public function storeOrder(Request $request)
    {
        $agentId    = auth()->id();
        $businessId = $request->session()->get('user.business_id');

        $request->validate([
            'customer_id'   => 'required|integer|exists:cooler_dealers,id',
            'items'         => 'required|array|min:1',
            'items.*.variation_id' => 'required|integer',
            'items.*.qty'          => 'required|numeric|min:0.01',
            'payment_method'=> 'required|in:mpesa,cash',
            'mpesa_phone'   => 'required_if:payment_method,mpesa|nullable|string',
            'notes'         => 'nullable|string',
        ]);

        // Ensure customer belongs to this agent
        $dealer = $this->findMyCustomer($request->customer_id, $businessId);

        DB::beginTransaction();
        try {
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

            // Use linked contact_id if available, else fallback to first business contact
            $contactId = $dealer->contact_id ?? DB::table('contacts')
                ->where('business_id', $businessId)
                ->value('id');

            $locationId = DB::table('business_locations')
                ->where('business_id', $businessId)
                ->value('id');

            $order = PosOrder::create([
                'business_id'    => $businessId,
                'location_id'    => $locationId,
                'contact_id'     => $contactId,
                'created_by'     => $agentId,
                'ref_no'         => 'AGT-' . strtoupper(Str::random(6)),
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

            if ($request->payment_method === 'mpesa' && $request->mpesa_phone) {
                $stkResult = $this->initiateMpesaPayment($order, $businessId, $request->mpesa_phone);
                return response()->json([
                    'success'              => true,
                    'order_id'             => $order->id,
                    'ref_no'               => $order->ref_no,
                    'total'                => $total,
                    'mpesa_initiated'      => $stkResult['success'],
                    'mpesa_transaction_id' => $stkResult['mpesa_transaction_id'] ?? null,
                    'message'              => $stkResult['success']
                        ? 'Order placed! MPESA prompt sent to ' . $request->mpesa_phone
                        : 'Order placed but MPESA prompt failed: ' . ($stkResult['message'] ?? ''),
                ]);
            }

            return response()->json([
                'success'  => true,
                'order_id' => $order->id,
                'ref_no'   => $order->ref_no,
                'total'    => $total,
                'message'  => 'Order placed successfully!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent storeOrder error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error placing order.']);
        }
    }

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

        if ($tx->isPending() && $tx->checkout_request_id) {
            $ageSeconds = now()->diffInSeconds($tx->created_at);
            if ($ageSeconds >= 8) {
                try {
                    $mpesaService = new MpesaService($tx->business_id);
                    $result = $mpesaService->querySTKPushStatus($tx->checkout_request_id);
                    if ($result['success'] && isset($result['result_code'])) {
                        $rc = (string) $result['result_code'];
                        if ($rc === '0') {
                            $receipt = $tx->mpesa_receipt_number ?? ('AGT-' . now()->format('YmdHis'));
                            $tx->markAsPaid($receipt, '0', 'Confirmed via query');
                            if ($orderId) {
                                PosOrder::where('id', $orderId)->update([
                                    'payment_status'       => 'paid',
                                    'mpesa_receipt'        => $receipt,
                                    'mpesa_transaction_id' => $tx->id,
                                    'status'               => 'processing',
                                ]);
                            }
                            $tx->refresh();
                        } elseif (!in_array($rc, ['500.001.1001', '500.001.1000', ''])) {
                            $tx->markAsFailed($rc, $result['result_desc'] ?? '');
                            $tx->refresh();
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Agent MPESA query failed', ['error' => $e->getMessage()]);
                }
            }
        }

        if ($tx->isPaid() && $orderId) {
            PosOrder::where('id', $orderId)
                ->where('payment_status', '!=', 'paid')
                ->update([
                    'payment_status'       => 'paid',
                    'mpesa_receipt'        => $tx->mpesa_receipt_number,
                    'mpesa_transaction_id' => $tx->id,
                    'status'               => 'processing',
                ]);
        }

        return response()->json([
            'success'        => true,
            'status'         => $tx->status,
            'is_paid'        => $tx->isPaid(),
            'receipt_number' => $tx->mpesa_receipt_number,
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function findMyCustomer(int $dealerId, $businessId): CoolerDealer
    {
        return CoolerDealer::forBusiness($businessId)
            ->forAgent(auth()->id())
            ->findOrFail($dealerId);
    }

    private function initiateMpesaPayment(PosOrder $order, $businessId, $phone): array
    {
        try {
            $mpesaService = new MpesaService($businessId);

            $tx = MpesaTransaction::create([
                'business_id'  => $businessId,
                'amount'       => $order->total_amount,
                'phone_number' => $phone,
                'reference'    => $order->ref_no,
                'description'  => 'Agent order ' . $order->ref_no,
                'status'       => MpesaTransaction::STATUS_PENDING,
                'initiated_by' => auth()->id(),
            ]);

            $result = $mpesaService->initiateSTKPush(
                $phone,
                $order->total_amount,
                $order->ref_no,
                'Order ' . $order->ref_no
            );

            if ($result['success']) {
                $tx->update([
                    'checkout_request_id' => $result['checkout_request_id'] ?? null,
                    'merchant_request_id' => $result['merchant_request_id'] ?? null,
                ]);
                $order->update(['mpesa_transaction_id' => $tx->id]);
            }

            return [
                'success'              => $result['success'],
                'mpesa_transaction_id' => $tx->id,
                'message'              => $result['message'] ?? '',
            ];
        } catch (\Exception $e) {
            Log::error('Agent MPESA initiate error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
