<?php

namespace Modules\Parcel\Entities;

use Illuminate\Database\Eloquent\Model;

class ParcelStatusLog extends Model
{
    protected $table = 'parcel_status_logs';
    protected $guarded = ['id'];

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function updated_by()
    {
        return $this->belongsTo(\App\User::class, 'updated_by_user_id');
    }
}
