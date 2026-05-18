<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserIpSetting extends Model
{
    protected $table = 'user_ip_settings';

    protected $fillable = ['user_id', 'bypass_ip_check', 'bypass_schedule'];

    protected $casts = ['bypass_ip_check' => 'boolean', 'bypass_schedule' => 'boolean'];
}
