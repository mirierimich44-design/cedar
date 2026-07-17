<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\NotificationUtil;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Owner ops pack: deploy guide, weekly ritual, data quality,
 * owner dashboard strip data, month-end SMS/WhatsApp, roles matrix.
 */
class OwnerOpsController extends Controller
{
    protected $transactionUtil;

    protected $businessUtil;

    protected $notificationUtil;

    public function __construct(
        TransactionUtil $transactionUtil,
        BusinessUtil $businessUtil,
        NotificationUtil $notificationUtil
    ) {
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->notificationUtil = $notificationUtil;
    }

    protected function bizId(Request $request)
    {
        return $request->session()->get('user.business_id');
    }

    protected function isOwnerLike(): bool
    {
        $u = auth()->user();

        return $this->businessUtil->is_admin($u)
            || $u->can('profit_loss_report.view')
            || $u->can('account.access')
            || $u->can('dashboard.data');
    }

    /**
     * 1 — Deploy checklist (in-app).
     */
    public function deployChecklist()
    {
        if (! $this->isOwnerLike() && ! auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        $checks = $this->liveDeployChecks();

        return view('report.owner.deploy_checklist', compact('checks'));
    }

    protected function liveDeployChecks(): array
    {
        $files = [
            'app/Http/Controllers/AdvancedReportsController.php',
            'app/Http/Controllers/ReportsHubController.php',
            'app/Http/Controllers/OwnerOpsController.php',
            'resources/views/report/hub/day_close.blade.php',
            'resources/views/report/partials/export_toolbar.blade.php',
            'resources/views/report/advanced/month_end_pack.blade.php',
            'resources/views/report/advanced/bank_mpesa_recon.blade.php',
            'resources/views/home/partials/owner_ops_strip.blade.php',
        ];
        $out = [];
        foreach ($files as $rel) {
            $path = base_path($rel);
            $out[] = [
                'path' => $rel,
                'ok' => file_exists($path),
                'mtime' => file_exists($path) ? date('Y-m-d H:i', filemtime($path)) : null,
            ];
        }
        $out[] = [
            'path' => 'Route reports.month_end_pack',
            'ok' => \Illuminate\Support\Facades\Route::has('reports.month_end_pack'),
            'mtime' => null,
        ];
        $out[] = [
            'path' => 'Route reports.day_close',
            'ok' => \Illuminate\Support\Facades\Route::has('reports.day_close'),
            'mtime' => null,
        ];
        $out[] = [
            'path' => 'Table activity_log',
            'ok' => Schema::hasTable('activity_log'),
            'mtime' => null,
        ];
        $out[] = [
            'path' => 'Table mpesa_transactions',
            'ok' => Schema::hasTable('mpesa_transactions'),
            'mtime' => null,
        ];

        return $out;
    }

    /**
     * 2 — Owner weekly ritual guide.
     */
    public function weeklyRitual()
    {
        if (! $this->isOwnerLike()) {
            abort(403, 'Unauthorized action.');
        }

        $steps = [
            [
                'when' => 'Every day (close)',
                'title' => 'Close the day',
                'help' => 'Cash, M-Pesa, invoices, top products for today.',
                'url' => route('reports.day_close'),
                'icon' => 'fa-calendar-check',
            ],
            [
                'when' => 'Every day',
                'title' => 'Cash register',
                'help' => 'Who opened/closed the till.',
                'url' => action([ReportController::class, 'getRegisterReport']),
                'icon' => 'fa-cash-register',
            ],
            [
                'when' => '2–3× per week',
                'title' => 'Bank / M-Pesa recon',
                'help' => 'Match POS payments to M-Pesa / bank statement.',
                'url' => route('reports.bank_mpesa_recon'),
                'icon' => 'fa-university',
            ],
            [
                'when' => 'Weekly',
                'title' => 'Customer credit',
                'help' => 'Chase ageing debts.',
                'url' => action([ReportController::class, 'getCustomerCreditReport']),
                'icon' => 'fa-credit-card',
            ],
            [
                'when' => 'Weekly',
                'title' => 'Reorder + expiry',
                'help' => 'What to order; what expires in 30/60/90 days.',
                'url' => route('reports.reorder_list'),
                'icon' => 'fa-truck-loading',
            ],
            [
                'when' => 'Weekly',
                'title' => 'Discount abuse',
                'help' => 'Who gave unusual discounts.',
                'url' => route('reports.discount_abuse'),
                'icon' => 'fa-percentage',
            ],
            [
                'when' => 'Monthly',
                'title' => 'Month-end pack',
                'help' => 'Print one PDF: P&L, stock, credit, top products.',
                'url' => route('reports.month_end_pack'),
                'icon' => 'fa-file-alt',
            ],
            [
                'when' => 'Monthly',
                'title' => 'Data quality',
                'help' => 'Ledger gaps, FEFO breaches, stock mismatches.',
                'url' => route('reports.data_quality'),
                'icon' => 'fa-heartbeat',
            ],
            [
                'when' => 'Monthly',
                'title' => 'Send month-end to phone',
                'help' => 'SMS / WhatsApp summary to owner.',
                'url' => route('reports.month_end_notify'),
                'icon' => 'fa-paper-plane',
            ],
        ];

        return view('report.owner.weekly_ritual', compact('steps'));
    }

    /**
     * 3 — Roles & permissions matrix (guide + live user caps).
     */
    public function rolesGuide()
    {
        if (! auth()->user()->can('user.view') && ! $this->businessUtil->is_admin(auth()->user())) {
            abort(403, 'Unauthorized action.');
        }

        $matrix = [
            [
                'role' => 'Cashier',
                'can' => 'POS sales, open/close own till, day close (if given profit_loss_report.view or use register report)',
                'cannot' => 'P&L, inventory valuation, audit log, cost prices (without view_purchase_price)',
                'perms' => ['sell.pos', 'sell.create', 'register.view'],
            ],
            [
                'role' => 'Supervisor / Pharmacist',
                'can' => 'Stock, expiry, reorder, FEFO, stock adjustments, shop orders',
                'cannot' => 'Full financial statements (optional)',
                'perms' => ['stock_report.view', 'stock_adjustment.create'],
            ],
            [
                'role' => 'Manager',
                'can' => 'All reports except business settings; discount abuse; recon',
                'cannot' => 'Delete business, manage all users (optional)',
                'perms' => ['profit_loss_report.view', 'purchase_n_sell_report.view', 'account.access'],
            ],
            [
                'role' => 'Owner / Admin',
                'can' => 'Everything + audit + month-end SMS + deploy checklist',
                'cannot' => '—',
                'perms' => ['dashboard.data', 'business_settings.access', 'user.view'],
            ],
        ];

        $users = User::where('business_id', session('user.business_id'))
            ->where('is_cmmsn_agnt', 0)
            ->select('id', 'first_name', 'last_name', 'username', 'max_sales_discount_percent')
            ->orderBy('first_name')
            ->limit(100)
            ->get();

        return view('report.owner.roles_guide', compact('matrix', 'users'));
    }

    /**
     * 4 — Data quality hub.
     */
    public function dataQuality(Request $request)
    {
        if (! auth()->user()->can('stock_report.view') && ! $this->isOwnerLike()) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $this->bizId($request);
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $location_id = $request->get('location_id') ?: (array_key_first($business_locations->toArray() ?? []) ?: null);

        $issues = [];

        // Ledger gap count
        try {
            if ($location_id) {
                $sql = "
                    SELECT COUNT(*) as cnt FROM (
                        SELECT vld.id
                        FROM variation_location_details vld
                        JOIN variations v ON v.id = vld.variation_id
                        JOIN products p ON p.id = vld.product_id
                        LEFT JOIN (
                            SELECT PL.variation_id, t.location_id,
                                SUM(PL.quantity - COALESCE(PL.quantity_sold,0) - COALESCE(PL.quantity_adjusted,0)
                                    - COALESCE(PL.quantity_returned,0) - COALESCE(PL.mfg_quantity_used,0)) AS free_qty
                            FROM purchase_lines PL
                            JOIN transactions t ON t.id = PL.transaction_id
                            WHERE t.location_id = ? AND t.business_id = ? AND t.status = 'received'
                              AND t.type IN ('purchase','opening_stock','purchase_transfer','production_purchase')
                            GROUP BY PL.variation_id, t.location_id
                        ) free ON free.variation_id = v.id AND free.location_id = vld.location_id
                        WHERE vld.location_id = ? AND p.business_id = ? AND p.enable_stock = 1
                          AND vld.qty_available > 0.0001
                          AND (vld.qty_available - COALESCE(free.free_qty,0)) > 0.0001
                        LIMIT 500
                    ) x
                ";
                $cnt = DB::selectOne($sql, [$location_id, $business_id, $location_id, $business_id]);
                $issues[] = [
                    'key' => 'ledger_gap',
                    'title' => 'Stock vs purchase ledger gaps',
                    'count' => (int) ($cnt->cnt ?? 0),
                    'severity' => 'medium',
                    'url' => route('reports.ledger_gap', ['location_id' => $location_id]),
                    'help' => 'System qty higher than free purchase qty — stock adjustments may fail.',
                ];
            }
        } catch (\Throwable $e) {
            $issues[] = ['key' => 'ledger_gap', 'title' => 'Ledger gap check failed', 'count' => 0, 'severity' => 'low', 'url' => route('reports.ledger_gap'), 'help' => $e->getMessage()];
        }

        // Products without expiry on purchase lines with stock
        try {
            $noExp = (int) DB::table('purchase_lines as pl')
                ->join('transactions as t', 'pl.transaction_id', '=', 't.id')
                ->where('t.business_id', $business_id)
                ->where('t.status', 'received')
                ->whereNull('pl.exp_date')
                ->whereRaw('(pl.quantity - COALESCE(pl.quantity_sold,0) - COALESCE(pl.quantity_adjusted,0) - COALESCE(pl.quantity_returned,0)) > 0.0001')
                ->when($location_id, fn ($q) => $q->where('t.location_id', $location_id))
                ->count();
            $issues[] = [
                'key' => 'no_expiry',
                'title' => 'Batches without expiry date',
                'count' => $noExp,
                'severity' => $noExp > 50 ? 'high' : ($noExp > 0 ? 'medium' : 'ok'),
                'url' => action([ReportController::class, 'getStockExpiryReport']),
                'help' => 'Pharmacy FEFO needs expiry on purchase batches.',
            ];
        } catch (\Throwable $e) {
        }

        // Negative stock
        try {
            $neg = (int) DB::table('variation_location_details as vld')
                ->join('products as p', 'p.id', '=', 'vld.product_id')
                ->where('p.business_id', $business_id)
                ->where('vld.qty_available', '<', -0.0001)
                ->when($location_id, fn ($q) => $q->where('vld.location_id', $location_id))
                ->count();
            $issues[] = [
                'key' => 'neg_stock',
                'title' => 'Negative stock lines',
                'count' => $neg,
                'severity' => $neg > 0 ? 'high' : 'ok',
                'url' => action([ReportController::class, 'getStockReport']),
                'help' => 'Over-selling or mapping issues.',
            ];
        } catch (\Throwable $e) {
        }

        // Open credit
        try {
            $creditDue = (float) DB::table('transactions as t')
                ->leftJoin(DB::raw('(SELECT transaction_id, SUM(IF(is_return=1,-1*amount,amount)) as paid FROM transaction_payments GROUP BY transaction_id) tp'), 't.id', '=', 'tp.transaction_id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereIn('t.payment_status', ['due', 'partial'])
                ->selectRaw('COALESCE(SUM(t.final_total - COALESCE(tp.paid,0)),0) as due')
                ->value('due');
            $issues[] = [
                'key' => 'credit',
                'title' => 'Customer credit outstanding',
                'count' => round($creditDue, 0),
                'severity' => $creditDue > 0 ? 'medium' : 'ok',
                'url' => action([ReportController::class, 'getCustomerCreditReport']),
                'help' => 'Amount customers still owe (approx).',
                'is_money' => true,
            ];
        } catch (\Throwable $e) {
        }

        // M-Pesa paid not linked
        try {
            if (Schema::hasTable('mpesa_transactions')) {
                $unlinked = (int) DB::table('mpesa_transactions')
                    ->where('business_id', $business_id)
                    ->where('status', 'paid')
                    ->whereNull('transaction_id')
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->count();
                $issues[] = [
                    'key' => 'mpesa_unlinked',
                    'title' => 'Paid M-Pesa STK not linked to sale (30d)',
                    'count' => $unlinked,
                    'severity' => $unlinked > 10 ? 'medium' : ($unlinked > 0 ? 'low' : 'ok'),
                    'url' => route('reports.bank_mpesa_recon'),
                    'help' => 'Reconcile or link receipts on payments.',
                ];
            }
        } catch (\Throwable $e) {
        }

        // Users with no discount cap
        try {
            $noCap = (int) User::where('business_id', $business_id)
                ->where(function ($q) {
                    $q->whereNull('max_sales_discount_percent')
                        ->orWhere('max_sales_discount_percent', '');
                })
                ->count();
            $issues[] = [
                'key' => 'discount_cap',
                'title' => 'Users without max discount %',
                'count' => $noCap,
                'severity' => $noCap > 0 ? 'medium' : 'ok',
                'url' => route('reports.roles_guide'),
                'help' => 'Set max_sales_discount_percent on users to block abuse.',
            ];
        } catch (\Throwable $e) {
        }

        return view('report.owner.data_quality', compact('business_locations', 'location_id', 'issues'));
    }

    /**
     * 5 — Owner dashboard data (for home strip + JSON).
     */
    public function ownerDashboardData(Request $request): array
    {
        $business_id = $this->bizId($request);
        $today = Carbon::today()->format('Y-m-d');
        $location_id = $request->get('location_id') ?: null;

        $sales = 0.0;
        $invoices = 0;
        try {
            $q = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereDate('transaction_date', $today);
            if ($location_id) {
                $q->where('location_id', $location_id);
            }
            $row = $q->selectRaw('COUNT(*) as c, COALESCE(SUM(final_total),0) as t')->first();
            $sales = (float) ($row->t ?? 0);
            $invoices = (int) ($row->c ?? 0);
        } catch (\Throwable $e) {
        }

        $credit = 0.0;
        try {
            $credit = (float) DB::table('transactions as t')
                ->leftJoin(DB::raw('(SELECT transaction_id, SUM(IF(is_return=1,-1*amount,amount)) as paid FROM transaction_payments GROUP BY transaction_id) tp'), 't.id', '=', 'tp.transaction_id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereIn('t.payment_status', ['due', 'partial'])
                ->selectRaw('COALESCE(SUM(t.final_total - COALESCE(tp.paid,0)),0) as due')
                ->value('due');
        } catch (\Throwable $e) {
        }

        $lowStock = 0;
        try {
            $lowStock = (int) DB::table('products as p')
                ->join('variations as v', 'p.id', '=', 'v.product_id')
                ->leftJoin('variation_location_details as vld', 'v.id', '=', 'vld.variation_id')
                ->where('p.business_id', $business_id)
                ->where('p.enable_stock', 1)
                ->whereNull('v.deleted_at')
                ->where(function ($q) {
                    $q->whereRaw('COALESCE(vld.qty_available,0) <= 0')
                        ->orWhereRaw('p.alert_quantity > 0 AND COALESCE(vld.qty_available,0) <= p.alert_quantity');
                })
                ->distinct('v.id')
                ->count('v.id');
        } catch (\Throwable $e) {
        }

        $openTills = 0;
        try {
            $openTills = (int) DB::table('cash_registers')
                ->where('business_id', $business_id)
                ->where('status', 'open')
                ->count();
        } catch (\Throwable $e) {
        }

        $expiring = 0;
        try {
            $until = Carbon::today()->addDays(90)->toDateString();
            $expiring = (int) DB::table('purchase_lines as pl')
                ->join('transactions as t', 'pl.transaction_id', '=', 't.id')
                ->where('t.business_id', $business_id)
                ->where('t.status', 'received')
                ->whereNotNull('pl.exp_date')
                ->whereDate('pl.exp_date', '>=', $today)
                ->whereDate('pl.exp_date', '<=', $until)
                ->whereRaw('(pl.quantity - COALESCE(pl.quantity_sold,0) - COALESCE(pl.quantity_adjusted,0) - COALESCE(pl.quantity_returned,0)) > 0.0001')
                ->count();
        } catch (\Throwable $e) {
        }

        return [
            'today' => $today,
            'sales_today' => $sales,
            'invoices_today' => $invoices,
            'credit_due' => $credit,
            'low_stock' => $lowStock,
            'open_tills' => $openTills,
            'expiring_90d' => $expiring,
            'links' => [
                'day_close' => route('reports.day_close'),
                'month_end' => route('reports.month_end_pack'),
                'recon' => route('reports.bank_mpesa_recon'),
                'credit' => action([ReportController::class, 'getCustomerCreditReport']),
                'reorder' => route('reports.reorder_list'),
                'ritual' => route('reports.weekly_ritual'),
            ],
        ];
    }

    public function ownerDashboardJson(Request $request)
    {
        if (! $this->isOwnerLike()) {
            abort(403);
        }

        return response()->json($this->ownerDashboardData($request));
    }

    /**
     * 6 — Month-end SMS / WhatsApp notify form + send.
     */
    public function monthEndNotifyForm(Request $request)
    {
        if (! $this->isOwnerLike()) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $this->bizId($request);
        $month = $request->get('month', Carbon::today()->format('Y-m'));
        $summary = $this->buildMonthEndText($business_id, $month);
        $u = auth()->user();
        $defaultPhone = $u->contact_number ?? $u->contact_no ?? $u->mobile ?? '';

        return view('report.owner.month_end_notify', compact('month', 'summary', 'defaultPhone'));
    }

    public function monthEndNotifySend(Request $request)
    {
        if (! $this->isOwnerLike()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'phone' => 'required|string|min:9',
            'channel' => 'required|in:sms,whatsapp,both',
            'month' => 'nullable|string',
        ]);

        $business_id = $this->bizId($request);
        $month = $request->get('month', Carbon::today()->format('Y-m'));
        $text = $this->buildMonthEndText($business_id, $month);
        $phone = preg_replace('/\s+/', '', $request->phone);
        // Kenya style: 07... -> 2547...
        if (preg_match('/^0\d{9}$/', $phone)) {
            $phone = '254'.substr($phone, 1);
        }
        if (strpos($phone, '+') === 0) {
            $phone = ltrim($phone, '+');
        }

        $results = [];
        $channel = $request->channel;

        if (in_array($channel, ['sms', 'both'], true)) {
            try {
                $this->notificationUtil->sendSms([
                    'sms_body' => $text,
                    'mobile_number' => $phone,
                    'business_id' => $business_id,
                ]);
                $results[] = 'SMS queued/sent';
            } catch (\Throwable $e) {
                $results[] = 'SMS failed: '.$e->getMessage();
                \Log::warning('Month-end SMS: '.$e->getMessage());
            }
        }

        if (in_array($channel, ['whatsapp', 'both'], true)) {
            try {
                $waPhone = (strpos($phone, '+') === 0) ? $phone : '+'.$phone;
                $ok = $this->notificationUtil->sendAfricasTalkingWhatsapp($waPhone, $text);
                $results[] = $ok ? 'WhatsApp sent' : 'WhatsApp failed (check Africa\'s Talking config)';
            } catch (\Throwable $e) {
                $results[] = 'WhatsApp failed: '.$e->getMessage();
            }
        }

        return redirect()
            ->route('reports.month_end_notify', ['month' => $month])
            ->with('status', [
                'success' => 1,
                'msg' => implode(' · ', $results),
            ]);
    }

    protected function buildMonthEndText($business_id, string $month): string
    {
        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->format('Y-m-d');
            $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->format('Y-m-d');
        } catch (\Throwable $e) {
            $start = Carbon::today()->startOfMonth()->format('Y-m-d');
            $end = Carbon::today()->format('Y-m-d');
            $month = Carbon::today()->format('Y-m');
        }
        if ($end > Carbon::today()->format('Y-m-d')) {
            $end = Carbon::today()->format('Y-m-d');
        }

        $sales = 0.0;
        $invoices = 0;
        try {
            $row = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereBetween('transaction_date', [$start.' 00:00:00', $end.' 23:59:59'])
                ->selectRaw('COUNT(*) as c, COALESCE(SUM(final_total),0) as t')
                ->first();
            $sales = (float) ($row->t ?? 0);
            $invoices = (int) ($row->c ?? 0);
        } catch (\Throwable $e) {
        }

        $net = 0.0;
        try {
            $pl = $this->transactionUtil->getProfitLossDetails(
                $business_id,
                null,
                $start,
                $end,
                null,
                auth()->user()->permitted_locations()
            );
            $net = (float) ($pl['net_profit'] ?? 0);
        } catch (\Throwable $e) {
        }

        $name = session('business.name') ?: 'Business';

        return "{$name} month-end {$month}\n"
            ."Sales: ".number_format($sales, 2)." ({$invoices} invoices)\n"
            ."Net profit (approx): ".number_format($net, 2)."\n"
            ."Open full pack in POS → Reports → Month-end pack.";
    }
}
