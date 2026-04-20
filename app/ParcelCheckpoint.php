<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParcelCheckpoint extends Model
{
    protected $fillable = [
        'parcel_id', 'location', 'checkpoint_type', 'status_note',
        'scanned_by', 'vehicle_reg',
    ];

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }

    public function scannedByUser()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public static function typeLabels(): array
    {
        return [
            'booked'                 => 'Booked',
            'collected_from_sender'  => 'Collected from Sender',
            'dispatched'             => 'Dispatched',
            'arrived_at_depot'       => 'Arrived at Depot',
            'out_for_delivery'       => 'Out for Delivery',
            'delivered'              => 'Delivered',
            'delivery_attempted'     => 'Delivery Attempted',
            'returned_to_sender'     => 'Returned to Sender',
            'exception'              => 'Exception',
        ];
    }
}
