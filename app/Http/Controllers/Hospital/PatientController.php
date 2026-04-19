<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Contact;
use App\Hospital\PatientDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    /**
     * List patients (customers with hospital patient_details, plus any
     * customer contact — the clinic can attach a MRN anytime).
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        $patients = Contact::leftJoin('patient_details as pd', 'pd.contact_id', '=', 'contacts.id')
            ->where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['customer', 'both'])
            ->select(
                'contacts.id',
                'contacts.name',
                'contacts.mobile',
                'contacts.email',
                'pd.uhid_number',
                'pd.gender',
                'pd.date_of_birth',
                'pd.blood_group'
            )
            ->orderBy('contacts.id', 'desc')
            ->paginate(25);

        return view('hospital.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('hospital.patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'mobile'        => 'required|string|max:30',
            'email'         => 'nullable|email|max:255',
            'gender'        => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date|before_or_equal:today',
        ]);

        $business_id = $request->session()->get('user.business_id');
        $user_id     = $request->session()->get('user.id');

        DB::beginTransaction();
        try {
            // 1. Create contact as customer
            $contact = Contact::create([
                'business_id'    => $business_id,
                'type'           => 'customer',
                'name'           => $request->name,
                'first_name'     => $request->name,
                'mobile'         => $request->mobile,
                'email'          => $request->email,
                'address_line_1' => $request->address,
                'city'           => $request->city,
                'country'        => $request->country ?? 'Kenya',
                'contact_status' => 'active',
                'created_by'     => $user_id,
            ]);

            // 2. Generate MRN: FAC-YYYYMM-SEQ (sequence per business/month)
            $prefix = 'MRN-' . now()->format('Ym') . '-';
            $lastSeq = PatientDetail::where('uhid_number', 'like', $prefix . '%')
                ->orderBy('id', 'desc')
                ->value('uhid_number');
            $seq = $lastSeq ? ((int) substr($lastSeq, strlen($prefix))) + 1 : 1;
            $mrn = $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);

            // 3. Patient details row
            PatientDetail::create([
                'contact_id'               => $contact->id,
                'uhid_number'              => $mrn,
                'blood_group'              => $request->blood_group,
                'allergies'                => $request->allergies,
                'chronic_conditions'       => $request->chronic_conditions,
                'date_of_birth'            => $request->date_of_birth,
                'gender'                   => $request->gender,
                'emergency_contact_name'   => $request->emergency_contact_name,
                'emergency_contact_number' => $request->emergency_contact_number,
                'insurance_provider'       => $request->insurance_provider,
                'insurance_policy_number'  => $request->insurance_policy_number,
            ]);

            DB::commit();

            return redirect()->route('hospital.patients.index')
                ->with('status', ['success' => 1, 'msg' => "Patient registered. MRN: {$mrn}"]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Patient registration failed: ' . $e->getMessage());
            return back()->withInput()->with('status', [
                'success' => 0,
                'msg'     => 'Could not register patient: ' . $e->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $patient = Contact::where('business_id', $business_id)
            ->with(['patientDetails', 'consultations.doctor', 'labRequests.test', 'admissions'])
            ->findOrFail($id);

        return view('hospital.patients.show', compact('patient'));
    }
}
