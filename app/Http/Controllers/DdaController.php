<?php

namespace App\Http\Controllers;

use App\DdaDrug;
use App\DdaDestructionLog;
use App\DdaDispenseLog;
use App\DdaPrescription;
use App\DdaStockLog;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DdaController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        if (!auth()->user()->can('dda.view')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session('user.business_id');

        $stats = [
            'dda_products'    => Product::where('business_id', $business_id)->where('is_dda', true)->count(),
            'prescriptions'   => DdaPrescription::where('business_id', $business_id)->count(),
            'dispensed_today' => DdaDispenseLog::where('business_id', $business_id)->whereDate('created_at', today())->count(),
            'destructions'    => DdaDestructionLog::where('business_id', $business_id)->count(),
        ];

        $recent_dispense = DdaDispenseLog::where('business_id', $business_id)
            ->with(['ddaDrug'])
            ->latest()->limit(5)->get();

        return view('dda.dashboard', compact('stats', 'recent_dispense'));
    }

    // ─── DDA Drug List ────────────────────────────────────────────────────────

    public function drugs()
    {
        if (!auth()->user()->can('dda.view')) {
            abort(403, 'Unauthorized');
        }

        $drugs = DdaDrug::orderBy('class')->orderBy('name')->get();
        return view('dda.drugs.index', compact('drugs'));
    }

    public function storeDrug(Request $request)
    {
        if (!auth()->user()->can('dda.manage')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'class'    => 'required|string',
            'schedule' => 'required|string',
        ]);

        DdaDrug::create($request->only('name', 'class', 'schedule', 'description', 'is_active'));

        return back()->with('status', ['success' => true, 'msg' => 'Drug added successfully.']);
    }

    public function updateDrug(Request $request, $id)
    {
        if (!auth()->user()->can('dda.manage')) {
            abort(403, 'Unauthorized');
        }

        $drug = DdaDrug::findOrFail($id);
        $drug->update($request->only('name', 'class', 'schedule', 'description', 'is_active'));

        return back()->with('status', ['success' => true, 'msg' => 'Drug updated successfully.']);
    }

    // ─── DDA Products ─────────────────────────────────────────────────────────

    public function products()
    {
        if (!auth()->user()->can('dda.view')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session('user.business_id');
        $products = Product::where('business_id', $business_id)
            ->where('is_dda', true)
            ->with('ddaDrug')
            ->get();

        $dda_drugs = DdaDrug::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('dda.products.index', compact('products', 'dda_drugs'));
    }

    // ─── Prescriptions ────────────────────────────────────────────────────────

    public function prescriptions()
    {
        if (!auth()->user()->can('dda.prescriptions.view')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session('user.business_id');
        $prescriptions = DdaPrescription::where('business_id', $business_id)
            ->latest()->paginate(20);

        return view('dda.prescriptions.index', compact('prescriptions'));
    }

    public function createPrescription()
    {
        if (!auth()->user()->can('dda.prescriptions.upload')) {
            abort(403, 'Unauthorized');
        }

        return view('dda.prescriptions.create');
    }

    public function storePrescription(Request $request)
    {
        if (!auth()->user()->can('dda.prescriptions.upload')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'patient_name'    => 'required|string|max:255',
            'prescriber_name' => 'required|string|max:255',
            'prescription_image' => 'required|image|max:5120',
        ]);

        $business_id = session('user.business_id');

        $file = $request->file('prescription_image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/dda'), $filename);

        DdaPrescription::create([
            'business_id'         => $business_id,
            'patient_name'        => $request->patient_name,
            'patient_id_number'   => $request->patient_id_number,
            'prescriber_name'     => $request->prescriber_name,
            'prescriber_reg_number' => $request->prescriber_reg_number,
            'prescription_number' => $request->prescription_number,
            'prescription_date'   => $request->prescription_date,
            'image_path'          => $filename,
            'notes'               => $request->notes,
            'created_by'          => auth()->id(),
        ]);

        return redirect()->route('dda.prescriptions')
            ->with('status', ['success' => true, 'msg' => 'Prescription uploaded successfully.']);
    }

    /**
     * AJAX: Upload prescription from POS screen
     */
    public function posUploadPrescription(Request $request)
    {
        $request->validate([
            'patient_name'       => 'required|string|max:255',
            'prescriber_name'    => 'required|string|max:255',
            'prescription_image' => 'required|image|max:5120',
        ]);

        $business_id = session('user.business_id');

        $file = $request->file('prescription_image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/dda'), $filename);

        $prescription = DdaPrescription::create([
            'business_id'          => $business_id,
            'patient_name'         => $request->patient_name,
            'prescriber_name'      => $request->prescriber_name,
            'prescriber_hospital'  => $request->prescriber_hospital,
            'prescription_date'    => now()->toDateString(),
            'image_path'           => $filename,
            'created_by'           => auth()->id(),
        ]);

        return response()->json([
            'success'         => true,
            'prescription_id' => $prescription->id,
            'patient_name'    => $prescription->patient_name,
            'msg'             => 'Prescription uploaded successfully.',
        ]);
    }

    // ─── View Prescription Image ─────────────────────────────────────────────

    public function viewPrescription($id)
    {
        if (!auth()->user()->can('dda.view')) {
            abort(403, 'Unauthorized');
        }

        $prescription = DdaPrescription::findOrFail($id);

        // Support both column names: image_path (newer) and prescription_image (migration)
        $filename = $prescription->image_path ?? $prescription->prescription_image ?? null;

        if (empty($filename)) {
            abort(404, 'No image for this prescription.');
        }

        $path = public_path('uploads/dda/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'Prescription image not found.');
        }

        $mime = mime_content_type($path);
        return response()->file($path, ['Content-Type' => $mime]);
    }

    // ─── Dispense Register ────────────────────────────────────────────────────

    public function dispenseRegister()
    {
        if (!auth()->user()->can('dda.view')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session('user.business_id');

        // Date range filter
        $start = request('start_date', now()->startOfMonth()->toDateString());
        $end   = request('end_date',   now()->toDateString());

        // Batch info subquery: get lot_number + exp_date from purchase_lines for each sell line
        $batchSub = DB::table('transaction_sell_lines_purchase_lines as slpl')
            ->join('purchase_lines as pl', 'pl.id', '=', 'slpl.purchase_line_id')
            ->select(
                'slpl.sell_line_id',
                DB::raw('MIN(pl.lot_number) as lot_number'),
                DB::raw('MIN(pl.exp_date)   as exp_date')
            )
            ->groupBy('slpl.sell_line_id');

        $dispense = DB::table('transaction_sell_lines as sl')
            ->join('transactions as t', 't.id', '=', 'sl.transaction_id')
            ->join('products as p', 'p.id', '=', 'sl.product_id')
            ->leftJoin('contacts as c', 'c.id', '=', 't.contact_id')
            ->leftJoin('dda_drugs as dd', 'dd.id', '=', 'p.dda_drug_id')
            ->leftJoin('users as u', 'u.id', '=', 't.created_by')
            ->leftJoinSub($batchSub, 'batch', function ($join) {
                $join->on('batch.sell_line_id', '=', 'sl.id');
            })
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->where('p.is_dda', 1)
            ->whereBetween(DB::raw('DATE(t.transaction_date)'), [$start, $end])
            ->select(
                't.id as transaction_id',
                't.invoice_no',
                't.transaction_date',
                'c.name as customer_name',
                'c.mobile as customer_phone',
                'p.name as product_name',
                'dd.name as drug_name',
                'dd.class as drug_class',
                'sl.quantity',
                'batch.lot_number',
                'batch.exp_date',
                DB::raw("TRIM(CONCAT(u.first_name, ' ', COALESCE(u.last_name, ''))) as dispensed_by")
            )
            ->orderBy('t.transaction_date', 'asc')  // asc for running register
            ->paginate(50);

        // Summary totals for current filter
        $totals = DB::table('transaction_sell_lines as sl')
            ->join('transactions as t', 't.id', '=', 'sl.transaction_id')
            ->join('products as p', 'p.id', '=', 'sl.product_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->where('p.is_dda', 1)
            ->whereBetween(DB::raw('DATE(t.transaction_date)'), [$start, $end])
            ->selectRaw('COUNT(DISTINCT t.id) as total_transactions, SUM(sl.quantity) as total_qty')
            ->first();

        return view('dda.dispense.index', compact('dispense', 'start', 'end', 'totals'));
    }

    public function storeDispense(Request $request)
    {
        if (!auth()->user()->can('dda.dispense')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'patient_name'   => 'required|string|max:255',
            'quantity_dispensed' => 'required|numeric|min:0.01',
            'dda_drug_id'    => 'required|integer',
            'dispensed_date' => 'required|date',
        ]);

        $business_id = session('user.business_id');

        DdaDispenseLog::create([
            'business_id'         => $business_id,
            'dda_drug_id'         => $request->dda_drug_id,
            'dda_prescription_id' => $request->dda_prescription_id,
            'patient_name'        => $request->patient_name,
            'patient_id_number'   => $request->patient_id_number,
            'prescriber_name'     => $request->prescriber_name,
            'quantity_dispensed'  => $request->quantity_dispensed,
            'unit'                => $request->unit,
            'batch_number'        => $request->batch_number,
            'dispensed_date'      => $request->dispensed_date,
            'dispensed_by'        => $request->dispensed_by ?? auth()->id(),
            'witnessed_by'        => $request->witnessed_by,
            'notes'               => $request->notes,
            'created_by'          => auth()->id(),
        ]);

        return back()->with('status', ['success' => true, 'msg' => 'Dispense record added.']);
    }

    // ─── Stock Balance ────────────────────────────────────────────────────────

    public function stockBalance()
    {
        if (!auth()->user()->can('dda.view')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session('user.business_id');

        // Pull live stock balances from variation_location_details.
        // qty_available is maintained by the system on every purchase/sale.
        $balance = DB::table('variation_location_details as vld')
            ->join('variations as v', 'v.id', '=', 'vld.variation_id')
            ->join('products as p', 'p.id', '=', 'v.product_id')
            ->join('business_locations as bl', 'bl.id', '=', 'vld.location_id')
            ->leftJoin('units as u', 'u.id', '=', 'p.unit_id')
            ->leftJoin('dda_drugs as dd', 'dd.id', '=', 'p.dda_drug_id')
            ->where('p.business_id', $business_id)
            ->where('p.is_dda', 1)
            ->select(
                'p.id as product_id',
                'p.name as product_name',
                'v.sub_sku as sku',
                'dd.name as drug_name',
                'bl.name as location_name',
                'u.short_name as unit_name',
                DB::raw('SUM(vld.qty_available) as qty_available')
            )
            ->groupBy('p.id', 'p.name', 'v.sub_sku', 'dd.name', 'bl.name', 'u.short_name')
            ->orderBy('p.name')
            ->get();

        return view('dda.stock.index', compact('balance'));
    }

    public function storeStockLog(Request $request)
    {
        if (!auth()->user()->can('dda.manage')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'type'         => 'required|in:in,out,adjustment,destruction',
            'quantity'     => 'required|numeric|min:0.01',
            'dda_drug_id'  => 'required|integer',
            'movement_date'=> 'required|date',
        ]);

        $business_id = session('user.business_id');

        DdaStockLog::create([
            'business_id'   => $business_id,
            'dda_drug_id'   => $request->dda_drug_id,
            'type'          => $request->type,
            'quantity'      => $request->quantity,
            'reference'     => $request->reference,
            'movement_date' => $request->movement_date,
            'notes'         => $request->notes,
            'created_by'    => auth()->id(),
        ]);

        return back()->with('status', ['success' => true, 'msg' => 'Stock entry recorded.']);
    }

    // ─── DDA Sales ────────────────────────────────────────────────────────────

    public function sales()
    {
        if (!auth()->user()->can('dda.reports.view')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session('user.business_id');
        $start = request('start_date', now()->startOfMonth()->toDateString());
        $end   = request('end_date',   now()->toDateString());

        $sales = DB::table('transaction_sell_lines as tsl')
            ->join('transactions as t', 't.id', '=', 'tsl.transaction_id')
            ->join('products as p', 'p.id', '=', 'tsl.product_id')
            ->leftJoin('contacts as c', 'c.id', '=', 't.contact_id')
            ->leftJoin('dda_drugs as d', 'd.id', '=', 'p.dda_drug_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->where('p.is_dda', true)
            ->whereBetween(DB::raw('DATE(t.transaction_date)'), [$start, $end])
            ->select(
                't.id as transaction_id',
                't.transaction_date',
                't.invoice_no',
                'c.name as customer_name',
                'p.name as product_name',
                'd.name as dda_drug_name',
                'd.class as dda_class',
                'tsl.quantity',
                'tsl.unit_price_before_discount',
                DB::raw('tsl.quantity * tsl.unit_price_before_discount as total')
            )
            ->orderBy('t.transaction_date', 'desc')
            ->paginate(25);

        // Totals for the filtered period
        $totals = DB::table('transaction_sell_lines as tsl')
            ->join('transactions as t', 't.id', '=', 'tsl.transaction_id')
            ->join('products as p', 'p.id', '=', 'tsl.product_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->where('p.is_dda', true)
            ->whereBetween(DB::raw('DATE(t.transaction_date)'), [$start, $end])
            ->selectRaw('
                COUNT(DISTINCT t.id)                                          as total_invoices,
                SUM(tsl.quantity)                                             as total_qty,
                SUM(tsl.quantity * tsl.unit_price_before_discount)            as total_revenue
            ')
            ->first();

        return view('dda.sales.index', compact('sales', 'start', 'end', 'totals'));
    }

    // ─── Disposal / Destruction Log ─────────────────────────────────────────

    public function destruction()
    {
        if (!auth()->user()->can('dda.view')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session('user.business_id');
        $status      = request('status', '');

        $query = DdaDestructionLog::where('business_id', $business_id)->with(['ddaDrug']);
        if ($status) {
            $query->where('status', $status);
        }
        $logs = $query->latest()->paginate(20);

        $dda_drugs = DdaDrug::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('dda.destruction.index', compact('logs', 'dda_drugs', 'status'));
    }

    public function storeDestruction(Request $request)
    {
        if (!auth()->user()->can('dda.destruction.manage')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'drug_name'   => 'required|string|max:255',
            'quantity'    => 'required|numeric|min:0.01',
            'reason'      => 'required|string',
        ]);

        $business_id = session('user.business_id');

        DdaDestructionLog::create([
            'business_id'  => $business_id,
            'drug_name'    => $request->drug_name,
            'dda_drug_id'  => $request->dda_drug_id ?: null,
            'quantity'     => $request->quantity,
            'unit'         => $request->unit,
            'batch_number' => $request->batch_number,
            'expiry_date'  => $request->expiry_date ?: null,
            'reason'       => $request->reason,
            'status'       => 'quarantined',
            'notes'        => $request->notes,
            'created_by'   => auth()->id(),
        ]);

        return back()->with('status', ['success' => true, 'msg' => 'Disposal request logged. Drug moved to quarantine.']);
    }

    public function updateDisposalStatus(Request $request, $id)
    {
        if (!auth()->user()->can('dda.destruction.manage')) {
            abort(403, 'Unauthorized');
        }

        $log = DdaDestructionLog::findOrFail($id);
        $new_status = $request->status;

        $update = ['status' => $new_status];

        if ($new_status === 'ppb_applied') {
            $update['ppb_application_number'] = $request->ppb_application_number;
            $update['ppb_application_date']   = $request->ppb_application_date;
        }

        if ($new_status === 'collected') {
            $update['disposal_company'] = $request->disposal_company;
            $update['collection_date']  = $request->collection_date;
        }

        if ($new_status === 'certificate_received') {
            $update['ppb_certificate_number'] = $request->ppb_certificate_number;
            $update['ppb_certificate_date']   = $request->ppb_certificate_date;

            if ($request->hasFile('certificate_image')) {
                $file     = $request->file('certificate_image');
                $filename = time() . '_cert_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/dda'), $filename);
                $update['certificate_image_path'] = $filename;
            }
        }

        $log->update($update);

        return back()->with('status', ['success' => true, 'msg' => 'Status updated successfully.']);
    }

    public function viewCertificate($id)
    {
        if (!auth()->user()->can('dda.view')) {
            abort(403, 'Unauthorized');
        }

        $log = DdaDestructionLog::findOrFail($id);

        if (empty($log->certificate_image_path)) {
            abort(404, 'No certificate uploaded.');
        }

        $path = public_path('uploads/dda/' . $log->certificate_image_path);
        if (!file_exists($path)) {
            abort(404, 'Certificate file not found.');
        }

        return response()->file($path, ['Content-Type' => mime_content_type($path)]);
    }

    // ─── Expired Drugs ───────────────────────────────────────────────────────

    public function expiredDrugs()
    {
        if (!auth()->user()->can('dda.view')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session('user.business_id');
        $filter      = request('filter', 'all'); // 'all' or 'dda'

        // quantity_remaining is not a stored column —
        // it is calculated as quantity - quantity_sold - quantity_adjusted - quantity_returned
        $remainingExpr = DB::raw(
            '(pl.quantity - pl.quantity_sold - pl.quantity_adjusted - pl.quantity_returned)'
        );

        $query = DB::table('purchase_lines as pl')
            ->join('variations as v', 'v.id', '=', 'pl.variation_id')
            ->join('products as p', 'p.id', '=', 'v.product_id')
            ->join('transactions as t', 't.id', '=', 'pl.transaction_id')
            ->leftJoin('business_locations as bl', 'bl.id', '=', 't.location_id')
            ->leftJoin('units as u', 'u.id', '=', 'p.unit_id')
            ->leftJoin('dda_drugs as dd', 'dd.id', '=', 'p.dda_drug_id')
            ->where('p.business_id', $business_id)
            ->whereNotNull('pl.exp_date')
            ->where('pl.exp_date', '<', now()->toDateString())
            ->whereRaw('(pl.quantity - pl.quantity_sold - pl.quantity_adjusted - pl.quantity_returned) > 0')
            ->select(
                'p.id as product_id',
                'p.name as product_name',
                'p.is_dda',
                'v.sub_sku as sku',
                'pl.id as purchase_line_id',
                'pl.lot_number',
                'pl.exp_date',
                DB::raw('(pl.quantity - pl.quantity_sold - pl.quantity_adjusted - pl.quantity_returned) as qty_remaining'),
                'u.short_name as unit_name',
                'bl.name as location_name',
                'dd.name as dda_drug_name',
                't.ref_no as purchase_ref'
            )
            ->orderBy('pl.exp_date', 'asc');

        if ($filter === 'dda') {
            $query->where('p.is_dda', 1);
        }

        $expired = $query->paginate(30);

        return view('dda.expired.index', compact('expired', 'filter'));
    }
}
