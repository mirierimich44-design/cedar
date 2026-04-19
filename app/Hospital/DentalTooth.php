<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class DentalTooth extends Model
{
    protected $table = 'hospital_dental_teeth';
    protected $guarded = ['id'];
}
