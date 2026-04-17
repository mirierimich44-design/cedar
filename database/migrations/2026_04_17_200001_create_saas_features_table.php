<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaasFeaturesTable extends Migration
{
    public function up()
    {
        Schema::create('saas_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->default('fa-check');
            $table->string('category')->default('core'); // core, pharmacy, inventory, communication, reporting, hosting
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_quarterly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->decimal('price_once', 10, 2)->default(0); // one-off/self-hosted
            $table->boolean('is_required')->default(false); // always included
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('saas_features');
    }
}
