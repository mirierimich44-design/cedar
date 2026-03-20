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
        Schema::dropIfExists('job_logs');

        Schema::create('job_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->foreign('job_id')->references('id')->on('job_cards')->onDelete('cascade');
            
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->enum('log_type', ['status_change', 'note', 'time_log', 'system', 'comment'])->default('note');
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->decimal('hours_logged', 8, 2)->nullable();
            $table->timestamp('logged_at')->nullable();
            $table->text('location_coordinates')->nullable();
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['job_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_logs');
    }
};
