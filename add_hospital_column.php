<?php
/**
 * Adds prescriber_hospital column to dda_prescriptions table.
 * Upload to pharma folder root, run: php add_hospital_column.php
 * Delete after running.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('dda_prescriptions', 'prescriber_hospital')) {
    Schema::table('dda_prescriptions', function (Blueprint $table) {
        $table->string('prescriber_hospital')->nullable()->after('prescriber_name');
    });
    echo "Added column: prescriber_hospital\n";
} else {
    echo "Column already exists: prescriber_hospital\n";
}

echo "Done! Delete this file.\n";
