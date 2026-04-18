<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaasSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('saas_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general'); // general, trial, campaign, payment
            $table->string('type', 20)->default('string');   // string, int, bool, json
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        $now = now();
        \DB::table('saas_settings')->insert([
            ['key' => 'trial_enabled',        'value' => '1',   'group' => 'trial',    'type' => 'bool',   'label' => 'Free Trial Enabled',      'description' => 'Allow new signups to start a free trial.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'trial_days',           'value' => '3',   'group' => 'trial',    'type' => 'int',    'label' => 'Trial Length (days)',     'description' => '0 = no trial. Common values: 1, 3, 7, 14.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'trial_grace_days',     'value' => '3',   'group' => 'trial',    'type' => 'int',    'label' => 'Trial Grace Period (days)', 'description' => 'Extra days after trial ends before account is suspended.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'campaign_active',      'value' => '0',   'group' => 'campaign', 'type' => 'bool',   'label' => 'Campaign Banner Active',  'description' => 'Show the campaign banner on pricing page.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'campaign_title',       'value' => 'Limited Time: Extended 14-Day Free Trial', 'group' => 'campaign', 'type' => 'string', 'label' => 'Campaign Title', 'description' => 'Headline shown on pricing page.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'campaign_message',     'value' => 'Sign up today and get double the normal trial period.', 'group' => 'campaign', 'type' => 'string', 'label' => 'Campaign Message', 'description' => 'Sub-message for the campaign banner.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'campaign_discount_percent', 'value' => '0', 'group' => 'campaign', 'type' => 'int', 'label' => 'Campaign Discount %', 'description' => '0-100. Applied to all feature prices during campaign.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mpesa_enabled',        'value' => '1',    'group' => 'payment', 'type' => 'bool',   'label' => 'M-Pesa Enabled',          'description' => 'Accept M-Pesa payments at checkout.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mpesa_mode',           'value' => 'manual', 'group' => 'payment', 'type' => 'string', 'label' => 'M-Pesa Mode',           'description' => 'manual = paybill instructions only. stk = Daraja STK Push auto-prompt.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mpesa_paybill',        'value' => '4117852', 'group' => 'payment', 'type' => 'string', 'label' => 'M-Pesa Paybill Number', 'description' => 'Shown on checkout pending page.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mpesa_till',           'value' => '',     'group' => 'payment', 'type' => 'string', 'label' => 'M-Pesa Till Number (optional)', 'description' => 'Leave blank to use paybill only.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mpesa_env',            'value' => 'sandbox', 'group' => 'payment', 'type' => 'string', 'label' => 'Daraja Environment',   'description' => 'sandbox or production. Switch to production when credentials are live.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mpesa_shortcode',      'value' => '',     'group' => 'payment', 'type' => 'string', 'label' => 'Daraja Shortcode',       'description' => 'STK Push business shortcode (Paybill or Till).', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mpesa_consumer_key',   'value' => '',     'group' => 'payment', 'type' => 'string', 'label' => 'Daraja Consumer Key',    'description' => 'From developer.safaricom.co.ke app credentials.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mpesa_consumer_secret','value' => '',     'group' => 'payment', 'type' => 'string', 'label' => 'Daraja Consumer Secret', 'description' => 'Keep this secret — do not share.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mpesa_passkey',        'value' => '',     'group' => 'payment', 'type' => 'string', 'label' => 'Lipa Na M-Pesa Passkey', 'description' => 'Used for STK Push password generation.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mpesa_callback_url',   'value' => 'https://apexpos.co.ke/api/mpesa/callback', 'group' => 'payment', 'type' => 'string', 'label' => 'STK Callback URL', 'description' => 'Must be HTTPS and publicly reachable.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'support_phone',        'value' => '+254 700 000 000', 'group' => 'general', 'type' => 'string', 'label' => 'Support Phone', 'description' => 'Shown on checkout/portal pages.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'support_email',        'value' => 'support@apexpos.co.ke', 'group' => 'general', 'type' => 'string', 'label' => 'Support Email', 'description' => '', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('saas_settings');
    }
}
