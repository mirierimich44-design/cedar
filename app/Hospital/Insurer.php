<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class Insurer extends Model
{
    protected $table = 'hospital_insurers';
    protected $guarded = ['id'];

    public function schemes() {
        return $this->hasMany(InsuranceScheme::class);
    }
}
