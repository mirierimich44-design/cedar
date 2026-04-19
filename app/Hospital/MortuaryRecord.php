<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class MortuaryRecord extends Model
{
    protected $table = 'hospital_mortuary_records';
    protected $guarded = ['id'];

    public function patient() {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }
}
