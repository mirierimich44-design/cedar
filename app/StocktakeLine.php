<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StocktakeLine extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stocktake_lines';

    /**
     * Get the transaction this line belongs to.
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class);
    }

    /**
     * Get the product for this line.
     */
    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }

    /**
     * Get the variation for this line.
     */
    public function variation()
    {
        return $this->belongsTo(\App\Variation::class);
    }
}
