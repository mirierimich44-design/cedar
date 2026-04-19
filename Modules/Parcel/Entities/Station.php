<?php

namespace Modules\Parcel\Entities;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $table = 'parcel_stations';
    protected $guarded = ['id'];

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    public function agent()
    {
        return $this->belongsTo(\App\User::class, 'agent_user_id');
    }
}
