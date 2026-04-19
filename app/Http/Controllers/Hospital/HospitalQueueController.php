<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\HospitalQueue;
use App\Contact;
use Illuminate\Http\Request;
use DB;

class HospitalQueueController extends Controller
{
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $location    = request()->get('location'); // e.g. ?location=triage

        $query = HospitalQueue::where('business_id', $business_id)
            ->where('status', '!=', 'completed')
            ->with(['patient', 'assigned_user'])
            ->orderBy('created_at', 'asc');

        if ($location) {
            $query->where('current_location', $location);
        }

        $queues           = $query->get();
        $active_location  = $location;

        // Counts per stage for the tab badges
        $stage_counts = HospitalQueue::where('business_id', $business_id)
            ->where('status', '!=', 'completed')
            ->selectRaw('current_location, COUNT(*) as cnt')
            ->groupBy('current_location')
            ->pluck('cnt', 'current_location');

        return view('hospital.queue.index', compact('queues', 'active_location', 'stage_counts'));
    }

    // Public Display View (for TV screens in waiting rooms)
    public function liveDisplay()
    {
        $business_id = request()->session()->get('user.business_id');
        
        $serving = HospitalQueue::where('business_id', $business_id)
            ->where('status', 'serving')
            ->with(['patient'])
            ->get();

        $waiting = HospitalQueue::where('business_id', $business_id)
            ->where('status', 'waiting')
            ->with(['patient'])
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        return view('hospital.queue.live_display', compact('serving', 'waiting'));
    }

    public function addToQueue(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        
        // Generate Token (Simple logic: A + count for the day)
        $today_count = HospitalQueue::where('business_id', $business_id)
            ->whereDate('created_at', \Carbon::now())
            ->count();
        $token = 'A-' . str_pad($today_count + 1, 3, '0', STR_PAD_LEFT);

        HospitalQueue::create([
            'business_id' => $business_id,
            'patient_id' => $request->patient_id,
            'appointment_id' => $request->appointment_id,
            'token_number' => $token,
            'current_location' => 'triage',
            'status' => 'waiting'
        ]);

        return redirect()->back()->with('status', ['success' => 1, 'msg' => 'Patient added to queue. Token: ' . $token]);
    }

    public function movePatient(Request $request)
    {
        $queue = HospitalQueue::findOrFail($request->queue_id);
        $queue->update([
            'current_location' => $request->next_location,
            'status' => 'waiting',
            'assigned_to' => $request->assigned_to ?? null
        ]);

        return redirect()->back()->with('status', ['success' => 1, 'msg' => 'Patient moved to ' . ucfirst($request->next_location)]);
    }

    public function updateStatus(Request $request)
    {
        $queue = HospitalQueue::findOrFail($request->queue_id);
        $data = ['status' => $request->status];
        
        if($request->status == 'serving') {
            $data['started_at'] = \Carbon::now();
        } elseif($request->status == 'completed') {
            $data['completed_at'] = \Carbon::now();
        }

        $queue->update($data);

        return redirect()->back();
    }
}
