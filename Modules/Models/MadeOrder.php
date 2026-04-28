<?php

namespace Modules\MadeToOrder\Models;

use Illuminate\Database\Eloquent\Model;

class MadeOrder extends Model
{
    protected $fillable = ['order_no','customer_id','order_date','total_cost','status','created_by'];

    public function items()
    {
        return $this->hasMany(MadeOrderItem::class, 'made_order_id');
    }
}
