<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\DentalTooth;
use App\Hospital\DentalProcedure;
use App\Hospital\Appointment;
use App\Contact;
use Illuminate\Http\Request;
use DB;

class DentalController extends Controller
{
    public function index($patient_id)
    {
        $patient = Contact::findOrFail($patient_id);
        $teeth = DentalTooth::where('patient_id', $patient_id)->get()->keyBy('tooth_number');
        $procedures = DentalProcedure::where('patient_id', $patient_id)
            ->with('doctor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hospital.dental.index', compact('patient', 'teeth', 'procedures'));
    }

    public function updateTooth(Request $request)
    {
        $tooth = DentalTooth::updateOrCreate(
            ['patient_id' => $request->patient_id, 'tooth_number' => $request->tooth_number],
            ['status' => $request->status, 'notes' => $request->notes]
        );

        return response()->json(['success' => true, 'msg' => 'Tooth updated']);
    }

    public function addProcedure(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        
        DentalProcedure::create([
            'business_id' => $business_id,
            'patient_id' => $request->patient_id,
            'doctor_id' => auth()->user()->id,
            'procedure_name' => $request->procedure_name,
            'price' => $request->price,
            'status' => 'completed'
        ]);

        return redirect()->back()->with('status', ['success' => 1, 'msg' => 'Procedure recorded']);
    }
}
