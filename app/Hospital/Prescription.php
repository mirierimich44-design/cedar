<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $table = 'hospital_prescriptions';
    protected $guarded = ['id'];

    public function patient() {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }

    public function doctor() {
        return $this->belongsTo(\App\User::class, 'doctor_id');
    }

    public function variation() {
        return $this->belongsTo(\App\ProductVariation::class, 'variation_id');
    }
}
