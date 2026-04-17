<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDdaDispenseLogTable extends Migration
{
    public function up()
    {
        Schema::create('dda_dispense_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedBigInteger('dda_drug_id')->nullable();
            $table->unsignedBigInteger('prescription_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_id_number')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('quantity', 22, 4);
            $table->string('unit')->nullable();
            $table->unsignedInteger('dispensed_by')->nullable();
            $table->unsignedInteger('verified_by')->nullable();
            $table->string('prescriber_name')->nullable();
            $table->string('prescriber_license')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('dda_drug_id')->references('id')->on('dda_drugs')->onDelete('set null');
            $table->foreign('prescription_id')->references('id')->on('dda_prescriptions')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dda_dispense_log');
    }
}
