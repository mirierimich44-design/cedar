<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDdaColumnsToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_dda')->default(false)->after('name');
            $table->unsignedBigInteger('dda_drug_id')->nullable()->after('is_dda');
            $table->foreign('dda_drug_id')->references('id')->on('dda_drugs')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['dda_drug_id']);
            $table->dropColumn(['is_dda', 'dda_drug_id']);
        });
    }
}
