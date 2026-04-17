<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaasInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('saas_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->unsignedInteger('business_id');
            $table->foreignId('subscription_id')->nullable()->constrained('saas_subscriptions')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('KES');
            $table->string('payment_method')->nullable(); // mpesa, stripe, paypal, bank, manual
            $table->string('payment_reference')->nullable();
            $table->string('status')->default('unpaid'); // unpaid, paid, failed, refunded
            $table->string('type')->default('subscription'); // subscription, hosting_renewal
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('saas_invoices');
    }
}
