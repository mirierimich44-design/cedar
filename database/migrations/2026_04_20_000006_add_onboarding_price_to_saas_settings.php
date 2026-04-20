<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnboardingPriceToSaasSettings2026 extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('saas_settings')) return;

        // Only insert if not already present
        $exists = \DB::table('saas_settings')->where('key', 'onboarding_monthly_price')->exists();
        if (! $exists) {
            \DB::table('saas_settings')->insert([
                'key'         => 'onboarding_monthly_price',
                'value'       => '2999',
                'group'       => 'payment',
                'type'        => 'int',
                'label'       => 'Monthly Subscription Price (KES)',
                'description' => 'Shown during onboarding activation and on pricing page.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down()
    {
        \DB::table('saas_settings')->where('key', 'onboarding_monthly_price')->delete();
    }
}
