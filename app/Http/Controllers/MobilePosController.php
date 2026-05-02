<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobilePosController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return response()->json(['message' => 'Invalid username or password.'], 401);
        }

        $user = Auth::user();

        if (!$user->business || !$user->business->is_active) {
            Auth::logout();
            return response()->json(['message' => 'Business account is inactive.'], 403);
        }

        if ($user->status !== 'active') {
            Auth::logout();
            return response()->json(['message' => 'Your account is inactive.'], 403);
        }

        if (!$user->allow_login) {
            Auth::logout();
            return response()->json(['message' => 'Login not allowed for this account.'], 403);
        }

        $user->tokens()->where('name', 'mobile-pos')->delete();
        $token = $user->createToken('mobile-pos')->accessToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'       => $user->id,
                'name'     => trim($user->first_name . ' ' . $user->last_name),
                'username' => $user->username,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Logged out.']);
    }

    public function posDetails(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;

        $register = \App\CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        $default_location = null;

        if ($register && $register->location_id) {
            $loc = \App\BusinessLocation::find($register->location_id);
            if ($loc) {
                $default_location = ['id' => $loc->id, 'name' => $loc->name];
            }
        }

        if (!$default_location) {
            $first = \App\BusinessLocation::where('business_id', $business_id)->first();
            if ($first) {
                $default_location = ['id' => $first->id, 'name' => $first->name];
            }
        }

        return response()->json([
            'user'             => [
                'id'       => $user->id,
                'name'     => trim($user->first_name . ' ' . $user->last_name),
                'username' => $user->username,
            ],
            'default_location' => $default_location,
        ]);
    }

    /**
     * GET /api/mobile/products?location_id=&term=
     *
     * Mirrors the web POS product search exactly:
     *   - joins product_locations to respect per-location product assignments
     *   - uses is_inactive=0 (not status='active')
     *   - no qty gate by default (matches web POS default check_qty=false)
     */
    public function products(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;
        $location_id = (int) $request->get('location_id');
        $term        = trim($request->get('term', ''));

        $query = \App\Variation::join('products as p', 'variations.product_id', '=', 'p.id')
            ->join('units as u', 'p.unit_id', '=', 'u.id')
            ->leftJoin('variation_location_details as vld', function ($join) use ($location_id) {
                $join->on('variations.id', '=', 'vld.variation_id');
                if ($location_id) {
                    $join->where('vld.location_id', $location_id);
                }
            })
            ->where('p.business_id', $business_id)
            ->where('p.is_inactive', 0)
            ->where('p.not_for_selling', 0)
            ->where('p.type', '!=', 'modifier')
            // Only show in-stock items (or products that don't track stock)
            ->where(function ($q) {
                $q->where('p.enable_stock', 0)
                  ->orWhereRaw('COALESCE(vld.qty_available, 0) > 0');
            });

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('p.name', 'like', "%{$term}%")
                  ->orWhere('variations.sub_sku', 'like', "%{$term}%")
                  ->orWhere('p.sku', 'like', "%{$term}%");
            });
        }

        $products = $query->select(
                'p.id as product_id',
                'variations.id as variation_id',
                'p.name',
                'variations.name as variation',
                'variations.sub_sku',
                'p.enable_stock',
                DB::raw('COALESCE(vld.qty_available, 0) as qty_available'),
                DB::raw('variations.default_sell_price as selling_price'),
                'u.short_name as unit'
            )
            ->distinct()
            ->orderBy('p.name')
            ->limit(150)
            ->get();

        return response()->json(['products' => $products]);
    }

    public function createSale(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;
        $user_id     = $user->id;
        $location_id = (int) $request->input('location_id');
        $products    = $request->input('products', []);
        $payments    = $request->input('payment', []);

        if (empty($products)) {
            return response()->json(['success' => false, 'msg' => 'No products provided.'], 422);
        }

        // Get walk-in customer for this business
        $contact = \App\Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->where('name', 'Walk-In Customer')
            ->first();

        if (!$contact) {
            $contact = \App\Contact::where('business_id', $business_id)
                ->where('type', 'customer')
                ->first();
        }

        if (!$contact) {
            return response()->json(['success' => false, 'msg' => 'No customer found. Please add a walk-in customer in the web app.'], 422);
        }

        $transactionUtil = app(\App\Utils\TransactionUtil::class);

        // Build sell lines and compute totals
        $sell_lines   = [];
        $total_before = 0;

        foreach ($products as $p) {
            $qty        = (float) ($p['quantity'] ?? 1);
            $unit_price = (float) ($p['unit_price'] ?? 0);
            $line_total = $qty * $unit_price;
            $total_before += $line_total;

            $sell_lines[] = [
                'variation_id'         => $p['variation_id'],
                'quantity'             => $qty,
                'unit_price'           => $unit_price,
                'unit_price_inc_tax'   => $unit_price,
                'item_tax'             => 0,
                'tax_id'               => null,
                'line_discount_type'   => 'fixed',
                'line_discount_amount' => 0,
            ];
        }

        $invoice_total = [
            'total_before_tax' => $total_before,
            'tax'              => 0,
            'final_total'      => $total_before,
        ];

        $input = [
            'location_id'      => $location_id,
            'contact_id'       => $contact->id,
            'transaction_date' => $request->input('transaction_date', now()->toDateString()),
            'status'           => 'final',
            'is_quotation'     => 0,
            'discount_type'    => 'fixed',
            'discount_amount'  => 0,
            'tax_rate_id'      => null,
            'sale_note'        => $request->input('sale_note', ''),
            'final_total'      => $total_before,
            'is_created_from_api' => 1,
        ];

        DB::beginTransaction();
        try {
            $transaction = $transactionUtil->createSellTransaction(
                $business_id, $input, $invoice_total, $user_id, false
            );

            $transactionUtil->createOrUpdateSellLines(
                $transaction, $sell_lines, $location_id, false, null, [], false
            );

            if (!empty($payments)) {
                $transactionUtil->createOrUpdatePaymentLines(
                    $transaction, $payments, $business_id, $user_id, false
                );
            }

            $transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            DB::commit();

            return response()->json([
                'success'    => true,
                'msg'        => 'Sale recorded successfully.',
                'invoice_no' => $transaction->invoice_no,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Mobile sale error: ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function paymentTypes(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;

        $types = \App\BusinessPaymentMethod::where('business_id', $business_id)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'account_id'])
            ->map(fn($m) => [
                'id'    => $m->name,
                'label' => ucfirst(str_replace('_', ' ', $m->name)),
            ]);

        if ($types->isEmpty()) {
            $types = collect([
                ['id' => 'cash',  'label' => 'Cash'],
                ['id' => 'card',  'label' => 'Card'],
                ['id' => 'mpesa', 'label' => 'M-Pesa'],
            ]);
        }

        return response()->json(['payment_types' => $types]);
    }

    public function expenseCategories(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;

        $categories = \App\ExpenseCategory::where('business_id', $business_id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['categories' => $categories]);
    }

    public function createExpense(Request $request)
    {
        $user = $request->user();
        Auth::guard('web')->setUser($user);
        $request->session()->put('user.business_id', $user->business_id);
        $request->session()->put('user.id', $user->id);

        $controller = app(\App\Http\Controllers\ExpenseController::class);
        return $controller->store($request);
    }

    /**
     * GET /api/mobile/till-summary
     */
    public function tillSummary(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;
        $location_id = (int) $request->get('location_id');
        $today       = now()->toDateString();

        $salesInfo = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->when($location_id, fn($q) => $q->where('location_id', $location_id))
            ->whereDate('transaction_date', $today)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(final_total), 0) as total')
            ->first();

        $payBreakdown = DB::table('transaction_payments as tp')
            ->join('transactions as t', 't.id', '=', 'tp.transaction_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->when($location_id, fn($q) => $q->where('t.location_id', $location_id))
            ->whereDate('t.transaction_date', $today)
            ->selectRaw('tp.method, COALESCE(SUM(tp.amount), 0) as total')
            ->groupBy('tp.method')
            ->orderByRaw('SUM(tp.amount) DESC')
            ->get();

        $register = \App\CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->when($location_id, fn($q) => $q->where('location_id', $location_id))
            ->first();

        return response()->json([
            'date'              => $today,
            'sales_count'       => (int) $salesInfo->count,
            'sales_total'       => (float) $salesInfo->total,
            'payment_breakdown' => $payBreakdown,
            'register_open'     => (bool) $register,
            'register_id'       => $register?->id,
        ]);
    }

    /**
     * POST /api/mobile/close-till
     */
    public function closeTill(Request $request)
    {
        $user = $request->user();

        $register = \App\CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if (!$register) {
            return response()->json(['message' => 'No open till found.'], 404);
        }

        $register->status         = 'close';
        $register->closing_amount = $request->get('closing_amount', 0);
        $register->closed_at      = now();
        $register->save();

        return response()->json(['message' => 'Till closed successfully.']);
    }

    /**
     * GET /api/mobile/recent-sales?location_id=&limit=
     */
    public function recentSales(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;
        $location_id = (int) $request->get('location_id');
        $limit       = min((int) $request->get('limit', 20), 50);

        $sales = DB::table('transactions as t')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->when($location_id, fn($q) => $q->where('t.location_id', $location_id))
            ->orderBy('t.created_at', 'desc')
            ->limit($limit)
            ->select([
                't.id',
                't.invoice_no',
                't.final_total',
                't.transaction_date',
                't.created_at',
            ])
            ->get()
            ->map(function ($sale) {
                $payments = DB::table('transaction_payments')
                    ->where('transaction_id', $sale->id)
                    ->selectRaw('method, SUM(amount) as amount')
                    ->groupBy('method')
                    ->get();

                $sale->payments = $payments;
                return $sale;
            });

        return response()->json(['sales' => $sales]);
    }
}
