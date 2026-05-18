<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kcb_buni_settings')) { return; } Schema::create('kcb_buni_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->text('app_key')->nullable();
            $table->text('app_secret')->nullable();
            $table->string('b2b_shortcode', 50)->nullable();
            $table->string('b2c_shortcode', 50)->nullable();
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->string('currency', 10)->default('KES');
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
        Schema::dropIfExists('kcb_buni_settings');
    }
};
