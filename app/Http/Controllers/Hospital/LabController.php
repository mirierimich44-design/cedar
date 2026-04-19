<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\LabTest;
use App\Hospital\LabRequest;
use App\Contact;
use Illuminate\Http\Request;
use DB;

class LabController extends Controller
{
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $lab_requests = LabRequest::where('business_id', $business_id)
            ->with(['patient', 'doctor', 'test'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('hospital.lab.index', compact('lab_requests'));
    }

    public function createTest()
    {
        return view('hospital.lab.create_test');
    }

    public function storeTest(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        LabTest::create([
            'business_id' => $business_id,
            'name' => $request->name,
            'short_name' => $request->short_name,
            'price' => $request->price,
            'result_unit' => $request->result_unit,
            'normal_range' => $request->normal_range
        ]);

        return redirect()->action([LabController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Lab test added to catalog']);
    }

    public function enterResult($id)
    {
        $request = LabRequest::with(['patient', 'test'])->findOrFail($id);
        return view('hospital.lab.enter_result', compact('request'));
    }

    public function storeResult(Request $request)
    {
        $lab_request = LabRequest::findOrFail($request->request_id);
        $lab_request->update([
            $lab_request->update([
                'radiologist_findings' => $request->radiologist_findings, // Assuming this is LabController... wait.
                'conclusion' => $request->conclusion,
                'status' => 'completed',
                'lab_tech_id' => auth()->user()->id
            ]);

            event(new \App\Events\LabTestCompleted($lab_request));

            return redirect()->action([LabController::class, 'index'])
                ->with('status', ['success' => 1, 'msg' => 'Test results updated']);
            }
            }

