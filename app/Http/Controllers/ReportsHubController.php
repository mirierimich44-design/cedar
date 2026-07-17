<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\LostSale;
use App\Transaction;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

/**
 * Pharmacy-friendly reports hub, day close, stock intelligence.
 * Complements ReportController (does not replace it).
 */
class ReportsHubController extends Controller
{
    protected $transactionUtil;

    protected $productUtil;

    protected $moduleUtil;

    protected $businessUtil;

    public function __construct(
        TransactionUtil $transactionUtil,
        ProductUtil $productUtil,
        ModuleUtil $moduleUtil,
        BusinessUtil $businessUtil
    ) {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
    }

    protected function canViewReports(): bool
    {
        $u = auth()->user();

        return $u->can('purchase_n_sell_report.view')
            || $u->can('contacts_report.view')
            || $u->can('stock_report.view')
            || $u->can('tax_report.view')
            || $u->can('trending_product_report.view')
            || $u->can('sales_representative.view')
            || $u->can('register_report.view')
            || $u->can('expense_report.view')
            || $u->can('profit_loss_report.view');
    }

    /**
     * Package A — Reports hub with plain-language tiles.
     */
    public function index(Request $request)
    {
        if (! $this->canViewReports()) {
            abort(403, 'Unauthorized action.');
        }

        $groups = $this->hubGroups();

        return view('report.hub.index', compact('groups'));
    }

    protected function hubGroups(): array
    {
        $can = function ($perm) {
            return auth()->user()->can($perm);
        };

        $link = function ($url, $title, $help, $icon = 'fa-chart-bar') {
            return compact('url', 'title', 'help', 'icon');
        };

        $groups = [];

        // Today / closing
        $daily = [];
        if ($can('profit_loss_report.view')) {
            $daily[] = $link(route('reports.day_close'), 'Close the day', 'Cash, M-Pesa, sales & top products for one date.', 'fa-calendar-check');
            $daily[] = $link(route('reports.daily_summary'), 'Daily summary', 'Full day snapshot with KPIs.', 'fa-sun');
            $daily[] = $link(route('reports.daily_reconciliation'), 'Daily reconciliation', 'Match sales to expected cash.', 'fa-balance-scale');
            $daily[] = $link(route('reports.lost_sales'), 'Lost sales', 'Products customers wanted but you could not sell.', 'fa-times-circle');
            $daily[] = $link(action([ReportController::class, 'getProfitLoss']), 'Profit & loss', 'Are we making money this period?', 'fa-chart-line');
        }
        if ($can('register_report.view')) {
            $daily[] = $link(action([ReportController::class, 'getRegisterReport']), 'Cash register', 'Who opened/closed the till.', 'fa-cash-register');
        }
        if (! empty($daily)) {
            $groups[] = ['key' => 'today', 'title' => 'Today / closing', 'color' => '#059669', 'items' => $daily];
        }

        // Sales & cash
        $sales = [];
        if ($can('purchase_n_sell_report.view')) {
            $sales[] = $link(action([ReportController::class, 'getproductSellReport']), 'Product sales', 'What sold, quantities & values.', 'fa-shopping-cart');
            $sales[] = $link(action([ReportController::class, 'sellPaymentReport']), 'Payments received', 'How customers paid (cash, M-Pesa…).', 'fa-money-bill-wave');
            $sales[] = $link(action([ReportController::class, 'getPurchaseSell']), 'Buying vs selling', 'Purchases compared to sales.', 'fa-exchange-alt');
            $sales[] = $link(action([ReportController::class, 'getDailyProductProfitReport']), 'Daily product profit', 'Margin by product for a day.', 'fa-coins');
        }
        if ($can('sell.view') || $can('direct_sell.access')) {
            $sales[] = $link(action([ReportController::class, 'getCustomerCreditReport']), 'Customer credit', 'Who still owes money (with ageing).', 'fa-credit-card');
        }
        if (auth()->check()) {
            $sales[] = $link(action([ReportController::class, 'getOrdersReport']), 'Shop orders', 'Stock orders staff created from POS.', 'fa-clipboard-list');
        }
        if (! empty($sales)) {
            $groups[] = ['key' => 'sales', 'title' => 'Sales & cash', 'color' => '#2563eb', 'items' => $sales];
        }

        // Stock & expiry
        $stock = [];
        if ($can('stock_report.view')) {
            $stock[] = $link(action([ReportController::class, 'getStockReport']), 'Stock on hand', 'Current quantities by product.', 'fa-boxes');
            $stock[] = $link(action([ReportController::class, 'getStockExpiryReport']), 'Expiring medicines', 'Batches nearing expiry.', 'fa-hourglass-half');
            $stock[] = $link(route('reports.expiry_smart'), 'Expiry (30 / 60 / 90 days)', 'Value at risk by age band.', 'fa-calendar-times');
            $stock[] = $link(action([ReportController::class, 'getLowStockVelocityReport']), 'Running out soon', 'Low stock based on how fast it sells.', 'fa-battery-quarter');
            $stock[] = $link(action([ReportController::class, 'getDeadStockReport']), 'Not selling', 'Products with little/no movement.', 'fa-snowflake');
            $stock[] = $link(action([ReportController::class, 'getStockAdjustmentReport']), 'Stock count fixes', 'History of stock adjustments.', 'fa-sliders-h');
            $stock[] = $link(route('reports.ledger_gap'), 'Stock vs purchase ledger', 'System stock without free purchase qty (mismatch risk).', 'fa-unlink');
            $stock[] = $link(route('reports.reorder_list'), 'Reorder list', 'What to order + open shop order.', 'fa-truck-loading');
            $stock[] = $link(action([ReportController::class, 'getFastMoversReport']), 'Fast movers', 'Top selling products.', 'fa-bolt');
        }
        if (! empty($stock)) {
            $groups[] = ['key' => 'stock', 'title' => 'Stock & expiry', 'color' => '#d97706', 'items' => $stock];
        }

        // Buying
        $buy = [];
        if ($can('purchase_n_sell_report.view')) {
            $buy[] = $link(action([ReportController::class, 'getproductPurchaseReport']), 'Product purchases', 'What you bought and paid.', 'fa-shopping-bag');
            $buy[] = $link(action([ReportController::class, 'purchasePaymentReport']), 'Payments to suppliers', 'Money paid out for purchases.', 'fa-hand-holding-usd');
            $buy[] = $link(action([ReportController::class, 'getPurchasePriceVarianceReport']), 'Purchase price changes', 'When buy prices moved (PPV).', 'fa-chart-area');
        }
        if ($can('supplier_report.view') || $can('contacts_report.view')) {
            $buy[] = $link(action([ReportController::class, 'getSupplierReport']), 'Supplier report', 'Balances and activity by supplier.', 'fa-truck');
        }
        if (! empty($buy)) {
            $groups[] = ['key' => 'buy', 'title' => 'Buying', 'color' => '#7c3aed', 'items' => $buy];
        }

        // People
        $people = [];
        if ($can('customer_report.view') || $can('contacts_report.view')) {
            $people[] = $link(action([ReportController::class, 'getCustomerReport']), 'Customers', 'Sales and balances by customer.', 'fa-users');
        }
        if ($can('sales_representative.view')) {
            $people[] = $link(action([ReportController::class, 'getSalesRepresentativeReport']), 'Sales staff', 'Performance by salesperson.', 'fa-user-tie');
            $people[] = $link(action([ReportController::class, 'getSellerDailyReport']), 'Seller daily', 'What each seller sold today.', 'fa-user-check');
        }
        if ($can('followups.view')) {
            $people[] = $link(action([ReportController::class, 'getFollowupReport']), 'Follow-ups', 'Customer follow-up list.', 'fa-phone');
        }
        if (! empty($people)) {
            $groups[] = ['key' => 'people', 'title' => 'People', 'color' => '#0891b2', 'items' => $people];
        }

        // Finance & control (advanced pack)
        $finance = [];
        if ($can('profit_loss_report.view') || $can('account.access') || $can('purchase_n_sell_report.view')) {
            $finance[] = $link(route('reports.month_end_pack'), 'Month-end pack', 'One printable pack: P&L, stock, credit, top products.', 'fa-file-alt');
            $finance[] = $link(route('reports.financial_statements'), 'Financial statements', 'P&L + simplified balance sheet + cash movement.', 'fa-file-invoice-dollar');
            $finance[] = $link(route('reports.bank_mpesa_recon'), 'Bank / M-Pesa recon', 'Daily totals, auto-match, unmatched, statement paste.', 'fa-university');
            $finance[] = $link(route('reports.multi_period'), 'Multi-period dashboard', 'Sales, gross & net over recent months.', 'fa-chart-bar');
            $finance[] = $link(route('reports.discount_abuse'), 'Discount abuse', 'Unusual invoice/line discounts by cashier.', 'fa-percentage');
            $finance[] = $link(route('reports.supplier_payables'), 'Supplier payables', 'Who we owe — 30/60/90 ageing.', 'fa-file-invoice');
            $finance[] = $link(route('reports.weekly_ritual'), 'Owner weekly ritual', 'Daily / weekly / monthly checklist.', 'fa-list-check');
            $finance[] = $link(route('reports.data_quality'), 'Data quality', 'Ledger gaps, expiry, negative stock, M-Pesa links.', 'fa-heartbeat');
            $finance[] = $link(route('reports.month_end_notify'), 'Send month-end SMS/WA', 'Push summary to owner phone.', 'fa-paper-plane');
            $finance[] = $link(route('reports.deploy_checklist'), 'Deploy checklist', 'What to upload & smoke-test.', 'fa-rocket');
        }
        if (auth()->user()->can('user.view') || auth()->user()->can('business_settings.access')) {
            $finance[] = $link(route('reports.roles_guide'), 'Roles & discount caps', 'Who sees finance + max discount %.', 'fa-user-shield');
        }
        if ($can('stock_report.view')) {
            $finance[] = $link(route('reports.fefo_compliance'), 'FEFO / batch log', 'Sales that skipped older-expiry batches.', 'fa-pills');
            $finance[] = $link(route('reports.inventory_valuation'), 'Inventory valuation', 'Formal stock value at cost & sell price.', 'fa-balance-scale');
        }
        if (auth()->user()->can('sell.view') || auth()->user()->can('account.access') || auth()->user()->can('business_settings.access') || auth()->user()->can('user.view')) {
            $finance[] = $link(route('reports.audit_export'), 'Audit log export', 'Who changed what — filter & CSV export.', 'fa-user-secret');
        }
        if (! empty($finance)) {
            $groups[] = ['key' => 'finance', 'title' => 'Finance & control', 'color' => '#be185d', 'items' => $finance];
        }

        // Compliance / other
        $other = [];
        if ($can('tax_report.view')) {
            $other[] = $link(action([ReportController::class, 'getTaxReport']), 'Tax report', 'Tax collected / paid.', 'fa-percent');
        }
        if ($can('expense_report.view')) {
            $other[] = $link(action([ReportController::class, 'getExpenseReport']), 'Expenses', 'Money spent on expenses.', 'fa-receipt');
        }
        if (! empty($other)) {
            $groups[] = ['key' => 'other', 'title' => 'Tax & expenses', 'color' => '#64748b', 'items' => $other];
        }

        return $groups;
    }

    /**
     * Package B — Day close pack.
     */
    public function dayClose(Request $request)
    {
        if (! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $location_id = $request->get('location_id') ?: null;
        $error = null;

        try {
            $date = Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable $e) {
            $date = Carbon::today()->format('Y-m-d');
        }

        try {
            $business_locations = BusinessLocation::forDropdown($business_id, true);
        } catch (\Throwable $e) {
            \Log::warning('Day close locations: '.$e->getMessage());
            $business_locations = collect(['' => __('report.all_locations')]);
        }

        try {
            $data = $this->buildDayCloseData($business_id, $date, $location_id);
        } catch (\Throwable $e) {
            \Log::error('Day close report failed: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            $error = $e->getMessage();
            $data = $this->emptyDayCloseData();
        }

        // Render inside try so Blade errors also surface as a soft failure (not raw 500)
        try {
            return view('report.hub.day_close', compact(
                'business_locations',
                'date',
                'location_id',
                'data',
                'error'
            ));
        } catch (\Throwable $e) {
            \Log::error('Day close view failed: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Absolute fallback — no layout/partials so hosting missing-view cannot 500 again
            $salesTotal = number_format((float) data_get($data, 'sales.total', 0), 2);
            $salesCount = (int) data_get($data, 'sales.count', 0);
            $msg = e($e->getMessage());

            return response(
                '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Day close</title></head><body style="font-family:system-ui;padding:24px">'
                .'<h1>Close the day — '.$date.'</h1>'
                .'<p style="color:#b91c1c"><strong>Page layout error:</strong> '.$msg.'</p>'
                .'<p>Sales: <strong>'.$salesTotal.'</strong> ('.$salesCount.' invoices)</p>'
                .'<p><a href="'.url('/reports').'">Back to reports</a></p>'
                .'</body></html>',
                200
            )->header('Content-Type', 'text/html; charset=UTF-8');
        }
    }

    protected function emptyDayCloseData(): array
    {
        $emptyAgg = (object) ['count' => 0, 'total' => 0, 'tax' => 0];

        return [
            'sales' => $emptyAgg,
            'payments' => collect(),
            'payment_total' => 0,
            'returns' => $emptyAgg,
            'expenses' => $emptyAgg,
            'purchases' => $emptyAgg,
            'top_products' => collect(),
            'cashiers' => collect(),
            'lost_sales_count' => 0,
            'lost_sales_value' => 0.0,
            'registers' => collect(),
            'net_approx' => 0,
        ];
    }

    protected function buildDayCloseData($business_id, $date, $location_id = null): array
    {
        $start = $date.' 00:00:00';
        $end = $date.' 23:59:59';
        $emptyAgg = (object) ['count' => 0, 'total' => 0, 'tax' => 0];

        // Use query builder (not Eloquent) — avoids global scopes / soft-delete surprises
        $txScope = function ($q) use ($business_id, $start, $end, $location_id) {
            $q->where('business_id', $business_id)
                ->whereBetween('transaction_date', [$start, $end]);
            if (! empty($location_id)) {
                $q->where('location_id', $location_id);
            }
        };

        $sales = $emptyAgg;
        try {
            $sales = DB::table('transactions')
                ->where(function ($q) use ($txScope) {
                    $txScope($q);
                })
                ->where('type', 'sell')
                ->where('status', 'final')
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(final_total),0) as total, COALESCE(SUM(tax_amount),0) as tax')
                ->first() ?: $emptyAgg;
        } catch (\Throwable $e) {
            \Log::warning('Day close sales: '.$e->getMessage());
        }

        $payments = collect();
        try {
            $pq = DB::table('transaction_payments as tp')
                ->join('transactions as t', 'tp.transaction_id', '=', 't.id')
                ->where('t.business_id', $business_id)
                ->whereBetween('t.transaction_date', [$start, $end])
                ->where('t.type', 'sell')
                ->where('t.status', 'final');
            if (! empty($location_id)) {
                $pq->where('t.location_id', $location_id);
            }
            // Parent-only payments when column exists (avoids double-counting child rows)
            try {
                if (\Schema::hasColumn('transaction_payments', 'parent_id')) {
                    $pq->whereNull('tp.parent_id');
                }
            } catch (\Throwable $e) {
            }
            $payments = $pq->select('tp.method', DB::raw('COALESCE(SUM(tp.amount),0) as total'))
                ->groupBy('tp.method')
                ->get();
        } catch (\Throwable $e) {
            \Log::warning('Day close payments: '.$e->getMessage());
        }

        $returns = $emptyAgg;
        try {
            $returns = DB::table('transactions')
                ->where(function ($q) use ($txScope) {
                    $txScope($q);
                })
                ->where('type', 'sell_return')
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(final_total),0) as total')
                ->first() ?: $emptyAgg;
        } catch (\Throwable $e) {
            \Log::warning('Day close returns: '.$e->getMessage());
        }

        $expenses = $emptyAgg;
        try {
            $expenses = DB::table('transactions')
                ->where(function ($q) use ($txScope) {
                    $txScope($q);
                })
                ->where('type', 'expense')
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(final_total),0) as total')
                ->first() ?: $emptyAgg;
        } catch (\Throwable $e) {
            \Log::warning('Day close expenses: '.$e->getMessage());
        }

        $purchases = $emptyAgg;
        try {
            $purchases = DB::table('transactions')
                ->where(function ($q) use ($txScope) {
                    $txScope($q);
                })
                ->where('type', 'purchase')
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(final_total),0) as total')
                ->first() ?: $emptyAgg;
        } catch (\Throwable $e) {
            \Log::warning('Day close purchases: '.$e->getMessage());
        }

        $topProducts = collect();
        try {
            $tq = DB::table('transaction_sell_lines as tsl')
                ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
                ->join('products as p', 'tsl.product_id', '=', 'p.id')
                ->where('t.business_id', $business_id)
                ->whereBetween('t.transaction_date', [$start, $end])
                ->where('t.type', 'sell')
                ->where('t.status', 'final');
            if (! empty($location_id)) {
                $tq->where('t.location_id', $location_id);
            }
            $topProducts = $tq->select(
                'p.name as product_name',
                'p.sku',
                DB::raw('SUM(tsl.quantity) as qty'),
                DB::raw('SUM(tsl.quantity * COALESCE(tsl.unit_price_inc_tax, tsl.unit_price, 0)) as revenue')
            )
                ->groupBy('tsl.product_id', 'p.name', 'p.sku')
                ->orderByDesc('qty')
                ->limit(15)
                ->get();
        } catch (\Throwable $e) {
            \Log::warning('Day close top products: '.$e->getMessage());
        }

        $cashiers = collect();
        try {
            $cq = DB::table('transactions as t')
                ->leftJoin('users as u', 't.created_by', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->whereBetween('t.transaction_date', [$start, $end])
                ->where('t.type', 'sell')
                ->where('t.status', 'final');
            if (! empty($location_id)) {
                $cq->where('t.location_id', $location_id);
            }
            $cashiers = $cq->select(
                DB::raw("TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) as cashier"),
                DB::raw('COUNT(t.id) as invoices'),
                DB::raw('COALESCE(SUM(t.final_total),0) as total')
            )
                ->groupBy('t.created_by', 'u.first_name', 'u.last_name')
                ->orderByDesc('total')
                ->get();
        } catch (\Throwable $e) {
            \Log::warning('Day close cashiers: '.$e->getMessage());
        }

        $lostSalesCount = 0;
        $lostSalesValue = 0.0;
        try {
            if (\Schema::hasTable('lost_sales')) {
                $row = DB::table('lost_sales')
                    ->where('business_id', $business_id)
                    ->whereDate('created_at', $date)
                    ->when(! empty($location_id), function ($q) use ($location_id) {
                        $q->where('location_id', $location_id);
                    })
                    ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(COALESCE(quantity,0) * COALESCE(selling_price,0)),0) as val')
                    ->first();
                $lostSalesCount = (int) ($row->cnt ?? 0);
                $lostSalesValue = (float) ($row->val ?? 0);
            }
        } catch (\Throwable $e) {
            \Log::warning('Day close lost sales: '.$e->getMessage());
            try {
                $lostSalesCount = (int) DB::table('lost_sales')
                    ->where('business_id', $business_id)
                    ->whereDate('created_at', $date)
                    ->count();
            } catch (\Throwable $e2) {
            }
        }

        $registers = collect();
        try {
            if (\Schema::hasTable('cash_registers')) {
                $rq = DB::table('cash_registers as cr')
                    ->leftJoin('users as u', 'cr.user_id', '=', 'u.id')
                    ->where('cr.business_id', $business_id)
                    ->whereDate('cr.created_at', $date);
                if (! empty($location_id)) {
                    $rq->where('cr.location_id', $location_id);
                }
                $registers = $rq->select(
                    'cr.id',
                    'cr.status',
                    'cr.created_at',
                    DB::raw('NULL as closed_at'),
                    DB::raw("TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) as user_name")
                )->orderByDesc('cr.id')->limit(20)->get();
            }
        } catch (\Throwable $e) {
            \Log::warning('Day close registers: '.$e->getMessage());
        }

        $paymentTotal = $payments->sum(function ($p) {
            return (float) ($p->total ?? 0);
        });

        $salesTotal = (float) ($sales->total ?? 0);
        $returnsTotal = (float) ($returns->total ?? 0);
        $expensesTotal = (float) ($expenses->total ?? 0);

        return [
            'sales' => $sales,
            'payments' => $payments,
            'payment_total' => $paymentTotal,
            'returns' => $returns,
            'expenses' => $expenses,
            'purchases' => $purchases,
            'top_products' => $topProducts,
            'cashiers' => $cashiers,
            'lost_sales_count' => $lostSalesCount,
            'lost_sales_value' => $lostSalesValue,
            'registers' => $registers,
            'net_approx' => $salesTotal - $returnsTotal - $expensesTotal,
        ];
    }

    /**
     * Package C — Stock vs free purchase ledger gap.
     */
    public function ledgerGap(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $location_id = $request->get('location_id');
        if (empty($location_id)) {
            $location_id = array_key_first($business_locations->toArray() ?? []) ?: null;
        }

        $rows = [];
        if ($location_id) {
            $sql = "
                SELECT
                    p.id AS product_id,
                    p.name,
                    p.sku,
                    v.id AS variation_id,
                    v.sub_sku,
                    vld.qty_available AS system_qty,
                    COALESCE(free.free_qty, 0) AS free_qty,
                    (vld.qty_available - COALESCE(free.free_qty, 0)) AS shortfall
                FROM variation_location_details vld
                JOIN variations v ON v.id = vld.variation_id
                JOIN products p ON p.id = vld.product_id
                LEFT JOIN (
                    SELECT
                        PL.variation_id,
                        PL.product_id,
                        t.location_id,
                        t.business_id,
                        SUM(
                            PL.quantity
                            - COALESCE(PL.quantity_sold, 0)
                            - COALESCE(PL.quantity_adjusted, 0)
                            - COALESCE(PL.quantity_returned, 0)
                            - COALESCE(PL.mfg_quantity_used, 0)
                        ) AS free_qty
                    FROM purchase_lines PL
                    JOIN transactions t ON t.id = PL.transaction_id
                    WHERE t.location_id = ?
                      AND t.business_id = ?
                      AND t.status = 'received'
                      AND t.type IN ('purchase', 'opening_stock', 'purchase_transfer', 'production_purchase')
                    GROUP BY PL.variation_id, PL.product_id, t.location_id, t.business_id
                ) free ON free.variation_id = v.id
                    AND free.product_id = p.id
                    AND free.location_id = vld.location_id
                    AND free.business_id = p.business_id
                WHERE vld.location_id = ?
                  AND p.business_id = ?
                  AND p.enable_stock = 1
                  AND vld.qty_available > 0.0001
                  AND (vld.qty_available - COALESCE(free.free_qty, 0)) > 0.0001
                ORDER BY shortfall DESC
                LIMIT 500
            ";
            $rows = DB::select($sql, [$location_id, $business_id, $location_id, $business_id]);
        }

        return view('report.hub.ledger_gap', compact('business_locations', 'location_id', 'rows'));
    }

    /**
     * Package C — Expiry with 30/60/90 bands.
     */
    public function expirySmart(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $location_id = $request->get('location_id');
        $band = $request->get('band', '90'); // 30, 60, 90, all_expired

        $days = in_array($band, ['30', '60', '90'], true) ? (int) $band : 90;
        $today = Carbon::today()->toDateString();
        $until = Carbon::today()->addDays($days)->toDateString();

        $q = DB::table('purchase_lines as pl')
            ->join('transactions as t', 'pl.transaction_id', '=', 't.id')
            ->join('products as p', 'pl.product_id', '=', 'p.id')
            ->join('variations as v', 'pl.variation_id', '=', 'v.id')
            ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
            ->where('t.business_id', $business_id)
            ->where('t.status', 'received')
            ->whereIn('t.type', ['purchase', 'opening_stock', 'purchase_transfer'])
            ->whereNotNull('pl.exp_date')
            ->whereRaw('(pl.quantity - COALESCE(pl.quantity_sold,0) - COALESCE(pl.quantity_adjusted,0) - COALESCE(pl.quantity_returned,0) - COALESCE(pl.mfg_quantity_used,0)) > 0.0001');

        if ($location_id) {
            $q->where('t.location_id', $location_id);
        }

        if ($band === 'expired') {
            $q->whereDate('pl.exp_date', '<', $today);
        } else {
            $q->whereDate('pl.exp_date', '>=', $today)
                ->whereDate('pl.exp_date', '<=', $until);
        }

        $rows = $q->select(
            'p.name as product_name',
            'v.sub_sku',
            'pl.lot_number',
            'pl.exp_date',
            'bl.name as location_name',
            DB::raw('(pl.quantity - COALESCE(pl.quantity_sold,0) - COALESCE(pl.quantity_adjusted,0) - COALESCE(pl.quantity_returned,0) - COALESCE(pl.mfg_quantity_used,0)) as qty_left'),
            DB::raw('(pl.quantity - COALESCE(pl.quantity_sold,0) - COALESCE(pl.quantity_adjusted,0) - COALESCE(pl.quantity_returned,0) - COALESCE(pl.mfg_quantity_used,0)) * COALESCE(pl.purchase_price_inc_tax, pl.purchase_price, 0) as value_at_risk'),
            DB::raw('DATEDIFF(pl.exp_date, CURDATE()) as days_left')
        )
            ->orderBy('pl.exp_date')
            ->limit(1000)
            ->get();

        $totalValue = $rows->sum('value_at_risk');
        $totalQty = $rows->sum('qty_left');

        return view('report.hub.expiry_smart', compact(
            'business_locations',
            'location_id',
            'band',
            'rows',
            'totalValue',
            'totalQty',
            'days'
        ));
    }

    /**
     * Package C — Reorder list (low stock) with link to shop orders.
     */
    public function reorderList(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $error = null;

        try {
            $business_locations = BusinessLocation::forDropdown($business_id, false);
        } catch (\Throwable $e) {
            \Log::warning('Reorder locations: '.$e->getMessage());
            $business_locations = collect();
        }

        // Normalize locations to a simple [id => name] array
        if ($business_locations instanceof \Illuminate\Support\Collection) {
            $locArray = $business_locations->toArray();
        } else {
            $locArray = (array) $business_locations;
        }

        $location_id = $request->get('location_id');
        if (empty($location_id) && ! empty($locArray)) {
            $location_id = array_key_first($locArray);
        }

        $rows = collect();
        if ($location_id) {
            try {
                $q = DB::table('products')
                    ->join('variations', 'products.id', '=', 'variations.product_id')
                    ->leftJoin('variation_location_details as vld', function ($join) use ($location_id) {
                        $join->on('variations.id', '=', 'vld.variation_id')
                            ->where('vld.location_id', '=', $location_id);
                    })
                    ->where('products.business_id', $business_id)
                    ->where('products.enable_stock', 1);

                // Soft-deletes column is not always present
                try {
                    if (\Schema::hasColumn('variations', 'deleted_at')) {
                        $q->whereNull('variations.deleted_at');
                    }
                } catch (\Throwable $e) {
                }

                $q->where(function ($w) {
                    $w->whereRaw('COALESCE(vld.qty_available, 0) <= 0')
                        ->orWhereRaw('COALESCE(products.alert_quantity, 0) > 0 AND COALESCE(vld.qty_available, 0) <= COALESCE(products.alert_quantity, 0)');
                });

                $rows = $q->select(
                    'products.id as product_id',
                    'products.name',
                    'products.sku',
                    'products.alert_quantity',
                    'variations.id as variation_id',
                    'variations.sub_sku',
                    'variations.default_purchase_price',
                    'variations.sell_price_inc_tax',
                    DB::raw('COALESCE(vld.qty_available, 0) as qty_available'),
                    DB::raw('CASE
                        WHEN COALESCE(products.alert_quantity, 0) > 0
                        THEN GREATEST(products.alert_quantity - COALESCE(vld.qty_available, 0), 1)
                        ELSE 1
                    END as suggest_qty')
                )
                    ->orderBy('products.name')
                    ->limit(500)
                    ->get();
            } catch (\Throwable $e) {
                \Log::error('Reorder list query failed: '.$e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                $error = $e->getMessage();
                $rows = collect();
            }
        }

        // Prefer blade; if missing, return simple HTML (avoids View not found 500)
        if (view()->exists('report.hub.reorder_list')) {
            try {
                return view('report.hub.reorder_list', compact(
                    'business_locations',
                    'location_id',
                    'rows',
                    'error'
                ));
            } catch (\Throwable $e) {
                \Log::error('Reorder list view failed: '.$e->getMessage());
                $error = $e->getMessage();
            }
        }

        return $this->reorderListFallback($business_locations, $location_id, $rows, $error);
    }

    /**
     * Minimal HTML reorder list when the blade is missing on the server.
     */
    protected function reorderListFallback($business_locations, $location_id, $rows, $error = null)
    {
        $biz = e(session('business.name') ?: 'Business');
        $err = $error ? '<p style="color:#b91c1c"><strong>Error:</strong> '.e($error).'</p>' : '';
        $opts = '';
        foreach ($business_locations as $id => $name) {
            $sel = ((string) $id === (string) $location_id) ? ' selected' : '';
            $opts .= '<option value="'.e($id).'"'.$sel.'>'.e($name).'</option>';
        }
        $body = '';
        foreach ($rows as $r) {
            $body .= '<tr>'
                .'<td>'.e($r->name).'</td>'
                .'<td>'.e($r->sub_sku ?: $r->sku).'</td>'
                .'<td>'.e(number_format((float) $r->qty_available, 2)).'</td>'
                .'<td>'.e(number_format((float) ($r->alert_quantity ?? 0), 2)).'</td>'
                .'<td><strong>'.e(number_format((float) $r->suggest_qty, 2)).'</strong></td>'
                .'<td>'.e(number_format((float) ($r->default_purchase_price ?? 0), 2)).'</td>'
                .'<td>'.e(number_format((float) ($r->sell_price_inc_tax ?? 0), 2)).'</td>'
                .'</tr>';
        }
        if ($body === '') {
            $body = '<tr><td colspan="7" style="text-align:center;color:#64748b">No low-stock products for this location.</td></tr>';
        }
        $action = url('/reports/reorder-list');
        $pos = url('/pos/create');
        $hub = url('/reports');

        $html = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Reorder list</title>
<style>
body{font-family:system-ui,sans-serif;background:#f8fafc;margin:0;padding:20px;color:#0f172a}
.box{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px;max-width:1100px;margin:0 auto}
h1{margin:0 0 8px;font-size:20px}table{width:100%;border-collapse:collapse;font-size:13px}
th,td{padding:8px;border-bottom:1px solid #f1f5f9;text-align:left}th{font-size:11px;text-transform:uppercase;color:#64748b}
a.btn{display:inline-block;background:#0f766e;color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none;margin:0 6px 12px 0;font-size:13px;font-weight:700}
</style></head><body>
<div class="box">
  <h1>Reorder list</h1>
  <p style="color:#64748b;font-size:13px">{$biz}</p>
  {$err}
  <a class="btn" href="{$pos}">Open POS</a>
  <a class="btn" href="{$hub}" style="background:#334155">Reports</a>
  <form method="get" action="{$action}" style="margin:12px 0">
    <label>Location </label>
    <select name="location_id" onchange="this.form.submit()">{$opts}</select>
  </form>
  <table>
    <thead><tr><th>Product</th><th>SKU</th><th>In stock</th><th>Alert</th><th>Suggest qty</th><th>Buy</th><th>Sell</th></tr></thead>
    <tbody>{$body}</tbody>
  </table>
  <p style="font-size:12px;color:#94a3b8;margin-top:12px">Upload resources/views/report/hub/reorder_list.blade.php for full UI.</p>
</div></body></html>
HTML;

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
