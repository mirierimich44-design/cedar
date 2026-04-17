<?php
/**
 * Upgrade dda_destruction_log to PPB disposal workflow.
 * Upload to pharma folder root, run: php add_disposal_columns.php
 * Delete after running.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "=== Upgrade Disposal Log Table ===\n\n";

Schema::table('dda_destruction_log', function (Blueprint $table) {
    if (!Schema::hasColumn('dda_destruction_log', 'drug_name'))
        $table->string('drug_name')->nullable()->after('business_id');

    if (!Schema::hasColumn('dda_destruction_log', 'batch_number'))
        $table->string('batch_number')->nullable()->after('unit');

    if (!Schema::hasColumn('dda_destruction_log', 'expiry_date'))
        $table->date('expiry_date')->nullable()->after('batch_number');

    if (!Schema::hasColumn('dda_destruction_log', 'status'))
        $table->enum('status', ['quarantined','ppb_applied','collected','certificate_received'])
              ->default('quarantined')->after('expiry_date');

    if (!Schema::hasColumn('dda_destruction_log', 'ppb_application_number'))
        $table->string('ppb_application_number')->nullable()->after('status');

    if (!Schema::hasColumn('dda_destruction_log', 'ppb_application_date'))
        $table->date('ppb_application_date')->nullable()->after('ppb_application_number');

    if (!Schema::hasColumn('dda_destruction_log', 'disposal_company'))
        $table->string('disposal_company')->nullable()->after('ppb_application_date');

    if (!Schema::hasColumn('dda_destruction_log', 'collection_date'))
        $table->date('collection_date')->nullable()->after('disposal_company');

    if (!Schema::hasColumn('dda_destruction_log', 'ppb_certificate_number'))
        $table->string('ppb_certificate_number')->nullable()->after('collection_date');

    if (!Schema::hasColumn('dda_destruction_log', 'ppb_certificate_date'))
        $table->date('ppb_certificate_date')->nullable()->after('ppb_certificate_number');

    if (!Schema::hasColumn('dda_destruction_log', 'certificate_image_path'))
        $table->string('certificate_image_path')->nullable()->after('ppb_certificate_date');
});

echo "Table upgraded successfully.\n";
echo "Done! Delete this file.\n";
