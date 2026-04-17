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
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedInteger('business_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency')->default('KES');
            $table->string('status')->default('unpaid');  // unpaid|paid|failed
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('saas_invoices');
    }
}
