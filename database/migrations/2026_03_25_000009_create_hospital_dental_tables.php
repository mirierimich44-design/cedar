<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalDentalTables extends Migration
{
    public function up()
    {
        // 1. Tooth Charting (Stores status of each tooth)
        Schema::create('hospital_dental_teeth', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned();
            $table->string('tooth_number'); // 1-32 for adults, A-T for kids
            $table->string('status')->nullable(); // e.g., decayed, missing, filled, crown
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('cascade');
        });

        // 2. Dental Procedures (Specific treatments)
        Schema::create('hospital_dental_procedures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('patient_id')->unsigned();
            $table->integer('doctor_id')->unsigned();
            $table->integer('consultation_id')->unsigned()->nullable();
            $table->string('procedure_name');
            $table->decimal('price', 22, 4)->default(0);
            $table->enum('status', ['planned', 'completed', 'cancelled'])->default('planned');
            $table->integer('transaction_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_dental_procedures');
        Schema::dropIfExists('hospital_dental_teeth');
    }
}
