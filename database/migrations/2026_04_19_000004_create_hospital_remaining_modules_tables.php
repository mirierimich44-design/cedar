<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // --- 1. Physiotherapy ---
        if (!Schema::hasTable('hospital_physio_plans')) {
            Schema::create('hospital_physio_plans', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('business_id')->unsigned();
                $table->integer('patient_id')->unsigned();
                $table->integer('doctor_id')->unsigned();
                $table->string('diagnosis');
                $table->text('treatment_goals')->nullable();
                $table->integer('total_sessions_planned')->default(1);
                $table->enum('status', ['active', 'completed', 'discontinued'])->default('active');
                $table->timestamps();

                $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
                $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('cascade');
                $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('hospital_physio_sessions')) {
            Schema::create('hospital_physio_sessions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('plan_id')->unsigned();
                $table->dateTime('session_date');
                $table->text('exercises_performed')->nullable();
                $table->text('progress_notes')->nullable();
                $table->integer('therapist_id')->unsigned();
                $table->timestamps();

                $table->foreign('plan_id')->references('id')->on('hospital_physio_plans')->onDelete('cascade');
                $table->foreign('therapist_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // --- 2. Mortuary Management ---
        if (!Schema::hasTable('hospital_mortuary_records')) {
            Schema::create('hospital_mortuary_records', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('business_id')->unsigned();
                $table->string('body_name')->nullable();
                $table->integer('patient_id')->unsigned()->nullable();
                $table->dateTime('admitted_at');
                $table->dateTime('released_at')->nullable();
                $table->string('relative_name')->nullable();
                $table->string('relative_phone')->nullable();
                $table->string('storage_location')->nullable();
                $table->string('cause_of_death')->nullable();
                $table->enum('status', ['admitted', 'released'])->default('admitted');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
                $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('set null');
            });
        }

        // --- 3. Advanced Inpatient Monitoring (Nursing Notes & Fluid Balance) ---
        if (!Schema::hasTable('hospital_nursing_notes')) {
            Schema::create('hospital_nursing_notes', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('admission_id')->unsigned();
                $table->dateTime('noted_at');
                $table->text('observation');
                $table->text('action_taken')->nullable();
                $table->decimal('fluid_input_ml', 10, 2)->default(0);
                $table->decimal('fluid_output_ml', 10, 2)->default(0);
                $table->integer('nurse_id')->unsigned();
                $table->timestamps();

                $table->foreign('admission_id')->references('id')->on('hospital_admissions')->onDelete('cascade');
                $table->foreign('nurse_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('hospital_nursing_notes');
        Schema::dropIfExists('hospital_mortuary_records');
        Schema::dropIfExists('hospital_physio_sessions');
        Schema::dropIfExists('hospital_physio_plans');
    }
};
