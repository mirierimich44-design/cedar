<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PosOrderLine extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pos_order_lines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'variation_id',
        'custom_product_name',
        'quantity',
        'unit_price',
    ];

    /**
     * Get the order that owns this line.
     */
    public function order()
    {
        return $this->belongsTo(PosOrder::class, 'order_id');
    }

    /**
     * Get the product for this line.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the variation for this line.
     */
    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
}
