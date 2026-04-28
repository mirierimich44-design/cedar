<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesapal_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('transaction_id')->nullable();  // FK to sales transaction
            $table->string('order_tracking_id', 100)->nullable();   // Pesapal-generated UUID
            $table->string('merchant_reference', 100)->nullable();  // Our reference (invoice_no)
            $table->enum('status', ['pending', 'completed', 'failed', 'reversed', 'invalid'])->default('pending');
            $table->decimal('amount', 22, 4);
            $table->string('currency', 10)->default('KES');
            $table->string('description', 100)->nullable();
            $table->string('payment_method', 50)->nullable();       // CARD, MPESA, MTN, etc.
            $table->string('confirmation_code', 100)->nullable();   // Provider receipt number
            $table->string('payment_account', 100)->nullable();     // Masked card / phone
            $table->text('status_description')->nullable();
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
            $table->unique('order_tracking_id');
            $table->index('merchant_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesapal_transactions');
    }
};
