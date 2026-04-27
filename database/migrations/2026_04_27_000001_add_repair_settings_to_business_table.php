<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            if (! Schema::hasColumn('business', 'repair_settings')) {
                $table->text('repair_settings')->nullable()->after('time_format');
            }
        });
    }

    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('repair_settings');
        });
    }
};
