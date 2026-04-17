<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DdaDispenseLog extends Model
{
    protected $table = 'dda_dispense_log';
    protected $guarded = ['id'];

    public function ddaDrug()
    {
        return $this->belongsTo(DdaDrug::class, 'dda_drug_id');
    }

    public function prescription()
    {
        return $this->belongsTo(DdaPrescription::class, 'dda_prescription_id');
    }

    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }
}
