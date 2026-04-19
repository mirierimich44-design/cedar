<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalQueueTable extends Migration
{
    public function up()
    {
        Schema::create('hospital_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('patient_id')->unsigned();
            $table->integer('appointment_id')->unsigned()->nullable();
            $table->string('token_number'); // e.g., A-001
            $table->enum('current_location', ['triage', 'consultation', 'laboratory', 'pharmacy', 'billing', 'completed'])->default('triage');
            $table->enum('status', ['waiting', 'serving', 'paused', 'completed'])->default('waiting');
            $table->integer('assigned_to')->unsigned()->nullable(); // Doctor or Tech ID
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_queue');
    }
}
