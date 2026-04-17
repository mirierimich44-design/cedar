<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDdaPrescriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('dda_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_id_number')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('prescriber_name');
            $table->string('prescriber_license')->nullable();
            $table->string('prescriber_hospital')->nullable();
            $table->string('prescription_image'); // file path
            $table->unsignedInteger('dispensed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dda_prescriptions');
    }
}
