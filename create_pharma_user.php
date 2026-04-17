<?php
/**
 * Create Pharma Admin User
 * Renames existing superadmin user to 'superadmin', then creates/updates
 * a pharmacy admin user with username 'admin'.
 * Upload to pharma folder root, run: php create_pharma_user.php
 * Delete after running.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== Create Pharma Admin User ===\n\n";

// 1. Find the pharma business
$business = DB::table('business')->first();
if (!$business) {
    echo "ERROR: No business found!\n";
    exit(1);
}
echo "Business: {$business->name} (ID: {$business->id})\n\n";

// 2. Rename the superadmin user's username from 'admin' to 'superadmin'
//    (superadmin users have business_id = 0 or null, or have the superadmin role)
$superadmin_user = DB::table('users')
    ->where('username', 'admin')
    ->where(function($q) use ($business) {
        $q->where('business_id', '!=', $business->id)
          ->orWhereNull('business_id')
          ->orWhere('business_id', 0);
    })
    ->first();

if ($superadmin_user) {
    DB::table('users')->where('id', $superadmin_user->id)->update(['username' => 'superadmin']);
    echo "Renamed superadmin user (ID: {$superadmin_user->id}, email: {$superadmin_user->email}) username: admin → superadmin\n\n";
} else {
    // Check if 'admin' username is taken by any user not in this business
    $any_admin = DB::table('users')->where('username', 'admin')->first();
    if ($any_admin && $any_admin->business_id != $business->id) {
        DB::table('users')->where('id', $any_admin->id)->update(['username' => 'superadmin']);
        echo "Renamed user (ID: {$any_admin->id}, email: {$any_admin->email}) username: admin → superadmin\n\n";
    } else {
        echo "No superadmin user with username 'admin' found — skipping rename.\n\n";
    }
}

// 3. Pharmacy admin user details
$email      = 'admin@zuedpharma.com';   // keep existing email
$password   = 'Pharma@2024';
$first_name = 'Admin';
$last_name  = '';
$username   = 'admin';

// 4. Find or create the pharmacy admin user
$existing = DB::table('users')
    ->where('business_id', $business->id)
    ->where(function($q) use ($email, $username) {
        $q->where('email', $email)->orWhere('username', $username);
    })
    ->first();

if ($existing) {
    DB::table('users')->where('id', $existing->id)->update([
        'username'   => $username,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'password'   => Hash::make($password),
    ]);
    echo "Updated pharmacy user: {$existing->email} (ID: {$existing->id})\n";
    $user_id = $existing->id;
} else {
    $user_id = DB::table('users')->insertGetId([
        'business_id' => $business->id,
        'first_name'  => $first_name,
        'last_name'   => $last_name,
        'username'    => $username,
        'email'       => $email,
        'password'    => Hash::make($password),
        'language'    => 'en',
        'allow_login' => 1,
        'status'      => 'active',
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);
    echo "Created pharmacy user: {$email} (ID: {$user_id})\n";
}

// 5. Find Admin role for this business
$admin_role = DB::table('roles')
    ->where('name', 'Admin#' . $business->id)
    ->where('guard_name', 'web')
    ->first();

if (!$admin_role) {
    $admin_role = DB::table('roles')
        ->where('name', 'like', 'Admin%')
        ->where('guard_name', 'web')
        ->first();
}

if (!$admin_role) {
    echo "ERROR: No Admin role found! Run fix_permissions.php first.\n";
    exit(1);
}

echo "Using role: {$admin_role->name} (ID: {$admin_role->id})\n";

// 6. Assign role to user (avoid duplicates)
$already_assigned = DB::table('model_has_roles')
    ->where('model_id', $user_id)
    ->where('role_id', $admin_role->id)
    ->exists();

if (!$already_assigned) {
    DB::table('model_has_roles')->insert([
        'role_id'    => $admin_role->id,
        'model_type' => 'App\User',
        'model_id'   => $user_id,
    ]);
    echo "Assigned role {$admin_role->name} to user\n";
} else {
    echo "Role already assigned\n";
}

// 7. Clear cache
\Artisan::call('permission:cache-reset');
\Artisan::call('optimize:clear');
echo "\nCache cleared.\n";

echo "\n=== Done! ===\n";
echo "Login URL:  https://pharma.apexpos.co.ke\n";
echo "Username:   admin\n";
echo "Password:   {$password}\n";
echo "\nDelete this file after use!\n";
