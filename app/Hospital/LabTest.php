<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    protected $table = 'hospital_lab_tests';
    protected $guarded = ['id'];
}
