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
        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('transaction_id')->nullable();
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled', 'expired'])->default('pending');
            $table->string('phone', 20);
            $table->decimal('amount', 22, 4);
            $table->string('account_reference', 100)->nullable();
            $table->string('merchant_request_id', 100)->nullable();
            $table->string('checkout_request_id', 100)->nullable();
            $table->string('mpesa_receipt_number', 50)->nullable();
            $table->string('result_code', 10)->nullable();
            $table->text('result_description')->nullable();
            $table->string('transaction_type', 50)->default('stk_push');
            $table->unsignedInteger('initiated_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('business_id')
                ->references('id')
                ->on('business')
                ->onDelete('cascade');

            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->onDelete('set null');

            $table->foreign('initiated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->index(['business_id', 'status']);
            $table->index('checkout_request_id');
            $table->index('mpesa_receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};
