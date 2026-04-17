<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DdaStockLog extends Model
{
    protected $table = 'dda_stock_log';
    protected $guarded = ['id'];

    public function ddaDrug()
    {
        return $this->belongsTo(DdaDrug::class, 'dda_drug_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
