<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaasBundle extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_popular' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function features()
    {
        return $this->belongsToMany(SaasFeature::class, 'saas_bundle_features', 'bundle_id', 'feature_id');
    }

    public function totalFor(string $cycle): float
    {
        return $this->features->sum(fn($f) => $f->priceFor($cycle));
    }
}
