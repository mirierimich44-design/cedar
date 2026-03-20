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
        Schema::dropIfExists('job_templates');

        Schema::create('job_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('job_categories')->onDelete('set null');
            
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_templates');
    }
};
