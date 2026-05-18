<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital_lab_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visit_id');
            $table->unsignedInteger('business_id');
            $table->string('test_name');
            $table->string('test_code')->nullable();
            $table->string('ordered_by');
            $table->timestamp('ordered_at')->useCurrent();
            $table->enum('status', [
                'pending',
                'sample_collected',
                'processing',
                'resulted',
                'cancelled',
            ])->default('pending');
            $table->text('result_value')->nullable();
            $table->string('result_unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->text('result_notes')->nullable();
            $table->timestamp('resulted_at')->nullable();
            $table->timestamps();

            $table->foreign('visit_id')->references('id')->on('hospital_visits')->onDelete('cascade');
            $table->index('business_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital_lab_orders');
    }
};
