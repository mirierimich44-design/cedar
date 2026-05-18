<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kcb_buni_transactions')) { return; } Schema::create('kcb_buni_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('transaction_id')->nullable();  // FK to sales transaction if applicable
            $table->string('conversation_id', 100)->nullable();
            $table->string('originator_conversation_id', 100)->nullable();
            $table->string('merchant_reference', 100)->nullable();
            $table->enum('type', ['B2B', 'B2C', 'AccountBalance', 'TransactionStatus'])->default('B2B');
            $table->enum('status', ['pending', 'completed', 'failed', 'reversed'])->default('pending');
            $table->decimal('amount', 22, 4)->default(0);
            $table->string('currency', 10)->default('KES');
            $table->string('recipient_name', 100)->nullable();
            $table->string('recipient_account', 100)->nullable();
            $table->string('confirmation_code', 100)->nullable();
            $table->text('status_description')->nullable();
            $table->unsignedInteger('initiated_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('business_id')
                ->references('id')
                ->on('business')
                ->onDelete('cascade');

            $table->foreign('initiated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->index(['business_id', 'status']);
            $table->unique('conversation_id');
            $table->index('merchant_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kcb_buni_transactions');
    }
};
