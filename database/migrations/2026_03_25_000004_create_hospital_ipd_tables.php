<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalIpdTables extends Migration
{
    public function up()
    {
        // 1. Wards Table
        Schema::create('hospital_wards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        // 2. Beds Table
        Schema::create('hospital_beds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ward_id')->unsigned();
            $table->string('bed_number');
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->decimal('daily_rate', 22, 4)->default(0);
            $table->timestamps();

            $table->foreign('ward_id')->references('id')->on('hospital_wards')->onDelete('cascade');
        });

        // 3. Admissions Table
        Schema::create('hospital_admissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('patient_id')->unsigned();
            $table->integer('bed_id')->unsigned();
            $table->integer('admitted_by')->unsigned();
            $table->dateTime('admission_date');
            $table->dateTime('discharge_date')->nullable();
            $table->text('reason_for_admission')->nullable();
            $table->enum('status', ['admitted', 'discharged'])->default('admitted');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('bed_id')->references('id')->on('hospital_beds')->onDelete('cascade');
            $table->foreign('admitted_by')->references('id')->on('users')->onDelete('cascade');
        });

        // 4. Daily Monitoring / Nursing Notes
        Schema::create('hospital_daily_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admission_id')->unsigned();
            $table->integer('recorded_by')->unsigned();
            $table->json('vitals')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('admission_id')->references('id')->on('hospital_admissions')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_daily_records');
        Schema::dropIfExists('hospital_admissions');
        Schema::dropIfExists('hospital_beds');
        Schema::dropIfExists('hospital_wards');
    }
}
