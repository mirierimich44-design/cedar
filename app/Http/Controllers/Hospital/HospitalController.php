<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\Appointment;
use App\Hospital\Consultation;
use App\Hospital\Ward;
use App\Hospital\Bed;
use App\Hospital\Admission;
use App\Hospital\DailyRecord;
use App\Hospital\LabTest;
use App\Hospital\LabRequest;
use App\Hospital\Prescription;
use App\Hospital\HospitalQueue;
use App\Contact;
use App\User;
use App\Variation;
use Illuminate\Http\Request;
use DB;

class HospitalController extends Controller
{
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $appointments = Appointment::where('business_id', $business_id)
            ->with(['patient', 'doctor'])
            ->orderBy('appointment_date', 'asc')
            ->get();
            
        return view('hospital.index', compact('appointments'));
    }

    // IPD Index - Ward & Bed Management
    public function ipdIndex()
    {
        $business_id = request()->session()->get('user.business_id');
        $wards = Ward::where('business_id', $business_id)
            ->with(['beds' => function($query) {
                $query->with(['ward']);
            }])
            ->get();

        $admissions = Admission::where('business_id', $business_id)
            ->where('status', 'admitted')
            ->with(['patient', 'bed.ward'])
            ->get();
            
        return view('hospital.ipd.index', compact('wards', 'admissions'));
    }

    public function createAdmission()
    {
        $business_id = request()->session()->get('user.business_id');
        $patients = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->pluck('name', 'id');
        
        $available_beds = Bed::whereHas('ward', function($query) use ($business_id) {
                $query->where('business_id', $business_id);
            })
            ->where('status', 'available')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->ward->name . ' - Bed: ' . $item->bed_number];
            });
            
        return view('hospital.ipd.create_admission', compact('patients', 'available_beds'));
    }

    public function storeAdmission(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        
        DB::transaction(function() use ($request, $business_id) {
            Admission::create([
                'business_id' => $business_id,
                'patient_id' => $request->patient_id,
                'bed_id' => $request->bed_id,
                'admitted_by' => auth()->user()->id,
                'admission_date' => $request->admission_date,
                'reason_for_admission' => $request->reason_for_admission,
                'status' => 'admitted'
            ]);

            Bed::where('id', $request->bed_id)->update(['status' => 'occupied']);
        });

        return redirect()->action([HospitalController::class, 'ipdIndex'])
            ->with('status', ['success' => 1, 'msg' => 'Patient admitted successfully']);
    }

    public function dischargePatient($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $admission = Admission::where('business_id', $business_id)->findOrFail($id);

        DB::transaction(function() use ($admission) {
            $admission->update([
                'status' => 'discharged',
                'discharge_date' => \Carbon::now()
            ]);

            Bed::where('id', $admission->bed_id)->update(['status' => 'available']);
        });

        return redirect()->action([HospitalController::class, 'ipdIndex'])
            ->with('status', ['success' => 1, 'msg' => 'Patient discharged successfully']);
    }

    public function addDailyRecord($id)
    {
        $admission = Admission::with('patient')->findOrFail($id);
        return view('hospital.ipd.daily_record', compact('admission'));
    }

    public function storeDailyRecord(Request $request)
    {
        DailyRecord::create([
            'admission_id' => $request->admission_id,
            'recorded_by' => auth()->user()->id,
            'vitals' => $request->vitals,
            'notes' => $request->notes
        ]);

        return redirect()->action([HospitalController::class, 'ipdIndex'])
            ->with('status', ['success' => 1, 'msg' => 'Daily record added']);
    }

    public function createAppointment()
    {
        $business_id = request()->session()->get('user.business_id');
        $patients = Contact::where('business_id', $business_id)
            ->where('type', 'customer') // Patients are stored as customers
            ->pluck('name', 'id');
        $doctors = User::where('business_id', $business_id)
            ->pluck('first_name', 'id');
            
        return view('hospital.create_appointment', compact('patients', 'doctors'));
    }

    public function storeAppointment(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        Appointment::create([
            'business_id' => $business_id,
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'notes' => $request->notes,
            'priority' => $request->priority,
            'created_by' => $user_id
        ]);

        return redirect()->action([HospitalController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Appointment created successfully']);
    }

    public function triage($id)
    {
        $queue = HospitalQueue::with('patient')->findOrFail($id);
        return view('hospital.triage', compact('queue'));
    }

    public function storeTriage(Request $request)
    {
        $queue = HospitalQueue::findOrFail($request->queue_id);
        
        // Update the queue to move to consultation
        $queue->update([
            'current_location' => 'consultation',
            'status' => 'waiting'
        ]);

        return redirect()->action([HospitalQueueController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Triage completed. Patient moved to Doctor.']);
    }

    public function consultation($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $appointment = Appointment::with(['patient.patientDetails'])->findOrFail($id);
        $lab_tests = LabTest::where('business_id', $business_id)->pluck('name', 'id');
        
        return view('hospital.consultation', compact('appointment', 'lab_tests'));
    }

    public function searchDrugs(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $term = $request->input('q');

        $query = Variation::join('products as p', 'variations.product_id', '=', 'p.id')
                ->where('p.business_id', $business_id)
                ->where('p.type', '!=', 'modifier');

        if (!empty($term)) {
            $query->where(function($q) use ($term) {
                $q->where('p.name', 'like', '%' . $term . '%')
                  ->orWhere('p.sku', 'like', '%' . $term . '%')
                  ->orWhere('variations.sub_sku', 'like', '%' . $term . '%');
            });
        }

        $products = $query->select('variations.id', DB::raw('CONCAT(p.name, " - ", variations.sub_sku) as text'))
                          ->limit(20)
                          ->get();

        return response()->json($products);
    }

    public function storeConsultation(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        
        DB::transaction(function() use ($request, $business_id) {
            $consultation = Consultation::create([
                'business_id' => $business_id,
                'patient_id' => $request->patient_id,
                'doctor_id' => auth()->user()->id,
                'appointment_id' => $request->appointment_id,
                'vitals' => $request->vitals,
                'symptoms' => $request->symptoms,
                'diagnosis' => $request->diagnosis,
                'status' => 'completed'
            ]);

            // Save Structured Prescriptions
            if (!empty($request->drugs)) {
                foreach ($request->drugs as $drug) {
                    if (!empty($drug['variation_id']) || !empty($drug['drug_name'])) {
                        Prescription::create([
                            'business_id' => $business_id,
                            'patient_id' => $request->patient_id,
                            'doctor_id' => auth()->user()->id,
                            'consultation_id' => $consultation->id,
                            'variation_id' => $drug['variation_id'] ?? null,
                            'drug_name' => $drug['drug_name'] ?? 'Manual Drug',
                            'quantity' => $drug['quantity'] ?? 1,
                            'dosage' => $drug['dosage'] ?? '',
                            'frequency' => $drug['frequency'] ?? '',
                            'duration' => $drug['duration'] ?? '',
                            'status' => 'pending'
                        ]);
                    }
                }
            }

            // Create lab requests if tests were selected
            if (!empty($request->lab_tests)) {
                foreach ($request->lab_tests as $test_id) {
                    LabRequest::create([
                        'business_id' => $business_id,
                        'patient_id' => $request->patient_id,
                        'doctor_id' => auth()->user()->id,
                        'test_id' => $test_id,
                        'consultation_id' => $consultation->id,
                        'status' => 'ordered'
                    ]);
                }
            }

            if($request->appointment_id) {
                Appointment::where('id', $request->appointment_id)->update(['status' => 'completed']);
            }
        });

        return redirect()->action([HospitalController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Consultation completed successfully']);
    }
}
