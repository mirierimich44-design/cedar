<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\PhysioPlan;
use App\Hospital\PhysioSession;
use App\Contact;
use Illuminate\Http\Request;

class PhysiotherapyController extends Controller
{
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $plans = PhysioPlan::where('business_id', $business_id)
            ->with(['patient', 'doctor'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('hospital.physio.index', compact('plans'));
    }

    public function createPlan()
    {
        $business_id = request()->session()->get('user.business_id');
        $patients = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        return view('hospital.physio.create_plan', compact('patients'));
    }

    public function storePlan(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        PhysioPlan::create([
            'business_id' => $business_id,
            'patient_id' => $request->patient_id,
            'doctor_id' => auth()->user()->id,
            'diagnosis' => $request->diagnosis,
            'treatment_goals' => $request->treatment_goals,
            'total_sessions_planned' => $request->total_sessions_planned
        ]);

        return redirect()->action([PhysiotherapyController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Physiotherapy plan created']);
    }

    public function addSession($plan_id)
    {
        $plan = PhysioPlan::with('patient')->findOrFail($plan_id);
        return view('hospital.physio.add_session', compact('plan'));
    }

    public function storeSession(Request $request)
    {
        PhysioSession::create([
            'plan_id' => $request->plan_id,
            'session_date' => $request->session_date,
            'exercises_performed' => $request->exercises_performed,
            'progress_notes' => $request->progress_notes,
            'therapist_id' => auth()->user()->id
        ]);

        return redirect()->action([PhysiotherapyController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Session recorded']);
    }
}
