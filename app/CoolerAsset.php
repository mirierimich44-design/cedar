<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoolerAsset extends Model
{
    use SoftDeletes;

    protected $table = 'cooler_assets';

    protected $fillable = [
        'business_id', 'asset_type', 'asset_number', 'serial_number', 'cooler_tag',
        'status', 'current_dealer_id', 'deployment_date', 'replacement_value', 'notes', 'created_by',
    ];

    protected $casts = [
        'deployment_date' => 'date',
        'replacement_value' => 'decimal:2',
    ];

    public static $statuses = ['available', 'deployed', 'under_maintenance', 'retrieved'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function currentDealer()
    {
        return $this->belongsTo(CoolerDealer::class, 'current_dealer_id');
    }

    public function agreements()
    {
        return $this->hasMany(CoolerAgreement::class, 'cooler_id');
    }

    public function activeAgreement()
    {
        return $this->hasOne(CoolerAgreement::class, 'cooler_id')->where('status', 'active');
    }

    public function retrievals()
    {
        return $this->hasMany(CoolerRetrieval::class, 'cooler_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeDeployed($query)
    {
        return $query->where('status', 'deployed');
    }

    public function getStatusBadgeAttribute()
    {
        $classes = [
            'available'        => 'success',
            'deployed'         => 'primary',
            'under_maintenance'=> 'warning',
            'retrieved'        => 'default',
        ];
        $label = ucwords(str_replace('_', ' ', $this->status));
        $class = $classes[$this->status] ?? 'default';
        return "<span class=\"label label-{$class}\">{$label}</span>";
    }
}
