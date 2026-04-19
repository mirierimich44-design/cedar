<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelPricingRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('route_id')->unsigned();
            $table->decimal('weight_min_kg', 22, 4);
            $table->decimal('weight_max_kg', 22, 4);
            $table->decimal('price_per_kg', 22, 4)->default(0);
            $table->decimal('flat_fee', 22, 4)->default(0);
            $table->timestamps();

            $table->foreign('route_id')->references('id')->on('parcel_routes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parcel_pricing_rules');
    }
}
