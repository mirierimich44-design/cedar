<?php

namespace Modules\InventoryEmptying\Entities;

use Illuminate\Database\Eloquent\Model;
use App\BusinessLocation as Branch;
use App\Variation;
use Modules\InventoryEmptying\Entities\InventoryEmptying;

class InventoryEmptyingProducts extends Model
{
    protected $guarded = ['id']; 
    protected $table = 'emptying_inventory_products';

    public function branch()
    {
        return $this->belongsTo(Branch::class , "branch_id");
    }    

    public function inventory()
    {
        return $this->belongsTo(InventoryEmptying::class , "branch_id");
    }    
    
    public function transaction()
    {
        return $this->belongsTo('App\Transaction', 'transaction_id');
    }    

    public function variation()
    {
        return $this->belongsTo(Variation::class,'variation_id');
    }


}
