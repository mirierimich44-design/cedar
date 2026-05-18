<?php

namespace Modules\Hospital\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Hospital\Entities\HospitalVisit;
use Modules\Hospital\Entities\Patient;
use Yajra\DataTables\Facades\DataTables;

class VisitController extends Controller
{
    /**
     * Display today's visits (DataTables).
     */
    public function index(Request $request)
    {
        if (! auth()->user()->can('hospital.receptionist') && ! auth()->user()->can('hospital.admin') && ! auth()->user()->can('hospital.doctor')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session()->get('user.business_id');

        if ($request->ajax()) {
            $visits = HospitalVisit::with('patient')
                ->where('business_id', $business_id)
                ->whereDate('visited_at', Carbon::today())
                ->select('hospital_visits.*');

            return DataTables::of($visits)
                ->addColumn('patient_name', fn ($v) => optional($v->patient)->full_name ?? '-')
                ->addColumn('patient_no', fn ($v) => optional($v->patient)->patient_no ?? '-')
                ->addColumn('triage_badge', function ($v) {
                    $class = $v->getTriageBadgeClass();
                    $cat   = $v->triage_category ?? 'N/A';
                    return "<span class=\"badge badge-{$class}\">" . ucfirst($cat) . '</span>';
                })
                ->addColumn('status_badge', function ($v) {
                    $map = [
                        'triage'       => 'info',
                        'consultation' => 'primary',
                        'lab'          => 'warning',
                        'pharmacy'     => 'secondary',
                        'discharged'   => 'success',
                        'admitted'     => 'dark',
                        'deceased'     => 'danger',
                    ];
                    $class = $map[$v->status] ?? 'secondary';
                    return "<span class=\"badge badge-{$class}\">" . ucfirst($v->status) . '</span>';
                })
                ->addColumn('action', function ($v) {
                    $url = route('hospital.visits.show', $v->id);
                    return "<a href=\"{$url}\" class=\"btn btn-xs btn-info\"><i class=\"fa fa-eye\"></i> View</a>";
                })
                ->rawColumns(['triage_badge', 'status_badge', 'action'])
                ->make(true);
        }

        // Status pipeline counts for today
        $statuses = ['triage', 'consultation', 'lab', 'pharmacy', 'discharged', 'admitted', 'deceased'];
        $counts   = [];
        foreach ($statuses as $s) {
            $counts[$s] = HospitalVisit::where('business_id', $business_id)
                ->whereDate('visited_at', Carbon::today())
                ->where('status', $s)
                ->count();
        }

        return view('hospital::visits.index', compact('counts'));
    }

    /**
     * Show the form for creating a new visit.
     */
    public function create(Request $request)
    {
        if (! auth()->user()->can('hospital.receptionist') && ! auth()->user()->can('hospital.admin')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session()->get('user.business_id');
        $patient     = null;

        if ($request->has('patient_id')) {
            $patient = Patient::where('business_id', $business_id)->find($request->patient_id);
        }

        $patients = Patient::where('business_id', $business_id)
            ->orderBy('first_name')
            ->get(['id', 'patient_no', 'first_name', 'last_name', 'dob', 'gender', 'blood_group', 'allergies']);

        return view('hospital::visits.create', compact('patient', 'patients'));
    }

    /**
     * Store a new visit.
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('hospital.receptionist') && ! auth()->user()->can('hospital.admin')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        $request->validate([
            'patient_id'      => 'required|exists:hospital_patients,id',
            'visit_type'      => 'required|in:outpatient,inpatient,emergency',
            'chief_complaint' => 'nullable|string',
            'triage_category' => 'nullable|in:green,yellow,orange,red,black',
            'triage_notes'    => 'nullable|string',
            'bp_systolic'     => 'nullable|integer',
            'bp_diastolic'    => 'nullable|integer',
            'temperature'     => 'nullable|numeric',
            'pulse_rate'      => 'nullable|integer',
            'respiratory_rate'    => 'nullable|integer',
            'oxygen_saturation'   => 'nullable|integer',
            'weight_kg'       => 'nullable|numeric',
            'height_cm'       => 'nullable|numeric',
            'assigned_doctor' => 'nullable|string|max:150',
        ]);

        try {
            DB::beginTransaction();

            $business_id = session()->get('user.business_id');
            $visit_no    = HospitalVisit::generateVisitNo($business_id);

            $visit = HospitalVisit::create(array_merge(
                $request->only([
                    'patient_id', 'visit_type', 'chief_complaint', 'triage_category', 'triage_notes',
                    'bp_systolic', 'bp_diastolic', 'temperature', 'pulse_rate', 'respiratory_rate',
                    'oxygen_saturation', 'weight_kg', 'height_cm', 'assigned_doctor',
                ]),
                [
                    'business_id' => $business_id,
                    'visit_no'    => $visit_no,
                    'visited_at'  => Carbon::now(),
                    'status'      => HospitalVisit::STATUS_TRIAGE,
                ]
            ));

            DB::commit();

            return response()->json([
                'success' => true,
                'msg'     => 'Visit registered successfully.',
                'data'    => ['id' => $visit->id, 'visit_no' => $visit->visit_no],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the patient journey for a visit.
     */
    public function show($id)
    {
        if (! auth()->user()->can('hospital.receptionist') && ! auth()->user()->can('hospital.admin') && ! auth()->user()->can('hospital.doctor')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session()->get('user.business_id');
        $visit       = HospitalVisit::where('business_id', $business_id)
            ->with(['patient', 'labOrders'])
            ->findOrFail($id);

        return view('hospital::visits.show', compact('visit'));
    }

    /**
     * Update visit status (advance through pipeline).
     */
    public function updateStatus(Request $request, $id)
    {
        if (! auth()->user()->can('hospital.receptionist') && ! auth()->user()->can('hospital.admin') && ! auth()->user()->can('hospital.doctor')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:triage,consultation,lab,pharmacy,discharged,admitted,deceased',
        ]);

        try {
            DB::beginTransaction();

            $business_id = session()->get('user.business_id');
            $visit       = HospitalVisit::where('business_id', $business_id)->findOrFail($id);

            $data = ['status' => $request->status];

            if ($request->status === HospitalVisit::STATUS_ADMITTED) {
                $data['admission_date'] = Carbon::now();
                if ($request->filled('ward')) {
                    $data['ward'] = $request->ward;
                }
                if ($request->filled('bed_number')) {
                    $data['bed_number'] = $request->bed_number;
                }
            }

            if ($request->status === HospitalVisit::STATUS_DISCHARGED) {
                $data['discharge_date'] = Carbon::now();
            }

            $visit->update($data);

            DB::commit();

            return response()->json(['success' => true, 'msg' => 'Visit status updated.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Update triage vital signs and category.
     */
    public function updateTriage(Request $request, $id)
    {
        if (! auth()->user()->can('hospital.doctor') && ! auth()->user()->can('hospital.admin') && ! auth()->user()->can('hospital.receptionist')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        $request->validate([
            'triage_category'   => 'nullable|in:green,yellow,orange,red,black',
            'triage_notes'      => 'nullable|string',
            'bp_systolic'       => 'nullable|integer',
            'bp_diastolic'      => 'nullable|integer',
            'temperature'       => 'nullable|numeric',
            'pulse_rate'        => 'nullable|integer',
            'respiratory_rate'  => 'nullable|integer',
            'oxygen_saturation' => 'nullable|integer',
            'weight_kg'         => 'nullable|numeric',
            'height_cm'         => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            $business_id = session()->get('user.business_id');
            $visit       = HospitalVisit::where('business_id', $business_id)->findOrFail($id);

            $visit->update($request->only([
                'triage_category', 'triage_notes', 'bp_systolic', 'bp_diastolic',
                'temperature', 'pulse_rate', 'respiratory_rate', 'oxygen_saturation',
                'weight_kg', 'height_cm',
            ]));

            DB::commit();

            return response()->json(['success' => true, 'msg' => 'Triage data updated.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}
