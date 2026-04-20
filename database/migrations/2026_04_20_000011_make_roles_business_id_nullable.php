<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The custom `roles` table has business_id NOT NULL, which prevents creating
 * a platform-level Superadmin role (not tied to any business).
 * Make it nullable so saas:create-admin can insert cleanly.
 */
class MakeRolesBusinessIdNullable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('roles')) return;

        Schema::table('roles', function (Blueprint $table) {
            // Drop the FK first, change the column, then re-add FK
            try {
                $table->dropForeign(['business_id']);
            } catch (\Exception $e) {
                // FK may already be gone or named differently — ignore
            }

            $table->integer('business_id')->unsigned()->nullable()->change();

            try {
                $table->foreign('business_id')
                      ->references('id')->on('business')
                      ->onDelete('cascade');
            } catch (\Exception $e) {
                // If FK re-add fails (e.g. old MySQL), fine — column is nullable now
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('roles')) return;

        Schema::table('roles', function (Blueprint $table) {
            try { $table->dropForeign(['business_id']); } catch (\Exception $e) {}
            $table->integer('business_id')->unsigned()->nullable(false)->change();
            try {
                $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            } catch (\Exception $e) {}
        });
    }
}
