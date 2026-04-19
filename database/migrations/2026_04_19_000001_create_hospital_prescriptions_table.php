<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalPrescriptionsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hospital_prescriptions')) {
            return;
        }

        Schema::create('hospital_prescriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('business_id');
            $table->unsignedBigInteger('patient_id');           // contacts.id
            $table->unsignedBigInteger('doctor_id')->nullable(); // users.id
            $table->unsignedBigInteger('consultation_id')->nullable();
            $table->unsignedInteger('variation_id')->nullable(); // variations.id
            $table->unsignedInteger('product_id')->nullable();   // products.id
            $table->string('drug_name')->nullable();
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration')->nullable();
            $table->decimal('quantity', 22, 4)->default(0);
            $table->decimal('dispensed_quantity', 22, 4)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'dispensed', 'partially_dispensed', 'cancelled'])
                  ->default('pending');
            $table->timestamp('dispensed_at')->nullable();
            $table->unsignedBigInteger('dispensed_by')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->index(['business_id', 'status']);
            $table->index('patient_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_prescriptions');
    }
}
