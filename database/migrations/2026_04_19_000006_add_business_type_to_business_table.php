<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBusinessTypeToBusinessTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('business', 'business_type')) {
            Schema::table('business', function (Blueprint $table) {
                $table->string('business_type')->nullable()->after('name')->index();
            });
        }
    }

    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('business_type');
        });
    }
}
