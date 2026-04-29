<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Show DB connection info
$dbName = DB::connection()->getDatabaseName();
$prefix = DB::getTablePrefix();
echo "=== DB INFO ===\n";
echo "  Database : $dbName\n";
echo "  Prefix   : '$prefix'\n";

// List all tables
echo "\n=== ALL TABLES IN DATABASE ===\n";
$tables = DB::select('SHOW TABLES');
$col = 'Tables_in_' . $dbName;
foreach ($tables as $t) {
    echo "  " . $t->$col . "\n";
}

echo "\nDone.\n";
