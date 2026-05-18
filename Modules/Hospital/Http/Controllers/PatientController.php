<?php

namespace Modules\Hospital\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Hospital\Entities\Patient;
use Yajra\DataTables\Facades\DataTables;

class PatientController extends Controller
{
    /**
     * Display a listing of patients.
     */
    public function index(Request $request)
    {
        if (! auth()->user()->can('hospital.receptionist') && ! auth()->user()->can('hospital.admin')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session()->get('user.business_id');

        if ($request->ajax()) {
            $patients = Patient::where('business_id', $business_id)
                ->select(['id', 'patient_no', 'first_name', 'last_name', 'phone', 'gender', 'blood_group', 'created_at']);

            return DataTables::of($patients)
                ->addColumn('full_name', fn ($p) => $p->full_name)
                ->addColumn('action', function ($p) {
                    $show = route('hospital.patients.show', $p->id);
                    $edit = route('hospital.patients.edit', $p->id);
                    return '<a href="' . $show . '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> View</a> '
                         . '<a href="' . $edit . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('hospital::patients.index');
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create()
    {
        if (! auth()->user()->can('hospital.receptionist') && ! auth()->user()->can('hospital.admin')) {
            abort(403, 'Unauthorized');
        }

        return view('hospital::patients.create');
    }

    /**
     * Store a newly created patient.
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('hospital.receptionist') && ! auth()->user()->can('hospital.admin')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        $request->validate([
            'first_name'              => 'required|string|max:100',
            'last_name'               => 'required|string|max:100',
            'gender'                  => 'required|in:M,F,Other',
            'dob'                     => 'nullable|date',
            'phone'                   => 'nullable|string|max:30',
            'email'                   => 'nullable|email|max:100',
            'address'                 => 'nullable|string',
            'blood_group'             => 'nullable|string|max:10',
            'allergies'               => 'nullable|string',
            'emergency_contact_name'  => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:30',
        ]);

        try {
            DB::beginTransaction();

            $business_id = session()->get('user.business_id');
            $patient_no  = Patient::generatePatientNo($business_id);

            $patient = Patient::create(array_merge(
                $request->only([
                    'first_name', 'last_name', 'dob', 'gender', 'phone', 'email',
                    'address', 'blood_group', 'allergies',
                    'emergency_contact_name', 'emergency_contact_phone',
                ]),
                ['business_id' => $business_id, 'patient_no' => $patient_no]
            ));

            DB::commit();

            return response()->json([
                'success' => true,
                'msg'     => 'Patient registered successfully.',
                'data'    => ['id' => $patient->id, 'patient_no' => $patient->patient_no],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified patient with visit history.
     */
    public function show($id)
    {
        if (! auth()->user()->can('hospital.receptionist') && ! auth()->user()->can('hospital.admin') && ! auth()->user()->can('hospital.doctor')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session()->get('user.business_id');
        $patient     = Patient::where('business_id', $business_id)->with('visits')->findOrFail($id);

        return view('hospital::patients.show', compact('patient'));
    }

    /**
     * Show the form for editing a patient.
     */
    public function edit($id)
    {
        if (! auth()->user()->can('hospital.receptionist') && ! auth()->user()->can('hospital.admin')) {
            abort(403, 'Unauthorized');
        }

        $business_id = session()->get('user.business_id');
        $patient     = Patient::where('business_id', $business_id)->findOrFail($id);

        return view('hospital::patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient.
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('hospital.receptionist') && ! auth()->user()->can('hospital.admin')) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        $request->validate([
            'first_name'              => 'required|string|max:100',
            'last_name'               => 'required|string|max:100',
            'gender'                  => 'required|in:M,F,Other',
            'dob'                     => 'nullable|date',
            'phone'                   => 'nullable|string|max:30',
            'email'                   => 'nullable|email|max:100',
            'address'                 => 'nullable|string',
            'blood_group'             => 'nullable|string|max:10',
            'allergies'               => 'nullable|string',
            'emergency_contact_name'  => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:30',
        ]);

        try {
            DB::beginTransaction();

            $business_id = session()->get('user.business_id');
            $patient     = Patient::where('business_id', $business_id)->findOrFail($id);

            $patient->update($request->only([
                'first_name', 'last_name', 'dob', 'gender', 'phone', 'email',
                'address', 'blood_group', 'allergies',
                'emergency_contact_name', 'emergency_contact_phone',
            ]));

            DB::commit();

            return response()->json(['success' => true, 'msg' => 'Patient updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}
