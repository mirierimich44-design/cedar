<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('etims_item_id')->nullable()->after('image');
            $table->string('etims_tax_category')->default('A')->after('etims_item_id'); // A=16%, B=8%, C=0%, etc.
            $table->string('etims_uom')->default('U')->after('etims_tax_category'); // U=Units, etc.
            $table->boolean('etims_synced')->default(0)->after('etims_uom');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['etims_item_id', 'etims_tax_category', 'etims_uom', 'etims_synced']);
        });
    }
};
