<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelCheckpointsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('parcel_checkpoints')) return;
        Schema::create('parcel_checkpoints', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parcel_id');
            $table->string('location');                            // town/depot name
            $table->enum('checkpoint_type', [
                'booked',
                'collected_from_sender',
                'dispatched',
                'arrived_at_depot',
                'out_for_delivery',
                'delivered',
                'delivery_attempted',
                'returned_to_sender',
                'exception'
            ]);
            $table->string('status_note')->nullable();
            $table->unsignedInteger('scanned_by')->nullable();
            $table->string('vehicle_reg')->nullable();
            $table->timestamps();
            $table->foreign('parcel_id')->references('id')->on('parcels')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('parcel_checkpoints');
    }
}
