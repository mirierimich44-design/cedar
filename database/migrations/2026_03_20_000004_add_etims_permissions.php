<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class AddEtimsPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
            ['name' => 'access_etims_report', 'guard_name' => 'web'],
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
        Permission::where('name', 'access_etims_report')->delete();
    }
}
