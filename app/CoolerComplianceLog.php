<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoolerComplianceLog extends Model
{
    protected $table = 'cooler_compliance_logs';

    protected $fillable = [
        'dealer_id', 'cooler_id', 'check_type', 'status', 'details', 'checked_at', 'checked_by',
    ];

    protected $casts = [
        'details'    => 'array',
        'checked_at' => 'datetime',
    ];

    public static $checkTypes = [
        'sales_volume'    => 'Sales Volume',
        'stock_level'     => 'Stock Level',
        'exclusivity'     => 'Exclusivity',
        'placement'       => 'Placement',
        'document_expiry' => 'Document Expiry',
    ];

    public function dealer()
    {
        return $this->belongsTo(CoolerDealer::class, 'dealer_id');
    }

    public function cooler()
    {
        return $this->belongsTo(CoolerAsset::class, 'cooler_id');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function scopeNonCompliant($query)
    {
        return $query->where('status', 'non_compliant');
    }

    public function getStatusBadgeAttribute(): string
    {
        $class = $this->status === 'compliant' ? 'success' : 'danger';
        $label = $this->status === 'compliant' ? 'Compliant' : 'Non-Compliant';
        return "<span class=\"label label-{$class}\">{$label}</span>";
    }
}
