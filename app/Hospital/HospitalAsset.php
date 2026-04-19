<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class HospitalAsset extends Model
{
    protected $table = 'hospital_assets';
    protected $guarded = ['id'];

    public function maintenance_logs() {
        return $this->hasMany(HospitalAssetMaintenance::class, 'asset_id');
    }
}
