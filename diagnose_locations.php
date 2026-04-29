<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== BUSINESSES ===\n";
$businesses = DB::table('businesses')->select('id','name')->get();
foreach ($businesses as $b) {
    echo "  id={$b->id}  name={$b->name}\n";
}

echo "\n=== BUSINESS LOCATIONS ===\n";
$locs = DB::table('business_locations')
    ->select('id','name','business_id','is_active','deleted_at')
    ->get();
foreach ($locs as $l) {
    $del = $l->deleted_at ? "SOFT-DELETED({$l->deleted_at})" : 'ok';
    $active = $l->is_active ? 'ACTIVE' : 'INACTIVE';
    echo "  id={$l->id}  business_id={$l->business_id}  {$active}  {$del}  name={$l->name}\n";
}

echo "\n=== ADMIN USERS ===\n";
$users = DB::table('users')
    ->select('id','username','email','business_id')
    ->where(function($q){
        $q->where('username','like','%admin%')
          ->orWhere('email','like','%admin%');
    })
    ->get();
foreach ($users as $u) {
    echo "  id={$u->id}  business_id={$u->business_id}  username={$u->username}  email={$u->email}\n";
}

echo "\n=== ALL USERS (business_id) ===\n";
$allusers = DB::table('users')->select('id','username','business_id')->get();
foreach ($allusers as $u) {
    echo "  id={$u->id}  business_id={$u->business_id}  username={$u->username}\n";
}

echo "\n=== location.* PERMISSIONS ===\n";
$perms = DB::table('permissions')->where('name','like','location.%')->get();
foreach ($perms as $p) {
    echo "  id={$p->id}  name={$p->name}\n";
}

echo "\n=== USER PERMISSIONS (location.*) ===\n";
$userPerms = DB::table('model_has_permissions')
    ->join('permissions','permissions.id','=','model_has_permissions.permission_id')
    ->where('permissions.name','like','location.%')
    ->select('model_has_permissions.model_id as user_id','permissions.name')
    ->get();
foreach ($userPerms as $up) {
    echo "  user_id={$up->user_id}  permission={$up->name}\n";
}

echo "\n=== ROLE PERMISSIONS (location.*) ===\n";
$rolePerms = DB::table('role_has_permissions')
    ->join('permissions','permissions.id','=','role_has_permissions.permission_id')
    ->join('roles','roles.id','=','role_has_permissions.role_id')
    ->where('permissions.name','like','location.%')
    ->select('roles.name as role','permissions.name as permission')
    ->get();
foreach ($rolePerms as $rp) {
    echo "  role={$rp->role}  permission={$rp->permission}\n";
}

echo "\n=== USER ROLES ===\n";
$userRoles = DB::table('model_has_roles')
    ->join('roles','roles.id','=','model_has_roles.role_id')
    ->select('model_has_roles.model_id as user_id','roles.name as role')
    ->get();
foreach ($userRoles as $ur) {
    echo "  user_id={$ur->user_id}  role={$ur->role}\n";
}

echo "\n=== USER HAS access_all_locations? ===\n";
$allLocPerm = DB::table('permissions')->where('name','access_all_locations')->first();
if ($allLocPerm) {
    $holders = DB::table('model_has_permissions')
        ->where('permission_id', $allLocPerm->id)
        ->pluck('model_id');
    $roleHolders = DB::table('role_has_permissions')
        ->join('roles','roles.id','=','role_has_permissions.role_id')
        ->where('permission_id', $allLocPerm->id)
        ->pluck('roles.name');
    echo "  Users with direct perm: " . implode(', ', $holders->toArray() ?: ['none']) . "\n";
    echo "  Roles with perm: " . implode(', ', $roleHolders->toArray() ?: ['none']) . "\n";
} else {
    echo "  'access_all_locations' permission NOT found in DB!\n";
}

echo "\nDone.\n";
