<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class AddPesapalPermissions extends Migration
{
    public function up()
    {
        $permissions = [
            ['name' => 'pesapal.manage_settings'],
            ['name' => 'pesapal.view_transactions'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission['name'],
                'guard_name' => 'web',
            ]);
        }
    }

    public function down()
    {
        Permission::whereIn('name', [
            'pesapal.manage_settings',
            'pesapal.view_transactions',
        ])->delete();
    }
}
