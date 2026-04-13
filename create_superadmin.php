<?php
/**
 * Superadmin creator script for UltimatePoS / Reenson
 * Run from project root: php create_superadmin.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// ── CONFIG ────────────────────────────────────────────────────────────────────
$username   = 'admin';
$email      = 'admin@zuedpharma.com';
$password   = 'Admin@1234';
$first_name = 'Super';
$last_name  = 'Admin';
// ─────────────────────────────────────────────────────────────────────────────

echo "Creating superadmin...\n";

// 1. Seed permissions if the table is empty
if (DB::table('permissions')->count() === 0) {
    echo "Permissions table empty — running PermissionsTableSeeder...\n";
    (new \Database\Seeders\PermissionsTableSeeder())->run();
    echo "Permissions seeded.\n";
}

// 2. Get business_id = 1 (first business)
$business_id = DB::table('business')->first()->id ?? 1;

// 3. Create or update user
$user = User::withTrashed()->where('username', $username)->first();
if ($user) {
    echo "User '{$username}' exists — updating password and restoring...\n";
    $user->password    = Hash::make($password);
    $user->deleted_at  = null;
    $user->save();
} else {
    $user = User::create([
        'salutation'   => 'Mr.',
        'first_name'   => $first_name,
        'last_name'    => $last_name,
        'username'     => $username,
        'email'        => $email,
        'password'     => Hash::make($password),
        'language'     => 'en',
        'business_id'  => $business_id,
        'is_active'    => 1,
    ]);
    echo "User created with ID: {$user->id}\n";
}

// 4. Ensure Admin role exists for this business and assign it
$role_name = 'Admin#' . $business_id;
$role = Role::firstOrCreate(['name' => $role_name, 'guard_name' => 'web']);

// Give role all permissions
$all_permissions = Permission::all();
$role->syncPermissions($all_permissions);
echo "Role '{$role_name}' has " . $all_permissions->count() . " permissions.\n";

// 5. Assign role to user
$user->syncRoles([$role_name]);
echo "Role '{$role_name}' assigned to user '{$username}'.\n";

// 6. Grant superadmin permission directly (create if missing, flush cache first)
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
$superadmin_perm = Permission::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
$user->givePermissionTo($superadmin_perm);
echo "Permission 'superadmin' granted.\n";

echo "\n✓ Done!\n";
echo "  URL:      your-site-url/login\n";
echo "  Username: {$username}\n";
echo "  Password: {$password}\n";
echo "\nChange the password after first login!\n";
