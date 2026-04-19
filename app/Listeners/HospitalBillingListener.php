<?php

namespace App\Listeners;

use App\Hospital\LabRequest;
use App\Hospital\RadiographyRequest;
use App\Hospital\HospitalBilling;
use Illuminate\Support\Facades\DB;

class HospitalBillingListener
{
    /**
     * Triggered when a Lab or Imaging test is marked 'completed'
     */
    public function handle($event)
    {
        $request = $event->request;
        $business_id = $request->business_id;

        // Ensure we don't double-bill if a transaction already exists
        if ($request->transaction_id) {
            return;
        }

        DB::transaction(function () use ($request, $business_id) {
            // Calculate price based on the test
            $test = ($request instanceof LabRequest) ? $request->test : $request->test;
            $amount = $test->price ?? 0;

            // Create billing record
            $billing = HospitalBilling::create([
                'business_id' => $business_id,
                'patient_id' => $request->patient_id,
                'admission_id' => $request->consultation_id,
                'description' => 'Automated billing for ' . $test->name,
                'amount' => $amount,
                'type' => 'debit',
                'status' => 'pending'
            ]);

            // Link to request
            $request->update(['billing_id' => $billing->id]);
        });
    }
}
