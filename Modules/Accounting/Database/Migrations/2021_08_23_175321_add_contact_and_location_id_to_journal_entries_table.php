<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddContactAndLocationIdToJournalEntriesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('journal_entries', 'contact_id') && Schema::hasColumn('journal_entries', 'location_id')) {
            return;
        }
        if (! Schema::hasColumn('journal_entries', 'client_id')) {
            return;
        }
        foreach (['client_id_index', 'branch_id_index'] as $index) {
            try {
                DB::statement("ALTER TABLE journal_entries DROP INDEX `{$index}`");
            } catch (\Exception $e) {
                // Index may not exist
            }
        }
        DB::statement('ALTER TABLE journal_entries CHANGE client_id contact_id INT(10) UNSIGNED NULL DEFAULT NULL');
        DB::statement('ALTER TABLE journal_entries CHANGE branch_id location_id INT(10) UNSIGNED NULL DEFAULT NULL');
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->index('contact_id');
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex('journal_entries_contact_id_index');
            $table->dropIndex('journal_entries_location_id_index');
        });
        DB::statement('ALTER TABLE `journal_entries` CHANGE `contact_id` `client_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE `journal_entries` CHANGE `location_id` `branch_id` INT(10) UNSIGNED NULL DEFAULT NULL;');
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->index('client_id', 'client_id_index');
            $table->index('branch_id', 'branch_id_index');
        });
    }
}