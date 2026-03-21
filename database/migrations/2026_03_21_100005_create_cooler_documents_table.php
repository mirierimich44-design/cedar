<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooler_documents', function (Blueprint $table) {
            $table->increments('id');

            // Polymorphic: belongs to CoolerDealer, CoolerAgreement, or CoolerRetrieval
            $table->morphs('documentable');

            $table->enum('document_type', [
                // Dealer personal docs
                'id_copy',
                'kra_pin_certificate',
                'passport_photo',
                // Dealer legal docs
                'county_business_permit',
                'certificate_of_registration',
                'certificate_of_incorporation',
                'tax_certificate',
                // Agreement docs
                'signed_agreement',
                // Retrieval photos
                'retrieval_photo_before',
                'retrieval_photo_during',
                'retrieval_photo_after',
                // Retrieval acknowledgement
                'acknowledgement_signature',
                'signed_retrieval_letter',
                // Other
                'other',
            ]);

            $table->string('file_path');
            $table->string('file_name');
            $table->bigInteger('file_size')->unsigned()->nullable();
            $table->string('mime_type')->nullable();
            $table->string('thumbnail_path')->nullable();

            $table->integer('uploaded_by')->unsigned()->nullable();
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamp('verified_at')->nullable();
            $table->integer('verified_by')->unsigned()->nullable();
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->text('verification_notes')->nullable();

            $table->date('expires_at')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');

            $table->json('metadata')->nullable()->comment('EXIF data, GPS, photo phase, etc.');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['documentable_type', 'documentable_id']);
            $table->index(['document_type', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooler_documents');
    }
};
