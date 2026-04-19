<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class DentalProcedure extends Model
{
    protected $table = 'hospital_dental_procedures';
    protected $guarded = ['id'];

    public function patient() {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }

    public function doctor() {
        return $this->belongsTo(\App\User::class, 'doctor_id');
    }

    public function consultation() {
        return $this->belongsTo(Consultation::class);
    }
}
