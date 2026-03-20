<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('job_requisitions');

        Schema::create('job_requisitions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->foreign('job_id')->references('id')->on('job_cards')->onDelete('cascade');
            
            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            
            $table->string('item_name');
            $table->string('sku')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('quantity_requested', 10, 3)->default(0);
            $table->decimal('quantity_approved', 10, 3)->nullable();
            $table->decimal('quantity_issued', 10, 3)->default(0);
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'partially_issued', 'fully_issued', 'rejected'])->default('pending');
            $table->text('reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('approved_by')->unsigned()->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamp('issued_at')->nullable();
            $table->integer('issued_by')->unsigned()->nullable();
            $table->foreign('issued_by')->references('id')->on('users')->onDelete('set null');
            
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['job_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_requisitions');
    }
};
