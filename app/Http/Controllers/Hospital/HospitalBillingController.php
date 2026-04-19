<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\Consultation;
use App\Hospital\LabRequest;
use App\Hospital\Admission;
use App\Hospital\DentalProcedure;
use App\Contact;
use App\Transaction;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use DB;

class HospitalBillingController extends Controller
{
    protected $transactionUtil;

    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        
        // Patients with unbilled items
        $patients = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->where(function($query) {
                $query->whereHas('consultations', function($q) { $q->whereNull('transaction_id'); })
                      ->orWhereHas('labRequests', function($q) { $q->whereNull('transaction_id'); })
                      ->orWhereHas('admissions', function($q) { $q->whereNull('transaction_id'); });
            })
            ->get();

        return view('hospital.billing.index', compact('patients'));
    }

    public function patientBill($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $patient = Contact::with(['patientDetails.insurer', 'patientDetails.insuranceScheme'])->findOrFail($id);

        $unbilled_consultations = Consultation::where('patient_id', $id)
            ->whereNull('transaction_id')
            ->get();

        $unbilled_labs = LabRequest::where('patient_id', $id)
            ->whereNull('transaction_id')
            ->with('test')
            ->get();

        $unbilled_admissions = Admission::where('patient_id', $id)
            ->whereNull('transaction_id')
            ->with('bed.ward')
            ->get();

        $unbilled_dental = DentalProcedure::where('patient_id', $id)
            ->whereNull('transaction_id')
            ->get();

        return view('hospital.billing.patient_bill', compact('patient', 'unbilled_consultations', 'unbilled_labs', 'unbilled_admissions', 'unbilled_dental'));
    }

    public function createInvoice(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');
        $patient_id = $request->patient_id;

        DB::transaction(function() use ($request, $business_id, $user_id, $patient_id) {
            
            $final_total = 0;

            // 1. Process Consultations
            if (!empty($request->consultations)) {
                foreach ($request->consultations as $c_id => $fee) {
                    $final_total += $fee;
                }
            }

            // 2. Process Labs
            if (!empty($request->labs)) {
                foreach ($request->labs as $l_id => $fee) {
                    $final_total += $fee;
                }
            }

            // 3. Process Admissions (Bed Charges)
            if (!empty($request->admissions)) {
                foreach ($request->admissions as $a_id => $data) {
                    $days = $data['days'];
                    $rate = $data['rate'];
                    $final_total += ($days * $rate);
                }
            }

            // 4. Process Dental
            if (!empty($request->dental)) {
                foreach ($request->dental as $d_id => $fee) {
                    $final_total += $fee;
                }
            }

            if ($final_total > 0) {
                // Create Transaction in main system
                $transaction_data = [
                    'business_id' => $business_id,
                    'contact_id' => $patient_id,
                    'type' => 'sell',
                    'status' => 'final',
                    'payment_status' => 'due',
                    'transaction_date' => \Carbon::now(),
                    'final_total' => $final_total,
                    'created_by' => $user_id,
                    'invoice_no' => $this->transactionUtil->getInvoiceNumber($business_id, 'final', null)
                ];

                $transaction = Transaction::create($transaction_data);

                // Update hospital tables with transaction_id
                if (!empty($request->consultations)) {
                    Consultation::whereIn('id', array_keys($request->consultations))->update(['transaction_id' => $transaction->id]);
                }
                if (!empty($request->labs)) {
                    LabRequest::whereIn('id', array_keys($request->labs))->update(['transaction_id' => $transaction->id]);
                }
                if (!empty($request->admissions)) {
                    Admission::whereIn('id', array_keys($request->admissions))->update(['transaction_id' => $transaction->id]);
                }
                if (!empty($request->dental)) {
                    DentalProcedure::whereIn('id', array_keys($request->dental))->update(['transaction_id' => $transaction->id]);
                }
            }
        });

        return redirect()->action([HospitalBillingController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Hospital invoice generated successfully']);
    }
}
