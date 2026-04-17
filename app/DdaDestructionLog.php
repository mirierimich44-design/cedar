<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DdaDestructionLog extends Model
{
    protected $table = 'dda_destruction_log';
    protected $guarded = ['id'];

    protected $casts = ['destruction_date' => 'date'];

    public function ddaDrug()
    {
        return $this->belongsTo(DdaDrug::class, 'dda_drug_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
