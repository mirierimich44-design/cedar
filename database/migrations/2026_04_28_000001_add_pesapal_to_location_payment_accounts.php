<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPesapalToLocationPaymentAccounts extends Migration
{
    public function up()
    {
        DB::table('business_locations')
            ->whereNotNull('default_payment_accounts')
            ->get()
            ->each(function ($location) {
                $accounts = json_decode($location->default_payment_accounts, true);
                if (! is_array($accounts)) return;

                $changed = false;

                if (! array_key_exists('pesapal', $accounts)) {
                    $accounts['pesapal'] = ['is_enabled' => 1, 'account' => null];
                    $changed = true;
                }

                if (! array_key_exists('kcb_buni', $accounts)) {
                    $accounts['kcb_buni'] = ['is_enabled' => 1, 'account' => null];
                    $changed = true;
                }

                if ($changed) {
                    DB::table('business_locations')
                        ->where('id', $location->id)
                        ->update(['default_payment_accounts' => json_encode($accounts)]);
                }
            });
    }

    public function down()
    {
        DB::table('business_locations')
            ->whereNotNull('default_payment_accounts')
            ->get()
            ->each(function ($location) {
                $accounts = json_decode($location->default_payment_accounts, true);
                if (! is_array($accounts)) return;

                unset($accounts['pesapal'], $accounts['kcb_buni']);

                DB::table('business_locations')
                    ->where('id', $location->id)
                    ->update(['default_payment_accounts' => json_encode($accounts)]);
            });
    }
}
