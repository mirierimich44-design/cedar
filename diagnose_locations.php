<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== BUSINESSES (table: business) ===\n";
$businesses = DB::table('business')->select('id','name')->get();
foreach ($businesses as $b) {
    echo "  id={$b->id}  name={$b->name}\n";
}

echo "\n=== BUSINESS LOCATIONS ===\n";
$locs = DB::table('business_locations')
    ->select('id','name','business_id','is_active','deleted_at')
    ->get();
foreach ($locs as $l) {
    $del    = $l->deleted_at ? "SOFT-DELETED({$l->deleted_at})" : 'not-deleted';
    $active = $l->is_active ? 'ACTIVE' : '*** INACTIVE ***';
    echo "  id={$l->id}  business_id={$l->business_id}  {$active}  {$del}  name={$l->name}\n";
}

echo "\n=== ALL USERS ===\n";
$users = DB::table('users')->select('id','username','email','business_id')->get();
foreach ($users as $u) {
    echo "  id={$u->id}  business_id={$u->business_id}  username={$u->username}  email={$u->email}\n";
}

echo "\n=== location.* PERMISSIONS IN DB ===\n";
$perms = DB::table('permissions')->where('name','like','location.%')->get();
if ($perms->isEmpty()) {
    echo "  *** NONE FOUND — location.X permissions were never created! ***\n";
} else {
    foreach ($perms as $p) {
        echo "  id={$p->id}  name={$p->name}\n";
    }
}

echo "\n=== access_all_locations PERMISSION ===\n";
$allLoc = DB::table('permissions')->where('name','access_all_locations')->first();
if (!$allLoc) {
    echo "  *** NOT FOUND in permissions table! ***\n";
} else {
    echo "  Found: id={$allLoc->id}\n";

    $directUsers = DB::table('model_has_permissions')
        ->where('permission_id', $allLoc->id)
        ->pluck('model_id');
    echo "  Users with it directly: " . ($directUsers->isEmpty() ? 'none' : $directUsers->implode(', ')) . "\n";

    $rolesWithIt = DB::table('role_has_permissions')
        ->join('roles','roles.id','=','role_has_permissions.role_id')
        ->where('role_has_permissions.permission_id', $allLoc->id)
        ->pluck('roles.name');
    echo "  Roles with it: " . ($rolesWithIt->isEmpty() ? 'none' : $rolesWithIt->implode(', ')) . "\n";
}

echo "\n=== USER ROLES ===\n";
$userRoles = DB::table('model_has_roles')
    ->join('roles','roles.id','=','model_has_roles.role_id')
    ->select('model_has_roles.model_id as user_id','roles.name as role','roles.id as role_id')
    ->get();
foreach ($userRoles as $ur) {
    echo "  user_id={$ur->user_id}  role_id={$ur->role_id}  role={$ur->role}\n";
}

echo "\n=== ROLE HAS location.* permissions? ===\n";
$roleLocPerms = DB::table('role_has_permissions')
    ->join('permissions','permissions.id','=','role_has_permissions.permission_id')
    ->join('roles','roles.id','=','role_has_permissions.role_id')
    ->where('permissions.name','like','location.%')
    ->select('roles.name as role','permissions.name as permission')
    ->get();
if ($roleLocPerms->isEmpty()) {
    echo "  *** NO roles have any location.X permission ***\n";
} else {
    foreach ($roleLocPerms as $rp) {
        echo "  role={$rp->role}  permission={$rp->permission}\n";
    }
}

echo "\n=== USER HAS location.* permissions directly? ===\n";
$userLocPerms = DB::table('model_has_permissions')
    ->join('permissions','permissions.id','=','model_has_permissions.permission_id')
    ->where('permissions.name','like','location.%')
    ->select('model_has_permissions.model_id as user_id','permissions.name')
    ->get();
if ($userLocPerms->isEmpty()) {
    echo "  *** NO users have any location.X permission directly ***\n";
} else {
    foreach ($userLocPerms as $up) {
        echo "  user_id={$up->user_id}  permission={$up->name}\n";
    }
}

echo "\nDone.\n";
