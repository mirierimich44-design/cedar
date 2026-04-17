<?php
/**
 * Fix Location Access
 * Assigns all business locations to all users of the business.
 * Upload to pharma folder root, run: php fix_location_access.php
 * Delete after running.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fix Location Access ===\n\n";

// 1. Find the business
$business = DB::table('business')->first();
if (!$business) {
    echo "ERROR: No business found!\n";
    exit(1);
}
echo "Business: {$business->name} (ID: {$business->id})\n\n";

// 2. Get all locations for this business
$locations = DB::table('business_locations')
    ->where('business_id', $business->id)
    ->whereNull('deleted_at')
    ->get();

if ($locations->isEmpty()) {
    echo "ERROR: No business locations found! Please create a location first.\n";
    exit(1);
}

echo "Found " . $locations->count() . " location(s):\n";
foreach ($locations as $loc) {
    echo "  - {$loc->name} (ID: {$loc->id})\n";
}
echo "\n";

// 3. Get all users for this business
$users = DB::table('users')
    ->where('business_id', $business->id)
    ->where('status', 'active')
    ->get();

echo "Found " . $users->count() . " user(s):\n";

foreach ($users as $user) {
    echo "  User: {$user->first_name} {$user->last_name} ({$user->email}) ID: {$user->id}\n";

    foreach ($locations as $loc) {
        $exists = DB::table('user_locations')
            ->where('user_id', $user->id)
            ->where('location_id', $loc->id)
            ->exists();

        if (!$exists) {
            DB::table('user_locations')->insert([
                'user_id'     => $user->id,
                'location_id' => $loc->id,
            ]);
            echo "    Assigned to location: {$loc->name}\n";
        } else {
            echo "    Already has access to: {$loc->name}\n";
        }
    }
}

echo "\n=== Done! Refresh and try opening the cash register again. ===\n";
echo "Delete this file after use!\n";
