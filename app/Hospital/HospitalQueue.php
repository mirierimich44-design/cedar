<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class HospitalQueue extends Model
{
    protected $table = 'hospital_queue';
    protected $guarded = ['id'];

    public function patient() {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }

    public function assigned_user() {
        return $this->belongsTo(\App\User::class, 'assigned_to');
    }
}
