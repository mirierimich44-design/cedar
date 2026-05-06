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
        if (Schema::hasTable('hospital_mortuary_records')) {
            return; // table already exists (created outside migrations)
        }
        Schema::create('hospital_mortuary_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('body_reference')->unique();
            $table->string('deceased_name');
            $table->date('deceased_dob')->nullable();
            $table->enum('gender', ['M', 'F', 'Other']);
            $table->text('cause_of_death')->nullable();
            $table->date('date_of_death');
            $table->time('time_of_death')->nullable();
            $table->string('brought_by');
            $table->string('brought_by_phone');
            $table->string('relationship');
            $table->string('storage_location')->nullable();
            $table->date('storage_date');
            $table->date('release_date')->nullable();
            $table->string('released_to')->nullable();
            $table->string('released_to_phone')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['stored', 'released', 'transferred'])->default('stored');
            $table->timestamps();

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
        Schema::dropIfExists('hospital_mortuary_records');
    }
};
