<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDdaStockLogTable extends Migration
{
    public function up()
    {
        Schema::create('dda_stock_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedBigInteger('dda_drug_id')->nullable();
            $table->enum('type', ['in', 'out', 'adjustment', 'destruction']);
            $table->decimal('quantity', 22, 4);
            $table->string('reference')->nullable(); // invoice / transaction number
            $table->string('supplier_name')->nullable();
            $table->string('supplier_license')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('recorded_by')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('dda_drug_id')->references('id')->on('dda_drugs')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dda_stock_log');
    }
}
