<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    protected $table = 'hospital_beds';
    protected $guarded = ['id'];

    public function ward() {
        return $this->belongsTo(Ward::class);
    }
}
