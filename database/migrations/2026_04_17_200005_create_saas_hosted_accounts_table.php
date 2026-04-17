<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaasHostedAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('saas_hosted_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('domain')->nullable();
            $table->string('server_ip')->nullable();
            $table->string('server_notes')->nullable();
            $table->decimal('hosting_fee_yearly', 10, 2)->default(0);
            $table->timestamp('next_renewal_date')->nullable();
            $table->string('status')->default('active'); // active, suspended, cancelled
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('saas_hosted_accounts');
    }
}
