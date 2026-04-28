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

                if (is_array($accounts) && ! array_key_exists('pesapal', $accounts)) {
                    $accounts['pesapal'] = [
                        'is_enabled' => 1,
                        'account'    => null,
                    ];

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

                if (is_array($accounts) && array_key_exists('pesapal', $accounts)) {
                    unset($accounts['pesapal']);

                    DB::table('business_locations')
                        ->where('id', $location->id)
                        ->update(['default_payment_accounts' => json_encode($accounts)]);
                }
            });
    }
}
