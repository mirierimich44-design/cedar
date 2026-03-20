<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToPosOrders extends Migration
{
    public function up()
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->decimal('total_amount', 22, 4)->default(0)->after('notes');
            $table->string('payment_method')->nullable()->after('total_amount');
            $table->string('payment_status')->default('pending')->after('payment_method');
            $table->string('mpesa_phone')->nullable()->after('payment_status');
            $table->string('mpesa_receipt')->nullable()->after('mpesa_phone');
            $table->unsignedBigInteger('mpesa_transaction_id')->nullable()->after('mpesa_receipt');
        });
    }

    public function down()
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropColumn([
                'total_amount', 'payment_method', 'payment_status',
                'mpesa_phone', 'mpesa_receipt', 'mpesa_transaction_id',
            ]);
        });
    }
}
