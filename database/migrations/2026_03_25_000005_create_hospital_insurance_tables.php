<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalInsuranceTables extends Migration
{
    public function up()
    {
        // 1. Insurers Master (Companies)
        Schema::create('hospital_insurers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        // 2. Insurance Schemes (Specific plans under a company)
        Schema::create('hospital_insurance_schemes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('insurer_id')->unsigned();
            $table->string('name');
            $table->decimal('co_payment_percentage', 5, 2)->default(0); // e.g. Patient pays 20%
            $table->decimal('co_payment_fixed', 22, 4)->default(0);      // e.g. Patient pays fixed 500 KES
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('insurer_id')->references('id')->on('hospital_insurers')->onDelete('cascade');
        });

        // 3. Link Patients to Insurance
        Schema::table('patient_details', function (Blueprint $table) {
            $table->integer('insurer_id')->unsigned()->nullable()->after('contact_id');
            $table->integer('insurance_scheme_id')->unsigned()->nullable()->after('insurer_id');
            $table->string('insurance_card_number')->nullable()->after('insurance_scheme_id');

            $table->foreign('insurer_id')->references('id')->on('hospital_insurers')->onDelete('set null');
            $table->foreign('insurance_scheme_id')->references('id')->on('hospital_insurance_schemes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('patient_details', function (Blueprint $table) {
            $table->dropForeign(['insurer_id']);
            $table->dropForeign(['insurance_scheme_id']);
            $table->dropColumn(['insurer_id', 'insurance_scheme_id', 'insurance_card_number']);
        });
        Schema::dropIfExists('hospital_insurance_schemes');
        Schema::dropIfExists('hospital_insurers');
    }
}
