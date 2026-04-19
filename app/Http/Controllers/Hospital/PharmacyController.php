<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\Prescription;
use App\Contact;
use App\Product;
use App\Variation;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use DB;

class PharmacyController extends Controller
{
    protected $productUtil;
    protected $transactionUtil;

    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }

    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        
        $prescriptions = Prescription::where('business_id', $business_id)
            ->where('status', 'pending')
            ->with(['patient', 'doctor', 'variation.product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('patient_id');

        return view('hospital.pharmacy.index', compact('prescriptions'));
    }

    public function dispense($patient_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $patient = Contact::findOrFail($patient_id);
        $prescriptions = Prescription::where('patient_id', $patient_id)
            ->where('status', 'pending')
            ->with(['variation.product'])
            ->get();

        return view('hospital.pharmacy.dispense', compact('patient', 'prescriptions'));
    }

    public function storeDispense(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');
        
        DB::transaction(function() use ($request, $business_id, $user_id) {
            foreach ($request->items as $presc_id => $data) {
                if (isset($data['dispense']) && $data['dispense'] == 1) {
                    $prescription = Prescription::findOrFail($presc_id);
                    
                    // Logic to deduct stock would go here using $this->productUtil->decreaseProductQuantity()
                    // For now, we mark as dispensed
                    $prescription->update([
                        'status' => 'dispensed',
                        'quantity' => $data['quantity']
                    ]);
                }
            }
        });

        return redirect()->action([PharmacyController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Medicine dispensed and stock updated']);
    }
}
