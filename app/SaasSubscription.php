<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SaasSubscription extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'starts_at'      => 'datetime',
        'ends_at'        => 'datetime',
        'grace_ends_at'  => 'datetime',
        'total_amount'   => 'float',
        'discount_percent' => 'float',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function features()
    {
        return $this->belongsToMany(SaasFeature::class, 'saas_subscription_features', 'subscription_id', 'feature_id')
            ->withPivot('price_locked');
    }

    public function invoices()
    {
        return $this->hasMany(SaasInvoice::class, 'subscription_id');
    }

    public function hasFeature(string $key): bool
    {
        return $this->features->contains('key', $key);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at && $this->ends_at->isFuture();
    }

    public function isInGrace(): bool
    {
        return $this->status === 'grace' && $this->grace_ends_at && $this->grace_ends_at->isFuture();
    }

    public function isAccessible(): bool
    {
        return $this->isActive() || $this->isInGrace();
    }

    public function daysLeft(): int
    {
        if (!$this->ends_at) return 0;
        return max(0, now()->diffInDays($this->ends_at, false));
    }

    public static function activateForBusiness(int $businessId, array $featureIds, string $cycle, float $total, string $hostingType = 'cloud'): self
    {
        $months = match($cycle) {
            'monthly'   => 1,
            'quarterly' => 3,
            'yearly'    => 12,
            'once'      => 999 * 12,
            default     => 1,
        };

        $sub = static::create([
            'business_id'    => $businessId,
            'billing_cycle'  => $cycle,
            'hosting_type'   => $hostingType,
            'total_amount'   => $total,
            'status'         => 'active',
            'starts_at'      => now(),
            'ends_at'        => now()->addMonths($months),
            'grace_ends_at'  => now()->addMonths($months)->addDays(7),
        ]);

        $features = SaasFeature::whereIn('id', $featureIds)->get();
        foreach ($features as $feature) {
            $sub->features()->attach($feature->id, ['price_locked' => $feature->priceFor($cycle)]);
        }

        return $sub;
    }
}
