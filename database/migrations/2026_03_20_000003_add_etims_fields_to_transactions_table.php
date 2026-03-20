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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('etims_invoice_number')->nullable()->after('final_total');
            $table->text('etims_qr_url')->nullable()->after('etims_invoice_number');
            $table->text('etims_signature')->nullable()->after('etims_qr_url');
            $table->enum('etims_sync_status', ['pending', 'success', 'failed'])->default('pending')->after('etims_signature');
            $table->text('etims_sync_error')->nullable()->after('etims_sync_status');
            $table->timestamp('etims_synced_at')->nullable()->after('etims_sync_error');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'etims_invoice_number', 
                'etims_qr_url', 
                'etims_signature', 
                'etims_sync_status', 
                'etims_sync_error',
                'etims_synced_at'
            ]);
        });
    }
};
