<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class RadiographyTest extends Model
{
    protected $table = 'hospital_radiography_tests';
    protected $guarded = ['id'];

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }
}
