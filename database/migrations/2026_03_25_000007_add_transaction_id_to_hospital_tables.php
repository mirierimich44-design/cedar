<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionIdToHospitalTables extends Migration
{
    public function up()
    {
        Schema::table('hospital_consultations', function (Blueprint $table) {
            $table->integer('transaction_id')->unsigned()->nullable()->after('appointment_id');
            $table->decimal('consultation_fee', 22, 4)->default(0)->after('status');
        });

        Schema::table('hospital_lab_requests', function (Blueprint $table) {
            $table->integer('transaction_id')->unsigned()->nullable()->after('consultation_id');
        });

        Schema::table('hospital_admissions', function (Blueprint $table) {
            $table->integer('transaction_id')->unsigned()->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('hospital_consultations', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'consultation_fee']);
        });
        Schema::table('hospital_lab_requests', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
        });
        Schema::table('hospital_admissions', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
        });
    }
}
