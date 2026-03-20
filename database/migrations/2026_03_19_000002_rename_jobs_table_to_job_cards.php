<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameJobsTableToJobCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only rename if 'jobs' exists and 'job_cards' doesn't
        if (Schema::hasTable('jobs') && !Schema::hasTable('job_cards')) {
            // Check if it's our jobs table (has ref_no) or Laravel's (has queue)
            if (Schema::hasColumn('jobs', 'ref_no')) {
                Schema::rename('jobs', 'job_cards');
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('job_cards') && !Schema::hasTable('jobs')) {
            Schema::rename('job_cards', 'jobs');
        }
    }
}
