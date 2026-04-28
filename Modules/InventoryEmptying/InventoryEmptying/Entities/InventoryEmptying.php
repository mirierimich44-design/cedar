<?php

namespace Modules\InventoryEmptying\Entities;

use Illuminate\Database\Eloquent\Model;

class InventoryEmptying extends Model
{
    protected $guarded = ['id']; 
    protected $table = 'inventories_empty';
    /**
     * user added.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }    
    public function product(){
        return $this->hasMany("Modules\InventoryEmptying\Entities\InventoryEmptyingProducts",'inventories_empty_id','id')
        ->distinct();
    }
}
