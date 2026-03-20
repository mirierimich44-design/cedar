<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLostSalesTable extends Migration
{
    public function up()
    {
        Schema::create('lost_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('location_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('variation_id')->unsigned()->nullable();
            $table->string('product_name');
            $table->string('sku')->nullable();
            $table->decimal('selling_price', 22, 4)->default(0);
            $table->decimal('quantity', 22, 4)->default(1);
            $table->text('notes')->nullable();
            $table->integer('created_by')->unsigned();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lost_sales');
    }
}
