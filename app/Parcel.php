<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id', 'location_id', 'waybill_number', 'route_id',
        'from_town', 'to_town',
        'sender_name', 'sender_phone', 'sender_id_number', 'sender_town',
        'receiver_name', 'receiver_phone', 'receiver_id_number', 'receiver_town', 'receiver_address',
        'parcel_type', 'parcel_description', 'weight_kg', 'declared_value', 'pieces', 'dimensions',
        'service_type', 'pickup_type', 'delivery_type',
        'freight_charge', 'insurance_charge', 'pickup_charge', 'delivery_charge',
        'total_amount', 'paid_amount', 'payment_method', 'payment_by', 'mpesa_code', 'payment_status',
        'status', 'created_by', 'driver_id', 'vehicle_reg',
        'expected_delivery_date', 'actual_delivery_date', 'delivered_to', 'delivery_proof',
        'notes', 'special_instructions',
    ];

    protected $dates = ['expected_delivery_date', 'actual_delivery_date', 'deleted_at'];

    // ── Relationships ────────────────────────────────────────────────────────

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function route()
    {
        return $this->belongsTo(ParcelRoute::class, 'route_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function checkpoints()
    {
        return $this->hasMany(ParcelCheckpoint::class)->orderBy('created_at', 'desc');
    }

    // ── Status helpers ───────────────────────────────────────────────────────

    public static function statusList(): array
    {
        return [
            'booked'            => 'Booked',
            'collected'         => 'Collected from Sender',
            'in_transit'        => 'In Transit',
            'at_depot'          => 'At Depot',
            'out_for_delivery'  => 'Out for Delivery',
            'delivered'         => 'Delivered',
            'returned'          => 'Returned',
            'cancelled'         => 'Cancelled',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'booked'            => 'info',
            'collected'         => 'primary',
            'in_transit'        => 'warning',
            'at_depot'          => 'default',
            'out_for_delivery'  => 'success',
            'delivered'         => 'success',
            'returned'          => 'danger',
            'cancelled'         => 'danger',
        ];
    }

    public function getStatusBadgeAttribute(): string
    {
        $colors = self::statusColors();
        $labels = self::statusList();
        $color = $colors[$this->status] ?? 'default';
        $label = $labels[$this->status] ?? $this->status;
        return "<span class=\"label label-{$color}\">{$label}</span>";
    }

    public static function parcelTypes(): array
    {
        return [
            'document'    => 'Document',
            'package'     => 'Package',
            'fragile'     => 'Fragile',
            'perishable'  => 'Perishable',
            'electronics' => 'Electronics',
            'clothing'    => 'Clothing',
            'other'       => 'Other',
        ];
    }

    // ── Waybill number generator ─────────────────────────────────────────────

    public static function generateWaybill(int $business_id): string
    {
        $prefix = 'WB';
        $date   = now()->format('Ymd');
        $last   = self::where('business_id', $business_id)
                      ->whereDate('created_at', today())
                      ->max('id');
        $seq = str_pad(($last ? ($last + 1) : 1), 4, '0', STR_PAD_LEFT);
        return "{$prefix}-{$date}-{$seq}";
    }
}
