<?php
/**
 * setup_users.php
 * Creates Superadmin + Admin#1 user for a fresh Yaffa / ApexPOS install.
 *
 * Run from project root AFTER migrations and passport:install:
 *   php setup_users.php
 */

require __DIR__ . '/vendor/autoload.php';

$app    = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// ── CONFIG — change these before running ─────────────────────────────────

$users = [
    'superadmin' => [
        'salutation'  => 'Mr.',
        'first_name'  => 'Super',
        'last_name'   => 'Admin',
        'username'    => 'admin',
        'email'       => 'admin@yaffa.test',
        'password'    => 'Admin@1234',
        'superadmin'  => true,        // gets 'superadmin' permission
    ],
    'admin1' => [
        'salutation'  => 'Mr.',
        'first_name'  => 'Admin',
        'last_name'   => 'One',
        'username'    => 'admin1',
        'email'       => 'admin1@yaffa.test',
        'password'    => 'Admin#1234',
        'superadmin'  => false,
    ],
];

// ─────────────────────────────────────────────────────────────────────────

echo "\n=== Yaffa User Setup ===\n\n";

// 1. Seed permissions if empty
if (DB::table('permissions')->count() === 0) {
    echo "Permissions table empty — running PermissionsTableSeeder...\n";
    (new \Database\Seeders\PermissionsTableSeeder())->run();
    echo "Permissions seeded.\n\n";
}

// 2. Resolve business_id
$business = DB::table('business')->first();
if (! $business) {
    echo "ERROR: No business found. Run migrations and seed a business first.\n";
    exit(1);
}
$business_id = $business->id;
echo "Using business_id: {$business_id} ({$business->name})\n\n";

// 3. Ensure Admin role exists with all permissions
$role_name = 'Admin#' . $business_id;
$role = Role::firstOrCreate(
    ['name' => $role_name, 'guard_name' => 'web'],
    ['business_id' => $business_id]
);
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
$all_permissions = Permission::all();
$role->syncPermissions($all_permissions);
echo "Role '{$role_name}' has {$all_permissions->count()} permissions.\n\n";

// 4. Create / update each user
foreach ($users as $key => $cfg) {
    echo "--- {$key} ---\n";

    $existing = User::withTrashed()->where('username', $cfg['username'])->first();

    if ($existing) {
        echo "  User '{$cfg['username']}' exists — updating password and restoring.\n";
        $existing->password   = Hash::make($cfg['password']);
        $existing->deleted_at = null;
        $existing->save();
        $user = $existing;
    } else {
        $user = User::create([
            'salutation'  => $cfg['salutation'],
            'first_name'  => $cfg['first_name'],
            'last_name'   => $cfg['last_name'],
            'username'    => $cfg['username'],
            'email'       => $cfg['email'],
            'password'    => Hash::make($cfg['password']),
            'language'    => 'en',
            'business_id' => $business_id,
            'is_active'   => 1,
        ]);
        echo "  Created user ID: {$user->id}\n";
    }

    // Assign Admin role
    $user->syncRoles([$role_name]);
    echo "  Role '{$role_name}' assigned.\n";

    // Superadmin permission
    if ($cfg['superadmin']) {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $perm = Permission::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $user->givePermissionTo($perm);
        echo "  'superadmin' permission granted.\n";
    }

    echo "\n";
}

// 5. Summary
echo "=== Done! ===\n\n";
echo str_pad('User', 12) . str_pad('Password', 16) . "Superadmin\n";
echo str_repeat('-', 40) . "\n";
foreach ($users as $cfg) {
    echo str_pad($cfg['username'], 12)
       . str_pad($cfg['password'], 16)
       . ($cfg['superadmin'] ? 'Yes' : 'No') . "\n";
}
echo "\nChange passwords after first login!\n\n";
