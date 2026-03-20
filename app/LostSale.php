<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LostSale extends Model
{
    protected $table = 'lost_sales';

    protected $fillable = [
        'business_id',
        'location_id',
        'product_id',
        'variation_id',
        'product_name',
        'sku',
        'selling_price',
        'quantity',
        'notes',
        'created_by',
    ];

    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }
}
