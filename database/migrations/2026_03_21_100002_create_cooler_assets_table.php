<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooler_assets', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');

            $table->string('asset_type');
            $table->string('asset_number');
            $table->string('serial_number')->nullable();
            $table->string('cooler_tag')->nullable();
            $table->enum('status', ['available', 'deployed', 'under_maintenance', 'retrieved'])->default('available');

            $table->integer('current_dealer_id')->unsigned()->nullable();
            $table->foreign('current_dealer_id')->references('id')->on('cooler_dealers')->onDelete('set null');

            $table->date('deployment_date')->nullable();
            $table->decimal('replacement_value', 12, 2)->nullable();
            $table->text('notes')->nullable();

            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'asset_number']);
            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooler_assets');
    }
};
