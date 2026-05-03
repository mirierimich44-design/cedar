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

        // Admin check — UltimatePOS stores role as "Admin#<business_id>"
        $is_admin = $user->hasRole('Admin#' . $business_id)
                 || $user->can('superadmin')
                 || $user->can('access_all_locations');

        // 1. Prefer the user's open cash register location
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

        // 2. Fall back to the user's first permitted location
        if (!$default_location) {
            $permitted = $user->permitted_locations($business_id);
            if ($permitted === 'all') {
                $first = \App\BusinessLocation::where('business_id', $business_id)->first();
            } else {
                $first = \App\BusinessLocation::whereIn('id', $permitted)->first();
            }
            if ($first) {
                $default_location = ['id' => $first->id, 'name' => $first->name];
            }
        }

        // All locations (for admin stock transfer etc.)
        $locations = \App\BusinessLocation::where('business_id', $business_id)
            ->get(['id', 'name']);

        return response()->json([
            'user'             => [
                'id'       => $user->id,
                'name'     => trim($user->first_name . ' ' . $user->last_name),
                'username' => $user->username,
                'is_admin' => $is_admin,
            ],
            'default_location' => $default_location,
            'locations'        => $locations,
        ]);
    }

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
        $productUtil     = app(\App\Utils\ProductUtil::class);

        $sell_lines   = [];
        $total_before = 0;

        foreach ($products as $p) {
            $qty        = (float) ($p['quantity'] ?? 1);
            $unit_price = (float) ($p['unit_price'] ?? 0);
            $total_before += $qty * $unit_price;

            $variation = \App\Variation::find($p['variation_id']);

            $sell_lines[] = [
                'product_id'           => $variation ? $variation->product_id : null,
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
            'location_id'         => $location_id,
            'contact_id'          => $contact->id,
            'transaction_date'    => $request->input('transaction_date', now()->toDateString()),
            'status'              => 'final',
            'is_quotation'        => 0,
            'discount_type'       => 'fixed',
            'discount_amount'     => 0,
            'tax_rate_id'         => null,
            'sale_note'           => $request->input('sale_note', ''),
            'final_total'         => $total_before,
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

            // Deduct stock for each sold item
            foreach ($sell_lines as $line) {
                if (!empty($line['product_id'])) {
                    $productUtil->decreaseProductQuantity(
                        $line['product_id'],
                        $line['variation_id'],
                        $location_id,
                        $line['quantity'],
                        0
                    );
                }
            }

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
        // Return standard payment types
        // (BusinessPaymentMethod model does not exist in this codebase)
        $types = collect([
            ['id' => 'cash',  'label' => 'Cash'],
            ['id' => 'card',  'label' => 'Card'],
            ['id' => 'mpesa', 'label' => 'M-Pesa'],
        ]);

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
        $user        = $request->user();
        $business_id = $user->business_id;
        $user_id     = $user->id;

        $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'category_id' => 'required|integer',
            'location_id' => 'required|integer',
        ]);

        // Merge validated fields into request format expected by TransactionUtil
        $request->merge([
            'final_total'         => $request->input('amount'),
            'expense_category_id' => $request->input('category_id'),
            'transaction_date'    => $request->input('date', now()->toDateString()),
            'payment'             => [[
                'method' => $request->input('payment_method', 'cash'),
                'amount' => $request->input('amount'),
                'note'   => $request->input('note', ''),
            ]],
        ]);

        DB::beginTransaction();
        try {
            $transactionUtil = app(\App\Utils\TransactionUtil::class);
            $expense = $transactionUtil->createExpense($request, $business_id, $user_id, false);

            DB::commit();
            return response()->json(['success' => true, 'msg' => 'Expense recorded.', 'ref_no' => $expense->ref_no]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Mobile expense error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }

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

    // ─── ADMIN ENDPOINTS ─────────────────────────────────────────────────────

    /**
     * GET /api/mobile/admin/dashboard
     * Cross-branch sales summary for today + yesterday comparison
     */
    public function adminDashboard(Request $request)
    {
        $business_id = $request->user()->business_id;
        $today       = now()->toDateString();
        $yesterday   = now()->subDay()->toDateString();

        // Per-branch sales today
        $branches = DB::table('transactions as t')
            ->join('business_locations as bl', 'bl.id', '=', 't.location_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->whereDate('t.transaction_date', $today)
            ->selectRaw('bl.id, bl.name, COUNT(*) as sales_count, COALESCE(SUM(t.final_total),0) as revenue')
            ->groupBy('bl.id', 'bl.name')
            ->orderByRaw('SUM(t.final_total) DESC')
            ->get();

        // Payment method breakdown today (all branches)
        $payments = DB::table('transaction_payments as tp')
            ->join('transactions as t', 't.id', '=', 'tp.transaction_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->whereDate('t.transaction_date', $today)
            ->selectRaw('tp.method, COALESCE(SUM(tp.amount),0) as total')
            ->groupBy('tp.method')
            ->orderByRaw('SUM(tp.amount) DESC')
            ->get();

        // Yesterday totals for comparison
        $yesterday_total = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereDate('transaction_date', $yesterday)
            ->sum('final_total');

        $today_total = $branches->sum('revenue');

        return response()->json([
            'today'           => $today,
            'today_total'     => (float) $today_total,
            'yesterday_total' => (float) $yesterday_total,
            'change_pct'      => $yesterday_total > 0
                ? round((($today_total - $yesterday_total) / $yesterday_total) * 100, 1)
                : null,
            'branches'        => $branches,
            'payment_methods' => $payments,
        ]);
    }

    /**
     * GET /api/mobile/admin/stock-alerts?location_id=
     */
    public function adminStockAlerts(Request $request)
    {
        $business_id = $request->user()->business_id;
        $location_id = (int) $request->get('location_id');

        $query = DB::table('variation_location_details as vld')
            ->join('products as p', 'p.id', '=', 'vld.product_id')
            ->join('variations as v', 'v.id', '=', 'vld.variation_id')
            ->join('business_locations as bl', 'bl.id', '=', 'vld.location_id')
            ->where('p.business_id', $business_id)
            ->where('p.enable_stock', 1)
            ->where('p.is_inactive', 0)
            ->where(function ($q) {
                $q->whereRaw('vld.qty_available <= p.alert_quantity')
                  ->orWhere('vld.qty_available', '<=', 0);
            });

        if ($location_id) $query->where('vld.location_id', $location_id);

        $alerts = $query->selectRaw('
                p.id as product_id, p.name, v.sub_sku as sku,
                vld.qty_available, p.alert_quantity,
                bl.id as location_id, bl.name as location
            ')
            ->orderBy('vld.qty_available')
            ->orderBy('p.name')
            ->get();

        return response()->json(['alerts' => $alerts]);
    }

    /**
     * GET /api/mobile/admin/staff-activity
     */
    public function adminStaffActivity(Request $request)
    {
        $business_id = $request->user()->business_id;
        $today       = now()->toDateString();

        $staff = DB::table('users as u')
            ->leftJoin('transactions as t', function ($j) use ($today) {
                $j->on('t.created_by', '=', 'u.id')
                  ->where('t.type', 'sell')
                  ->where('t.status', 'final')
                  ->whereDate('t.transaction_date', $today);
            })
            ->leftJoin('cash_registers as cr', function ($j) {
                $j->on('cr.user_id', '=', 'u.id')
                  ->where('cr.status', 'open');
            })
            ->leftJoin('business_locations as bl', 'bl.id', '=', 'cr.location_id')
            ->where('u.business_id', $business_id)
            ->where('u.status', 'active')
            ->selectRaw('
                u.id, CONCAT(u.first_name, " ", u.last_name) as name, u.username,
                COUNT(t.id) as sales_count,
                COALESCE(SUM(t.final_total), 0) as sales_total,
                cr.status as register_status,
                bl.name as location,
                cr.opening_amount,
                cr.created_at as till_opened_at
            ')
            ->groupBy('u.id', 'u.first_name', 'u.last_name', 'u.username',
                      'cr.status', 'bl.name', 'cr.opening_amount', 'cr.created_at')
            ->orderByRaw('SUM(t.final_total) DESC')
            ->get();

        return response()->json(['staff' => $staff, 'date' => $today]);
    }

    /**
     * GET /api/mobile/admin/sales-report?location_id=&period=today
     */
    public function adminSalesReport(Request $request)
    {
        $business_id = $request->user()->business_id;
        $location_id = (int) $request->get('location_id');
        $period      = $request->get('period', 'today');

        $dateFrom = match($period) {
            'week'  => now()->startOfWeek()->toDateString(),
            'month' => now()->startOfMonth()->toDateString(),
            default => now()->toDateString(),
        };
        $dateTo = now()->toDateString();

        $baseQuery = fn() => DB::table('transaction_sell_lines as tsl')
            ->join('transactions as t', 't.id', '=', 'tsl.transaction_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->when($location_id, fn($q) => $q->where('t.location_id', $location_id))
            ->whereBetween(DB::raw('DATE(t.transaction_date)'), [$dateFrom, $dateTo]);

        // Top 10 products
        $top_products = $baseQuery()
            ->join('products as p', 'p.id', '=', 'tsl.product_id')
            ->selectRaw('p.name, SUM(tsl.quantity) as qty_sold, SUM(tsl.quantity * tsl.unit_price_inc_tax) as revenue')
            ->groupBy('p.id', 'p.name')
            ->orderByRaw('SUM(tsl.quantity * tsl.unit_price_inc_tax) DESC')
            ->limit(10)
            ->get();

        // Hourly sales (today only)
        $hourly = DB::table('transactions as t')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->when($location_id, fn($q) => $q->where('t.location_id', $location_id))
            ->whereDate('t.transaction_date', now()->toDateString())
            ->selectRaw('HOUR(t.created_at) as hour, COUNT(*) as count, COALESCE(SUM(t.final_total),0) as total')
            ->groupBy(DB::raw('HOUR(t.created_at)'))
            ->orderBy('hour')
            ->get();

        // Slow movers — products with no sales in last 7 days at this business
        $slow = DB::table('products as p')
            ->join('variations as v', 'v.product_id', '=', 'p.id')
            ->join('variation_location_details as vld', 'vld.variation_id', '=', 'v.id')
            ->leftJoin(DB::raw('(SELECT tsl.variation_id, MAX(t.transaction_date) as last_sold
                FROM transaction_sell_lines tsl
                JOIN transactions t ON t.id = tsl.transaction_id
                WHERE t.type = "sell" AND t.status = "final"
                GROUP BY tsl.variation_id) as ls'), 'ls.variation_id', '=', 'v.id')
            ->where('p.business_id', $business_id)
            ->where('p.is_inactive', 0)
            ->where('p.enable_stock', 1)
            ->when($location_id, fn($q) => $q->where('vld.location_id', $location_id))
            ->where('vld.qty_available', '>', 0)
            ->where(function ($q) {
                $q->whereNull('ls.last_sold')
                  ->orWhereRaw('ls.last_sold < DATE_SUB(NOW(), INTERVAL 7 DAY)');
            })
            ->selectRaw('p.name, v.sub_sku as sku, vld.qty_available, ls.last_sold')
            ->orderBy('vld.qty_available', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'period'       => $period,
            'date_from'    => $dateFrom,
            'date_to'      => $dateTo,
            'top_products' => $top_products,
            'hourly'       => $hourly,
            'slow_movers'  => $slow,
        ]);
    }

    /**
     * GET /api/mobile/admin/expenses-summary?period=today
     */
    public function adminExpensesSummary(Request $request)
    {
        $business_id = $request->user()->business_id;
        $period      = $request->get('period', 'today');

        $dateFrom = match($period) {
            'week'  => now()->startOfWeek()->toDateString(),
            'month' => now()->startOfMonth()->toDateString(),
            default => now()->toDateString(),
        };

        // Per branch totals
        $by_branch = DB::table('transactions as t')
            ->join('business_locations as bl', 'bl.id', '=', 't.location_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'expense')
            ->whereBetween(DB::raw('DATE(t.transaction_date)'), [$dateFrom, now()->toDateString()])
            ->selectRaw('bl.id, bl.name as location, COUNT(*) as count, COALESCE(SUM(t.final_total),0) as total')
            ->groupBy('bl.id', 'bl.name')
            ->orderByRaw('SUM(t.final_total) DESC')
            ->get();

        // Per category
        $by_category = DB::table('transactions as t')
            ->join('expense_categories as ec', 'ec.id', '=', 't.expense_category_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'expense')
            ->whereBetween(DB::raw('DATE(t.transaction_date)'), [$dateFrom, now()->toDateString()])
            ->selectRaw('ec.name as category, COUNT(*) as count, COALESCE(SUM(t.final_total),0) as total')
            ->groupBy('ec.id', 'ec.name')
            ->orderByRaw('SUM(t.final_total) DESC')
            ->get();

        return response()->json([
            'period'      => $period,
            'grand_total' => (float) $by_branch->sum('total'),
            'by_branch'   => $by_branch,
            'by_category' => $by_category,
        ]);
    }

    /**
     * POST /api/mobile/admin/stock-transfer
     */
    public function adminStockTransfer(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;

        $request->validate([
            'from_location_id' => 'required|integer',
            'to_location_id'   => 'required|integer|different:from_location_id',
            'items'            => 'required|array|min:1',
            'items.*.variation_id' => 'required|integer',
            'items.*.product_id'   => 'required|integer',
            'items.*.quantity'     => 'required|numeric|min:0.01',
        ]);

        $from = (int) $request->input('from_location_id');
        $to   = (int) $request->input('to_location_id');
        $items = $request->input('items');

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $vid = (int) $item['variation_id'];
                $pid = (int) $item['product_id'];
                $qty = (float) $item['quantity'];

                // Check available stock at source
                $src = DB::table('variation_location_details')
                    ->where('variation_id', $vid)
                    ->where('location_id', $from)
                    ->first();

                if (!$src || $src->qty_available < $qty) {
                    DB::rollBack();
                    $product = \App\Product::find($pid);
                    return response()->json([
                        'success' => false,
                        'msg'     => "Insufficient stock for {$product->name} at source location.",
                    ], 422);
                }

                // Decrement source
                DB::table('variation_location_details')
                    ->where('variation_id', $vid)->where('location_id', $from)
                    ->decrement('qty_available', $qty);

                // Increment destination (create row if not exists)
                $dest = DB::table('variation_location_details')
                    ->where('variation_id', $vid)->where('location_id', $to)->first();

                if ($dest) {
                    DB::table('variation_location_details')
                        ->where('variation_id', $vid)->where('location_id', $to)
                        ->increment('qty_available', $qty);
                } else {
                    $variation = \App\Variation::find($vid);
                    DB::table('variation_location_details')->insert([
                        'product_id'          => $pid,
                        'variation_id'        => $vid,
                        'product_variation_id'=> $variation->product_variation_id,
                        'location_id'         => $to,
                        'qty_available'       => $qty,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'msg' => 'Stock transferred successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Mobile stock transfer error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}
