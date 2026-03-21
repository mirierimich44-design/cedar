<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooler_dealers', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');

            $table->integer('contact_id')->unsigned()->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');

            // Personal details
            $table->string('name');
            $table->string('id_number');
            $table->string('kra_pin');
            $table->string('postal_address')->nullable();
            $table->string('phone');

            // Business details
            $table->string('outlet_name');
            $table->enum('channel', ['retail', 'wholesale', 'supermarket', 'kiosk'])->default('retail');
            $table->string('building')->nullable();
            $table->string('road')->nullable();
            $table->string('area')->nullable();
            $table->integer('years_in_business')->default(0);

            $table->json('brands_stocked')->nullable();
            $table->decimal('compliance_score', 5, 2)->default(100.00);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');

            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooler_dealers');
    }
};
