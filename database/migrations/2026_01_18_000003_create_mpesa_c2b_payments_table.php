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
        Schema::create('mpesa_c2b_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('transaction_type', 50)->nullable();
            $table->string('trans_id', 50)->unique();
            $table->timestamp('trans_time')->nullable();
            $table->decimal('amount', 22, 4);
            $table->string('business_shortcode', 20)->nullable();
            $table->string('bill_ref_number', 100)->nullable();
            $table->decimal('org_account_balance', 22, 4)->nullable();
            $table->string('msisdn', 20);
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->json('raw_response')->nullable();
            $table->enum('status', ['received', 'matched', 'used', 'unmatched'])->default('received');
            $table->string('matched_to_type', 50)->nullable();
            $table->unsignedInteger('matched_to_id')->nullable();
            $table->timestamps();

            $table->foreign('business_id')
                ->references('id')
                ->on('business')
                ->onDelete('cascade');

            $table->index(['business_id', 'status']);
            $table->index('msisdn');
            $table->index('bill_ref_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_c2b_payments');
    }
};
