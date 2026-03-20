<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('ref_no', 191)->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('business_id');
            $table->index('location_id');
            $table->index('status');
        });

        Schema::create('pos_order_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variation_id');
            $table->decimal('quantity', 22, 4)->default(1);
            $table->decimal('unit_price', 22, 4)->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('pos_orders')->onDelete('cascade');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_order_lines');
        Schema::dropIfExists('pos_orders');
    }
}
