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
            $table->string('category')->default('core');
            $table->text('description')->nullable();
            $table->decimal('price_monthly',  10, 2)->default(0);
            $table->decimal('price_quarterly',10, 2)->default(0);
            $table->decimal('price_yearly',   10, 2)->default(0);
            $table->decimal('price_once',     10, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('saas_features');
    }
}
