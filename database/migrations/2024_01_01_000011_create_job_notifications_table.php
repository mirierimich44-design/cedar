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
        Schema::dropIfExists('job_notifications');

        Schema::create('job_notifications', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('job_id')->unsigned()->nullable();
            $table->foreign('job_id')->references('id')->on('job_cards')->onDelete('cascade');
            
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            $table->integer('user_id')->unsigned()->nullable()->comment('User to notify');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->string('contact_email')->nullable()->comment('External email for customer notifications');
            $table->string('contact_phone')->nullable()->comment('External phone for SMS notifications');
            $table->enum('notification_type', ['job_assigned', 'job_started', 'job_completed', 'job_approved', 'job_cancelled', 'requisition_approved', 'requisition_rejected', 'reminder', 'custom']);
            $table->string('title');
            $table->text('message');
            $table->enum('channel', ['email', 'sms', 'in_app'])->default('in_app');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(['business_id', 'created_at']);
            $table->index(['contact_email', 'is_sent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_notifications');
    }
};
