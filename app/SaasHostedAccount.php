<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaasHostedAccount extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'next_renewal_date' => 'datetime',
        'hosting_fee_yearly' => 'float',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function daysToRenewal(): int
    {
        if (!$this->next_renewal_date) return 0;
        return max(0, now()->diffInDays($this->next_renewal_date, false));
    }
}
