<?php

namespace Modules\Parcel\Entities;

use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    protected $table = 'parcel_pricing_rules';
    protected $guarded = ['id'];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
