<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BusinessFeatureSetting extends Model
{
    protected $fillable = ['business_id', 'feature_key', 'is_enabled'];

    protected $casts = ['is_enabled' => 'boolean'];

    /**
     * Static list of ALL features with metadata.
     */
    public static function featureList(): array
    {
        return [
            // ── Custom Built Features ──────────────────────────────────────────
            'cloud_sync'        => ['label' => 'Cloud Sync',        'icon' => '🔄', 'category' => 'Custom Features', 'description' => 'Two-way sync between local devices and the cloud server'],
            'ai_analytics'      => ['label' => 'AI Analytics',      'icon' => '🤖', 'category' => 'Custom Features', 'description' => 'Auto-procurement suggestions and AI-powered business insights'],
            'approvals'         => ['label' => 'Approvals',         'icon' => '✅', 'category' => 'Custom Features', 'description' => 'Require approval workflow for sales, purchases and adjustments'],
            'dda_register'      => ['label' => 'DDA Register',      'icon' => '💊', 'category' => 'Custom Features', 'description' => 'Dangerous Drugs Act register and dispensing control'],
            'bi_dashboard'      => ['label' => 'BI Dashboard',      'icon' => '📊', 'category' => 'Custom Features', 'description' => 'Business intelligence dashboard with advanced analytics'],
            'hospital_module'   => ['label' => 'Hospital (HMS)',     'icon' => '🏥', 'category' => 'Custom Features', 'description' => 'Hospital management: patients, queues, wards, theatre'],
            'parcel_module'     => ['label' => 'Parcel & Courier',  'icon' => '📦', 'category' => 'Custom Features', 'description' => 'Parcel tracking, routes, checkpoints and delivery management'],
            'customer_orders'   => ['label' => 'Customer Orders',   'icon' => '🛍️', 'category' => 'Custom Features', 'description' => 'Online customer orders and order links management'],
            // ── nWidart Modules ────────────────────────────────────────────────
            'essentials'        => ['label' => 'Essentials',        'icon' => '⚡', 'category' => 'Modules', 'description' => 'Core POS essentials — required for basic operation'],
            'accounting'        => ['label' => 'Accounting',        'icon' => '📒', 'category' => 'Modules', 'description' => 'Full accounting: journals, ledgers, trial balance, P&L'],
            'crm'               => ['label' => 'CRM',               'icon' => '👥', 'category' => 'Modules', 'description' => 'Customer relationship management and lead tracking'],
            'manufacturing'     => ['label' => 'Manufacturing',     'icon' => '🏭', 'category' => 'Modules', 'description' => 'Production orders, BOM, and manufacturing management'],
            'repair'            => ['label' => 'Repair',            'icon' => '🔧', 'category' => 'Modules', 'description' => 'Repair jobs, job cards, and technician management'],
            'project'           => ['label' => 'Project',           'icon' => '📋', 'category' => 'Modules', 'description' => 'Project management and time tracking'],
            'asset_management'  => ['label' => 'Asset Management',  'icon' => '🏗️',  'category' => 'Modules', 'description' => 'Track, depreciate and manage business assets'],
            'ecommerce'         => ['label' => 'eCommerce',         'icon' => '🛒', 'category' => 'Modules', 'description' => 'Online store and ecommerce integration'],
            'woocommerce'       => ['label' => 'WooCommerce Sync',  'icon' => '🔗', 'category' => 'Modules', 'description' => 'Sync products and orders with WooCommerce'],
            'field_force'       => ['label' => 'Field Force',       'icon' => '🚗', 'category' => 'Modules', 'description' => 'Field sales agents, routes, and visit management'],
            'spreadsheet'       => ['label' => 'Spreadsheet',       'icon' => '📊', 'category' => 'Modules', 'description' => 'Advanced spreadsheet reports and data exports'],
            'gym'               => ['label' => 'Gym Management',    'icon' => '💪', 'category' => 'Modules', 'description' => 'Gym memberships, classes and attendance tracking'],
            'product_catalogue' => ['label' => 'Product Catalogue', 'icon' => '📚', 'category' => 'Modules', 'description' => 'Public product catalogue and price lists'],
            'connector'         => ['label' => 'Connector / API',   'icon' => '🔌', 'category' => 'Modules', 'description' => 'REST API and third-party integrations'],
            'cms'               => ['label' => 'CMS',               'icon' => '📝', 'category' => 'Modules', 'description' => 'Content management for the customer-facing portal'],
            'inbox_report'      => ['label' => 'Inbox / Reports',   'icon' => '📬', 'category' => 'Modules', 'description' => 'Scheduled reports delivered to inbox'],
            // ── Security ───────────────────────────────────────────────────────
            'ip_restriction'    => ['label' => 'IP Access Control', 'icon' => '🛡️', 'category' => 'Security', 'description' => 'Restrict system access to whitelisted IP addresses and network locations', 'default_enabled' => false],
        ];
    }

    /**
     * Check if a feature is enabled for a business (cached 5 min).
     * Defaults to TRUE if no row exists (backward compatible),
     * UNLESS the feature definition specifies 'default_enabled' => false.
     */
    public static function isEnabled(string $feature, int $businessId): bool
    {
        return Cache::remember("bfs_{$businessId}_{$feature}", 300, function () use ($feature, $businessId) {
            $row = self::where('business_id', $businessId)->where('feature_key', $feature)->first();
            if ($row !== null) {
                return (bool) $row->is_enabled;
            }
            // Use the feature's declared default, falling back to true for backward compatibility
            $list = self::featureList();
            return isset($list[$feature]['default_enabled']) ? (bool) $list[$feature]['default_enabled'] : true;
        });
    }

    public static function clearCache(int $businessId, string $feature): void
    {
        Cache::forget("bfs_{$businessId}_{$feature}");
    }
}
