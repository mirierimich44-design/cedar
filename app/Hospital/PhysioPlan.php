<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class PhysioPlan extends Model
{
    protected $table = 'hospital_physio_plans';
    protected $guarded = ['id'];

    public function patient() {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }

    public function doctor() {
        return $this->belongsTo(\App\User::class, 'doctor_id');
    }

    public function sessions() {
        return $this->hasMany(PhysioSession::class, 'plan_id');
    }
}
