<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmptyingInventoryProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emptying_inventory_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger("branch_id");
            $table->foreign('branch_id')->references('id')->on('business_locations');
            $table->unsignedInteger("variation_id");
            $table->foreign('variation_id')->references('id')->on('variations');

            $table->unsignedInteger("transaction_id");
            $table->foreign('transaction_id')->references('id')->on('transactions');

            $table->unsignedBigInteger("inventories_empty_id");
            $table->foreign('inventories_empty_id')->references('id')->on('inventories_empty');


            $table->string("qty_before");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emptying_inventory_products');
    }
}
