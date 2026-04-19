<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class PregnancyProfile extends Model
{
    protected $table = 'hospital_pregnancy_profiles';
    protected $guarded = ['id'];

    public function patient() {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }

    public function visits() {
        return $this->hasMany(AncVisit::class, 'pregnancy_profile_id');
    }
}
