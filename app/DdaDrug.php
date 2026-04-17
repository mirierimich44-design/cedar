<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DdaDrug extends Model
{
    protected $guarded = ['id'];

    public function products()
    {
        return $this->hasMany(Product::class, 'dda_drug_id');
    }

    public function dispenseLog()
    {
        return $this->hasMany(DdaDispenseLog::class, 'dda_drug_id');
    }

    public function stockLog()
    {
        return $this->hasMany(DdaStockLog::class, 'dda_drug_id');
    }

    public function destructionLog()
    {
        return $this->hasMany(DdaDestructionLog::class, 'dda_drug_id');
    }

    public static function classList()
    {
        return [
            'Opioid', 'Benzodiazepine', 'Barbiturate',
            'Dissociative', 'Sedative', 'Anticonvulsant', 'Precursor',
        ];
    }

    public static function scheduleList()
    {
        return ['I', 'II', 'III', 'IV'];
    }
}
