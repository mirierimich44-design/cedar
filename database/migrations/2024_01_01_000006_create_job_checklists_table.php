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
        Schema::dropIfExists('job_checklists');

        Schema::create('job_checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->foreign('job_id')->references('id')->on('job_cards')->onDelete('cascade');
            
            $table->integer('template_item_id')->unsigned()->nullable();
            $table->foreign('template_item_id')->references('id')->on('job_template_items')->onDelete('set null');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('completed_by')->unsigned()->nullable();
            $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps();

            $table->index(['job_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_checklists');
    }
};
