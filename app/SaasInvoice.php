<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaasInvoice extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'amount'  => 'float',
        'paid_at' => 'datetime',
        'due_at'  => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function subscription()
    {
        return $this->belongsTo(SaasSubscription::class, 'subscription_id');
    }

    public static function generateNumber(): string
    {
        $last = static::latest()->first();
        $num  = $last ? ((int) substr($last->invoice_no, 4)) + 1 : 1;
        return 'INV-' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
}
