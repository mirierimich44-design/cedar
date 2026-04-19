<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelStatusLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcel_status_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parcel_id')->unsigned();
            $table->string('status');
            $table->bigInteger('station_id')->unsigned()->nullable();
            $table->text('notes')->nullable();
            $table->integer('updated_by_user_id')->unsigned();
            $table->timestamps();

            $table->foreign('parcel_id')->references('id')->on('parcels')->onDelete('cascade');
            $table->foreign('station_id')->references('id')->on('parcel_stations')->onDelete('set null');
            $table->foreign('updated_by_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parcel_status_logs');
    }
}
