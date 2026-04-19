<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_stations', function (Blueprint $table) {
            $table->id();
            $table->integer('business_id')->unsigned();
            $table->string('name');
            $table->string('town');
            $table->string('county')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->integer('agent_user_id')->unsigned()->nullable();
            $table->boolean('is_origin_capable')->default(true);
            $table->boolean('is_destination_capable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parcel_stations');
    }
}
