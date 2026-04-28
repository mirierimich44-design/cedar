<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        Permission::create(['name' => 'kcb_buni.manage_settings']);
        Permission::create(['name' => 'kcb_buni.view_transactions']);
    }

    public function down(): void
    {
        Permission::whereIn('name', [
            'kcb_buni.manage_settings',
            'kcb_buni.view_transactions'
        ])->delete();
    }
};
