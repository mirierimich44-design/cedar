<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParcelRoute extends Model
{
    protected $fillable = [
        'business_id', 'from_town', 'to_town', 'distance_km',
        'base_price', 'price_per_kg', 'min_price', 'express_multiplier',
        'is_active', 'transit_days', 'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_price' => 'float',
        'price_per_kg' => 'float',
        'min_price' => 'float',
        'express_multiplier' => 'float',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function parcels()
    {
        return $this->hasMany(Parcel::class, 'route_id');
    }

    /**
     * Calculate shipping price for a given weight and service type
     */
    public function calculatePrice(float $weight_kg, string $service_type = 'standard'): float
    {
        $price = $this->base_price + ($this->price_per_kg * $weight_kg);
        if ($service_type === 'express') {
            $price = $price * $this->express_multiplier;
        }
        return max($price, $this->min_price);
    }

    public function getRouteNameAttribute(): string
    {
        return $this->from_town . ' → ' . $this->to_town;
    }

    public static function getDropdown(int $business_id): array
    {
        return self::where('business_id', $business_id)
            ->where('is_active', true)
            ->orderBy('from_town')
            ->get()
            ->mapWithKeys(fn($r) => [$r->id => $r->from_town . ' → ' . $r->to_town])
            ->toArray();
    }
}
