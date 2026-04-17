<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class AddDdaPermissions extends Migration
{
    public function up()
    {
        $permissions = [
            'dda.view',
            'dda.manage',
            'dda.dispense',
            'dda.prescriptions.view',
            'dda.prescriptions.upload',
            'dda.reports.view',
            'dda.destruction.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Grant all DDA permissions to any existing Admin roles
        $adminRoles = \Spatie\Permission\Models\Role::where('name', 'like', 'Admin#%')->get();
        foreach ($adminRoles as $role) {
            $role->givePermissionTo($permissions);
        }
    }

    public function down()
    {
        $permissions = [
            'dda.view', 'dda.manage', 'dda.dispense',
            'dda.prescriptions.view', 'dda.prescriptions.upload',
            'dda.reports.view', 'dda.destruction.manage',
        ];
        foreach ($permissions as $perm) {
            Permission::where('name', $perm)->delete();
        }
    }
}
