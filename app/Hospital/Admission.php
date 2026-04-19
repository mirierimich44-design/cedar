<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $table = 'hospital_admissions';
    protected $guarded = ['id'];

    public function patient() {
        return $this->belongsTo(\App\Contact::class, 'patient_id');
    }

    public function bed() {
        return $this->belongsTo(Bed::class, 'bed_id');
    }

    public function admitted_by_user() {
        return $this->belongsTo(\App\User::class, 'admitted_by');
    }

    public function dailyRecords() {
        return $this->hasMany(DailyRecord::class);
    }
}
