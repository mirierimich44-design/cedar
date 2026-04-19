<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesapal_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->text('consumer_key')->nullable();
            $table->text('consumer_secret')->nullable();
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->string('currency', 10)->default('KES');
            $table->string('ipn_id', 100)->nullable();     // Stored after IPN registration
            $table->string('ipn_url')->nullable();          // The registered IPN URL
            $table->string('callback_url')->nullable();     // Where customer is redirected after payment
            $table->boolean('is_active')->default(false);
            $table->timestamp('last_tested_at')->nullable();
            $table->timestamps();

            $table->foreign('business_id')
                ->references('id')
                ->on('business')
                ->onDelete('cascade');

            $table->unique('business_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesapal_settings');
    }
};
