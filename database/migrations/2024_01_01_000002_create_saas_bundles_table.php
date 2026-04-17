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
            $table->string('color')->default('#3490dc');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table — named to match belongsToMany in SaasBundle::features()
        Schema::create('saas_bundle_features', function (Blueprint $table) {
            $table->unsignedBigInteger('bundle_id');
            $table->unsignedBigInteger('feature_id');
            $table->primary(['bundle_id', 'feature_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('saas_bundle_features');
        Schema::dropIfExists('saas_bundles');
    }
}
