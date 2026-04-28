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
