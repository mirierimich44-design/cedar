<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Make roles.business_id nullable using raw SQL — no Doctrine DBAL needed.
 * This allows a platform-level Superadmin role (not tied to any business).
 */
class MakeRolesBusinessIdNullable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('roles')) return;

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop the FK if it exists (try common name patterns)
        foreach (['roles_business_id_foreign', 'roles_ibfk_1'] as $fk) {
            try {
                DB::statement("ALTER TABLE `roles` DROP FOREIGN KEY `{$fk}`");
            } catch (\Exception $e) {
                // FK not found by this name — try next
            }
        }

        // Change the column to nullable (raw SQL, no Doctrine DBAL required)
        DB::statement('ALTER TABLE `roles` MODIFY `business_id` INT UNSIGNED NULL DEFAULT NULL');

        // Re-add the FK (nullable column still participates in FK — NULL rows are skipped)
        try {
            DB::statement('ALTER TABLE `roles` ADD CONSTRAINT `roles_business_id_foreign`
                           FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // If FK already exists or business table has issues, ignore — column is nullable now
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        if (! Schema::hasTable('roles')) return;

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            DB::statement("ALTER TABLE `roles` DROP FOREIGN KEY `roles_business_id_foreign`");
        } catch (\Exception $e) {}
        DB::statement('ALTER TABLE `roles` MODIFY `business_id` INT UNSIGNED NOT NULL');
        try {
            DB::statement('ALTER TABLE `roles` ADD CONSTRAINT `roles_business_id_foreign`
                           FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE');
        } catch (\Exception $e) {}
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
