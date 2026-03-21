<?php

namespace App\Http\Controllers;

use App\Business;
use App\Transaction;
use App\Utils\DigitaxService;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class EtimsReportController extends Controller
{
    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    public function index()
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $transactions = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->leftJoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
                ->select([
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'c.name as customer_name',
                    'transactions.final_total',
                    'transactions.etims_invoice_number',
                    'transactions.etims_sync_status',
                    'transactions.etims_sync_error'
                ]);

            if (!empty(request()->sync_status)) {
                $transactions->where('transactions.etims_sync_status', request()->sync_status);
            }

            return Datatables::of($transactions)
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('final_total', '<span class="display_currency" data-currency_symbol="true">{{$final_total}}</span>')
                ->editColumn('etims_sync_status', function($row) {
                    $status_color = [
                        'pending' => 'bg-yellow',
                        'success' => 'bg-green',
                        'failed' => 'bg-red'
                    ];
                    $color = $status_color[$row->etims_sync_status] ?? 'bg-gray';
                    return '<span class="label ' . $color . '">' . ucfirst($row->etims_sync_status) . '</span>';
                })
                ->addColumn('action', function($row) {
                    $html = '';
                    if ($row->etims_sync_status != 'success') {
                        $html .= '<button data-href="' . action([\App\Http\Controllers\EtimsReportController::class, 'syncInvoice'], [$row->id]) . '" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary sync-invoice"><i class="fa fa-refresh"></i> Sync</button>';
                    }
                    return $html;
                })
                ->rawColumns(['final_total', 'etims_sync_status', 'action'])
                ->make(true);
        }

        $stats = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->select(
                DB::raw('count(*) as total_count'),
                DB::raw('sum(case when etims_sync_status = "success" then 1 else 0 end) as success_count'),
                DB::raw('sum(case when etims_sync_status = "failed" then 1 else 0 end) as failed_count'),
                DB::raw('sum(case when etims_sync_status = "pending" then 1 else 0 end) as pending_count')
            )->first();

        return view('etims.index')->with(compact('stats'));
    }

    public function settings()
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);

        return view('etims.settings', compact('business'));
    }

    public function saveSettings(Request $request)
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'digitax_api_key' => 'nullable|string|max:255',
            'etims_tpin' => 'nullable|string|max:50',
            'etims_sync_mode' => 'required|in:realtime,background,manual',
        ]);

        $business_id = request()->session()->get('user.business_id');
        $business = Business::findOrFail($business_id);

        $business->update([
            'etims_enabled'   => $request->input('etims_enabled') == 1 ? 1 : 0,
            'digitax_api_key' => $request->digitax_api_key,
            'etims_tpin'      => $request->etims_tpin,
            'etims_sync_mode' => $request->etims_sync_mode,
        ]);

        return redirect()->route('etims.settings')->with('status', 'eTIMS settings saved successfully.');
    }

    public function syncInvoice($id)
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $transaction = Transaction::where('business_id', $business_id)->findOrFail($id);
            
            $business = \App\Business::find($business_id);
            if (empty($business->digitax_api_key)) {
                return ['success' => false, 'msg' => 'Digitax API Key not configured.'];
            }

            $digitaxService = new DigitaxService();
            $result = $digitaxService->setApiKey($business->digitax_api_key)->createSale($transaction);

            if ($result['success']) {
                return ['success' => true, 'msg' => 'Invoice synced successfully with eTIMS.'];
            } else {
                return ['success' => false, 'msg' => 'Sync failed: ' . $result['error']];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function syncAll()
    {
        if (!auth()->user()->can('access_etims_report')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $business = Business::find($business_id);

            if (empty($business->digitax_api_key)) {
                return response()->json(['success' => false, 'msg' => 'Digitax API Key not configured.']);
            }

            $pending = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereIn('etims_sync_status', ['pending', 'failed'])
                ->get();

            if ($pending->isEmpty()) {
                return response()->json(['success' => true, 'msg' => 'No pending or failed invoices to sync.', 'count' => 0]);
            }

            $digitaxService = new DigitaxService();
            $digitaxService->setApiKey($business->digitax_api_key);

            $synced = 0;
            $failed = 0;
            foreach ($pending as $transaction) {
                $result = $digitaxService->createSale($transaction);
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
}
