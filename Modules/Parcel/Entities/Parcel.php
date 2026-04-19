<?php

namespace Modules\Parcel\Entities;

use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    protected $table = 'parcels';
    protected $guarded = ['id'];

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    public function origin()
    {
        return $this->belongsTo(Station::class, 'origin_station_id');
    }

    public function destination()
    {
        return $this->belongsTo(Station::class, 'destination_station_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function status_logs()
    {
        return $this->hasMany(ParcelStatusLog::class, 'parcel_id');
    }

    public function booked_by()
    {
        return $this->belongsTo(\App\User::class, 'booked_by_user_id');
    }
}
