<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessIdToIpAllowed extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ip_allowed') && !Schema::hasColumn('ip_allowed', 'business_id')) {
            Schema::table('ip_allowed', function (Blueprint $table) {
                $table->unsignedBigInteger('business_id')->nullable()->after('id');
                $table->index('business_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('ip_allowed')) {
            Schema::table('ip_allowed', function (Blueprint $table) {
                $table->dropIndex(['business_id']);
                $table->dropColumn('business_id');
            });
        }
    }
}
