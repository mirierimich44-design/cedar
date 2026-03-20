<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSyncedToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'is_synced')) {
                $table->boolean('is_synced')->default(0)->after('status');
            }
        });

        Schema::table('transaction_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('transaction_payments', 'is_synced')) {
                $table->boolean('is_synced')->default(0)->after('amount');
            }
        });

        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'is_synced')) {
                $table->boolean('is_synced')->default(0)->after('contact_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('is_synced');
        });

        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->dropColumn('is_synced');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('is_synced');
        });
    }
}
