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
        Schema::create('mpesa_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->text('consumer_key')->nullable();
            $table->text('consumer_secret')->nullable();
            $table->text('passkey')->nullable();
            $table->string('shortcode', 20)->nullable();
            $table->enum('shortcode_type', ['paybill', 'till'])->default('paybill');
            $table->string('till_number', 20)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->string('callback_url')->nullable();
            $table->string('validation_url')->nullable();
            $table->string('confirmation_url')->nullable();
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_settings');
    }
};
