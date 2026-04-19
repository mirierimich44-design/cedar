<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->integer('business_id')->unsigned();
            $table->string('waybill_number')->unique();
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->bigInteger('origin_station_id')->unsigned();
            $table->bigInteger('destination_station_id')->unsigned();
            $table->bigInteger('route_id')->unsigned()->nullable();
            $table->decimal('weight_kg', 22, 4)->default(0);
            $table->text('description')->nullable();
            $table->decimal('declared_value', 22, 4)->default(0);
            $table->decimal('charge_amount', 22, 4)->default(0);
            $table->enum('payment_method', ['mpesa', 'cash', 'cod'])->default('cash');
            $table->enum('payment_status', ['pending', 'paid', 'partially_paid'])->default('pending');
            $table->string('mpesa_reference')->nullable();
            $table->enum('collection_type', ['pickup', 'delivery'])->default('pickup');
            $table->enum('status', ['booked', 'in_transit', 'arrived', 'collected', 'failed'])->default('booked');
            $table->integer('booked_by_user_id')->unsigned();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('origin_station_id')->references('id')->on('parcel_stations')->onDelete('cascade');
            $table->foreign('destination_station_id')->references('id')->on('parcel_stations')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('parcel_routes')->onDelete('set null');
            $table->foreign('booked_by_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parcels');
    }
}
