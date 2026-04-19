<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class PatientDetail extends Model
{
    protected $table = 'patient_details';
    protected $guarded = ['id'];

    public function contact() {
        return $this->belongsTo(\App\Contact::class);
    }
}
