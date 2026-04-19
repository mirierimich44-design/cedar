<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalConsultationsTable extends Migration
{
    public function up()
    {
        Schema::create('hospital_consultations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('patient_id')->unsigned();
            $table->integer('doctor_id')->unsigned();
            $table->integer('appointment_id')->unsigned()->nullable();
            $table->json('vitals')->nullable(); // bp, weight, height, temp, spo2, pulse
            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('prescription')->nullable();
            $table->text('advice')->nullable();
            $table->enum('status', ['ongoing', 'completed'])->default('ongoing');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('hospital_appointments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_consultations');
    }
}
