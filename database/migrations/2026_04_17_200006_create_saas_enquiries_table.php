<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaasEnquiriesTable extends Migration
{
    public function up()
    {
        Schema::create('saas_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('email');
            $table->string('phone');
            $table->string('cycle');
            $table->string('hosting');
            $table->json('feature_ids');
            $table->decimal('total', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, contacted, converted, cancelled
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('saas_enquiries');
    }
}
