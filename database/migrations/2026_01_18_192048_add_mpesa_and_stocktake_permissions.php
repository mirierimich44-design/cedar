<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class AddMpesaAndStocktakePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
            ['name' => 'mpesa.access'],
            ['name' => 'mpesa.view_transactions'],
            ['name' => 'mpesa.manage_settings'],
            ['name' => 'stocktake.view'],
            ['name' => 'stocktake.manage'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'web'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissions = [
            'mpesa.access',
            'mpesa.view_transactions',
            'mpesa.manage_settings',
            'stocktake.view',
            'stocktake.manage',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
}
