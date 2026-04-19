<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalMaternityTables extends Migration
{
    public function up()
    {
        // 1. Pregnancy Profile (One per pregnancy)
        Schema::create('hospital_pregnancy_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('patient_id')->unsigned();
            $table->date('lmp_date')->nullable(); // Last Menstrual Period
            $table->date('edd_date')->nullable(); // Expected Delivery Date
            $table->integer('gravida')->nullable(); // Number of pregnancies
            $table->integer('parity')->nullable();  // Number of births
            $table->text('medical_history')->nullable();
            $table->enum('status', ['ongoing', 'delivered', 'terminated'])->default('ongoing');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('cascade');
        });

        // 2. ANC Visits (Individual check-ups)
        Schema::create('hospital_anc_visits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pregnancy_profile_id')->unsigned();
            $table->integer('doctor_id')->unsigned();
            $table->date('visit_date');
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('bp')->nullable();
            $table->decimal('fundal_height', 8, 2)->nullable(); // Fetal growth
            $table->string('fetal_presentation')->nullable();
            $table->string('fetal_heart_rate')->nullable();
            $table->string('tt_dose')->nullable(); // Tetanus Toxoid
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('pregnancy_profile_id')->references('id')->on('hospital_pregnancy_profiles')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_anc_visits');
        Schema::dropIfExists('hospital_pregnancy_profiles');
    }
}
