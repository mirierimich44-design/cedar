<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessTypeToBusinessTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('business', 'business_type')) return;
        Schema::table('business', function (Blueprint $table) {
            $table->string('business_type')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('business_type');
        });
    }
}
