<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedBigInteger('patient_id');
            $table->string('visit_no')->unique();
            $table->timestamp('visited_at')->useCurrent();
            $table->enum('visit_type', ['outpatient', 'inpatient', 'emergency'])->default('outpatient');
            $table->text('chief_complaint')->nullable();
            $table->enum('triage_category', ['green', 'yellow', 'orange', 'red', 'black'])->nullable();
            $table->text('triage_notes')->nullable();
            $table->unsignedSmallInteger('bp_systolic')->nullable();
            $table->unsignedSmallInteger('bp_diastolic')->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->unsignedSmallInteger('pulse_rate')->nullable();
            $table->unsignedSmallInteger('respiratory_rate')->nullable();
            $table->unsignedSmallInteger('oxygen_saturation')->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->decimal('height_cm', 6, 2)->nullable();
            $table->string('assigned_doctor')->nullable();
            $table->enum('status', [
                'triage',
                'consultation',
                'lab',
                'pharmacy',
                'discharged',
                'admitted',
                'deceased',
            ])->default('triage');
            $table->timestamp('admission_date')->nullable();
            $table->timestamp('discharge_date')->nullable();
            $table->string('ward')->nullable();
            $table->string('bed_number')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('hospital_patients')->onDelete('cascade');
            $table->index('business_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_visits');
    }
};
