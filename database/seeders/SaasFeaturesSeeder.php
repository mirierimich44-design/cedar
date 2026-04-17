<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\SaasFeature;
use App\SaasBundle;

class SaasFeaturesSeeder extends Seeder
{
    public function run()
    {
        $features = [
            // CORE
            ['key' => 'core_pos',        'name' => 'Point of Sale (POS)',       'category' => 'core',          'icon' => 'fa-cash-register', 'is_required' => true,  'price_monthly' => 500,  'price_quarterly' => 1350, 'price_yearly' => 5000,  'price_once' => 15000, 'sort_order' => 1],
            ['key' => 'core_contacts',   'name' => 'Customer & Supplier Mgmt',  'category' => 'core',          'icon' => 'fa-users',          'is_required' => true,  'price_monthly' => 0,    'price_quarterly' => 0,    'price_yearly' => 0,     'price_once' => 0,     'sort_order' => 2],
            ['key' => 'core_users',      'name' => 'User & Role Management',    'category' => 'core',          'icon' => 'fa-user-shield',    'is_required' => true,  'price_monthly' => 0,    'price_quarterly' => 0,    'price_yearly' => 0,     'price_once' => 0,     'sort_order' => 3],

            // INVENTORY
            ['key' => 'inventory',       'name' => 'Inventory Management',      'category' => 'inventory',     'icon' => 'fa-boxes',          'is_required' => false, 'price_monthly' => 400,  'price_quarterly' => 1080, 'price_yearly' => 4000,  'price_once' => 12000, 'sort_order' => 10],
            ['key' => 'expiry_tracking', 'name' => 'Expiry Date Tracking',      'category' => 'inventory',     'icon' => 'fa-calendar-times', 'is_required' => false, 'price_monthly' => 200,  'price_quarterly' => 540,  'price_yearly' => 2000,  'price_once' => 6000,  'sort_order' => 11],
            ['key' => 'multi_location',  'name' => 'Multi-Location Support',    'category' => 'inventory',     'icon' => 'fa-map-marker-alt', 'is_required' => false, 'price_monthly' => 300,  'price_quarterly' => 810,  'price_yearly' => 3000,  'price_once' => 9000,  'sort_order' => 12],
            ['key' => 'purchases',       'name' => 'Purchase & Supplier Orders','category' => 'inventory',     'icon' => 'fa-truck',          'is_required' => false, 'price_monthly' => 300,  'price_quarterly' => 810,  'price_yearly' => 3000,  'price_once' => 9000,  'sort_order' => 13],

            // PHARMACY / DDA
            ['key' => 'dda_module',      'name' => 'DDA Drug Control',          'category' => 'pharmacy',      'icon' => 'fa-pills',          'is_required' => false, 'price_monthly' => 800,  'price_quarterly' => 2160, 'price_yearly' => 8000,  'price_once' => 20000, 'sort_order' => 20],
            ['key' => 'prescriptions',   'name' => 'Prescription Management',   'category' => 'pharmacy',      'icon' => 'fa-file-medical',   'is_required' => false, 'price_monthly' => 600,  'price_quarterly' => 1620, 'price_yearly' => 6000,  'price_once' => 18000, 'sort_order' => 21],
            ['key' => 'pharmacy_reports','name' => 'Pharmacy Compliance Reports','category' => 'pharmacy',     'icon' => 'fa-shield-alt',     'is_required' => false, 'price_monthly' => 300,  'price_quarterly' => 810,  'price_yearly' => 3000,  'price_once' => 9000,  'sort_order' => 22],

            // COMPLIANCE
            ['key' => 'etims',           'name' => 'KRA eTIMS Integration',     'category' => 'compliance',    'icon' => 'fa-file-invoice',   'is_required' => false, 'price_monthly' => 500,  'price_quarterly' => 1350, 'price_yearly' => 5000,  'price_once' => 15000, 'sort_order' => 25],

            // REPORTING
            ['key' => 'reports_basic',   'name' => 'Sales & Financial Reports', 'category' => 'reporting',     'icon' => 'fa-chart-bar',      'is_required' => false, 'price_monthly' => 300,  'price_quarterly' => 810,  'price_yearly' => 3000,  'price_once' => 9000,  'sort_order' => 30],
            ['key' => 'reports_advanced','name' => 'Advanced Analytics',        'category' => 'reporting',     'icon' => 'fa-chart-line',     'is_required' => false, 'price_monthly' => 400,  'price_quarterly' => 1080, 'price_yearly' => 4000,  'price_once' => 12000, 'sort_order' => 31],

            // COMMUNICATION
            ['key' => 'sms',             'name' => 'SMS Notifications',         'category' => 'communication', 'icon' => 'fa-sms',            'is_required' => false, 'price_monthly' => 200,  'price_quarterly' => 540,  'price_yearly' => 2000,  'price_once' => 6000,  'sort_order' => 40],
            ['key' => 'email_notif',     'name' => 'Email Notifications',       'category' => 'communication', 'icon' => 'fa-envelope',       'is_required' => false, 'price_monthly' => 100,  'price_quarterly' => 270,  'price_yearly' => 1000,  'price_once' => 3000,  'sort_order' => 41],

            // RESTAURANT
            ['key' => 'restaurant',      'name' => 'Restaurant Module',         'category' => 'restaurant',    'icon' => 'fa-utensils',       'is_required' => false, 'price_monthly' => 500,  'price_quarterly' => 1350, 'price_yearly' => 5000,  'price_once' => 14000, 'sort_order' => 50],

            // SERVICE / SETUP
            ['key' => 'setup_cost',      'name' => 'One-time Setup & Training', 'category' => 'service',       'icon' => 'fa-tools',          'is_required' => false, 'price_monthly' => 0,    'price_quarterly' => 0,    'price_yearly' => 0,     'price_once' => 5000,  'sort_order' => 60],
        ];

        foreach ($features as $f) {
            SaasFeature::updateOrCreate(['key' => $f['key']], $f);
        }

        // Seed bundles
        $basicIds     = SaasFeature::whereIn('key', ['core_pos', 'core_contacts', 'core_users', 'inventory', 'reports_basic', 'setup_cost'])->pluck('id');
        $pharmacyIds  = SaasFeature::whereIn('key', ['core_pos', 'core_contacts', 'core_users', 'inventory', 'expiry_tracking', 'dda_module', 'prescriptions', 'pharmacy_reports', 'sms', 'reports_basic', 'etims', 'setup_cost'])->pluck('id');
        $retailIds    = SaasFeature::whereIn('key', ['core_pos', 'core_contacts', 'core_users', 'inventory', 'expiry_tracking', 'purchases', 'multi_location', 'reports_basic', 'reports_advanced', 'sms', 'etims', 'setup_cost'])->pluck('id');

        $bundles = [
            ['name' => 'Basic Retail',     'slug' => 'basic-retail',    'description' => 'Perfect for small shops and kiosks.',             'color' => '#0369a1', 'is_popular' => false, 'sort_order' => 1, 'features' => $basicIds],
            ['name' => 'Pharmacy Suite',   'slug' => 'pharmacy-suite',  'description' => 'Full DDA compliance and prescription management.', 'color' => '#0f766e', 'is_popular' => true,  'sort_order' => 2, 'features' => $pharmacyIds],
            ['name' => 'Retail Pro',       'slug' => 'retail-pro',      'description' => 'Multi-location retail with full analytics.',       'color' => '#7c3aed', 'is_popular' => false, 'sort_order' => 3, 'features' => $retailIds],
        ];

        foreach ($bundles as $b) {
            $featureIds = $b['features'];
            unset($b['features']);
            $bundle = SaasBundle::updateOrCreate(['slug' => $b['slug']], $b);
            $bundle->features()->sync($featureIds);
        }
    }
}
