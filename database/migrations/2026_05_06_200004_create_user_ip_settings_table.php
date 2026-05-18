<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserIpSettingsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('user_ip_settings')) return;

        Schema::create('user_ip_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->boolean('bypass_ip_check')->default(false);
            $table->boolean('bypass_schedule')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_ip_settings');
    }
}
