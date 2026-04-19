<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class NursingNote extends Model
{
    protected $table = 'hospital_nursing_notes';
    protected $guarded = ['id'];

    public function admission() {
        return $this->belongsTo(Admission::class);
    }

    public function nurse() {
        return $this->belongsTo(\App\User::class, 'nurse_id');
    }
}
