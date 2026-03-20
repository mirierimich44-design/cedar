<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variation_id');
            $table->string('customer_phone', 50);
            $table->string('customer_name', 191)->nullable();
            $table->text('comment')->nullable();
            $table->string('status')->default('pending'); // pending, contacted, resolved
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index('business_id');
            $table->index('location_id');
            $table->index('status');
            $table->index('customer_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('followups');
    }
}
