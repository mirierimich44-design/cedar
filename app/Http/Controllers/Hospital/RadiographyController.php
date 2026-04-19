<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\RadiographyTest;
use App\Hospital\RadiographyRequest;
use App\Contact;
use Illuminate\Http\Request;
use DB;

class RadiographyController extends Controller
{
    /**
     * Display listing of radiography requests.
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $requests = RadiographyRequest::where('business_id', $business_id)
            ->with(['patient', 'doctor', 'test', 'radiologist'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('hospital.radiography.index', compact('requests'));
    }

    /**
     * Show form to add a new test to the catalog.
     */
    public function createTest()
    {
        return view('hospital.radiography.create_test');
    }

    /**
     * Store new radiography test in catalog.
     */
    public function storeTest(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        RadiographyTest::create([
            'business_id' => $business_id,
            'name' => $request->name,
            'short_name' => $request->short_name,
            'type' => $request->type,
            'price' => $request->price
        ]);

        return redirect()->action([RadiographyController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Radiography test added to catalog']);
    }

    /**
     * Enter findings for a request.
     */
    public function enterResult($id)
    {
        $request = RadiographyRequest::with(['patient', 'test'])->findOrFail($id);
        return view('hospital.radiography.enter_result', compact('request'));
    }

    /**
     * Store findings.
     */
    public function storeResult(Request $request)
    {
        $radiography_request = RadiographyRequest::findOrFail($request->request_id);
        $radiography_request->update([
            'radiologist_findings' => $request->radiologist_findings,
            'conclusion' => $request->conclusion,
            'status' => 'completed',
            'radiologist_id' => auth()->user()->id
        ]);

        return redirect()->action([RadiographyController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Imaging findings updated']);
    }
}
