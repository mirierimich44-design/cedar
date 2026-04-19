<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class PhysioSession extends Model
{
    protected $table = 'hospital_physio_sessions';
    protected $guarded = ['id'];

    public function plan() {
        return $this->belongsTo(PhysioPlan::class, 'plan_id');
    }

    public function therapist() {
        return $this->belongsTo(\App\User::class, 'therapist_id');
    }
}
