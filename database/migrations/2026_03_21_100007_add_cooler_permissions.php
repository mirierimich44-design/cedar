<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class AddCoolerPermissions extends Migration
{
    public function up()
    {
        $permissions = [
            // Assets
            ['name' => 'cooler.asset.view',   'guard_name' => 'web'],
            ['name' => 'cooler.asset.create', 'guard_name' => 'web'],
            ['name' => 'cooler.asset.update', 'guard_name' => 'web'],
            ['name' => 'cooler.asset.delete', 'guard_name' => 'web'],
            // Dealers
            ['name' => 'cooler.dealer.view',       'guard_name' => 'web'],
            ['name' => 'cooler.dealer.create',     'guard_name' => 'web'],
            ['name' => 'cooler.dealer.update',     'guard_name' => 'web'],
            ['name' => 'cooler.dealer.delete',     'guard_name' => 'web'],
            ['name' => 'cooler.dealer.verify_docs','guard_name' => 'web'],
            // Agreements
            ['name' => 'cooler.agreement.view',   'guard_name' => 'web'],
            ['name' => 'cooler.agreement.create', 'guard_name' => 'web'],
            ['name' => 'cooler.agreement.sign',   'guard_name' => 'web'],
            ['name' => 'cooler.agreement.terminate','guard_name' => 'web'],
            // Retrievals
            ['name' => 'cooler.retrieval.view',     'guard_name' => 'web'],
            ['name' => 'cooler.retrieval.initiate', 'guard_name' => 'web'],
            ['name' => 'cooler.retrieval.execute',  'guard_name' => 'web'],
            // Documents
            ['name' => 'cooler.document.view',     'guard_name' => 'web'],
            ['name' => 'cooler.document.download', 'guard_name' => 'web'],
            ['name' => 'cooler.document.delete',   'guard_name' => 'web'],
            // Compliance & Reports
            ['name' => 'cooler.compliance.view', 'guard_name' => 'web'],
            ['name' => 'cooler.report.view',     'guard_name' => 'web'],
        ];

        $timestamp = \Carbon::now()->toDateTimeString();

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission['name'])->exists()) {
                Permission::create(array_merge($permission, ['created_at' => $timestamp]));
            }
        }
    }

    public function down()
    {
        $names = [
            'cooler.asset.view', 'cooler.asset.create', 'cooler.asset.update', 'cooler.asset.delete',
            'cooler.dealer.view', 'cooler.dealer.create', 'cooler.dealer.update', 'cooler.dealer.delete', 'cooler.dealer.verify_docs',
            'cooler.agreement.view', 'cooler.agreement.create', 'cooler.agreement.sign', 'cooler.agreement.terminate',
            'cooler.retrieval.view', 'cooler.retrieval.initiate', 'cooler.retrieval.execute',
            'cooler.document.view', 'cooler.document.download', 'cooler.document.delete',
            'cooler.compliance.view', 'cooler.report.view',
        ];

        Permission::whereIn('name', $names)->delete();
    }
}
