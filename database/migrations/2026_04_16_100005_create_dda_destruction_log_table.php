<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDdaDestructionLogTable extends Migration
{
    public function up()
    {
        Schema::create('dda_destruction_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedBigInteger('dda_drug_id')->nullable();
            $table->decimal('quantity', 22, 4);
            $table->string('unit')->nullable();
            $table->enum('reason', ['expired', 'damaged', 'contaminated', 'recalled', 'other']);
            $table->date('destruction_date');
            $table->string('destruction_method')->nullable(); // incineration, chemical, etc.
            $table->unsignedInteger('witness_1')->nullable();
            $table->unsignedInteger('witness_2')->nullable();
            $table->string('ppb_officer')->nullable();
            $table->string('certificate_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('dda_drug_id')->references('id')->on('dda_drugs')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dda_destruction_log');
    }
}
