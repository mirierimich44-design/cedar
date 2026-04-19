<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\PregnancyProfile;
use App\Hospital\AncVisit;
use App\Contact;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class MaternityController extends Controller
{
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $profiles = PregnancyProfile::where('business_id', $business_id)
            ->with('patient')
            ->where('status', 'ongoing')
            ->orderBy('edd_date', 'asc')
            ->get();
            
        return view('hospital.maternity.index', compact('profiles'));
    }

    public function createProfile()
    {
        $business_id = request()->session()->get('user.business_id');
        $patients = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('name', 'id');
            
        return view('hospital.maternity.create_profile', compact('patients'));
    }

    public function storeProfile(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        
        // Calculate EDD (LMP + 280 days) if not provided
        $edd = $request->edd_date;
        if(empty($edd) && !empty($request->lmp_date)) {
            $edd = Carbon::parse($request->lmp_date)->addDays(280)->toDateString();
        }

        PregnancyProfile::create([
            'business_id' => $business_id,
            'patient_id' => $request->patient_id,
            'lmp_date' => $request->lmp_date,
            'edd_date' => $edd,
            'gravida' => $request->gravida,
            'parity' => $request->parity,
            'medical_history' => $request->medical_history,
            'status' => 'ongoing'
        ]);

        return redirect()->action([MaternityController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Pregnancy profile created']);
    }

    public function showProfile($id)
    {
        $profile = PregnancyProfile::with(['patient', 'visits.doctor'])->findOrFail($id);
        return view('hospital.maternity.show_profile', compact('profile'));
    }

    public function storeAncVisit(Request $request)
    {
        AncVisit::create([
            'pregnancy_profile_id' => $request->pregnancy_profile_id,
            'doctor_id' => auth()->user()->id,
            'visit_date' => $request->visit_date,
            'weight' => $request->weight,
            'bp' => $request->bp,
            'fundal_height' => $request->fundal_height,
            'fetal_presentation' => $request->fetal_presentation,
            'fetal_heart_rate' => $request->fetal_heart_rate,
            'tt_dose' => $request->tt_dose,
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('status', ['success' => 1, 'msg' => 'ANC Visit recorded']);
    }
}
