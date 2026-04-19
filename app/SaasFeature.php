<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaasFeature extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
        'price_monthly'   => 'float',
        'price_quarterly' => 'float',
        'price_yearly'    => 'float',
        'price_once'      => 'float',
        'applicable_to'   => 'array',
    ];

    public function bundles()
    {
        return $this->belongsToMany(SaasBundle::class, 'saas_bundle_features', 'feature_id', 'bundle_id');
    }

    public function priceFor(string $cycle): float
    {
        return match($cycle) {
            'monthly'   => $this->price_monthly,
            'quarterly' => $this->price_quarterly,
            'yearly'    => $this->price_yearly,
            'once'      => $this->price_once,
            default     => $this->price_monthly,
        };
    }

    public static function activeByCategory(): \Illuminate\Support\Collection
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
    }
}
