<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\Consultation;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class HospitalReportController extends Controller
{
    public function moh705Index()
    {
        return view('hospital.reports.moh705_index');
    }

    public function moh705A(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end_date = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Under 5 years report
        $data = Consultation::where('business_id', $business_id)
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->whereHas('patient', function($query) {
                $query->whereHas('patientDetails', function($q) {
                    $q->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 5');
                });
            })
            ->select('diagnosis', DB::raw('count(*) as total'))
            ->groupBy('diagnosis')
            ->get();

        return view('hospital.reports.moh705A', compact('data', 'start_date', 'end_date'));
    }

    public function moh705B(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end_date = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Over 5 years report
        $data = Consultation::where('business_id', $business_id)
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->whereHas('patient', function($query) {
                $query->whereHas('patientDetails', function($q) {
                    $q->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 5');
                });
            })
            ->select('diagnosis', DB::raw('count(*) as total'))
            ->groupBy('diagnosis')
            ->get();

        return view('hospital.reports.moh705B', compact('data', 'start_date', 'end_date'));
    }
}
