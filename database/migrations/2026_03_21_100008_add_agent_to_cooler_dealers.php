<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddAgentToCoolerDealers extends Migration
{
    public function up()
    {
        // Add agent_id FK to cooler_dealers
        Schema::table('cooler_dealers', function (Blueprint $table) {
            $table->unsignedInteger('agent_id')->nullable()->after('created_by');
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('set null');
        });

        // New agent-specific permissions (global, no business_id)
        $agentPermissions = [
            ['name' => 'cooler.agent.portal', 'guard_name' => 'web'],
            ['name' => 'cooler.order.create', 'guard_name' => 'web'],
            ['name' => 'cooler.order.view',   'guard_name' => 'web'],
        ];

        $timestamp = \Carbon::now()->toDateTimeString();
        foreach ($agentPermissions as $perm) {
            if (!Permission::where('name', $perm['name'])->exists()) {
                Permission::create(array_merge($perm, ['created_at' => $timestamp]));
            }
        }

        $rolePermissions = [
            'cooler.agent.portal',
            'cooler.dealer.view',
            'cooler.dealer.create',
            'cooler.retrieval.view',
            'cooler.retrieval.initiate',
            'cooler.retrieval.execute',
            'cooler.document.view',
            'cooler.document.download',
            'cooler.order.create',
            'cooler.order.view',
        ];

        $perms = Permission::whereIn('name', $rolePermissions)->get();

        // Roles are scoped per business — create 'Cooler Agent#<id>' for every business
        $businesses = DB::table('business')->pluck('id');

        foreach ($businesses as $businessId) {
            $roleName = 'Cooler Agent#' . $businessId;
            $role = Role::firstOrCreate([
                'name'        => $roleName,
                'guard_name'  => 'web',
                'business_id' => $businessId,
            ]);
            $role->syncPermissions($perms);
        }
    }

    public function down()
    {
        Schema::table('cooler_dealers', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn('agent_id');
        });

        Permission::whereIn('name', ['cooler.agent.portal', 'cooler.order.create', 'cooler.order.view'])->delete();

        // Remove all Cooler Agent roles across all businesses
        Role::where('name', 'like', 'Cooler Agent#%')->delete();
    }
}
