<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class AddJobPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
            ['name' => 'job.view', 'guard_name' => 'web'],
            ['name' => 'job.create', 'guard_name' => 'web'],
            ['name' => 'job.update', 'guard_name' => 'web'],
            ['name' => 'job.delete', 'guard_name' => 'web'],
        ];

        $time_stamp = \Carbon::now()->toDateTimeString();
        
        foreach ($permissions as $permission) {
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
            'job.view',
            'job.create',
            'job.update',
            'job.delete',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
}
