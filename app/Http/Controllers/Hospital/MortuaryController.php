<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\MortuaryRecord;
use App\Contact;
use Illuminate\Http\Request;

class MortuaryController extends Controller
{
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $records = MortuaryRecord::where('business_id', $business_id)
            ->with('patient')
            ->orderBy('admitted_at', 'desc')
            ->get();
            
        return view('hospital.mortuary.index', compact('records'));
    }

    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $patients = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        return view('hospital.mortuary.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        MortuaryRecord::create([
            'business_id' => $business_id,
            'patient_id' => $request->patient_id,
            'body_name' => $request->body_name,
            'admitted_at' => $request->admitted_at,
            'relative_name' => $request->relative_name,
            'relative_phone' => $request->relative_phone,
            'storage_location' => $request->storage_location,
            'cause_of_death' => $request->cause_of_death,
            'status' => 'admitted'
        ]);

        return redirect()->action([MortuaryController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Body intake recorded']);
    }

    public function release($id)
    {
        $record = MortuaryRecord::findOrFail($id);
        $record->update([
            'status' => 'released',
            'released_at' => now()
        ]);

        return redirect()->action([MortuaryController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Body released']);
    }
}
