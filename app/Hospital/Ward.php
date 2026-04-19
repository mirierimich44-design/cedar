<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $table = 'hospital_wards';
    protected $guarded = ['id'];

    public function beds() {
        return $this->hasMany(Bed::class);
    }
}
