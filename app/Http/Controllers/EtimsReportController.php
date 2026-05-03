<?php

namespace App\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Transaction;
use App\Utils\DigitaxService;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EtimsReportController extends Controller
{
    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /* ──────────────────────────────────────────
     |  MAIN DASHBOARD
     ─────────────────────────────────────────── */
    public function index()
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);

        // Overall stats
        $stats = $this->_stats($business_id);

        // Last 7 days trend
        $trend = $this->_trend($business_id, 7);

        // Tax category breakdown
        $tax_breakdown = $this->_taxCategoryBreakdown($business_id);

        return view('etims.index', compact('stats', 'trend', 'tax_breakdown', 'business_locations'));
    }

    /* ──────────────────────────────────────────
     |  SALES DATATABLE  (AJAX)
     ─────────────────────────────────────────── */
    public function salesData()
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403);
        }

        $business_id = request()->session()->get('user.business_id');

        $q = Transaction::where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->leftJoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
            ->leftJoin('business_locations as bl', 'transactions.location_id', '=', 'bl.id')
            ->select([
                'transactions.id',
                'transactions.transaction_date',
                'transactions.invoice_no',
                'c.name as customer_name',
                'bl.name as location_name',
                'transactions.final_total',
                'transactions.etims_invoice_number',
                'transactions.etims_sync_status',
                'transactions.etims_sync_error',
                'transactions.etims_synced_at',
            ]);

        if (!empty(request()->sync_status)) {
            $q->where('transactions.etims_sync_status', request()->sync_status);
        }
        if (!empty(request()->location_id)) {
            $q->where('transactions.location_id', request()->location_id);
        }
        if (!empty(request()->start_date)) {
            $q->whereDate('transactions.transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $q->whereDate('transactions.transaction_date', '<=', request()->end_date);
        }

        return DataTables::of($q)
            ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
            ->editColumn('final_total', '<span class="display_currency" data-currency_symbol="true">{{$final_total}}</span>')
            ->editColumn('etims_sync_status', function ($row) {
                return $this->_statusBadge($row->etims_sync_status, $row->etims_sync_error, $row->etims_synced_at);
            })
            ->addColumn('action', function ($row) {
                $html = '';
                if ($row->etims_sync_status !== 'success') {
                    $url = action([EtimsReportController::class, 'syncInvoice'], [$row->id]);
                    $html .= '<button data-href="'.$url.'" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary sync-single-btn"><i class="fa fa-refresh"></i> Retry</button>';
                }
                return $html;
            })
            ->rawColumns(['final_total', 'etims_sync_status', 'action'])
            ->make(true);
    }

    /* ──────────────────────────────────────────
     |  PURCHASES DATATABLE  (AJAX)
     ─────────────────────────────────────────── */
    public function purchasesData()
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403);
        }

        $business_id = request()->session()->get('user.business_id');

        $q = Transaction::where('transactions.business_id', $business_id)
            ->whereIn('transactions.type', ['purchase', 'purchase_return'])
            ->where('transactions.status', 'received')
            ->leftJoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
            ->leftJoin('business_locations as bl', 'transactions.location_id', '=', 'bl.id')
            ->select([
                'transactions.id',
                'transactions.transaction_date',
                'transactions.ref_no',
                'transactions.type',
                'c.name as supplier_name',
                'bl.name as location_name',
                'transactions.final_total',
                'transactions.etims_invoice_number',
                'transactions.etims_sync_status',
                'transactions.etims_sync_error',
                'transactions.etims_synced_at',
            ]);

        if (!empty(request()->sync_status)) {
            $q->where('transactions.etims_sync_status', request()->sync_status);
        }
        if (!empty(request()->location_id)) {
            $q->where('transactions.location_id', request()->location_id);
        }
        if (!empty(request()->start_date)) {
            $q->whereDate('transactions.transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $q->whereDate('transactions.transaction_date', '<=', request()->end_date);
        }

        return DataTables::of($q)
            ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
            ->editColumn('type', function ($row) {
                return $row->type === 'purchase_return'
                    ? '<span class="label label-warning">Return</span>'
                    : '<span class="label label-info">Purchase</span>';
            })
            ->editColumn('final_total', '<span class="display_currency" data-currency_symbol="true">{{$final_total}}</span>')
            ->editColumn('etims_sync_status', function ($row) {
                return $this->_statusBadge($row->etims_sync_status, $row->etims_sync_error, $row->etims_synced_at);
            })
            ->addColumn('action', function ($row) {
                $html = '';
                if ($row->etims_sync_status !== 'success') {
                    $url = action([EtimsReportController::class, 'syncInvoice'], [$row->id]);
                    $html .= '<button data-href="'.$url.'" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary sync-single-btn"><i class="fa fa-refresh"></i> Retry</button>';
                }
                return $html;
            })
            ->rawColumns(['type', 'final_total', 'etims_sync_status', 'action'])
            ->make(true);
    }

    /* ──────────────────────────────────────────
     |  ANALYTICS  (AJAX — chart data)
     ─────────────────────────────────────────── */
    public function analytics()
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403);
        }

        $business_id = request()->session()->get('user.business_id');
        $days = (int) (request()->days ?? 30);
        $days = min($days, 365);

        return response()->json([
            'trend'         => $this->_trend($business_id, $days),
            'stats'         => $this->_stats($business_id),
            'tax_breakdown' => $this->_taxCategoryBreakdown($business_id),
        ]);
    }

    /* ──────────────────────────────────────────
     |  SYNC SINGLE INVOICE
     ─────────────────────────────────────────── */
    public function syncInvoice($id)
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403);
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $transaction = Transaction::where('business_id', $business_id)->findOrFail($id);
            $business    = Business::find($business_id);

            if (empty($business->digitax_api_key)) {
                return response()->json(['success' => false, 'msg' => 'Digitax API Key not configured. Go to eTIMS Settings.']);
            }

            $digitaxService = new DigitaxService();
            $digitaxService->setApiKey($business->digitax_api_key);

            $result = in_array($transaction->type, ['purchase', 'purchase_return'])
                ? $digitaxService->createPurchase($transaction)
                : $digitaxService->createSale($transaction);

            return response()->json($result['success']
                ? ['success' => true,  'msg' => 'Invoice synced successfully.']
                : ['success' => false, 'msg' => 'Sync failed: '.($result['error'] ?? 'Unknown error')]
            );

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }

    /* ──────────────────────────────────────────
     |  SYNC ALL PENDING + FAILED
     ─────────────────────────────────────────── */
    public function syncAll()
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403);
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $business    = Business::find($business_id);

            if (empty($business->digitax_api_key)) {
                return response()->json(['success' => false, 'msg' => 'Digitax API Key not configured.']);
            }

            $type   = request()->type ?? 'sell';  // 'sell' or 'purchase'
            $status = request()->status ?? 'received';

            $pending = Transaction::where('business_id', $business_id)
                ->where('type', $type)
                ->where('status', $type === 'sell' ? 'final' : 'received')
                ->whereIn('etims_sync_status', ['pending', 'failed'])
                ->get();

            if ($pending->isEmpty()) {
                return response()->json(['success' => true, 'msg' => 'No pending or failed invoices to sync.', 'count' => 0]);
            }

            $digitaxService = new DigitaxService();
            $digitaxService->setApiKey($business->digitax_api_key);

            $synced = 0; $failed = 0;
            foreach ($pending as $tx) {
                $result = in_array($tx->type, ['purchase', 'purchase_return'])
                    ? $digitaxService->createPurchase($tx)
                    : $digitaxService->createSale($tx);
                $result['success'] ? $synced++ : $failed++;
            }

            return response()->json([
                'success' => true,
                'msg'     => "Sync complete: {$synced} synced, {$failed} failed.",
                'count'   => $synced,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }

    /* ──────────────────────────────────────────
     |  SETTINGS
     ─────────────────────────────────────────── */
    public function settings()
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403);
        }

        $business_id = request()->session()->get('user.business_id');
        $business    = Business::findOrFail($business_id);

        return view('etims.settings', compact('business'));
    }

    public function saveSettings(Request $request)
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403);
        }

        $request->validate([
            'digitax_api_key' => 'nullable|string|max:255',
            'etims_tpin'      => 'nullable|string|max:50',
            'etims_sync_mode' => 'required|in:realtime,background,manual',
        ]);

        $business_id = request()->session()->get('user.business_id');
        Business::findOrFail($business_id)->update([
            'etims_enabled'   => $request->input('etims_enabled') == 1 ? 1 : 0,
            'digitax_api_key' => $request->digitax_api_key,
            'etims_tpin'      => $request->etims_tpin,
            'etims_sync_mode' => $request->etims_sync_mode,
        ]);

        return redirect()->route('etims.settings')->with('status', 'eTIMS settings saved successfully.');
    }

    /* ──────────────────────────────────────────
     |  PRIVATE HELPERS
     ─────────────────────────────────────────── */
    /* ──────────────────────────────────────────
     |  VAT REPORT DATA  (AJAX)
     |  Returns tax-category breakdown for the
     |  selected period — used for VAT returns.
     ─────────────────────────────────────────── */
    public function vatData(Request $request)
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403);
        }

        $business_id = request()->session()->get('user.business_id');

        // Tax category labels & rates (KRA eTIMS spec)
        $categories = [
            'A' => ['label' => 'A – Standard Rate (16%)',  'rate' => 0.16],
            'B' => ['label' => 'B – Petroleum/LPG (8%)',   'rate' => 0.08],
            'C' => ['label' => 'C – Zero-Rated (0%)',      'rate' => 0.00],
            'D' => ['label' => 'D – Exempt',               'rate' => 0.00],
            'E' => ['label' => 'E – Non-VAT (eTIMS only)', 'rate' => 0.00],
        ];

        $q = DB::table('transactions')
            ->join('transaction_sell_lines as tsl', 'tsl.transaction_id', '=', 'transactions.id')
            ->join('products as p', 'p.id', '=', 'tsl.product_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->where('transactions.etims_sync_status', 'success');

        if ($request->filled('start_date')) {
            $q->whereDate('transactions.transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $q->whereDate('transactions.transaction_date', '<=', $request->end_date);
        }
        if ($request->filled('location_id')) {
            $q->where('transactions.location_id', $request->location_id);
        }

        $rows = $q->select(
                'p.etims_tax_category as category',
                DB::raw('COUNT(DISTINCT transactions.id) as invoice_count'),
                DB::raw('SUM(tsl.quantity) as total_qty'),
                // Taxable amount excluding VAT
                DB::raw('SUM(
                    CASE
                        WHEN p.etims_tax_category = "A" THEN (tsl.unit_price_inc_tax * tsl.quantity) / 1.16
                        WHEN p.etims_tax_category = "B" THEN (tsl.unit_price_inc_tax * tsl.quantity) / 1.08
                        ELSE (tsl.unit_price_inc_tax * tsl.quantity)
                    END
                ) as taxable_amount'),
                // Tax collected
                DB::raw('SUM(
                    CASE
                        WHEN p.etims_tax_category = "A" THEN (tsl.unit_price_inc_tax * tsl.quantity) - ((tsl.unit_price_inc_tax * tsl.quantity) / 1.16)
                        WHEN p.etims_tax_category = "B" THEN (tsl.unit_price_inc_tax * tsl.quantity) - ((tsl.unit_price_inc_tax * tsl.quantity) / 1.08)
                        ELSE 0
                    END
                ) as tax_amount'),
                DB::raw('SUM(tsl.unit_price_inc_tax * tsl.quantity) as gross_amount')
            )
            ->groupBy('p.etims_tax_category')
            ->orderBy('p.etims_tax_category')
            ->get();

        // Build result with labels
        $result = [];
        $totals = ['invoice_count' => 0, 'taxable_amount' => 0, 'tax_amount' => 0, 'gross_amount' => 0, 'total_qty' => 0];

        foreach ($rows as $row) {
            $cat = strtoupper($row->category ?? 'A');
            $result[] = [
                'category'      => $cat,
                'label'         => $categories[$cat]['label'] ?? $cat,
                'rate'          => ($categories[$cat]['rate'] ?? 0) * 100 . '%',
                'invoice_count' => (int) $row->invoice_count,
                'total_qty'     => round($row->total_qty, 2),
                'taxable_amount'=> round($row->taxable_amount, 2),
                'tax_amount'    => round($row->tax_amount, 2),
                'gross_amount'  => round($row->gross_amount, 2),
            ];
            $totals['invoice_count']  += $row->invoice_count;
            $totals['taxable_amount'] += $row->taxable_amount;
            $totals['tax_amount']     += $row->tax_amount;
            $totals['gross_amount']   += $row->gross_amount;
            $totals['total_qty']      += $row->total_qty;
        }

        // Round totals
        foreach (['taxable_amount','tax_amount','gross_amount','total_qty'] as $k) {
            $totals[$k] = round($totals[$k], 2);
        }

        return response()->json(['rows' => $result, 'totals' => $totals]);
    }

    /* ──────────────────────────────────────────
     |  MONTHLY COMPLIANCE (AJAX)
     |  12-month rolling compliance breakdown
     ─────────────────────────────────────────── */
    public function monthlyCompliance(Request $request)
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403);
        }

        $business_id = request()->session()->get('user.business_id');
        $months      = (int) ($request->months ?? 12);
        $months      = min($months, 24);

        $rows = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('transaction_date', '>=', now()->subMonths($months)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(transaction_date, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN etims_sync_status = "success" THEN 1 ELSE 0 END) as synced'),
                DB::raw('SUM(CASE WHEN etims_sync_status = "failed"  THEN 1 ELSE 0 END) as failed'),
                DB::raw('SUM(CASE WHEN etims_sync_status = "pending" THEN 1 ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN etims_sync_status = "success" THEN final_total ELSE 0 END) as revenue_synced'),
                DB::raw('SUM(final_total) as total_revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($r) {
                $r->compliance_pct = $r->total > 0 ? round(($r->synced / $r->total) * 100, 1) : 0;
                return $r;
            });

        return response()->json($rows);
    }

    /* ──────────────────────────────────────────
     |  EXPORT VAT REPORT  (CSV download)
     ─────────────────────────────────────────── */
    public function exportVat(Request $request)
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403);
        }

        $business_id = request()->session()->get('user.business_id');
        $from = $request->start_date ?? now()->startOfMonth()->toDateString();
        $to   = $request->end_date   ?? now()->toDateString();

        $rows = DB::table('transactions')
            ->join('transaction_sell_lines as tsl', 'tsl.transaction_id', '=', 'transactions.id')
            ->join('products as p', 'p.id', '=', 'tsl.product_id')
            ->join('contacts as c', 'c.id', '=', 'transactions.contact_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->where('transactions.etims_sync_status', 'success')
            ->whereDate('transactions.transaction_date', '>=', $from)
            ->whereDate('transactions.transaction_date', '<=', $to)
            ->select(
                'transactions.transaction_date',
                'transactions.invoice_no',
                'transactions.etims_invoice_number',
                'c.name as customer_name',
                DB::raw('IFNULL(c.tax_number, "") as customer_pin'),
                'p.etims_tax_category as tax_category',
                'p.name as item_name',
                'tsl.quantity',
                DB::raw('ROUND(tsl.unit_price_inc_tax, 2) as unit_price_inc_tax'),
                DB::raw('ROUND(tsl.unit_price_inc_tax * tsl.quantity, 2) as gross_amount'),
                DB::raw('ROUND(
                    CASE
                        WHEN p.etims_tax_category = "A" THEN (tsl.unit_price_inc_tax * tsl.quantity) / 1.16
                        WHEN p.etims_tax_category = "B" THEN (tsl.unit_price_inc_tax * tsl.quantity) / 1.08
                        ELSE (tsl.unit_price_inc_tax * tsl.quantity)
                    END, 2) as taxable_amount'),
                DB::raw('ROUND(
                    CASE
                        WHEN p.etims_tax_category = "A" THEN (tsl.unit_price_inc_tax * tsl.quantity) - ((tsl.unit_price_inc_tax * tsl.quantity) / 1.16)
                        WHEN p.etims_tax_category = "B" THEN (tsl.unit_price_inc_tax * tsl.quantity) - ((tsl.unit_price_inc_tax * tsl.quantity) / 1.08)
                        ELSE 0
                    END, 2) as vat_amount')
            )
            ->orderBy('transactions.transaction_date')
            ->orderBy('transactions.invoice_no')
            ->get();

        $filename = 'etims_vat_report_' . $from . '_to_' . $to . '.csv';

        $response = new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            // Headers
            fputcsv($handle, [
                'Date', 'Invoice No', 'eTIMS Invoice No', 'Customer Name', 'Customer PIN',
                'Tax Category', 'Item', 'Qty', 'Unit Price (inc VAT)',
                'Gross Amount', 'Taxable Amount (exc VAT)', 'VAT Amount',
            ]);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->transaction_date,
                    $row->invoice_no,
                    $row->etims_invoice_number,
                    $row->customer_name,
                    $row->customer_pin,
                    $row->tax_category,
                    $row->item_name,
                    $row->quantity,
                    $row->unit_price_inc_tax,
                    $row->gross_amount,
                    $row->taxable_amount,
                    $row->vat_amount,
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    private function _stats($business_id)
    {
        $sell = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')->where('status', 'final')
            ->select(
                DB::raw('count(*) as total'),
                DB::raw('sum(case when etims_sync_status="success" then 1 else 0 end) as synced'),
                DB::raw('sum(case when etims_sync_status="failed"  then 1 else 0 end) as failed'),
                DB::raw('sum(case when etims_sync_status="pending" then 1 else 0 end) as pending'),
                DB::raw('sum(case when etims_sync_status="success" then final_total else 0 end) as revenue_synced'),
                DB::raw('sum(final_total) as total_revenue')
            )->first();

        $purchase = Transaction::where('business_id', $business_id)
            ->whereIn('type', ['purchase','purchase_return'])->where('status', 'received')
            ->select(
                DB::raw('count(*) as total'),
                DB::raw('sum(case when etims_sync_status="success" then 1 else 0 end) as synced'),
                DB::raw('sum(case when etims_sync_status="failed"  then 1 else 0 end) as failed'),
                DB::raw('sum(case when etims_sync_status="pending" then 1 else 0 end) as pending')
            )->first();

        return compact('sell', 'purchase');
    }

    private function _trend($business_id, $days = 30)
    {
        $rows = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('transaction_date', '>=', now()->subDays($days))
            ->select(
                DB::raw('DATE(transaction_date) as day'),
                DB::raw('sum(case when etims_sync_status="success" then 1 else 0 end) as synced'),
                DB::raw('sum(case when etims_sync_status="failed"  then 1 else 0 end) as failed'),
                DB::raw('sum(case when etims_sync_status="pending" then 1 else 0 end) as pending'),
                DB::raw('count(*) as total')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return $rows;
    }

    private function _taxCategoryBreakdown($business_id)
    {
        return DB::table('transactions')
            ->join('transaction_sell_lines as tsl', 'tsl.transaction_id', '=', 'transactions.id')
            ->join('products as p', 'p.id', '=', 'tsl.product_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->select(
                'p.etims_tax_category',
                DB::raw('count(distinct transactions.id) as invoice_count'),
                DB::raw('sum(tsl.unit_price_inc_tax * tsl.quantity) as taxable_amount')
            )
            ->groupBy('p.etims_tax_category')
            ->get();
    }

    private function _statusBadge($status, $error = null, $synced_at = null)
    {
        $map = [
            'success' => ['tw-bg-green-100 tw-text-green-800',  'fa-check-circle',  'Synced'],
            'failed'  => ['tw-bg-red-100 tw-text-red-800',     'fa-times-circle',  'Failed'],
            'pending' => ['tw-bg-yellow-100 tw-text-yellow-800','fa-clock-o',       'Pending'],
        ];
        [$cls, $icon, $label] = $map[$status] ?? ['tw-bg-gray-100 tw-text-gray-600', 'fa-question', ucfirst($status)];

        $tip = '';
        if ($status === 'failed' && $error) {
            $safe = htmlspecialchars($error, ENT_QUOTES);
            $tip  = ' title="'.e($error).'"';
        }
        if ($status === 'success' && $synced_at) {
            $tip = ' title="Synced at '.($synced_at ?? '').'"';
        }

        return '<span class="tw-inline-flex tw-items-center tw-gap-1 tw-px-2 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-semibold '.$cls.'"'.$tip.'>'
            .'<i class="fa '.$icon.'"></i> '.$label.'</span>';
    }
}
