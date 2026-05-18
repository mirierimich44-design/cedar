<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpAccessLogsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ip_access_logs')) return;

        Schema::create('ip_access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('username_attempted')->nullable();
            $table->string('ip_address');
            $table->string('isp')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('device_type')->nullable();
            $table->enum('outcome', ['success', 'blocked_ip', 'blocked_schedule', 'wrong_password', 'account_disabled'])->default('success');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ip_access_logs');
    }
}
