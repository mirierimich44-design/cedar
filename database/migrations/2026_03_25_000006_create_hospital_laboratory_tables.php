<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalLaboratoryTables extends Migration
{
    public function up()
    {
        // 1. Lab Test Catalog (The tests the hospital offers)
        Schema::create('hospital_lab_tests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->decimal('price', 22, 4)->default(0);
            $table->string('result_unit')->nullable(); // e.g. mg/dL, g/L
            $table->string('normal_range')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        // 2. Lab Requests (Orders sent by doctors)
        Schema::create('hospital_lab_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('patient_id')->unsigned();
            $table->integer('doctor_id')->unsigned();
            $table->integer('test_id')->unsigned();
            $table->integer('consultation_id')->unsigned()->nullable();
            $table->enum('status', ['ordered', 'paid', 'sample_collected', 'completed', 'cancelled'])->default('ordered');
            $table->text('result_notes')->nullable();
            $table->json('result_data')->nullable(); // For detailed parameters
            $table->integer('lab_tech_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('hospital_lab_tests')->onDelete('cascade');
            $table->foreign('lab_tech_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_lab_requests');
        Schema::dropIfExists('hospital_lab_tests');
    }
}
