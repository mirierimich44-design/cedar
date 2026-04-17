<?php
/**
 * Create Default Business Location
 * Upload to pharma folder root, run: php create_location.php
 * Delete after running.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Create Business Location ===\n\n";

$business = DB::table('business')->first();
if (!$business) {
    echo "ERROR: No business found!\n";
    exit(1);
}
echo "Business: {$business->name} (ID: {$business->id})\n\n";

// Find valid invoice_scheme_id and invoice_layout_id for this business
$invoice_scheme = DB::table('invoice_schemes')->where('business_id', $business->id)->first();
if (!$invoice_scheme) {
    // Create a default one
    $scheme_id = DB::table('invoice_schemes')->insertGetId([
        'business_id'   => $business->id,
        'name'          => 'Default',
        'scheme_type'   => 'blank',
        'prefix'        => 'INV',
        'start_number'  => 1,
        'invoice_count' => 0,
        'total_digits'  => 4,
        'is_default'    => 1,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);
    echo "Created invoice scheme (ID: {$scheme_id})\n";
} else {
    $scheme_id = $invoice_scheme->id;
    echo "Using invoice scheme: {$invoice_scheme->name} (ID: {$scheme_id})\n";
}

$invoice_layout = DB::table('invoice_layouts')->where('business_id', $business->id)->first();
if (!$invoice_layout) {
    $layout_id = DB::table('invoice_layouts')->insertGetId([
        'business_id' => $business->id,
        'name'        => 'Default',
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);
    echo "Created invoice layout (ID: {$layout_id})\n";
} else {
    $layout_id = $invoice_layout->id;
    echo "Using invoice layout: {$invoice_layout->name} (ID: {$layout_id})\n";
}

// Check if location already exists (including soft-deleted)
$any = DB::table('business_locations')->where('business_id', $business->id)->first();
if ($any) {
    // Restore if soft-deleted
    DB::table('business_locations')
        ->where('business_id', $business->id)
        ->update(['deleted_at' => null]);
    echo "Restored existing location (ID: {$any->id}): {$any->name}\n";
    $location_id = $any->id;
} else {
    $location_id = DB::table('business_locations')->insertGetId([
        'business_id'          => $business->id,
        'location_id'          => 'PH001',
        'name'                 => 'Main Pharmacy',
        'landmark'             => '',
        'city'                 => '',
        'state'                => '',
        'country'              => 'Kenya',
        'zip_code'             => '',
        'invoice_scheme_id'    => $scheme_id,
        'invoice_layout_id'    => $layout_id,
        'selling_price_group_id' => null,
        'print_receipt_on_invoice' => 1,
        'receipt_printer_type' => 'browser',
        'is_active'            => 1,
        'default_payment_accounts' => json_encode(['cash' => ['is_enabled' => 1, 'account' => '']]),
        'created_at'           => now(),
        'updated_at'           => now(),
    ]);
    echo "Created location: Main Pharmacy (ID: {$location_id})\n";
}

// Ensure location permission exists and is assigned to Admin role
$location_perm_name = 'location.' . $location_id;

$perm = DB::table('permissions')
    ->where('name', $location_perm_name)
    ->where('guard_name', 'web')
    ->first();

if (!$perm) {
    $perm_id = DB::table('permissions')->insertGetId([
        'name'       => $location_perm_name,
        'guard_name' => 'web',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created permission: {$location_perm_name}\n";
} else {
    $perm_id = $perm->id;
    echo "Permission already exists: {$location_perm_name}\n";
}

// Also ensure access_all_locations permission exists
$all_perm = DB::table('permissions')
    ->where('name', 'access_all_locations')
    ->where('guard_name', 'web')
    ->first();

if (!$all_perm) {
    $all_perm_id = DB::table('permissions')->insertGetId([
        'name'       => 'access_all_locations',
        'guard_name' => 'web',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created permission: access_all_locations\n";
} else {
    $all_perm_id = $all_perm->id;
    echo "Permission already exists: access_all_locations\n";
}

// Assign both permissions to all Admin roles for this business
$admin_roles = DB::table('roles')
    ->where('name', 'like', 'Admin%')
    ->where('guard_name', 'web')
    ->get();

foreach ($admin_roles as $role) {
    foreach ([$perm_id, $all_perm_id] as $pid) {
        $exists = DB::table('role_has_permissions')
            ->where('permission_id', $pid)
            ->where('role_id', $role->id)
            ->exists();
        if (!$exists) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $pid,
                'role_id'       => $role->id,
            ]);
        }
    }
    echo "Role {$role->name}: location permissions assigned\n";
}

\Artisan::call('optimize:clear');
echo "\nCache cleared.\n";
echo "\n=== Done! Refresh and try POS again. ===\n";
echo "Delete this file after use!\n";
