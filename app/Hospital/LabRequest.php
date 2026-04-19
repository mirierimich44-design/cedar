<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class LabRequest extends Model
{
    protected $table = 'hospital_lab_requests';
    protected $guarded = ['id'];

    public function patient() {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }

    public function doctor() {
        return $this->belongsTo(\App\User::class, 'doctor_id');
    }

    public function test() {
        return $this->belongsTo(LabTest::class, 'test_id');
    }
}
