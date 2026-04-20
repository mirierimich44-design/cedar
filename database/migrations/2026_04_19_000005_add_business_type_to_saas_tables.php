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
        Schema::table('saas_bundles', function (Blueprint $table) {
            $table->string('business_type')->nullable()->after('name')->index();
        });

        Schema::table('saas_features', function (Blueprint $table) {
            $table->json('applicable_to')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('saas_bundles', function (Blueprint $table) {
            $table->dropColumn('business_type');
        });

        Schema::table('saas_features', function (Blueprint $table) {
            $table->dropColumn('applicable_to');
        });
    }
};
