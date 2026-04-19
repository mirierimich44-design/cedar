<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Idempotent seeder for Hospital Management System permissions.
 * Safe to re-run — only adds missing rows.
 */
class HospitalPermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'hospital.view',
            'hospital.patient.view',
            'hospital.patient.create',
            'hospital.patient.update',
            'hospital.patient.delete',
            'hospital.appointment.view',
            'hospital.appointment.create',
            'hospital.appointment.update',
            'hospital.queue.manage',
            'hospital.consultation.create',
            'hospital.consultation.view',
            'hospital.lab.view',
            'hospital.lab.request',
            'hospital.lab.result',
            'hospital.ipd.view',
            'hospital.ipd.admit',
            'hospital.ipd.discharge',
            'hospital.pharmacy.view',
            'hospital.pharmacy.dispense',
            'hospital.billing.view',
            'hospital.billing.create',
            'hospital.reports.view',
            'hospital.assets.view',
            'hospital.assets.manage',
            'hospital.maternity.view',
            'hospital.maternity.manage',
            'hospital.dental.view',
            'hospital.dental.manage',
        ];

        $existing = \DB::table('permissions')->pluck('name')->toArray();
        foreach ($permissions as $name) {
            if (in_array($name, $existing)) continue;
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        $this->command->info('Hospital permissions seeded.');
    }
}
