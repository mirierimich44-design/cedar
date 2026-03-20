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
        Schema::dropIfExists('job_cards');
        Schema::dropIfExists('jobs');

        Schema::create('job_cards', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            $table->integer('location_id')->unsigned()->nullable();
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('set null');
            
            $table->integer('contact_id')->unsigned()->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            
            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('job_categories')->onDelete('set null');
            
            $table->integer('template_id')->unsigned()->nullable();
            $table->foreign('template_id')->references('id')->on('job_templates')->onDelete('set null');
            
            $table->string('ref_no')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'on_hold', 'completed', 'approved', 'cancelled'])->default('pending');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('assigned_to')->unsigned()->nullable()->comment('User ID of assigned worker');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->default(0);
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->default(0);
            $table->text('worker_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('location_coordinates')->nullable()->comment('GPS coordinates');
            $table->text('location_address')->nullable();
            
            $table->integer('completed_by')->unsigned()->nullable()->comment('User ID who marked complete');
            $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');
            
            $table->integer('approved_by')->unsigned()->nullable()->comment('User ID who approved');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_paid')->default(false);
            $table->integer('rating')->nullable()->comment('1-5 rating');
            $table->text('rating_feedback')->nullable();
            
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'contact_id']);
            $table->index(['business_id', 'assigned_to']);
            $table->index(['business_id', 'due_date']);
            $table->index('ref_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_cards');
    }
};
