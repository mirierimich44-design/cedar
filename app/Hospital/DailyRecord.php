<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class DailyRecord extends Model
{
    protected $table = 'hospital_daily_records';
    protected $guarded = ['id'];

    protected $casts = [
        'vitals' => 'array'
    ];

    public function admission() {
        return $this->belongsTo(Admission::class);
    }

    public function recorded_by_user() {
        return $this->belongsTo(\App\User::class, 'recorded_by');
    }
}
