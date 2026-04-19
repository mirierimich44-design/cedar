<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\Admission;
use App\Hospital\DailyRecord;
use App\Hospital\NursingNote;
use App\Hospital\Ward;
use App\Hospital\Bed;
use App\Contact;
use Illuminate\Http\Request;
use DB;

class InpatientController extends Controller
{
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $admissions = Admission::where('business_id', $business_id)
            ->with(['patient', 'ward', 'bed'])
            ->where('status', 'admitted')
            ->orderBy('admitted_at', 'desc')
            ->get();
            
        return view('hospital.inpatient.index', compact('admissions'));
    }

    public function showAdmission($id)
    {
        $admission = Admission::with(['patient', 'ward', 'bed', 'daily_records', 'nursing_notes.nurse'])->findOrFail($id);
        return view('hospital.inpatient.show_admission', compact('admission'));
    }

    public function addNursingNote(Request $request)
    {
        NursingNote::create([
            'admission_id' => $request->admission_id,
            'noted_at' => now(),
            'observation' => $request->observation,
            'action_taken' => $request->action_taken,
            'fluid_input_ml' => $request->fluid_input_ml ?? 0,
            'fluid_output_ml' => $request->fluid_output_ml ?? 0,
            'nurse_id' => auth()->user()->id
        ]);

        return redirect()->back()->with('status', ['success' => 1, 'msg' => 'Nursing note added']);
    }

    public function discharge($id)
    {
        $admission = Admission::findOrFail($id);
        DB::transaction(function() use ($admission) {
            $admission->update([
                'status' => 'discharged',
                'discharged_at' => now()
            ]);
            
            // Free the bed
            Bed::where('id', $admission->bed_id)->update(['is_available' => 1]);
        });

        return redirect()->action([InpatientController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Patient discharged']);
    }
}
