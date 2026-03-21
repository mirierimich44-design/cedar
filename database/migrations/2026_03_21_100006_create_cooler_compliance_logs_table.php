<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooler_compliance_logs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('dealer_id')->unsigned();
            $table->foreign('dealer_id')->references('id')->on('cooler_dealers')->onDelete('cascade');

            $table->integer('cooler_id')->unsigned()->nullable();
            $table->foreign('cooler_id')->references('id')->on('cooler_assets')->onDelete('set null');

            $table->enum('check_type', [
                'sales_volume',
                'stock_level',
                'exclusivity',
                'placement',
                'document_expiry',
            ]);

            $table->enum('status', ['compliant', 'non_compliant'])->default('compliant');
            $table->json('details')->nullable();

            $table->timestamp('checked_at');
            $table->integer('checked_by')->unsigned()->nullable();
            $table->foreign('checked_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();

            $table->index(['dealer_id', 'check_type']);
            $table->index(['dealer_id', 'status']);
            $table->index('checked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooler_compliance_logs');
    }
};
