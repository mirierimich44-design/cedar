<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpAccessLog extends Model
{
    protected $table = 'ip_access_logs';

    protected $fillable = [
        'user_id', 'business_id', 'business_location_id',
        'username_attempted', 'ip_address',
        'isp', 'country', 'city',
        'browser', 'os', 'device_type', 'outcome',
    ];

    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'business_location_id');
    }
}
