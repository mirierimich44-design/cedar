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
        Schema::dropIfExists('job_template_items');

        Schema::create('job_template_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_template_id')->unsigned();
            $table->foreign('job_template_id')->references('id')->on('job_templates')->onDelete('cascade');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->index(['job_template_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_template_items');
    }
};
