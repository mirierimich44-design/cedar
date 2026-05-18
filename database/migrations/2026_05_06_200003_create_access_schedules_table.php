<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessSchedulesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('access_schedules')) return;

        Schema::create('access_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->tinyInteger('day_of_week'); // 0=Sunday … 6=Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['role_id', 'day_of_week']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('access_schedules');
    }
}
