<?php

namespace App\Jobs;

use App\Business;
use App\Transaction;
use App\Utils\DigitaxService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncEtimsPurchaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction_id;

    public function __construct($transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }

    public function handle()
    {
        $transaction = Transaction::find($this->transaction_id);
        if (!$transaction) {
            return;
        }

        $business = Business::find($transaction->business_id);
        if (empty($business->etims_enabled) || empty($business->digitax_api_key)) {
            return;
        }

        $digitaxService = new DigitaxService();
        $digitaxService->setApiKey($business->digitax_api_key)->createPurchase($transaction);
    }
}
