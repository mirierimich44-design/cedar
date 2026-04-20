<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelsTable2026 extends Migration
{
    public function up()
    {
        if (Schema::hasTable('parcels')) return;
        Schema::create('parcels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('location_id')->nullable();
            $table->string('waybill_number')->unique();             // e.g. RNS-20260420-0001

            // Route
            $table->unsignedInteger('route_id')->nullable();
            $table->string('from_town');
            $table->string('to_town');

            // Sender
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->string('sender_id_number')->nullable();
            $table->string('sender_town')->nullable();

            // Receiver
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('receiver_id_number')->nullable();
            $table->string('receiver_town')->nullable();
            $table->text('receiver_address')->nullable();

            // Parcel details
            $table->enum('parcel_type', ['document', 'package', 'fragile', 'perishable', 'electronics', 'clothing', 'other'])->default('package');
            $table->string('parcel_description')->nullable();
            $table->decimal('weight_kg', 8, 3)->default(0);
            $table->decimal('declared_value', 10, 2)->default(0);
            $table->integer('pieces')->default(1);
            $table->string('dimensions')->nullable();               // e.g. "30x20x15 cm"

            // Service type
            $table->enum('service_type', ['standard', 'express', 'overnight'])->default('standard');
            $table->enum('pickup_type', ['drop_off', 'home_pickup'])->default('drop_off');
            $table->enum('delivery_type', ['depot_pickup', 'home_delivery'])->default('depot_pickup');

            // Pricing
            $table->decimal('freight_charge', 10, 2)->default(0);
            $table->decimal('insurance_charge', 10, 2)->default(0);
            $table->decimal('pickup_charge', 10, 2)->default(0);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'mpesa', 'credit', 'card'])->default('cash');
            $table->enum('payment_by', ['sender', 'receiver', 'third_party'])->default('sender');
            $table->string('mpesa_code')->nullable();
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');

            // Status & tracking
            $table->enum('status', [
                'booked',
                'collected',
                'in_transit',
                'at_depot',
                'out_for_delivery',
                'delivered',
                'returned',
                'cancelled'
            ])->default('booked');

            // Assignment
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('driver_id')->nullable();
            $table->string('vehicle_reg')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->datetime('actual_delivery_date')->nullable();
            $table->string('delivered_to')->nullable();            // name of person who received
            $table->string('delivery_proof')->nullable();          // photo/signature path

            $table->text('notes')->nullable();
            $table->text('special_instructions')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('parcel_routes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('parcels');
    }
}
