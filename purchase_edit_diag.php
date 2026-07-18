<?php
/**
 * Purchase edit view check — upload to public_html/pos/
 * Open: https://liyonpharmacy.co.ke/pos/purchase_edit_diag.php?key=liyonfix500
 * DELETE after use.
 */
$key = $_GET['key'] ?? '';
if ($key !== 'liyonfix500') {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}
header('Content-Type: text/plain; charset=utf-8');
$base = __DIR__;
$files = [
    'resources/views/purchase/edit.blade.php',
    'resources/views/purchase/create.blade.php',
    'resources/views/purchase/partials/edit_purchase_entry_row.blade.php',
    'resources/views/purchase/partials/purchase_entry_row.blade.php',
    'resources/views/purchase/partials/purchase_slim_styles.blade.php',
    'resources/views/purchase/partials/purchase_line_details_modal.blade.php',
    'resources/views/purchase/partials/purchase_line_details_js.blade.php',
    'resources/views/purchase/partials/import_purchase_products_modal.blade.php',
    'resources/views/purchase/partials/keyboard_shortcuts.blade.php',
    'resources/views/purchase/partials/keyboard_shortcuts_details.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/partials/error.blade.php',
    'resources/views/contact/create.blade.php',
];
echo "=== Purchase edit view check ===\n";
echo 'Base: '.$base."\n\n";
$miss = 0;
foreach ($files as $rel) {
    $full = $base.'/'.str_replace('\\', '/', $rel);
    $ok = is_file($full);
    if (!$ok) {
        $miss++;
    }
    echo ($ok ? '[OK]  ' : '[MISS]').$rel;
    if ($ok) {
        echo '  '.date('Y-m-d H:i', filemtime($full)).'  '.filesize($full).' bytes';
        if (!is_readable($full)) {
            echo '  NOT READABLE';
        }
    }
    echo "\n";
}
$dir = $base.'/resources/views/purchase';
echo "\n-- purchase/ listing --\n";
if (is_dir($dir)) {
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') {
            continue;
        }
        $p = $dir.'/'.$f;
        echo (is_dir($p) ? '[dir] ' : '[file]').$f."\n";
    }
} else {
    echo "MISSING DIR resources/views/purchase\n";
}
$pdir = $base.'/resources/views/purchase/partials';
echo "\n-- purchase/partials/ listing --\n";
if (is_dir($pdir)) {
    foreach (scandir($pdir) as $f) {
        if ($f === '.' || $f === '..') {
            continue;
        }
        echo '[file] '.$f."\n";
    }
} else {
    echo "MISSING DIR partials\n";
}
echo "\nMissing count: {$miss}\n";
if ($miss === 0) {
    echo "All purchase edit files present. Clear views: php artisan view:clear\n";
} else {
    echo "Upload missing files from pos_purchase_slim_ui.zip into matching folders.\n";
}
