<?php

namespace Modules\Pesapal\Entities;

use Illuminate\Database\Eloquent\Model;

class PesapalTransaction extends Model
{
    protected $table = 'pesapal_transactions';

    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED    = 'failed';
    const STATUS_REVERSED  = 'reversed';
    const STATUS_INVALID   = 'invalid';

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
        'amount'   => 'decimal:4',
    ];

    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    public function initiatedBy()
    {
        return $this->belongsTo(\App\User::class, 'initiated_by');
    }

    public function saleTransaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeByBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function markAsCompleted($confirmation_code, $payment_method = null, $payment_account = null, $description = null)
    {
        $this->update([
            'status'           => self::STATUS_COMPLETED,
            'confirmation_code' => $confirmation_code,
            'payment_method'   => $payment_method,
            'payment_account'  => $payment_account,
            'status_description' => $description ?? 'Payment completed',
        ]);
    }

    public function markAsFailed($status, $description = null)
    {
        $this->update([
            'status'             => in_array($status, [self::STATUS_FAILED, self::STATUS_REVERSED, self::STATUS_INVALID])
                                        ? $status : self::STATUS_FAILED,
            'status_description' => $description,
        ]);
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'bg-success',
            self::STATUS_PENDING   => 'bg-warning',
            self::STATUS_FAILED    => 'bg-danger',
            self::STATUS_REVERSED  => 'bg-info',
            self::STATUS_INVALID   => 'bg-secondary',
            default                => 'bg-secondary',
        };
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_PENDING   => 'Pending',
            self::STATUS_FAILED    => 'Failed',
            self::STATUS_REVERSED  => 'Reversed',
            self::STATUS_INVALID   => 'Invalid',
            default                => ucfirst($this->status),
        };
    }
}
