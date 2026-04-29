<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logFile = storage_path('logs/laravel.log');
if (!file_exists($logFile)) {
    echo "Log file not found.\n";
    exit;
}

// Get last 150 lines of the log
$lines = file($logFile);
$last  = array_slice($lines, -150);
echo implode('', $last);
