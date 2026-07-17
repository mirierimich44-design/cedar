<?php
/**
 * EMERGENCY 500 DIAG — upload to public_html/pos/ (same folder as index.php)
 * Open: https://liyonpharmacy.co.ke/pos/emergency_diag.php?key=liyonfix500
 * DELETE this file after use.
 */
$key = $_GET['key'] ?? '';
if ($key !== 'liyonfix500') {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "=== Liyon POS emergency diag ===\n";
echo 'Time: '.date('c')."\n";
echo 'PHP: '.PHP_VERSION."\n";
echo 'CWD: '.getcwd()."\n\n";

$checks = [
    'index.php' => __DIR__.'/index.php',
    'artisan' => __DIR__.'/artisan',
    '.env' => __DIR__.'/.env',
    'vendor/autoload.php' => __DIR__.'/vendor/autoload.php',
    'bootstrap/app.php' => __DIR__.'/bootstrap/app.php',
    'app/Http/Middleware/AdminSidebarMenu.php' => __DIR__.'/app/Http/Middleware/AdminSidebarMenu.php',
    'app/Http/Controllers/OwnerOpsController.php' => __DIR__.'/app/Http/Controllers/OwnerOpsController.php',
    'app/Http/Controllers/AdvancedReportsController.php' => __DIR__.'/app/Http/Controllers/AdvancedReportsController.php',
    'app/Http/Controllers/ReportsHubController.php' => __DIR__.'/app/Http/Controllers/ReportsHubController.php',
    'routes/web.php' => __DIR__.'/routes/web.php',
    'bootstrap/cache/routes-v7.php' => __DIR__.'/bootstrap/cache/routes-v7.php',
    'bootstrap/cache/config.php' => __DIR__.'/bootstrap/cache/config.php',
];

echo "=== File checks ===\n";
foreach ($checks as $label => $path) {
    $ok = file_exists($path);
    echo ($ok ? '[OK]  ' : '[MISS]').$label;
    if ($ok) {
        echo '  '.date('Y-m-d H:i', filemtime($path)).'  '.filesize($path).' bytes';
    }
    echo "\n";
}

// Clear route/config cache if requested
if (isset($_GET['clear_cache']) && $_GET['clear_cache'] === '1') {
    echo "\n=== Clearing bootstrap/cache route+config ===\n";
    foreach (['routes-v7.php', 'routes.php', 'config.php', 'packages.php', 'services.php'] as $f) {
        $p = __DIR__.'/bootstrap/cache/'.$f;
        if (file_exists($p)) {
            @unlink($p);
            echo "Deleted $f\n";
        }
    }
    $views = __DIR__.'/storage/framework/views';
    if (is_dir($views)) {
        $n = 0;
        foreach (glob($views.'/*') as $vf) {
            if (is_file($vf)) {
                @unlink($vf);
                $n++;
            }
        }
        echo "Cleared $n compiled views\n";
    }
}

echo "\n=== Latest Laravel log (last 80 lines) ===\n";
$logDir = __DIR__.'/storage/logs';
$latest = null;
if (is_dir($logDir)) {
    $files = glob($logDir.'/laravel*.log');
    if ($files) {
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        $latest = $files[0];
    }
}
if ($latest && is_readable($latest)) {
    echo 'File: '.$latest."\n\n";
    $lines = file($latest);
    $slice = array_slice($lines, -80);
    echo implode('', $slice);
} else {
    echo "No log file found or not readable.\n";
}

echo "\n=== Try boot Laravel ===\n";
try {
    require __DIR__.'/vendor/autoload.php';
    $app = require __DIR__.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "Bootstrap: OK\n";
    echo 'APP_ENV: '.config('app.env')."\n";
    echo 'APP_DEBUG: '.(config('app.debug') ? 'true' : 'false')."\n";
    // Route names we need
    $names = [
        'reports.hub', 'reports.day_close', 'reports.month_end_pack',
        'reports.bank_mpesa_recon', 'reports.weekly_ritual',
    ];
    foreach ($names as $n) {
        echo (Illuminate\Support\Facades\Route::has($n) ? '[OK]  ' : '[MISS]')."route $n\n";
    }
    // Class existence
    foreach ([
        'App\\Http\\Controllers\\OwnerOpsController',
        'App\\Http\\Controllers\\AdvancedReportsController',
        'App\\Http\\Controllers\\ReportsHubController',
    ] as $c) {
        echo (class_exists($c) ? '[OK]  ' : '[MISS]')."$c\n";
    }
} catch (Throwable $e) {
    echo "BOOT FAILED: ".$e->getMessage()."\n";
    echo $e->getFile().':'.$e->getLine()."\n";
    echo $e->getTraceAsString()."\n";
}

echo "\n=== Done ===\n";
echo "If routes are MISS: upload routes/web.php then open this URL with &clear_cache=1\n";
echo "Delete emergency_diag.php when finished.\n";
