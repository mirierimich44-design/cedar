<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceIpAccessTables extends Migration
{
    public function up()
    {
        // Add is_banned flag to ip_allowed
        if (Schema::hasTable('ip_allowed') && !Schema::hasColumn('ip_allowed', 'is_banned')) {
            Schema::table('ip_allowed', function (Blueprint $table) {
                $table->boolean('is_banned')->default(false)->after('is_active');
                $table->string('ban_reason')->nullable()->after('is_banned');
            });
        }

        // Add location_id to ip_access_logs so we can see which business location a login came from
        if (Schema::hasTable('ip_access_logs') && !Schema::hasColumn('ip_access_logs', 'business_location_id')) {
            Schema::table('ip_access_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('business_location_id')->nullable()->after('user_id');
                $table->unsignedBigInteger('business_id')->nullable()->after('user_id');
                $table->index('ip_address');
                $table->index('business_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('ip_allowed')) {
            Schema::table('ip_allowed', function (Blueprint $table) {
                $table->dropColumn(['is_banned', 'ban_reason']);
            });
        }
        if (Schema::hasTable('ip_access_logs')) {
            Schema::table('ip_access_logs', function (Blueprint $table) {
                $table->dropIndex(['ip_address']);
                $table->dropIndex(['business_id']);
                $table->dropColumn(['business_location_id', 'business_id']);
            });
        }
    }
}
