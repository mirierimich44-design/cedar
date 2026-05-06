<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('business_feature_settings')) { return; } Schema::create('business_feature_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('business_id')->index();
            $table->string('feature_key', 100);
            $table->tinyInteger('is_enabled')->default(1);
            $table->timestamps();

            $table->unique(['business_id', 'feature_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_feature_settings');
    }
};
