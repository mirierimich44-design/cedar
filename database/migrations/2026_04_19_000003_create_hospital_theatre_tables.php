<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // 1. Operating Theatres / Rooms
        Schema::create('hospital_theatres', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('name');
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        // 2. Surgery Types Catalog
        Schema::create('hospital_surgeries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 22, 4)->default(0);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });

        // 3. Theatre Bookings & Records
        Schema::create('hospital_theatre_bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('patient_id')->unsigned();
            $table->integer('surgery_id')->unsigned();
            $table->integer('theatre_id')->unsigned();
            $table->integer('surgeon_id')->unsigned();
            $table->integer('anaesthetist_id')->unsigned()->nullable();
            
            $table->dateTime('scheduled_at');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            
            $table->text('pre_op_diagnosis')->nullable();
            $table->text('post_op_diagnosis')->nullable();
            $table->text('procedure_notes')->nullable();
            $table->text('complications')->nullable();
            
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('surgery_id')->references('id')->on('hospital_surgeries')->onDelete('cascade');
            $table->foreign('theatre_id')->references('id')->on('hospital_theatres')->onDelete('cascade');
            $table->foreign('surgeon_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('anaesthetist_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_theatre_bookings');
        Schema::dropIfExists('hospital_surgeries');
        Schema::dropIfExists('hospital_theatres');
    }
};
