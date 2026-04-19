<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\Consultation;
use App\Hospital\Prescription;
use App\Hospital\LabRequest;
use App\Hospital\RadiographyRequest;
use App\Hospital\TheatreBooking;
use App\Hospital\PhysioSession;
use App\Contact;
use Illuminate\Http\Request;

class PatientTimelineController extends Controller
{
    /**
     * Display a consolidated medical history for a patient.
     */
    public function show($patient_id)
    {
        $patient = Contact::findOrFail($patient_id);
        
        // 1. Get Consultations
        $consultations = Consultation::where('patient_id', $patient_id)->with('doctor')->get();
        
        // 2. Get Lab Requests
        $labs = LabRequest::where('patient_id', $patient_id)->with(['test', 'lab_tech'])->get();
        
        // 3. Get Radiography
        $imaging = RadiographyRequest::where('patient_id', $patient_id)->with(['test', 'radiologist'])->get();
        
        // 4. Get Surgeries
        $surgeries = TheatreBooking::where('patient_id', $patient_id)->with(['surgery', 'surgeon'])->get();

        // 5. Get Physio Sessions
        $physio = PhysioSession::whereHas('plan', function($q) use ($patient_id) {
            $q->where('patient_id', $patient_id);
        })->with('therapist')->get();

        // 6. Consolidate into a chronological timeline
        $timeline = collect();

        foreach ($consultations as $c) {
            $timeline->push([
                'date' => $c->created_at,
                'type' => 'Consultation',
                'title' => 'Doctor Consultation',
                'description' => $c->diagnosis,
                'provider' => 'Dr. ' . ($c->doctor->user_full_name ?? 'N/A'),
                'icon' => 'fa-user-md',
                'bg' => 'bg-blue'
            ]);
        }

        foreach ($labs as $l) {
            $timeline->push([
                'date' => $l->created_at,
                'type' => 'Lab',
                'title' => 'Laboratory: ' . $l->test->name,
                'description' => 'Status: ' . ucfirst($l->status),
                'provider' => $l->lab_tech->user_full_name ?? 'Pending',
                'icon' => 'fa-flask',
                'bg' => 'bg-purple'
            ]);
        }

        foreach ($imaging as $i) {
            $timeline->push([
                'date' => $i->created_at,
                'type' => 'Imaging',
                'title' => 'Radiography: ' . $i->test->name,
                'description' => $i->conclusion,
                'provider' => $i->radiologist->user_full_name ?? 'Pending',
                'icon' => 'fa-radiation',
                'bg' => 'bg-orange'
            ]);
        }

        foreach ($surgeries as $s) {
            $timeline->push([
                'date' => $s->scheduled_at,
                'type' => 'Surgery',
                'title' => 'Theatre: ' . $s->surgery->name,
                'description' => $s->post_op_diagnosis,
                'provider' => 'Dr. ' . ($s->surgeon->user_full_name ?? 'N/A'),
                'icon' => 'fa-cut',
                'bg' => 'bg-red'
            ]);
        }

        foreach ($physio as $p) {
            $timeline->push([
                'date' => $p->session_date,
                'type' => 'Physio',
                'title' => 'Physiotherapy Session',
                'description' => $p->progress_notes,
                'provider' => $p->therapist->user_full_name ?? 'N/A',
                'icon' => 'fa-walking',
                'bg' => 'bg-green'
            ]);
        }

        $timeline = $timeline->sortByDesc('date');

        return view('hospital.timeline.show', compact('patient', 'timeline'));
    }
}
