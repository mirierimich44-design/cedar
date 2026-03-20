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
        Schema::table('mpesa_settings', function (Blueprint $table) {
            $table->string('initiator_name')->nullable()->after('passkey');
            $table->text('security_credential')->nullable()->after('initiator_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mpesa_settings', function (Blueprint $table) {
            $table->dropColumn(['initiator_name', 'security_credential']);
        });
    }
};
