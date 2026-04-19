<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class RadiographyRequest extends Model
{
    protected $table = 'hospital_radiography_requests';
    protected $guarded = ['id'];

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    public function patient()
    {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(\App\User::class, 'doctor_id');
    }

    public function test()
    {
        return $this->belongsTo(RadiographyTest::class, 'test_id');
    }

    public function radiologist()
    {
        return $this->belongsTo(\App\User::class, 'radiologist_id');
    }
}
