<?php

namespace Modules\MadeToOrder\Models;

use Illuminate\Database\Eloquent\Model;

class MadeOrderItem extends Model
{
    protected $fillable = ['made_order_id','item_name','length','width','height','thickness','quantity','material_id','unit_price','total_price','notes'];

    public function order()
    {
        return $this->belongsTo(MadeOrder::class, 'made_order_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
