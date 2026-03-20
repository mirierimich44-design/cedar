<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class AddViewProfitPermission extends Migration
{
    public function up()
    {
        // Clear Spatie's cache — its internal duplicate check reads from cache,
        // not the DB, causing false PermissionAlreadyExists exceptions.
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Use raw DB insert to bypass Spatie's overridden create() entirely
        if (!DB::table('permissions')->where('name', 'view_profit')->where('guard_name', 'web')->exists()) {
            DB::table('permissions')->insert([
                'name'       => 'view_profit',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Clear cache again so the role grant reads fresh data
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Grant this permission to Admin role by default
        $admin = \Spatie\Permission\Models\Role::where('name', 'Admin#1')->first();
        if ($admin && !$admin->hasPermissionTo('view_profit')) {
            $admin->givePermissionTo('view_profit');
        }
    }

    public function down()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        DB::table('permissions')->where('name', 'view_profit')->where('guard_name', 'web')->delete();
    }
}
