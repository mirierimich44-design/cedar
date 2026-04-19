<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_routes', function (Blueprint $table) {
            $table->id();
            $table->integer('business_id')->unsigned();
            $table->bigInteger('origin_station_id')->unsigned();
            $table->bigInteger('destination_station_id')->unsigned();
            $table->decimal('base_price_per_kg', 22, 4)->default(0);
            $table->decimal('min_price', 22, 4)->default(0);
            $table->integer('estimated_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('origin_station_id')->references('id')->on('parcel_stations')->onDelete('cascade');
            $table->foreign('destination_station_id')->references('id')->on('parcel_stations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parcel_routes');
    }
}
