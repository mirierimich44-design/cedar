<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaasBundlesTable extends Migration
{
    public function up()
    {
        Schema::create('saas_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#0f766e');
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('saas_bundle_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained('saas_bundles')->onDelete('cascade');
            $table->foreignId('feature_id')->constrained('saas_features')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('saas_bundle_features');
        Schema::dropIfExists('saas_bundles');
    }
}
