<?php
// Read most recent Laravel log — works with both single and daily log drivers
$logDir = __DIR__ . '/storage/logs';

// Find all log files sorted newest first
$files = glob($logDir . '/*.log');
if (empty($files)) {
    echo "No log files found in $logDir\n";
    exit;
}
usort($files, fn($a,$b) => filemtime($b) - filemtime($a));

$logFile = $files[0];
echo "Reading: $logFile  (modified: " . date('Y-m-d H:i:s', filemtime($logFile)) . ")\n";
echo str_repeat('-', 80) . "\n";

// Last 200 lines
$lines = file($logFile);
$last  = array_slice($lines, -200);
echo implode('', $last);
