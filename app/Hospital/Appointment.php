<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'hospital_appointments';
    protected $guarded = ['id'];

    public function business() {
        return $this->belongsTo(\App\Business::class);
    }

    public function patient() {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }

    public function doctor() {
        return $this->belongsTo(\App\User::class, 'doctor_id');
    }

    public function creator() {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
