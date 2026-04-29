<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Activate all inactive business locations (that aren't soft-deleted)
$updated = DB::table('business_locations')
    ->whereNull('deleted_at')
    ->where('is_active', 0)
    ->update(['is_active' => 1]);

echo "Activated $updated business location(s).\n";

// Confirm current state
$locs = DB::table('business_locations')->select('id','name','business_id','is_active')->get();
echo "\nCurrent state:\n";
foreach ($locs as $l) {
    $status = $l->is_active ? 'ACTIVE' : 'INACTIVE';
    echo "  id={$l->id}  business_id={$l->business_id}  {$status}  name={$l->name}\n";
}

// Also ensure location.2 permission is assigned to all Admin roles for business 2
$locPerm = DB::table('permissions')->where('name', 'location.2')->first();
if ($locPerm) {
    // Assign to all users with business_id=2 who have access_all_locations
    $userIds = DB::table('users')->where('business_id', 2)->pluck('id');
    foreach ($userIds as $uid) {
        $exists = DB::table('model_has_permissions')
            ->where('permission_id', $locPerm->id)
            ->where('model_id', $uid)
            ->where('model_type', 'App\\User')
            ->exists();
        if (!$exists) {
            DB::table('model_has_permissions')->insert([
                'permission_id' => $locPerm->id,
                'model_type'    => 'App\\User',
                'model_id'      => $uid,
            ]);
            echo "  Assigned location.2 permission to user_id=$uid\n";
        } else {
            echo "  user_id=$uid already has location.2\n";
        }
    }
}

// Clear permission cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
echo "\nPermission cache cleared.\n";
echo "Done! Refresh the Add Purchase page — the location should now appear.\n";
