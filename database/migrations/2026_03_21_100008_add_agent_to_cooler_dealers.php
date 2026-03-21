<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddAgentToCoolerDealers extends Migration
{
    public function up()
    {
        // Add agent_id FK to cooler_dealers so each customer is owned by the agent who onboarded them
        Schema::table('cooler_dealers', function (Blueprint $table) {
            $table->unsignedInteger('agent_id')->nullable()->after('created_by');
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('set null');
        });

        // New agent-specific permissions
        $agentPermissions = [
            ['name' => 'cooler.agent.portal',   'guard_name' => 'web'], // access agent portal
            ['name' => 'cooler.order.create',   'guard_name' => 'web'], // place orders for customers
            ['name' => 'cooler.order.view',     'guard_name' => 'web'], // view own orders
        ];

        $timestamp = \Carbon::now()->toDateTimeString();
        foreach ($agentPermissions as $perm) {
            if (!Permission::where('name', $perm['name'])->exists()) {
                Permission::create(array_merge($perm, ['created_at' => $timestamp]));
            }
        }

        // Create 'Cooler Agent' role if it doesn't exist and assign permissions
        $role = Role::firstOrCreate(['name' => 'Cooler Agent', 'guard_name' => 'web']);

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
        $role->syncPermissions($perms);
    }

    public function down()
    {
        Schema::table('cooler_dealers', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn('agent_id');
        });

        Permission::whereIn('name', ['cooler.agent.portal', 'cooler.order.create', 'cooler.order.view'])->delete();

        $role = Role::where('name', 'Cooler Agent')->first();
        if ($role) $role->delete();
    }
}
