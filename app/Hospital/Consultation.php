<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $table = 'hospital_consultations';
    protected $guarded = ['id'];

    protected $casts = [
        'vitals' => 'array'
    ];

    public function patient() {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }

    public function doctor() {
        return $this->belongsTo(\App\User::class, 'doctor_id');
    }

    public function appointment() {
        return $this->belongsTo(Appointment::class);
    }
}
