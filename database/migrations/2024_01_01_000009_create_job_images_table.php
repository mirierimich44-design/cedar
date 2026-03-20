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
        Schema::dropIfExists('job_images');

        Schema::create('job_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->foreign('job_id')->references('id')->on('job_cards')->onDelete('cascade');
            
            $table->integer('job_log_id')->unsigned()->nullable();
            $table->foreign('job_log_id')->references('id')->on('job_logs')->onDelete('set null');
            
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->text('caption')->nullable();
            $table->text('location_coordinates')->nullable()->comment('GPS where photo was taken');
            $table->timestamp('taken_at')->nullable();
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
        Schema::dropIfExists('job_images');
    }
};
