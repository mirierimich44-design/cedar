<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessSchedule extends Model
{
    protected $table = 'access_schedules';

    protected $fillable = ['role_id', 'day_of_week', 'start_time', 'end_time', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static $days = [
        0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday',
        3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday',
    ];
}
