<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooler_retrievals', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');

            $table->integer('agreement_id')->unsigned()->nullable();
            $table->foreign('agreement_id')->references('id')->on('cooler_agreements')->onDelete('set null');

            $table->integer('cooler_id')->unsigned();
            $table->foreign('cooler_id')->references('id')->on('cooler_assets')->onDelete('cascade');

            $table->integer('dealer_id')->unsigned();
            $table->foreign('dealer_id')->references('id')->on('cooler_dealers')->onDelete('cascade');

            $table->date('retrieval_date');
            $table->enum('reason', [
                'not_purchasing_from_stockist',
                'not_stocking_to_capacity',
                'not_displaying_sbck_products_only',
                'unauthorized_rebrand_or_relocation',
                'business_ownership_change',
                'liquidation',
                'company_discretion',
                'other',
            ]);
            $table->text('reason_notes')->nullable();

            // Authorized staff (pulled from session at creation time)
            $table->string('authorized_staff_name');
            $table->string('authorized_staff_id_no')->nullable();
            $table->string('authorized_staff_tel')->nullable();

            // Field execution
            $table->string('customer_signature_path')->nullable();
            $table->timestamp('acknowledgement_date')->nullable();
            $table->string('gps_coordinates')->nullable();
            $table->string('retrieval_letter_path')->nullable();

            $table->enum('status', ['initiated', 'in_progress', 'completed'])->default('initiated');
            $table->text('notes')->nullable();

            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index(['cooler_id']);
            $table->index(['dealer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooler_retrievals');
    }
};
