<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class AddNewFeaturePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
            // Separate report permissions
            ['name' => 'customer_report.view', 'guard_name' => 'web'],
            ['name' => 'supplier_report.view', 'guard_name' => 'web'],
            
            // Orders permissions
            ['name' => 'orders.view', 'guard_name' => 'web'],
            ['name' => 'orders.create', 'guard_name' => 'web'],
            ['name' => 'orders.update', 'guard_name' => 'web'],
            ['name' => 'orders.delete', 'guard_name' => 'web'],
            
            // Follow-ups permissions
            ['name' => 'followups.view', 'guard_name' => 'web'],
            ['name' => 'followups.create', 'guard_name' => 'web'],
            ['name' => 'followups.update', 'guard_name' => 'web'],
            ['name' => 'followups.delete', 'guard_name' => 'web'],
        ];

        $time_stamp = \Carbon::now()->toDateTimeString();
        
        foreach ($permissions as $permission) {
            // Check if permission already exists
            $exists = Permission::where('name', $permission['name'])->exists();
            if (!$exists) {
                Permission::create([
                    'name' => $permission['name'],
                    'guard_name' => $permission['guard_name'],
                    'created_at' => $time_stamp,
                ]);
            }
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
            'customer_report.view',
            'supplier_report.view',
            'orders.view',
            'orders.create',
            'orders.update',
            'orders.delete',
            'followups.view',
            'followups.create',
            'followups.update',
            'followups.delete',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
}
