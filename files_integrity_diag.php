<?php
/**
 * FILES INTEGRITY SCANNER — upload to public_html/pos/ (same folder as index.php / artisan)
 *
 * Open:
 *   https://liyonpharmacy.co.ke/pos/files_integrity_diag.php?key=liyonfix500
 * Optional:
 *   &format=json
 *   &clear_views=1   (clears compiled blade cache)
 *
 * DELETE this file after use.
 */
$key = $_GET['key'] ?? '';
if ($key !== 'liyonfix500') {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$asJson = (isset($_GET['format']) && $_GET['format'] === 'json');
if (!$asJson) {
    header('Content-Type: text/plain; charset=utf-8');
}
error_reporting(E_ALL);
ini_set('display_errors', '1');

$base = __DIR__;

/** @return array{path:string,exists:bool,size:?int,mtime:?string,readable:?bool} */
function check_path($base, $rel)
{
    $full = $base . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rel);
    $exists = file_exists($full);
    return [
        'path' => $rel,
        'exists' => $exists,
        'size' => $exists ? filesize($full) : null,
        'mtime' => $exists ? date('Y-m-d H:i:s', filemtime($full)) : null,
        'readable' => $exists ? is_readable($full) : null,
        'full' => $full,
    ];
}

// Critical files that have caused 500s / broken UI after partial deploys
$required = [
    // Core boot
    'index.php',
    'artisan',
    '.env',
    'vendor/autoload.php',
    'bootstrap/app.php',
    'routes/web.php',
    'routes/api.php',

    // Controllers (recent hotfixes + core)
    'app/Http/Controllers/HomeController.php',
    'app/Http/Controllers/Auth/LoginController.php',
    'app/Http/Controllers/SellController.php',
    'app/Http/Controllers/PurchaseController.php',
    'app/Http/Controllers/ReportController.php',
    'app/Http/Controllers/ReportsHubController.php',
    'app/Http/Controllers/AdvancedReportsController.php',
    'app/Http/Controllers/OwnerOpsController.php',
    'app/Http/Middleware/AdminSidebarMenu.php',

    // Auth / home (login loop recovery)
    'resources/views/auth/login.blade.php',
    'resources/views/home/index.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/welcome.blade.php',

    // Purchase (Detail button + edit)
    'resources/views/purchase/create.blade.php',
    'resources/views/purchase/edit.blade.php',
    'resources/views/purchase/index.blade.php',
    'resources/views/purchase/partials/purchase_entry_row.blade.php',
    'resources/views/purchase/partials/edit_purchase_entry_row.blade.php',
    'resources/views/purchase/partials/purchase_line_details_modal.blade.php',
    'resources/views/purchase/partials/purchase_line_details_js.blade.php',
    'resources/views/purchase/partials/import_purchase_products_modal.blade.php',
    'resources/views/purchase/partials/keyboard_shortcuts.blade.php',
    'js/purchase.js',
    'js/product.js',

    // Sell list
    'resources/views/sell/index.blade.php',
    'resources/views/sell/create.blade.php',

    // Report CSS + critical reports
    'resources/views/report/partials/report_modern_css.blade.php',
    'resources/views/report/partials/export_toolbar.blade.php',
    'resources/views/report/profit_loss.blade.php',
    'resources/views/report/purchase_sell.blade.php',
    'resources/views/report/stock_report.blade.php',
    'resources/views/report/hub/index.blade.php',
    'resources/views/report/hub/day_close.blade.php',
    'resources/views/report/hub/reorder_list.blade.php',
    'resources/views/report/owner/dashboard.blade.php',
    'resources/views/report/advanced/financial_statements.blade.php',
    'resources/views/report/advanced/bank_mpesa_recon.blade.php',
    'resources/views/report/advanced/month_end_pack.blade.php',
    'resources/views/report/advanced/inventory_valuation.blade.php',
    'resources/views/report/advanced/discount_abuse.blade.php',
    'resources/views/report/advanced/fefo_compliance.blade.php',
    'resources/views/report/advanced/audit_export.blade.php',
    'resources/views/report/advanced/multi_period.blade.php',
    'resources/views/report/advanced/supplier_payables.blade.php',

    // Writable dirs (presence)
    'storage/logs',
    'storage/framework/views',
    'storage/framework/cache',
    'bootstrap/cache',
];

// Optional but expected for owner ops / day close
$optional = [
    'resources/views/home/partials/owner_ops_strip.blade.php',
    'resources/views/report/owner/data_quality.blade.php',
    'resources/views/report/owner/deploy_checklist.blade.php',
    'resources/views/report/owner/roles_guide.blade.php',
    'resources/views/report/owner/weekly_ritual.blade.php',
    'resources/views/report/owner/month_end_notify.blade.php',
    'resources/views/report/hub/expiry_smart.blade.php',
    'resources/views/report/hub/ledger_gap.blade.php',
];

$results = [];
$missing = [];
$okCount = 0;

foreach ($required as $rel) {
    $r = check_path($base, $rel);
    $r['tier'] = 'required';
    $results[] = $r;
    if ($r['exists']) {
        $okCount++;
    } else {
        $missing[] = $rel;
    }
}

$optMissing = [];
foreach ($optional as $rel) {
    $r = check_path($base, $rel);
    $r['tier'] = 'optional';
    $results[] = $r;
    if (!$r['exists']) {
        $optMissing[] = $rel;
    }
}

// Blade scan: any view that @includes report_modern_css without includeIf is softer issue
// Quick purchase detail wiring check via string contains
$purchaseChecks = [];
$editPath = $base . '/resources/views/purchase/edit.blade.php';
if (file_exists($editPath)) {
    $editSrc = file_get_contents($editPath);
    $purchaseChecks['edit_includes_details_modal'] = strpos($editSrc, 'purchase_line_details_modal') !== false;
    $purchaseChecks['edit_includes_details_js'] = strpos($editSrc, 'purchase_line_details_js') !== false;
}
$editRowPath = $base . '/resources/views/purchase/partials/edit_purchase_entry_row.blade.php';
if (file_exists($editRowPath)) {
    $rowSrc = file_get_contents($editRowPath);
    $purchaseChecks['edit_row_has_details_btn'] = strpos($rowSrc, 'btn-purchase-details') !== false;
    $purchaseChecks['edit_row_has_data_row'] = strpos($rowSrc, 'data-row') !== false;
}
$createPath = $base . '/resources/views/purchase/create.blade.php';
if (file_exists($createPath)) {
    $createSrc = file_get_contents($createPath);
    $purchaseChecks['create_includes_details_modal'] = strpos($createSrc, 'purchase_line_details_modal') !== false
        || strpos($createSrc, 'purchase_line_details_modal') !== false
        || strpos($createSrc, 'id="purchase_line_details_modal"') !== false;
    $purchaseChecks['create_has_details_handler'] = (
        strpos($createSrc, 'btn-purchase-details') !== false
        || strpos($createSrc, 'purchase_line_details_js') !== false
    );
}

// Latest log tail
$logTail = '';
$logDir = $base . '/storage/logs';
$latestLog = null;
if (is_dir($logDir)) {
    $files = glob($logDir . '/laravel*.log') ?: [];
    if ($files) {
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        $latestLog = $files[0];
        $lines = @file($latestLog);
        if ($lines) {
            $logTail = implode('', array_slice($lines, -40));
        }
    }
}

if (isset($_GET['clear_views']) && $_GET['clear_views'] === '1') {
    $views = $base . '/storage/framework/views';
    $n = 0;
    if (is_dir($views)) {
        foreach (glob($views . '/*') as $vf) {
            if (is_file($vf) && @unlink($vf)) {
                $n++;
            }
        }
    }
    $clearedViews = $n;
} else {
    $clearedViews = null;
}

$payload = [
    'time' => date('c'),
    'php' => PHP_VERSION,
    'cwd' => getcwd(),
    'base' => $base,
    'required_ok' => $okCount,
    'required_total' => count($required),
    'required_missing' => $missing,
    'optional_missing' => $optMissing,
    'purchase_detail_wiring' => $purchaseChecks,
    'cleared_compiled_views' => $clearedViews,
    'latest_log' => $latestLog ? basename($latestLog) : null,
    'files' => $results,
    'log_tail' => $logTail,
];

if ($asJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_PRETTY_PRINT);
    exit;
}

echo "=== Liyon POS files integrity scan ===\n";
echo 'Time: ' . $payload['time'] . "\n";
echo 'PHP: ' . $payload['php'] . "\n";
echo 'Base: ' . $payload['base'] . "\n";
echo 'Required: ' . $okCount . ' / ' . count($required) . " OK\n\n";

if ($clearedViews !== null) {
    echo "Cleared compiled views: {$clearedViews}\n\n";
}

echo "=== REQUIRED MISSING (" . count($missing) . ") ===\n";
if (!$missing) {
    echo "(none)\n";
} else {
    foreach ($missing as $m) {
        echo "[MISS] {$m}\n";
    }
}

echo "\n=== OPTIONAL MISSING (" . count($optMissing) . ") ===\n";
if (!$optMissing) {
    echo "(none)\n";
} else {
    foreach ($optMissing as $m) {
        echo "[MISS] {$m}\n";
    }
}

echo "\n=== PURCHASE DETAIL BUTTON WIRING ===\n";
foreach ($purchaseChecks as $k => $v) {
    echo ($v ? '[OK]  ' : '[FAIL]') . $k . "\n";
}

echo "\n=== ALL REQUIRED FILE DETAILS ===\n";
foreach ($results as $r) {
    if ($r['tier'] !== 'required') {
        continue;
    }
    if ($r['exists']) {
        echo '[OK]  ' . $r['path'] . '  ' . $r['mtime'] . '  ' . $r['size'] . " bytes\n";
    } else {
        echo '[MISS]' . $r['path'] . "\n";
    }
}

echo "\n=== LATEST LOG (" . ($latestLog ? basename($latestLog) : 'none') . ") last 40 lines ===\n";
echo $logTail ?: "(no log)\n";

echo "\n=== HOW TO FIX MISSING FILES ===\n";
echo "1. From your PC project, upload the missing paths into public_html/pos/ keeping folders.\n";
echo "2. After upload open: ?key=liyonfix500&clear_views=1\n";
echo "3. Or Hostinger Terminal:\n";
echo "   cd ~/domains/liyonpharmacy.co.ke/public_html/pos\n";
echo "   php artisan view:clear && php artisan cache:clear && php artisan route:clear\n";
echo "4. DELETE this diag file when done.\n";
