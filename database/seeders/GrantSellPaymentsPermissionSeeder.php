<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GrantSellPaymentsPermissionSeeder extends Seeder
{
    /**
     * Ensures the sell.payments permission exists and assigns it to all
     * existing Cashier roles so the Collect Payment feature is accessible.
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $perm = Permission::firstOrCreate(['name' => 'sell.payments', 'guard_name' => 'web']);

        $count = 0;
        Role::where('name', 'like', 'Cashier#%')->chunk(50, function ($roles) use ($perm, &$count) {
            foreach ($roles as $role) {
                if (! $role->hasPermissionTo($perm)) {
                    $role->givePermissionTo($perm);
                    $count++;
                }
            }
        });

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info("sell.payments granted to {$count} Cashier role(s).");
    }
}
