<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hospital_bills')) return;
        Schema::create('hospital_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('location_id')->nullable();
            $table->string('bill_number')->unique();
            $table->unsignedInteger('patient_id')->nullable();   // contact_id reference
            $table->string('patient_name');
            $table->string('patient_phone')->nullable();
            $table->string('patient_dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nhif_number')->nullable();
            $table->string('doctor_name')->nullable();
            $table->date('visit_date');
            $table->enum('visit_type', ['outpatient', 'inpatient', 'emergency'])->default('outpatient');
            $table->text('diagnosis')->nullable();
            $table->json('bill_items')->nullable();               // [{service, qty, unit_price, total}]
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('nhif_amount', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'mpesa', 'nhif', 'insurance', 'card'])->default('cash');
            $table->string('mpesa_code')->nullable();
            $table->enum('status', ['draft', 'active', 'cancelled'])->default('active');
            $table->unsignedInteger('created_by');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_bills');
    }
};
