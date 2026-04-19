<?php

namespace App\Hospital;

use Illuminate\Database\Eloquent\Model;

class HospitalAssetMaintenance extends Model
{
    protected $table = 'hospital_asset_maintenance';
    protected $guarded = ['id'];

    public function asset() {
        return $this->belongsTo(HospitalAsset::class, 'asset_id');
    }
}
