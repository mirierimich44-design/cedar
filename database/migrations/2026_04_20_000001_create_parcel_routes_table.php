<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('parcel_routes')) return;
        Schema::create('parcel_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('business_id');
            $table->string('from_town');
            $table->string('to_town');
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->decimal('base_price', 10, 2)->default(0);      // flat base rate
            $table->decimal('price_per_kg', 10, 2)->default(0);    // per kg charge
            $table->decimal('min_price', 10, 2)->default(0);       // minimum charge
            $table->decimal('express_multiplier', 5, 2)->default(1.5); // express surcharge
            $table->boolean('is_active')->default(true);
            $table->string('transit_days')->nullable();             // e.g. "1-2 days"
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('parcel_routes');
    }
};
