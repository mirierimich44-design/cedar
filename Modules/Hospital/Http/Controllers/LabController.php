<?php

namespace Modules\Hospital\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Hospital\Entities\LabOrder;
use Modules\Hospital\Entities\HospitalVisit;
use Yajra\DataTables\Facades\DataTables;

class LabController extends Controller
{
    /**
     * Display pending lab orders (DataTables).
     */
    public function index(Request $request)
    {
        if (! auth()->user()->can('hospital.lab_technician') && ! auth()->user()->can('hospital.admin')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session()->get('user.business_id');

        if ($request->ajax()) {
            $orders = LabOrder::with(['visit.patient'])
                ->where('hospital_lab_orders.business_id', $business_id)
                ->whereIn('hospital_lab_orders.status', ['pending', 'sample_collected', 'processing'])
                ->select('hospital_lab_orders.*');

            return DataTables::of($orders)
                ->addColumn('visit_no', fn ($o) => optional($o->visit)->visit_no ?? '-')
                ->addColumn('patient_name', fn ($o) => optional(optional($o->visit)->patient)->full_name ?? '-')
                ->addColumn('status_badge', function ($o) {
                    $map = [
                        'pending'          => 'warning',
                        'sample_collected' => 'info',
                        'processing'       => 'primary',
                        'resulted'         => 'success',
                        'cancelled'        => 'danger',
                    ];
                    $class = $map[$o->status] ?? 'secondary';
                    return "<span class=\"badge badge-{$class}\">" . ucwords(str_replace('_', ' ', $o->status)) . '</span>';
                })
                ->addColumn('action', function ($o) {
                    return '<button class="btn btn-xs btn-success btn-enter-result" data-id="' . $o->id . '" data-toggle="modal" data-target="#resultModal">'
                         . '<i class="fa fa-flask"></i> Enter Result</button>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('hospital::lab.index');
    }

    /**
     * Store a new lab order for a visit.
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('hospital.doctor') && ! auth()->user()->can('hospital.admin')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        $request->validate([
            'visit_id'   => 'required|exists:hospital_visits,id',
            'test_name'  => 'required|string|max:200',
            'test_code'  => 'nullable|string|max:50',
            'ordered_by' => 'required|string|max:150',
        ]);

        try {
            DB::beginTransaction();

            $business_id = session()->get('user.business_id');

            // Confirm the visit belongs to this business
            HospitalVisit::where('business_id', $business_id)->findOrFail($request->visit_id);

            $order = LabOrder::create([
                'visit_id'    => $request->visit_id,
                'business_id' => $business_id,
                'test_name'   => $request->test_name,
                'test_code'   => $request->test_code,
                'ordered_by'  => $request->ordered_by,
                'ordered_at'  => Carbon::now(),
                'status'      => 'pending',
            ]);

            DB::commit();

            return response()->json(['success' => true, 'msg' => 'Lab order created.', 'data' => ['id' => $order->id]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Record a result for a lab order.
     */
    public function updateResult(Request $request, $id)
    {
        if (! auth()->user()->can('hospital.lab_technician') && ! auth()->user()->can('hospital.admin')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        $request->validate([
            'result_value'   => 'required|string',
            'result_unit'    => 'nullable|string|max:50',
            'reference_range'=> 'nullable|string|max:100',
            'result_notes'   => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $business_id = session()->get('user.business_id');
            $order = LabOrder::where('business_id', $business_id)->findOrFail($id);

            $order->update([
                'result_value'    => $request->result_value,
                'result_unit'     => $request->result_unit,
                'reference_range' => $request->reference_range,
                'result_notes'    => $request->result_notes,
                'resulted_at'     => Carbon::now(),
                'status'          => 'resulted',
            ]);

            DB::commit();

            return response()->json(['success' => true, 'msg' => 'Result recorded.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}
