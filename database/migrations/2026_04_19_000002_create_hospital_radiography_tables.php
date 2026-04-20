<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // 1. Radiography Test Catalog (X-Ray, Ultrasound, CT Scan, etc.)
        Schema::create('hospital_radiography_tests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->enum('type', ['x-ray', 'ultrasound', 'ct-scan', 'mri', 'other'])->default('x-ray');
            $table->decimal('price', 22, 4)->default(0);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        // 2. Radiography Requests & Results
        Schema::create('hospital_radiography_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('patient_id')->unsigned();
            $table->integer('doctor_id')->unsigned();
            $table->integer('test_id')->unsigned();
            $table->integer('consultation_id')->unsigned()->nullable();
            $table->integer('transaction_id')->unsigned()->nullable(); // For billing
            $table->enum('status', ['ordered', 'paid', 'completed', 'cancelled'])->default('ordered');
            $table->text('clinical_history')->nullable();
            $table->text('radiologist_findings')->nullable();
            $table->text('conclusion')->nullable();
            $table->integer('radiologist_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('hospital_radiography_tests')->onDelete('cascade');
            $table->foreign('radiologist_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_radiography_requests');
        Schema::dropIfExists('hospital_radiography_tests');
    }
};
