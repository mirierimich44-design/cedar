<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCustomProductNameToPosOrderLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add custom product name field using raw SQL to avoid Doctrine DBAL requirement
        if (!Schema::hasColumn('pos_order_lines', 'custom_product_name')) {
            Schema::table('pos_order_lines', function (Blueprint $table) {
                $table->string('custom_product_name', 255)->nullable()->after('variation_id');
            });
        }

        // Make product_id and variation_id nullable using raw SQL
        DB::statement('ALTER TABLE `pos_order_lines` MODIFY `product_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `pos_order_lines` MODIFY `variation_id` BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_order_lines', function (Blueprint $table) {
            if (Schema::hasColumn('pos_order_lines', 'custom_product_name')) {
                $table->dropColumn('custom_product_name');
            }
        });
    }
}
