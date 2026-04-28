<?php
/**
 * Fix Missing Walk-in Customers & Units
 * This script ensures every business has a default Walk-in Customer and Pieces unit.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Business;
use App\Contact;
use App\Unit;
use App\User;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\DB;

$businessUtil = new BusinessUtil();

echo "=== Fixing Missing Default Resources ===\n\n";

$businesses = Business::all();

foreach ($businesses as $business) {
    echo "Processing Business: {$business->name} (ID: {$business->id})...\n";

    // 1. Check/Fix Walk-in Customer
    $walk_in_exists = Contact::where('business_id', $business->id)
                             ->where('is_default', 1)
                             ->exists();

    if (!$walk_in_exists) {
        $admin = User::where('business_id', $business->id)->first();
        if ($admin) {
            $ref_count = $businessUtil->setAndGetReferenceCount('contacts', $business->id);
            $contact_id = $businessUtil->generateReferenceNumber('contacts', $ref_count, $business->id);

            Contact::create([
                'business_id' => $business->id,
                'type'        => 'customer',
                'name'        => 'Walk-In Customer',
                'created_by'  => $admin->id,
                'is_default'  => 1,
                'contact_id'  => $contact_id,
                'credit_limit'=> 0,
            ]);
            echo "  [+] Created Walk-In Customer\n";
        } else {
            echo "  [!] ERROR: No admin user found for this business. Skipping customer creation.\n";
        }
    } else {
        echo "  [.] Walk-In Customer already exists.\n";
    }

    // 2. Check/Fix Default Unit (Pieces)
    $unit_exists = Unit::where('business_id', $business->id)
                       ->where('actual_name', 'Pieces')
                       ->exists();

    if (!$unit_exists) {
        $admin = User::where('business_id', $business->id)->first();
        if ($admin) {
            Unit::create([
                'business_id' => $business->id,
                'actual_name' => 'Pieces',
                'short_name'  => 'Pc(s)',
                'allow_decimal'=> 0,
                'created_by'  => $admin->id,
            ]);
            echo "  [+] Created 'Pieces' Unit\n";
        }
    } else {
        echo "  [.] 'Pieces' unit already exists.\n";
    }
    echo "\n";
}

\Artisan::call('optimize:clear');
echo "=== Done! All businesses checked and fixed. ===\n";
echo "Delete this file after use!\n";
