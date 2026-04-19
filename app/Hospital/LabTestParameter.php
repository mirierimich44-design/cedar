<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class LabTestParameter extends Model
{
    protected $table = 'hospital_lab_test_parameters';
    protected $guarded = ['id'];
}
