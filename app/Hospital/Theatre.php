<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class Theatre extends Model
{
    protected $table = 'hospital_theatres';
    protected $guarded = ['id'];
}
