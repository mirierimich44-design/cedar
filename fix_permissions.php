<?php
/**
 * Fix Permissions & Enabled Modules
 * Fixes: POS button missing, missing permissions
 * Upload to pharma folder root, run: php fix_permissions.php
 * Delete after running.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fix Permissions & Modules ===\n\n";

// 1. Find the business
$business = DB::table('business')->first();
if (!$business) {
    echo "ERROR: No business found!\n";
    exit(1);
}
echo "Business: {$business->name} (ID: {$business->id})\n\n";

// 2. Show current enabled modules
$current_modules = json_decode($business->enabled_modules ?? '[]', true) ?? [];
echo "Current enabled modules: " . implode(', ', $current_modules) . "\n\n";

// 3. Ensure all needed modules are enabled
$required_modules = [
    'purchases', 'add_sale', 'pos_sale', 'stock_adjustment',
    'expense', 'account', 'tables', 'modifiers',
    'service_staff', 'subscription', 'types_of_service',
    'kitchen', 'repairs', 'manufacturing', 'woocommerce',
    'payroll', 'asset', 'custom_labels', 'contacts', 'crm',
];

$merged_modules = array_unique(array_merge($current_modules, $required_modules));
DB::table('business')->where('id', $business->id)->update([
    'enabled_modules' => json_encode($merged_modules),
]);
echo "Updated enabled modules to include: pos_sale, purchases, add_sale + others\n\n";

// 4. Get Admin roles for this business
$admin_roles = DB::table('roles')
    ->where('name', 'like', 'Admin%')
    ->where('guard_name', 'web')
    ->get();

echo "Found " . count($admin_roles) . " Admin role(s)\n";

// 5. All permissions that Admin should have
$all_permissions = DB::table('permissions')->where('guard_name', 'web')->get();
echo "Total permissions in system: " . count($all_permissions) . "\n\n";

foreach ($admin_roles as $role) {
    echo "Processing role: {$role->name} (ID: {$role->id})\n";

    $existing = DB::table('role_has_permissions')
        ->where('role_id', $role->id)
        ->pluck('permission_id')
        ->toArray();

    $added = 0;
    foreach ($all_permissions as $perm) {
        if (!in_array($perm->id, $existing)) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $perm->id,
                'role_id'       => $role->id,
            ]);
            $added++;
        }
    }
    echo "  Added $added missing permissions\n";
}

// 6. Show the admin user
$admin_user = DB::table('users')->where('business_id', $business->id)->first();
if ($admin_user) {
    echo "\nAdmin user: {$admin_user->first_name} {$admin_user->last_name} ({$admin_user->email})\n";

    // Check if user has a role assigned
    $user_roles = DB::table('model_has_roles')->where('model_id', $admin_user->id)->get();
    echo "Roles assigned: " . $user_roles->count() . "\n";

    if ($user_roles->count() === 0) {
        // Assign the first Admin role
        $first_admin_role = DB::table('roles')->where('name', 'like', 'Admin%')->first();
        if ($first_admin_role) {
            DB::table('model_has_roles')->insert([
                'role_id'    => $first_admin_role->id,
                'model_type' => 'App\User',
                'model_id'   => $admin_user->id,
            ]);
            echo "Assigned role {$first_admin_role->name} to user\n";
        }
    }
}

// 7. Clear permission cache
\Artisan::call('permission:cache-reset');
echo "\nPermission cache cleared.\n";

\Artisan::call('optimize:clear');
echo "App cache cleared.\n";

echo "\n=== Done! Delete this file now. ===\n";
