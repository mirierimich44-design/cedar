<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDdaDrugsTable extends Migration
{
    public function up()
    {
        Schema::create('dda_drugs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('class'); // Opioid, Benzodiazepine, Barbiturate, etc.
            $table->string('schedule')->default('II'); // I, II, III, IV
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dda_drugs');
    }
}
