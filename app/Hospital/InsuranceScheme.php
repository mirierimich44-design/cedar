<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class InsuranceScheme extends Model
{
    protected $table = 'hospital_insurance_schemes';
    protected $guarded = ['id'];

    public function insurer() {
        return $this->belongsTo(Insurer::class);
    }
}
