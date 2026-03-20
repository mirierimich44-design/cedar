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
        Schema::dropIfExists('job_signatures');

        Schema::create('job_signatures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id')->unsigned();
            $table->foreign('job_id')->references('id')->on('job_cards')->onDelete('cascade');
            
            $table->enum('signature_type', ['worker', 'manager', 'customer'])->default('worker');
            $table->string('signature_data')->comment('Base64 encoded signature');
            $table->string('signer_name');
            $table->string('signer_email')->nullable();
            $table->string('signer_phone')->nullable();
            $table->text('location_coordinates')->nullable();
            $table->timestamp('signed_at');
            $table->integer('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['job_id', 'signature_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_signatures');
    }
};
