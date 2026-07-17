<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Advanced owner/finance reports:
 * financial statements pack, bank/M-Pesa recon, multi-period,
 * discount abuse, FEFO log, audit export, inventory valuation.
 */
class AdvancedReportsController extends Controller
{
    protected $transactionUtil;

    protected $businessUtil;

    protected $moduleUtil;

    public function __construct(
        TransactionUtil $transactionUtil,
        BusinessUtil $businessUtil,
        ModuleUtil $moduleUtil
    ) {
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    protected function bizId(Request $request)
    {
        return $request->session()->get('user.business_id');
    }

    protected function canFinance(): bool
    {
        $u = auth()->user();

        return $u->can('profit_loss_report.view')
            || $u->can('account.access')
            || $u->can('purchase_n_sell_report.view');
    }

    protected function canStock(): bool
    {
        return auth()->user()->can('stock_report.view');
    }

    protected function parseRange(Request $request): array
    {
        $start = $request->get('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $end = $request->get('end_date', Carbon::today()->format('Y-m-d'));
        try {
            $start = Carbon::parse($start)->format('Y-m-d');
        } catch (\Throwable $e) {
            $start = Carbon::today()->startOfMonth()->format('Y-m-d');
        }
        try {
            $end = Carbon::parse($end)->format('Y-m-d');
        } catch (\Throwable $e) {
            $end = Carbon::today()->format('Y-m-d');
        }
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }
        $location_id = $request->get('location_id') ?: null;

        return compact('start', 'end', 'location_id');
    }

    /**
     * Full financial statements pack (P&L + simplified BS + cash movement).
     */
    public function financialStatements(Request $request)
    {
        if (! $this->canFinance()) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $this->bizId($request);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        extract($this->parseRange($request));

        $pl = [];
        try {
            $permitted = auth()->user()->permitted_locations();
            $pl = $this->transactionUtil->getProfitLossDetails(
                $business_id,
                $location_id,
                $start,
                $end,
                null,
                $permitted
            );
        } catch (\Throwable $e) {
            \Log::warning('Financial statements P&L: '.$e->getMessage());
            $pl = [
                'total_sell' => 0, 'total_purchase' => 0, 'opening_stock' => 0, 'closing_stock' => 0,
                'gross_profit' => 0, 'net_profit' => 0, 'total_expense' => 0, 'total_adjustment' => 0,
            ];
        }

        $cogs = ((float) ($pl['opening_stock'] ?? 0) + (float) ($pl['total_purchase'] ?? 0))
            - (float) ($pl['closing_stock'] ?? 0);

        // Simplified balance-sheet style snapshot at end date
        $bs = $this->buildSimpleBalanceSheet($business_id, $end, $location_id);

        // Cash movement in period (payments in/out)
        $cash = $this->buildCashMovement($business_id, $start, $end, $location_id);

        return view('report.advanced.financial_statements', compact(
            'business_locations',
            'start',
            'end',
            'location_id',
            'pl',
            'cogs',
            'bs',
            'cash'
        ));
    }

    protected function buildSimpleBalanceSheet($business_id, $end_date, $location_id = null): array
    {
        $out = [
            'closing_stock' => 0,
            'customer_due' => 0,
            'supplier_due' => 0,
            'account_balances' => [],
            'cash_total' => 0,
        ];

        try {
            $permitted = auth()->user()->permitted_locations();
            $out['closing_stock'] = (float) $this->transactionUtil->getOpeningClosingStock(
                $business_id,
                $end_date,
                $location_id,
                false,
                false,
                [],
                $permitted
            );
        } catch (\Throwable $e) {
        }

        try {
            $sell = $this->transactionUtil->getSellTotals($business_id, null, $end_date, $location_id);
            $out['customer_due'] = (float) ($sell['invoice_due'] ?? 0);
        } catch (\Throwable $e) {
        }

        try {
            $purchase = $this->transactionUtil->getPurchaseTotals($business_id, null, $end_date, $location_id);
            $out['supplier_due'] = (float) ($purchase['purchase_due'] ?? 0);
        } catch (\Throwable $e) {
        }

        try {
            if (Schema::hasTable('accounts') && Schema::hasTable('account_transactions')) {
                $rows = DB::table('accounts')
                    ->leftJoin('account_transactions as at', function ($j) use ($end_date) {
                        $j->on('at.account_id', '=', 'accounts.id')
                            ->whereNull('at.deleted_at')
                            ->whereDate('at.operation_date', '<=', $end_date);
                    })
                    ->where('accounts.business_id', $business_id)
                    ->where('accounts.is_closed', 0)
                    ->whereNull('accounts.deleted_at')
                    ->groupBy('accounts.id', 'accounts.name')
                    ->select(
                        'accounts.id',
                        'accounts.name',
                        DB::raw("COALESCE(SUM(IF(at.type='credit', at.amount, -at.amount)),0) as balance")
                    )
                    ->get();
                $out['account_balances'] = $rows;
                $out['cash_total'] = (float) $rows->sum('balance');
            }
        } catch (\Throwable $e) {
            \Log::warning('BS accounts: '.$e->getMessage());
        }

        $out['total_assets'] = $out['closing_stock'] + $out['customer_due'] + $out['cash_total'];
        $out['total_liabilities'] = $out['supplier_due'];
        $out['equity_approx'] = $out['total_assets'] - $out['total_liabilities'];

        return $out;
    }

    protected function buildCashMovement($business_id, $start, $end, $location_id = null): array
    {
        $in = collect();
        $out = collect();
        try {
            $q = DB::table('transaction_payments as tp')
                ->join('transactions as t', 'tp.transaction_id', '=', 't.id')
                ->where('t.business_id', $business_id)
                ->whereBetween(DB::raw('DATE(COALESCE(tp.paid_on, tp.created_at))'), [$start, $end]);
            if ($location_id) {
                $q->where('t.location_id', $location_id);
            }
            try {
                if (Schema::hasColumn('transaction_payments', 'parent_id')) {
                    $q->whereNull('tp.parent_id');
                }
            } catch (\Throwable $e) {
            }

            $byMethod = $q->select(
                'tp.method',
                DB::raw('COALESCE(t.type, tp.payment_for, "payment") as tx_type'),
                DB::raw('SUM(tp.amount) as total'),
                DB::raw('COUNT(*) as cnt')
            )
                ->groupBy('tp.method', DB::raw('COALESCE(t.type, tp.payment_for, "payment")'))
                ->get();

            $inTypes = ['sell', 'sell_return', 'hms_booking', 'gym_subscription'];
            foreach ($byMethod as $row) {
                $type = $row->tx_type ?? '';
                if (in_array($type, $inTypes, true) || $type === 'payment') {
                    // treat sell as inflow; purchase as outflow
                }
                if (in_array($type, ['purchase', 'expense', 'purchase_return'], true)) {
                    $out->push($row);
                } else {
                    $in->push($row);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Cash movement: '.$e->getMessage());
        }

        return [
            'in' => $in,
            'out' => $out,
            'in_total' => (float) $in->sum('total'),
            'out_total' => (float) $out->sum('total'),
        ];
    }

    /**
     * Bank / M-Pesa reconciliation ledger.
     */
    public function bankMpesaRecon(Request $request)
    {
        if (! $this->canFinance()) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $this->bizId($request);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        extract($this->parseRange($request));
        $method = $request->get('method', 'all'); // all|cash|card|mpesa|custom

        $posPayments = collect();
        try {
            $pq = DB::table('transaction_payments as tp')
                ->join('transactions as t', 'tp.transaction_id', '=', 't.id')
                ->leftJoin('users as u', 'tp.created_by', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->whereBetween(DB::raw('DATE(COALESCE(tp.paid_on, tp.created_at))'), [$start, $end]);
            if ($location_id) {
                $pq->where('t.location_id', $location_id);
            }
            if ($method !== 'all' && $method !== '') {
                if ($method === 'mpesa') {
                    $pq->where(function ($w) {
                        $w->where('tp.method', 'like', '%mpesa%')
                            ->orWhere('tp.method', 'custom_pay_1')
                            ->orWhere('tp.method', 'custom_pay_2')
                            ->orWhere('tp.method', 'custom_pay_3');
                    });
                } else {
                    $pq->where('tp.method', $method);
                }
            }
            try {
                if (Schema::hasColumn('transaction_payments', 'parent_id')) {
                    $pq->whereNull('tp.parent_id');
                }
            } catch (\Throwable $e) {
            }

            $posPayments = $pq->select(
                'tp.id',
                'tp.amount',
                'tp.method',
                'tp.paid_on',
                'tp.created_at',
                'tp.transaction_no',
                'tp.card_transaction_number',
                'tp.cheque_number',
                't.invoice_no',
                't.type as transaction_type',
                't.ref_no',
                DB::raw("TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) as cashier")
            )
                ->orderByDesc(DB::raw('COALESCE(tp.paid_on, tp.created_at)'))
                ->limit(2000)
                ->get();
        } catch (\Throwable $e) {
            \Log::warning('Recon POS payments: '.$e->getMessage());
        }

        $posByMethod = $posPayments->groupBy('method')->map(function ($rows, $m) {
            return (object) [
                'method' => $m,
                'count' => $rows->count(),
                'total' => (float) $rows->sum('amount'),
            ];
        })->values();

        $mpesaRows = collect();
        $mpesaTotal = 0.0;
        try {
            if (Schema::hasTable('mpesa_transactions')) {
                $mq = DB::table('mpesa_transactions')
                    ->where('business_id', $business_id)
                    ->where('status', 'paid')
                    ->whereBetween(DB::raw('DATE(COALESCE(updated_at, created_at))'), [$start, $end]);
                $mpesaRows = $mq->orderByDesc('id')->limit(1000)->get();
                $mpesaTotal = (float) $mpesaRows->sum(function ($r) {
                    return (float) ($r->amount ?? 0);
                });
            }
        } catch (\Throwable $e) {
            \Log::warning('Recon M-Pesa: '.$e->getMessage());
        }

        $accountLedger = collect();
        try {
            if (Schema::hasTable('account_transactions') && Schema::hasTable('accounts')) {
                $accountLedger = DB::table('account_transactions as at')
                    ->join('accounts as a', 'at.account_id', '=', 'a.id')
                    ->where('a.business_id', $business_id)
                    ->whereNull('at.deleted_at')
                    ->whereBetween(DB::raw('DATE(at.operation_date)'), [$start, $end])
                    ->select(
                        'at.id',
                        'at.type',
                        'at.amount',
                        'at.operation_date',
                        'at.note',
                        'at.sub_type',
                        'a.name as account_name'
                    )
                    ->orderByDesc('at.operation_date')
                    ->limit(1000)
                    ->get();
            }
        } catch (\Throwable $e) {
        }

        // Daily POS totals (stronger recon)
        $dailyPos = $posPayments->groupBy(function ($p) {
            $dt = $p->paid_on ?: $p->created_at;

            return $dt ? substr((string) $dt, 0, 10) : 'unknown';
        })->map(function ($rows, $day) {
            return (object) [
                'day' => $day,
                'count' => $rows->count(),
                'total' => (float) $rows->sum('amount'),
                'cash' => (float) $rows->where('method', 'cash')->sum('amount'),
                'mpesa_like' => (float) $rows->filter(function ($r) {
                    $m = strtolower((string) ($r->method ?? ''));

                    return str_contains($m, 'mpesa') || str_contains($m, 'mobile')
                        || in_array($m, ['custom_pay_1', 'custom_pay_2', 'custom_pay_3'], true);
                })->sum('amount'),
                'card' => (float) $rows->where('method', 'card')->sum('amount'),
                'other' => (float) $rows->reject(function ($r) {
                    $m = strtolower((string) ($r->method ?? ''));

                    return $m === 'cash' || $m === 'card'
                        || str_contains($m, 'mpesa') || str_contains($m, 'mobile')
                        || in_array($m, ['custom_pay_1', 'custom_pay_2', 'custom_pay_3'], true);
                })->sum('amount'),
            ];
        })->sortBy('day')->values();

        // C2B M-Pesa if table exists
        $c2bRows = collect();
        $c2bTotal = 0.0;
        try {
            if (Schema::hasTable('mpesa_c2b_payments')) {
                $c2bRows = DB::table('mpesa_c2b_payments')
                    ->where('business_id', $business_id)
                    ->whereBetween(DB::raw('DATE(COALESCE(trans_time, created_at))'), [$start, $end])
                    ->orderByDesc('id')
                    ->limit(1000)
                    ->get();
                $c2bTotal = (float) $c2bRows->sum(function ($r) {
                    return (float) ($r->amount ?? 0);
                });
            }
        } catch (\Throwable $e) {
        }

        // Auto-match M-Pesa module ↔ POS payments
        $match = $this->matchMpesaToPos($posPayments, $mpesaRows);
        $matched = $match['matched'];
        $posUnmatched = $match['pos_unmatched'];
        $mpesaUnmatched = $match['mpesa_unmatched'];

        // Statement lines paste (one amount per line or CSV amount column)
        $statementLinesRaw = (string) $request->get('statement_lines', '');
        $statementLines = $this->parseStatementLines($statementLinesRaw);
        $statementLinesTotal = (float) collect($statementLines)->sum('amount');

        $statementTotal = $request->get('statement_total');
        $statementTotal = $statementTotal !== null && $statementTotal !== ''
            ? (float) str_replace([',', ' '], '', $statementTotal)
            : ($statementLinesTotal > 0 ? $statementLinesTotal : null);
        $posTotal = (float) $posPayments->sum('amount');
        $variance = $statementTotal !== null ? ($statementTotal - $posTotal) : null;

        // Focus filter for mobile money comparison
        $posMpesaLikeTotal = (float) $posPayments->filter(function ($r) {
            $m = strtolower((string) ($r->method ?? ''));

            return str_contains($m, 'mpesa') || str_contains($m, 'mobile')
                || in_array($m, ['custom_pay_1', 'custom_pay_2', 'custom_pay_3'], true);
        })->sum('amount');
        $mpesaGap = $mpesaTotal + $c2bTotal - $posMpesaLikeTotal;

        return view('report.advanced.bank_mpesa_recon', compact(
            'business_locations',
            'start',
            'end',
            'location_id',
            'method',
            'posPayments',
            'posByMethod',
            'posTotal',
            'mpesaRows',
            'mpesaTotal',
            'c2bRows',
            'c2bTotal',
            'accountLedger',
            'statementTotal',
            'statementLinesRaw',
            'statementLines',
            'statementLinesTotal',
            'variance',
            'dailyPos',
            'matched',
            'posUnmatched',
            'mpesaUnmatched',
            'posMpesaLikeTotal',
            'mpesaGap'
        ));
    }

    /**
     * Match M-Pesa STK rows to POS payment rows.
     */
    protected function matchMpesaToPos($posPayments, $mpesaRows): array
    {
        $matched = [];
        $usedPos = [];
        $usedMpesa = [];

        $posList = $posPayments->values();
        $mpesaList = $mpesaRows->values();

        $posKey = function ($p) {
            return strtoupper(trim((string) ($p->transaction_no ?: $p->card_transaction_number ?: '')));
        };

        // 1) Receipt / transaction_no exact match
        foreach ($mpesaList as $mi => $m) {
            $receipt = strtoupper(trim((string) ($m->mpesa_receipt_number ?? '')));
            if ($receipt === '') {
                continue;
            }
            foreach ($posList as $pi => $p) {
                if (isset($usedPos[$pi])) {
                    continue;
                }
                $pk = $posKey($p);
                if ($pk !== '' && ($pk === $receipt || str_contains($pk, $receipt) || str_contains($receipt, $pk))) {
                    $matched[] = (object) [
                        'how' => 'receipt',
                        'pos' => $p,
                        'mpesa' => $m,
                        'amount_pos' => (float) $p->amount,
                        'amount_mpesa' => (float) ($m->amount ?? 0),
                    ];
                    $usedPos[$pi] = true;
                    $usedMpesa[$mi] = true;
                    break;
                }
            }
        }

        // 2) Linked transaction_id on mpesa row
        foreach ($mpesaList as $mi => $m) {
            if (isset($usedMpesa[$mi]) || empty($m->transaction_id)) {
                continue;
            }
            // find any POS payment on same sell transaction by invoice if we can — skip if no join
            // Mark linked if amount matches any unused POS on same day
            $mAmt = round((float) ($m->amount ?? 0), 2);
            $mDay = substr((string) ($m->updated_at ?? $m->created_at ?? ''), 0, 10);
            foreach ($posList as $pi => $p) {
                if (isset($usedPos[$pi])) {
                    continue;
                }
                $pDay = substr((string) ($p->paid_on ?: $p->created_at), 0, 10);
                if (round((float) $p->amount, 2) === $mAmt && $pDay === $mDay) {
                    $matched[] = (object) [
                        'how' => 'amount+date+link',
                        'pos' => $p,
                        'mpesa' => $m,
                        'amount_pos' => (float) $p->amount,
                        'amount_mpesa' => $mAmt,
                    ];
                    $usedPos[$pi] = true;
                    $usedMpesa[$mi] = true;
                    break;
                }
            }
        }

        // 3) Amount + same calendar day (remaining)
        foreach ($mpesaList as $mi => $m) {
            if (isset($usedMpesa[$mi])) {
                continue;
            }
            $mAmt = round((float) ($m->amount ?? 0), 2);
            $mDay = substr((string) ($m->updated_at ?? $m->created_at ?? ''), 0, 10);
            foreach ($posList as $pi => $p) {
                if (isset($usedPos[$pi])) {
                    continue;
                }
                $pDay = substr((string) ($p->paid_on ?: $p->created_at), 0, 10);
                if (round((float) $p->amount, 2) === $mAmt && $pDay === $mDay) {
                    $matched[] = (object) [
                        'how' => 'amount+date',
                        'pos' => $p,
                        'mpesa' => $m,
                        'amount_pos' => (float) $p->amount,
                        'amount_mpesa' => $mAmt,
                    ];
                    $usedPos[$pi] = true;
                    $usedMpesa[$mi] = true;
                    break;
                }
            }
        }

        $posUnmatched = $posList->filter(function ($p, $pi) use ($usedPos) {
            return ! isset($usedPos[$pi]);
        })->values();

        $mpesaUnmatched = $mpesaList->filter(function ($m, $mi) use ($usedMpesa) {
            return ! isset($usedMpesa[$mi]);
        })->values();

        return compact('matched', 'posUnmatched', 'mpesaUnmatched');
    }

    /**
     * Parse pasted bank/M-Pesa statement lines into amounts.
     */
    protected function parseStatementLines(string $raw): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $out = [];
        foreach ($lines as $i => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            // Prefer last number on the line (common CSV/export style)
            if (preg_match_all('/-?\d{1,3}(?:,\d{3})*(?:\.\d+)?|-?\d+(?:\.\d+)?/', $line, $m)) {
                $num = end($m[0]);
                $amount = (float) str_replace(',', '', $num);
                $out[] = [
                    'line' => $i + 1,
                    'text' => $line,
                    'amount' => $amount,
                ];
            }
        }

        return $out;
    }

    /**
     * Month-end pack: one printable page — P&L + stock + credit + top products.
     */
    public function monthEndPack(Request $request)
    {
        if (! $this->canFinance()) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $this->bizId($request);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $location_id = $request->get('location_id') ?: null;

        // Default: current calendar month
        $month = $request->get('month', Carbon::today()->format('Y-m'));
        try {
            $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable $e) {
            $monthStart = Carbon::today()->startOfMonth();
            $month = $monthStart->format('Y-m');
        }
        $start = $monthStart->format('Y-m-d');
        $end = $monthStart->copy()->endOfMonth()->format('Y-m-d');
        if ($end > Carbon::today()->format('Y-m-d')) {
            $end = Carbon::today()->format('Y-m-d');
        }

        // P&L
        $pl = [];
        $cogs = 0.0;
        try {
            $permitted = auth()->user()->permitted_locations();
            $pl = $this->transactionUtil->getProfitLossDetails(
                $business_id,
                $location_id,
                $start,
                $end,
                null,
                $permitted
            );
            $cogs = ((float) ($pl['opening_stock'] ?? 0) + (float) ($pl['total_purchase'] ?? 0))
                - (float) ($pl['closing_stock'] ?? 0);
        } catch (\Throwable $e) {
            \Log::warning('Month-end P&L: '.$e->getMessage());
            $pl = [
                'total_sell' => 0, 'total_purchase' => 0, 'opening_stock' => 0, 'closing_stock' => 0,
                'gross_profit' => 0, 'net_profit' => 0, 'total_expense' => 0,
            ];
        }

        // Payments by method for the month
        $paymentsByMethod = collect();
        try {
            $pq = DB::table('transaction_payments as tp')
                ->join('transactions as t', 'tp.transaction_id', '=', 't.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereBetween(DB::raw('DATE(COALESCE(tp.paid_on, tp.created_at))'), [$start, $end]);
            if ($location_id) {
                $pq->where('t.location_id', $location_id);
            }
            try {
                if (Schema::hasColumn('transaction_payments', 'parent_id')) {
                    $pq->whereNull('tp.parent_id');
                }
            } catch (\Throwable $e) {
            }
            $paymentsByMethod = $pq->select('tp.method', DB::raw('SUM(tp.amount) as total'), DB::raw('COUNT(*) as cnt'))
                ->groupBy('tp.method')
                ->get();
        } catch (\Throwable $e) {
        }

        // Stock value (current — as-of month end approximation = current qty)
        $stock = ['qty' => 0, 'value_cost' => 0, 'value_sell' => 0, 'skus' => 0];
        try {
            $sq = DB::table('variation_location_details as vld')
                ->join('variations as v', 'v.id', '=', 'vld.variation_id')
                ->join('products as p', 'p.id', '=', 'vld.product_id')
                ->where('p.business_id', $business_id)
                ->where('p.enable_stock', 1)
                ->whereNull('v.deleted_at')
                ->where('vld.qty_available', '>', 0.0001);
            if ($location_id) {
                $sq->where('vld.location_id', $location_id);
            }
            $agg = $sq->selectRaw(
                'COUNT(*) as skus,
                 COALESCE(SUM(vld.qty_available),0) as qty,
                 COALESCE(SUM(vld.qty_available * COALESCE(v.dpp_inc_tax, v.default_purchase_price, 0)),0) as value_cost,
                 COALESCE(SUM(vld.qty_available * COALESCE(v.sell_price_inc_tax, v.default_sell_price, 0)),0) as value_sell'
            )->first();
            if ($agg) {
                $stock = [
                    'qty' => (float) $agg->qty,
                    'value_cost' => (float) $agg->value_cost,
                    'value_sell' => (float) $agg->value_sell,
                    'skus' => (int) $agg->skus,
                ];
            }
        } catch (\Throwable $e) {
            \Log::warning('Month-end stock: '.$e->getMessage());
        }

        // Customer credit outstanding (open AR — not limited to month sales)
        $credit = [
            'invoices' => 0,
            'total_due' => 0.0,
            'age_0_30' => 0.0,
            'age_31_60' => 0.0,
            'age_61_90' => 0.0,
            'age_90_plus' => 0.0,
            'top' => collect(),
        ];
        try {
            $cq = DB::table('transactions as t')
                ->leftJoin('contacts as c', 't.contact_id', '=', 'c.id')
                ->leftJoin(DB::raw('(SELECT transaction_id, SUM(IF(is_return = 1, -1 * amount, amount)) as total_paid FROM transaction_payments GROUP BY transaction_id) as tp'), 't.id', '=', 'tp.transaction_id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereIn('t.payment_status', ['due', 'partial']);
            if ($location_id) {
                $cq->where('t.location_id', $location_id);
            }
            $creditRows = $cq->select(
                't.id',
                'c.name as customer_name',
                't.invoice_no',
                't.transaction_date',
                't.final_total',
                DB::raw('COALESCE(tp.total_paid, 0) as total_paid'),
                DB::raw('(t.final_total - COALESCE(tp.total_paid, 0)) as total_due'),
                DB::raw('DATEDIFF(CURDATE(), DATE(t.transaction_date)) as days_overdue')
            )
                ->whereRaw('(t.final_total - COALESCE(tp.total_paid, 0)) > 0.009')
                ->orderByDesc(DB::raw('(t.final_total - COALESCE(tp.total_paid, 0))'))
                ->limit(500)
                ->get();

            $credit['invoices'] = $creditRows->count();
            $credit['total_due'] = (float) $creditRows->sum('total_due');
            foreach ($creditRows as $r) {
                $d = (int) $r->days_overdue;
                $due = (float) $r->total_due;
                if ($d <= 30) {
                    $credit['age_0_30'] += $due;
                } elseif ($d <= 60) {
                    $credit['age_31_60'] += $due;
                } elseif ($d <= 90) {
                    $credit['age_61_90'] += $due;
                } else {
                    $credit['age_90_plus'] += $due;
                }
            }
            $credit['top'] = $creditRows->take(15)->values();
        } catch (\Throwable $e) {
            \Log::warning('Month-end credit: '.$e->getMessage());
        }

        // Top products sold in month
        $topProducts = collect();
        try {
            $tq = DB::table('transaction_sell_lines as tsl')
                ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
                ->join('products as p', 'tsl.product_id', '=', 'p.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereBetween('t.transaction_date', [$start.' 00:00:00', $end.' 23:59:59']);
            if ($location_id) {
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
                ->limit(20)
                ->get();
        } catch (\Throwable $e) {
            \Log::warning('Month-end top products: '.$e->getMessage());
        }

        $businessName = session('business.name') ?: 'Business';
        $generatedAt = Carbon::now()->format('Y-m-d H:i');

        return view('report.advanced.month_end_pack', compact(
            'business_locations',
            'location_id',
            'month',
            'start',
            'end',
            'pl',
            'cogs',
            'paymentsByMethod',
            'stock',
            'credit',
            'topProducts',
            'businessName',
            'generatedAt'
        ));
    }

    /**
     * Multi-period dashboard (last N months).
     */
    public function multiPeriodDashboard(Request $request)
    {
        if (! $this->canFinance()) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $this->bizId($request);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $location_id = $request->get('location_id') ?: null;
        $months = max(3, min(12, (int) $request->get('months', 6)));

        $periods = [];
        $cursor = Carbon::today()->startOfMonth();
        for ($i = $months - 1; $i >= 0; $i--) {
            $m = $cursor->copy()->subMonths($i);
            $start = $m->copy()->startOfMonth()->format('Y-m-d');
            $end = $m->copy()->endOfMonth()->format('Y-m-d');
            if ($end > Carbon::today()->format('Y-m-d')) {
                $end = Carbon::today()->format('Y-m-d');
            }

            $sales = 0.0;
            $purchases = 0.0;
            $expenses = 0.0;
            $gross = 0.0;
            $net = 0.0;
            $invoices = 0;

            try {
                $sq = DB::table('transactions')
                    ->where('business_id', $business_id)
                    ->where('type', 'sell')
                    ->where('status', 'final')
                    ->whereBetween('transaction_date', [$start.' 00:00:00', $end.' 23:59:59']);
                if ($location_id) {
                    $sq->where('location_id', $location_id);
                }
                $agg = $sq->selectRaw('COUNT(*) as cnt, COALESCE(SUM(final_total),0) as total')->first();
                $sales = (float) ($agg->total ?? 0);
                $invoices = (int) ($agg->cnt ?? 0);
            } catch (\Throwable $e) {
            }

            try {
                $pq = DB::table('transactions')
                    ->where('business_id', $business_id)
                    ->where('type', 'purchase')
                    ->whereBetween('transaction_date', [$start.' 00:00:00', $end.' 23:59:59']);
                if ($location_id) {
                    $pq->where('location_id', $location_id);
                }
                $purchases = (float) $pq->sum('final_total');
            } catch (\Throwable $e) {
            }

            try {
                $eq = DB::table('transactions')
                    ->where('business_id', $business_id)
                    ->where('type', 'expense')
                    ->whereBetween('transaction_date', [$start.' 00:00:00', $end.' 23:59:59']);
                if ($location_id) {
                    $eq->where('location_id', $location_id);
                }
                $expenses = (float) $eq->sum('final_total');
            } catch (\Throwable $e) {
            }

            try {
                $permitted = auth()->user()->permitted_locations();
                $pl = $this->transactionUtil->getProfitLossDetails(
                    $business_id,
                    $location_id,
                    $start,
                    $end,
                    null,
                    $permitted
                );
                $gross = (float) ($pl['gross_profit'] ?? 0);
                $net = (float) ($pl['net_profit'] ?? 0);
            } catch (\Throwable $e) {
            }

            $periods[] = [
                'label' => $m->format('M Y'),
                'start' => $start,
                'end' => $end,
                'sales' => $sales,
                'purchases' => $purchases,
                'expenses' => $expenses,
                'gross' => $gross,
                'net' => $net,
                'invoices' => $invoices,
                'avg_ticket' => $invoices > 0 ? $sales / $invoices : 0,
            ];
        }

        $totals = [
            'sales' => array_sum(array_column($periods, 'sales')),
            'purchases' => array_sum(array_column($periods, 'purchases')),
            'expenses' => array_sum(array_column($periods, 'expenses')),
            'gross' => array_sum(array_column($periods, 'gross')),
            'net' => array_sum(array_column($periods, 'net')),
            'invoices' => array_sum(array_column($periods, 'invoices')),
        ];

        return view('report.advanced.multi_period', compact(
            'business_locations',
            'location_id',
            'months',
            'periods',
            'totals'
        ));
    }

    /**
     * Margin / discount abuse.
     */
    public function discountAbuse(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view') && ! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $this->bizId($request);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        extract($this->parseRange($request));
        $min_discount_pct = (float) $request->get('min_discount_pct', 5);
        $min_amount = (float) $request->get('min_amount', 0);

        $invoiceDiscounts = collect();
        $lineDiscounts = collect();
        $byCashier = collect();

        try {
            $iq = DB::table('transactions as t')
                ->leftJoin('users as u', 't.created_by', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereBetween('t.transaction_date', [$start.' 00:00:00', $end.' 23:59:59'])
                ->where(function ($w) use ($min_discount_pct, $min_amount) {
                    $w->where(function ($x) use ($min_discount_pct) {
                        $x->where('t.discount_type', 'percentage')
                            ->where('t.discount_amount', '>=', $min_discount_pct);
                    })->orWhere(function ($x) use ($min_amount) {
                        $x->where(function ($y) {
                            $y->where('t.discount_type', 'fixed')
                                ->orWhereNull('t.discount_type');
                        })->where('t.discount_amount', '>', $min_amount);
                    })->orWhere('t.discount_amount', '>', 0);
                });
            if ($location_id) {
                $iq->where('t.location_id', $location_id);
            }

            $invoiceDiscounts = $iq->select(
                't.id',
                't.invoice_no',
                't.transaction_date',
                't.total_before_tax',
                't.discount_type',
                't.discount_amount',
                't.final_total',
                DB::raw("TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) as cashier")
            )
                ->orderByDesc('t.discount_amount')
                ->limit(500)
                ->get()
                ->map(function ($r) {
                    $base = (float) $r->total_before_tax;
                    $disc = (float) $r->discount_amount;
                    if ($r->discount_type === 'percentage') {
                        $r->discount_value = $base * $disc / 100;
                        $r->discount_pct = $disc;
                    } else {
                        $r->discount_value = $disc;
                        $r->discount_pct = $base > 0 ? ($disc / $base * 100) : 0;
                    }

                    return $r;
                })
                ->filter(function ($r) use ($min_discount_pct) {
                    return (float) $r->discount_pct >= $min_discount_pct || (float) $r->discount_value > 0;
                })
                ->values();
        } catch (\Throwable $e) {
            \Log::warning('Discount invoice: '.$e->getMessage());
        }

        try {
            $lq = DB::table('transaction_sell_lines as tsl')
                ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
                ->join('products as p', 'tsl.product_id', '=', 'p.id')
                ->leftJoin('users as u', 't.created_by', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereBetween('t.transaction_date', [$start.' 00:00:00', $end.' 23:59:59'])
                ->where(function ($w) {
                    $w->where('tsl.line_discount_amount', '>', 0)
                        ->orWhereRaw('tsl.unit_price_before_discount > tsl.unit_price');
                });
            if ($location_id) {
                $lq->where('t.location_id', $location_id);
            }

            $lineDiscounts = $lq->select(
                't.invoice_no',
                't.transaction_date',
                'p.name as product_name',
                'tsl.quantity',
                'tsl.unit_price_before_discount',
                'tsl.unit_price',
                'tsl.line_discount_type',
                'tsl.line_discount_amount',
                DB::raw("TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) as cashier")
            )
                ->orderByDesc('tsl.line_discount_amount')
                ->limit(500)
                ->get();
        } catch (\Throwable $e) {
            \Log::warning('Discount lines: '.$e->getMessage());
        }

        $byCashier = $invoiceDiscounts->groupBy(function ($r) {
            return $r->cashier ?: '—';
        })->map(function ($rows, $name) {
            return (object) [
                'cashier' => $name,
                'invoices' => $rows->count(),
                'discount_value' => (float) $rows->sum('discount_value'),
                'sales' => (float) $rows->sum('final_total'),
            ];
        })->sortByDesc('discount_value')->values();

        $totalDiscountValue = (float) $invoiceDiscounts->sum('discount_value');

        return view('report.advanced.discount_abuse', compact(
            'business_locations',
            'start',
            'end',
            'location_id',
            'min_discount_pct',
            'min_amount',
            'invoiceDiscounts',
            'lineDiscounts',
            'byCashier',
            'totalDiscountValue'
        ));
    }

    /**
     * FEFO / batch compliance log.
     */
    public function fefoCompliance(Request $request)
    {
        if (! $this->canStock()) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $this->bizId($request);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        extract($this->parseRange($request));
        $only_flagged = $request->get('only_flagged', '1') === '1';

        $rows = collect();
        try {
            // Sold lines mapped to purchase batches
            $sql = "
                SELECT
                    t.invoice_no,
                    t.transaction_date,
                    p.name AS product_name,
                    p.sku,
                    v.sub_sku,
                    tsl.quantity AS sold_qty,
                    pl.lot_number,
                    pl.exp_date AS sold_batch_exp,
                    older.older_exp,
                    older.older_lot,
                    older.older_free_qty,
                    CASE
                        WHEN older.older_exp IS NOT NULL AND pl.exp_date IS NOT NULL
                             AND older.older_exp < pl.exp_date
                        THEN 1 ELSE 0
                    END AS fefo_breach
                FROM transaction_sell_lines tsl
                JOIN transactions t ON t.id = tsl.transaction_id
                JOIN products p ON p.id = tsl.product_id
                JOIN variations v ON v.id = tsl.variation_id
                LEFT JOIN transaction_sell_lines_purchase_lines tsp ON tsp.sell_line_id = tsl.id
                LEFT JOIN purchase_lines pl ON pl.id = tsp.purchase_line_id
                LEFT JOIN (
                    SELECT
                        pl2.variation_id,
                        t2.location_id,
                        MIN(pl2.exp_date) AS older_exp,
                        SUBSTRING_INDEX(GROUP_CONCAT(pl2.lot_number ORDER BY pl2.exp_date ASC SEPARATOR '||'), '||', 1) AS older_lot,
                        SUM(
                            pl2.quantity
                            - COALESCE(pl2.quantity_sold,0)
                            - COALESCE(pl2.quantity_adjusted,0)
                            - COALESCE(pl2.quantity_returned,0)
                            - COALESCE(pl2.mfg_quantity_used,0)
                        ) AS older_free_qty
                    FROM purchase_lines pl2
                    JOIN transactions t2 ON t2.id = pl2.transaction_id
                    WHERE t2.business_id = ?
                      AND t2.status = 'received'
                      AND pl2.exp_date IS NOT NULL
                      AND (
                            pl2.quantity
                            - COALESCE(pl2.quantity_sold,0)
                            - COALESCE(pl2.quantity_adjusted,0)
                            - COALESCE(pl2.quantity_returned,0)
                            - COALESCE(pl2.mfg_quantity_used,0)
                          ) > 0.0001
                    GROUP BY pl2.variation_id, t2.location_id
                ) older ON older.variation_id = tsl.variation_id AND older.location_id = t.location_id
                WHERE t.business_id = ?
                  AND t.type = 'sell'
                  AND t.status = 'final'
                  AND t.transaction_date BETWEEN ? AND ?
            ";
            $params = [$business_id, $business_id, $start.' 00:00:00', $end.' 23:59:59'];
            if ($location_id) {
                $sql .= ' AND t.location_id = ? ';
                $params[] = $location_id;
            }
            $sql .= ' ORDER BY t.transaction_date DESC LIMIT 2000 ';

            $rows = collect(DB::select($sql, $params));
            if ($only_flagged) {
                $rows = $rows->filter(function ($r) {
                    return ! empty($r->fefo_breach);
                })->values();
            }
            $rows = $rows->take(1000)->values();
        } catch (\Throwable $e) {
            \Log::warning('FEFO report: '.$e->getMessage());
            // Fallback: simple sold lots list
            try {
                $fq = DB::table('transaction_sell_lines as tsl')
                    ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
                    ->join('products as p', 'tsl.product_id', '=', 'p.id')
                    ->leftJoin('transaction_sell_lines_purchase_lines as tsp', 'tsp.sell_line_id', '=', 'tsl.id')
                    ->leftJoin('purchase_lines as pl', 'pl.id', '=', 'tsp.purchase_line_id')
                    ->where('t.business_id', $business_id)
                    ->where('t.type', 'sell')
                    ->where('t.status', 'final')
                    ->whereBetween('t.transaction_date', [$start.' 00:00:00', $end.' 23:59:59']);
                if ($location_id) {
                    $fq->where('t.location_id', $location_id);
                }
                $rows = $fq->select(
                    't.invoice_no',
                    't.transaction_date',
                    'p.name as product_name',
                    'p.sku',
                    'tsl.quantity as sold_qty',
                    'pl.lot_number',
                    'pl.exp_date as sold_batch_exp',
                    DB::raw('NULL as older_exp'),
                    DB::raw('NULL as older_lot'),
                    DB::raw('0 as older_free_qty'),
                    DB::raw('0 as fefo_breach')
                )->orderByDesc('t.transaction_date')->limit(1000)->get();
            } catch (\Throwable $e2) {
            }
        }

        $breachCount = $rows->where('fefo_breach', 1)->count();

        return view('report.advanced.fefo_compliance', compact(
            'business_locations',
            'start',
            'end',
            'location_id',
            'only_flagged',
            'rows',
            'breachCount'
        ));
    }

    /**
     * Audit / activity log export.
     */
    public function auditLogExport(Request $request)
    {
        if (! auth()->user()->can('sell.view') && ! auth()->user()->can('account.access') && ! auth()->user()->can('profit_loss_report.view')) {
            // fall back: admins often have business settings
            if (! auth()->user()->can('business_settings.access') && ! auth()->user()->can('user.view')) {
                abort(403, 'Unauthorized action.');
            }
        }

        $business_id = $this->bizId($request);
        extract($this->parseRange($request));
        $user_id = $request->get('user_id') ?: null;
        $log_name = $request->get('log_name') ?: null;
        $export = $request->get('export');

        $rows = collect();
        try {
            if (Schema::hasTable('activity_log')) {
                $q = DB::table('activity_log as al')
                    ->leftJoin('users as u', 'al.causer_id', '=', 'u.id')
                    ->where(function ($w) use ($business_id) {
                        $w->where('al.business_id', $business_id)
                            ->orWhereNull('al.business_id');
                    })
                    ->whereBetween(DB::raw('DATE(al.created_at)'), [$start, $end]);
                if ($user_id) {
                    $q->where('al.causer_id', $user_id);
                }
                if ($log_name) {
                    $q->where('al.log_name', $log_name);
                }
                $rows = $q->select(
                    'al.id',
                    'al.log_name',
                    'al.description',
                    'al.subject_type',
                    'al.subject_id',
                    'al.causer_id',
                    'al.created_at',
                    'al.properties',
                    DB::raw("TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) as user_name")
                )
                    ->orderByDesc('al.created_at')
                    ->limit(3000)
                    ->get();
            }
        } catch (\Throwable $e) {
            \Log::warning('Audit log: '.$e->getMessage());
        }

        if ($export === 'csv') {
            $filename = 'audit_log_'.$start.'_'.$end.'.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ];
            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID', 'When', 'User', 'Log', 'Description', 'Subject', 'Subject ID', 'Properties']);
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->id,
                        $r->created_at,
                        $r->user_name,
                        $r->log_name,
                        $r->description,
                        class_basename((string) $r->subject_type),
                        $r->subject_id,
                        is_string($r->properties) ? $r->properties : json_encode($r->properties),
                    ]);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        $users = [];
        try {
            $users = DB::table('users')
                ->where('business_id', $business_id)
                ->select('id', DB::raw("TRIM(CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,''))) as name"))
                ->orderBy('first_name')
                ->pluck('name', 'id')
                ->toArray();
        } catch (\Throwable $e) {
        }

        $logNames = $rows->pluck('log_name')->unique()->filter()->values();

        return view('report.advanced.audit_export', compact(
            'start',
            'end',
            'user_id',
            'log_name',
            'rows',
            'users',
            'logNames'
        ));
    }

    /**
     * Formal inventory valuation.
     */
    public function inventoryValuation(Request $request)
    {
        if (! $this->canStock()) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $this->bizId($request);
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $location_id = $request->get('location_id');
        if (empty($location_id)) {
            $location_id = array_key_first($business_locations->toArray() ?? []) ?: null;
        }
        $as_of = $request->get('as_of', Carbon::today()->format('Y-m-d'));
        try {
            $as_of = Carbon::parse($as_of)->format('Y-m-d');
        } catch (\Throwable $e) {
            $as_of = Carbon::today()->format('Y-m-d');
        }
        $basis = $request->get('basis', 'purchase'); // purchase|sell

        $rows = collect();
        $totals = ['qty' => 0, 'value_cost' => 0, 'value_sell' => 0];

        if ($location_id) {
            try {
                $rows = DB::table('variation_location_details as vld')
                    ->join('variations as v', 'v.id', '=', 'vld.variation_id')
                    ->join('products as p', 'p.id', '=', 'vld.product_id')
                    ->where('vld.location_id', $location_id)
                    ->where('p.business_id', $business_id)
                    ->where('p.enable_stock', 1)
                    ->whereNull('v.deleted_at')
                    ->where('vld.qty_available', '>', 0.0001)
                    ->select(
                        'p.id as product_id',
                        'p.name',
                        'p.sku',
                        'v.id as variation_id',
                        'v.sub_sku',
                        'v.default_purchase_price',
                        'v.dpp_inc_tax',
                        'v.default_sell_price',
                        'v.sell_price_inc_tax',
                        'vld.qty_available'
                    )
                    ->orderBy('p.name')
                    ->limit(5000)
                    ->get()
                    ->map(function ($r) {
                        $cost = (float) ($r->dpp_inc_tax ?? $r->default_purchase_price ?? 0);
                        $sell = (float) ($r->sell_price_inc_tax ?? $r->default_sell_price ?? 0);
                        $qty = (float) $r->qty_available;
                        $r->unit_cost = $cost;
                        $r->unit_sell = $sell;
                        $r->value_cost = $qty * $cost;
                        $r->value_sell = $qty * $sell;

                        return $r;
                    });

                $totals['qty'] = (float) $rows->sum('qty_available');
                $totals['value_cost'] = (float) $rows->sum('value_cost');
                $totals['value_sell'] = (float) $rows->sum('value_sell');
            } catch (\Throwable $e) {
                \Log::warning('Inventory valuation: '.$e->getMessage());
            }
        }

        // Sort by selected basis value
        if ($basis === 'sell') {
            $rows = $rows->sortByDesc('value_sell')->values();
        } else {
            $rows = $rows->sortByDesc('value_cost')->values();
        }

        return view('report.advanced.inventory_valuation', compact(
            'business_locations',
            'location_id',
            'as_of',
            'basis',
            'rows',
            'totals'
        ));
    }

    /**
     * Supplier payables ageing (mirror of customer credit).
     */
    public function supplierPayables(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')
            && ! auth()->user()->can('supplier_report.view')
            && ! auth()->user()->can('contacts_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $this->bizId($request);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        extract($this->parseRange($request));

        $rows = collect();
        $totals = [
            'total_due' => 0.0,
            'age_0_30' => 0.0,
            'age_31_60' => 0.0,
            'age_61_90' => 0.0,
            'age_90_plus' => 0.0,
            'count' => 0,
        ];

        try {
            $q = DB::table('transactions as t')
                ->leftJoin('contacts as c', 't.contact_id', '=', 'c.id')
                ->leftJoin(DB::raw('(SELECT transaction_id, SUM(IF(is_return = 1, -1 * amount, amount)) as total_paid FROM transaction_payments GROUP BY transaction_id) as tp'), 't.id', '=', 'tp.transaction_id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'purchase')
                ->whereIn('t.status', ['received', 'pending', 'ordered'])
                ->whereIn('t.payment_status', ['due', 'partial'])
                ->whereRaw('(t.final_total - COALESCE(tp.total_paid, 0)) > 0.009');
            if ($location_id) {
                $q->where('t.location_id', $location_id);
            }

            $rows = $q->select(
                't.id',
                'c.name as supplier_name',
                'c.mobile',
                't.ref_no',
                't.transaction_date',
                't.final_total',
                't.payment_status',
                DB::raw('COALESCE(tp.total_paid, 0) as total_paid'),
                DB::raw('(t.final_total - COALESCE(tp.total_paid, 0)) as total_due'),
                DB::raw('DATEDIFF(CURDATE(), DATE(t.transaction_date)) as days_overdue')
            )
                ->orderByDesc(DB::raw('(t.final_total - COALESCE(tp.total_paid, 0))'))
                ->limit(1000)
                ->get();

            $totals['count'] = $rows->count();
            $totals['total_due'] = (float) $rows->sum('total_due');
            foreach ($rows as $r) {
                $d = (int) $r->days_overdue;
                $due = (float) $r->total_due;
                if ($d <= 30) {
                    $totals['age_0_30'] += $due;
                } elseif ($d <= 60) {
                    $totals['age_31_60'] += $due;
                } elseif ($d <= 90) {
                    $totals['age_61_90'] += $due;
                } else {
                    $totals['age_90_plus'] += $due;
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Supplier payables: '.$e->getMessage());
        }

        return view('report.advanced.supplier_payables', compact(
            'business_locations',
            'start',
            'end',
            'location_id',
            'rows',
            'totals'
        ));
    }
}
