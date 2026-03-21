<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooler_agreements', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');

            $table->integer('cooler_id')->unsigned();
            $table->foreign('cooler_id')->references('id')->on('cooler_assets')->onDelete('cascade');

            $table->integer('dealer_id')->unsigned();
            $table->foreign('dealer_id')->references('id')->on('cooler_dealers')->onDelete('cascade');

            $table->date('agreement_date');
            $table->decimal('sales_volume_target', 12, 2)->nullable()->comment('Monthly sales volume target in KES');
            $table->enum('status', ['draft', 'active', 'terminated'])->default('draft');
            $table->text('termination_reason')->nullable();
            $table->date('termination_date')->nullable();

            // Signatories
            $table->string('company_signatory_name')->nullable();
            $table->string('company_signature_path')->nullable();
            $table->timestamp('company_signed_at')->nullable();

            $table->string('rsm_tsm_signatory_name')->nullable();
            $table->string('rsm_tsm_signature_path')->nullable();
            $table->timestamp('rsm_tsm_signed_at')->nullable();

            $table->string('dealer_signatory_name')->nullable();
            $table->string('dealer_signature_path')->nullable();
            $table->timestamp('dealer_signed_at')->nullable();

            $table->string('distributor_signatory_name')->nullable();
            $table->string('distributor_signature_path')->nullable();
            $table->timestamp('distributor_signed_at')->nullable();

            $table->string('generated_pdf_path')->nullable();

            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index(['dealer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooler_agreements');
    }
};
