<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'followups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'location_id',
        'product_id',
        'variation_id',
        'customer_phone',
        'customer_name',
        'comment',
        'quantity',
        'status',
        'created_by',
    ];

    /**
     * Get the product for this follow-up.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the variation for this follow-up.
     */
    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }

    /**
     * Get the business location for this follow-up.
     */
    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    /**
     * Get the user who created this follow-up.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include follow-ups for a specific business.
     */
    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Status options for follow-ups.
     */
    public static function statusOptions()
    {
        return [
            'pending' => __('lang_v1.pending'),
            'contacted' => __('lang_v1.contacted'),
            'resolved' => __('lang_v1.resolved'),
        ];
    }
}
