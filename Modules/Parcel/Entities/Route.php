<?php

namespace Modules\Parcel\Entities;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'parcel_routes';
    protected $guarded = ['id'];

    public function origin()
    {
        return $this->belongsTo(Station::class, 'origin_station_id');
    }

    public function destination()
    {
        return $this->belongsTo(Station::class, 'destination_station_id');
    }

    public function pricing_rules()
    {
        return $this->hasMany(PricingRule::class, 'route_id');
    }
}
