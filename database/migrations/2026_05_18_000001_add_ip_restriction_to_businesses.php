<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIpRestrictionToBusinesses extends Migration
{
    public function up()
    {
        // Table is named 'business' (not 'businesses') in this app
        $tbl = Schema::hasTable('businesses') ? 'businesses' : 'business';
        if (!Schema::hasColumn($tbl, 'enable_ip_restriction')) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->boolean('enable_ip_restriction')->default(false)->after('id');
            });
        }
    }

    public function down()
    {
        $tbl = Schema::hasTable('businesses') ? 'businesses' : 'business';
        Schema::table($tbl, function (Blueprint $table) {
            $table->dropColumn('enable_ip_restriction');
        });
    }
}
