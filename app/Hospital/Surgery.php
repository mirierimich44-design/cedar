<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class Surgery extends Model
{
    protected $table = 'hospital_surgeries';
    protected $guarded = ['id'];
}
