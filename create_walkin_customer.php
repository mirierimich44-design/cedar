<?php
/**
 * Create Walk-in Customer
 * Creates the default walk-in customer for the business.
 * Upload to pharma folder root, run: php create_walkin_customer.php
 * Delete after running.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Create Walk-in Customer ===\n\n";

$business = DB::table('business')->first();
if (!$business) { echo "ERROR: No business found!\n"; exit(1); }
echo "Business: {$business->name} (ID: {$business->id})\n\n";

$admin_user = DB::table('users')->where('business_id', $business->id)->first();

// Check if already exists
$existing = DB::table('contacts')
    ->where('business_id', $business->id)
    ->where('is_default', 1)
    ->first();

if ($existing) {
    echo "Walk-in customer already exists: {$existing->name} (ID: {$existing->id})\n";
    exit(0);
}

// Get actual columns from the contacts table to avoid mismatches
$columns = DB::select("SHOW COLUMNS FROM contacts");
$col_names = array_map(fn($c) => $c->Field, $columns);

$data = [
    'business_id' => $business->id,
    'type'        => 'customer',
    'name'        => 'Walk-in Customer',
    'is_default'  => 1,
    'created_by'  => $admin_user->id,
    'created_at'  => now(),
    'updated_at'  => now(),
];

// Add optional fields only if the column exists
$optional = [
    'mobile'                 => '0000000000',
    'supplier_business_name' => null,
    'tax_number'             => null,
    'city'                   => null,
    'state'                  => null,
    'country'                => null,
    'pay_term_number'        => null,
    'pay_term_type'          => null,
];
foreach ($optional as $col => $val) {
    if (in_array($col, $col_names)) {
        $data[$col] = $val;
    }
}

$contact_id = DB::table('contacts')->insertGetId($data);

echo "Created Walk-in Customer (ID: {$contact_id})\n";

\Artisan::call('optimize:clear');
echo "Cache cleared.\n";
echo "\n=== Done! ===\n";
echo "Delete this file after use!\n";
