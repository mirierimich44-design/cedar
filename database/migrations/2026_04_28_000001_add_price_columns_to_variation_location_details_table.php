<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('variation_location_details', function (Blueprint $table) {
            $table->decimal('default_sell_price', 22, 4)->nullable()->after('qty_available');
            $table->decimal('sell_price_inc_tax', 22, 4)->nullable()->after('default_sell_price');
        });
    }

    public function down()
    {
        Schema::table('variation_location_details', function (Blueprint $table) {
            $table->dropColumn(['default_sell_price', 'sell_price_inc_tax']);
        });
    }
};
