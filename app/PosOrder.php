<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PosOrder extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pos_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'location_id',
        'contact_id',
        'created_by',
        'ref_no',
        'status',
        'notes',
        // payment fields added for customer order links
        'total_amount',
        'payment_method',
        'payment_status',
        'mpesa_phone',
        'mpesa_receipt',
        'mpesa_transaction_id',
    ];

    /**
     * Get the order lines for this order.
     */
    public function orderLines()
    {
        return $this->hasMany(PosOrderLine::class, 'order_id');
    }

    /**
     * Get the business location for this order.
     */
    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    /**
     * Get the contact (customer) for this order.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /**
     * Get the user who created this order.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include orders for a specific business.
     */
    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('pos_orders.business_id', $business_id);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('pos_orders.status', $status);
    }
}
