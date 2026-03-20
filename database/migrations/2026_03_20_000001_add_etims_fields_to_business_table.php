<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->string('digitax_api_key')->nullable()->after('woocommerce_api_settings');
            $table->enum('etims_sync_mode', ['realtime', 'background', 'manual'])->default('background')->after('digitax_api_key');
            $table->string('etims_tpin')->nullable()->after('etims_sync_mode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn(['digitax_api_key', 'etims_sync_mode', 'etims_tpin']);
        });
    }
};
