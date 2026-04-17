<?php
/**
 * Create DDA Products
 * Creates a product in the main product list for each DDA drug that
 * doesn't already have one. Products will have is_dda=1 and be linked
 * to the corresponding dda_drug entry.
 *
 * After running, go to Products > edit each DDA product to set price,
 * category, stock etc.
 *
 * Upload to pharma folder root, run: php create_dda_products.php
 * Delete after running.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Create DDA Products ===\n\n";

$business = DB::table('business')->first();
if (!$business) { echo "ERROR: No business found!\n"; exit(1); }
echo "Business: {$business->name} (ID: {$business->id})\n\n";

$admin_user = DB::table('users')->where('business_id', $business->id)->first();
if (!$admin_user) { echo "ERROR: No user found!\n"; exit(1); }

// Find or create a default unit (Tablet / Pieces)
$unit = DB::table('units')
    ->where('business_id', $business->id)
    ->first();

if (!$unit) {
    $unit_id = DB::table('units')->insertGetId([
        'business_id'   => $business->id,
        'actual_name'   => 'Pieces',
        'short_name'    => 'Pcs',
        'allow_decimal' => 0,
        'created_by'    => $admin_user->id,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);
    echo "Created default unit: Pieces (ID: {$unit_id})\n";
} else {
    $unit_id = $unit->id;
    echo "Using unit: {$unit->actual_name} (ID: {$unit_id})\n";
}

// Find the business location
$location = DB::table('business_locations')
    ->where('business_id', $business->id)
    ->whereNull('deleted_at')
    ->first();

if (!$location) { echo "ERROR: No business location found! Run create_location.php first.\n"; exit(1); }
echo "Location: {$location->name} (ID: {$location->id})\n\n";

// Get all DDA drugs
$dda_drugs = DB::table('dda_drugs')->where('is_active', 1)->get();
echo "Found " . $dda_drugs->count() . " active DDA drugs\n\n";

$created = 0;
$skipped = 0;

foreach ($dda_drugs as $drug) {
    // Check if a product already linked to this drug exists
    $existing = DB::table('products')
        ->where('business_id', $business->id)
        ->where('dda_drug_id', $drug->id)
        ->first();

    if ($existing) {
        echo "  SKIP: {$drug->name} — product already exists (ID: {$existing->id})\n";
        $skipped++;
        continue;
    }

    // Generate unique SKU
    $sku_base = 'DDA-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $drug->name), 0, 6));
    $sku = $sku_base;
    $i = 1;
    while (DB::table('products')->where('sku', $sku)->where('business_id', $business->id)->exists()) {
        $sku = $sku_base . $i++;
    }

    DB::beginTransaction();
    try {
        // 1. Insert product
        $product_id = DB::table('products')->insertGetId([
            'name'           => $drug->name,
            'business_id'    => $business->id,
            'type'           => 'single',
            'unit_id'        => $unit_id,
            'brand_id'       => null,
            'category_id'    => null,
            'sub_category_id'=> null,
            'tax'            => null,
            'tax_type'       => 'exclusive',
            'enable_stock'   => 1,
            'alert_quantity' => 5,
            'sku'            => $sku,
            'barcode_type'   => 'C128',
            'not_for_selling'=> 0,
            'is_dda'         => 1,
            'dda_drug_id'    => $drug->id,
            'product_description' => $drug->description,
            'created_by'     => $admin_user->id,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // 2. Insert product_variation (dummy for single-type products)
        $pv_id = DB::table('product_variations')->insertGetId([
            'name'       => 'DUMMY',
            'product_id' => $product_id,
            'is_dummy'   => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Insert variation
        $variation_id = DB::table('variations')->insertGetId([
            'name'                  => 'DUMMY',
            'product_id'            => $product_id,
            'sub_sku'               => $sku,
            'product_variation_id'  => $pv_id,
            'default_purchase_price'=> 1,
            'dpp_inc_tax'           => 1,
            'profit_percent'        => 100,
            'default_sell_price'    => 2,
            'sell_price_inc_tax'    => 2,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        // 4. Insert variation_location_details for stock tracking
        DB::table('variation_location_details')->insert([
            'product_id'        => $product_id,
            'product_variation_id' => $pv_id,
            'variation_id'      => $variation_id,
            'location_id'       => $location->id,
            'qty_available'     => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        DB::commit();
        echo "  CREATED: {$drug->name} (Product ID: {$product_id}, SKU: {$sku})\n";
        $created++;

    } catch (\Exception $e) {
        DB::rollBack();
        echo "  ERROR: {$drug->name} — " . $e->getMessage() . "\n";
    }
}

echo "\n=== Done! Created: {$created}, Skipped: {$skipped} ===\n";
echo "Go to Products in the menu to edit prices and categories.\n";
echo "Delete this file after use!\n";
