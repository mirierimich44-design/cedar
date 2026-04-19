<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\Theatre;
use App\Hospital\Surgery;
use App\Hospital\TheatreBooking;
use App\Contact;
use App\User;
use Illuminate\Http\Request;
use DB;

class TheatreController extends Controller
{
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $bookings = TheatreBooking::where('business_id', $business_id)
            ->with(['patient', 'surgery', 'theatre', 'surgeon'])
            ->orderBy('scheduled_at', 'asc')
            ->get();
            
        return view('hospital.theatre.index', compact('bookings'));
    }

    public function createBooking()
    {
        $business_id = request()->session()->get('user.business_id');
        $patients = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $surgeries = Surgery::where('business_id', $business_id)->pluck('name', 'id');
        $theatres = Theatre::where('business_id', $business_id)->where('is_active', 1)->pluck('name', 'id');
        $users = User::where('business_id', $business_id)->pluck('first_name', 'id'); // Simplified

        return view('hospital.theatre.create_booking', compact('patients', 'surgeries', 'theatres', 'users'));
    }

    public function storeBooking(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        TheatreBooking::create([
            'business_id' => $business_id,
            'patient_id' => $request->patient_id,
            'surgery_id' => $request->surgery_id,
            'theatre_id' => $request->theatre_id,
            'surgeon_id' => $request->surgeon_id,
            'anaesthetist_id' => $request->anaesthetist_id,
            'scheduled_at' => $request->scheduled_at,
            'pre_op_diagnosis' => $request->pre_op_diagnosis,
            'status' => 'scheduled'
        ]);

        return redirect()->action([TheatreController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Surgery scheduled successfully']);
    }

    public function editRecord($id)
    {
        $booking = TheatreBooking::with(['patient', 'surgery', 'theatre'])->findOrFail($id);
        return view('hospital.theatre.edit_record', compact('booking'));
    }

    public function updateRecord(Request $request, $id)
    {
        $booking = TheatreBooking::findOrFail($id);
        $booking->update([
            'post_op_diagnosis' => $request->post_op_diagnosis,
            'procedure_notes' => $request->procedure_notes,
            'complications' => $request->complications,
            'status' => 'completed',
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at
        ]);

        return redirect()->action([TheatreController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Surgical record updated']);
    }
}
