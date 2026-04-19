<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class AncVisit extends Model
{
    protected $table = 'hospital_anc_visits';
    protected $guarded = ['id'];

    public function profile() {
        return $this->belongsTo(PregnancyProfile::class, 'pregnancy_profile_id');
    }

    public function doctor() {
        return $this->belongsTo(\App\User::class, 'doctor_id');
    }
}
