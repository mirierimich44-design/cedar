<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class TheatreBooking extends Model
{
    protected $table = 'hospital_theatre_bookings';
    protected $guarded = ['id'];

    public function patient()
    {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }

    public function surgery()
    {
        return $this->belongsTo(Surgery::class, 'surgery_id');
    }

    public function theatre()
    {
        return $this->belongsTo(Theatre::class, 'theatre_id');
    }

    public function surgeon()
    {
        return $this->belongsTo(\App\User::class, 'surgeon_id');
    }

    public function anaesthetist()
    {
        return $this->belongsTo(\App\User::class, 'anaesthetist_id');
    }
}
