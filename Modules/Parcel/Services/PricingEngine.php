<?php

namespace Modules\Parcel\Services;

use Modules\Parcel\Entities\Route;
use Modules\Parcel\Entities\PricingRule;

class PricingEngine
{
    /**
     * Calculate the parcel charge based on route and weight.
     *
     * @param Route $route
     * @param float $weightKg
     * @return float
     */
    public function calculate(Route $route, float $weightKg): float
    {
        // Find a specific pricing rule for the weight range
        $rule = PricingRule::where('route_id', $route->id)
            ->where('weight_min_kg', '<=', $weightKg)
            ->where('weight_max_kg', '>=', $weightKg)
            ->first();

        if ($rule) {
            // Priority: Flat fee + (per kg * weight)
            $charge = $rule->flat_fee + ($rule->price_per_kg * $weightKg);
            return max($route->min_price, $charge);
        }
        
        // Fallback: base route rate per kg
        $charge = $route->base_price_per_kg * $weightKg;
        return max($route->min_price, $charge);
    }
}
