<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mpesa_transactions';

    /**
     * Status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:4',
    ];

    /**
     * Get the business.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    /**
     * Get the user who initiated the transaction.
     */
    public function initiatedBy()
    {
        return $this->belongsTo(\App\User::class, 'initiated_by');
    }

    /**
     * Get the related sale transaction.
     */
    public function saleTransaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }

    /**
     * Scope for pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for paid transactions.
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope for failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', [self::STATUS_FAILED, self::STATUS_CANCELLED, self::STATUS_EXPIRED]);
    }

    /**
     * Scope by business.
     */
    public function scopeByBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if transaction is paid.
     */
    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Mark as paid.
     */
    public function markAsPaid($receipt_number, $result_code = '0', $result_description = 'Success')
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'mpesa_receipt_number' => $receipt_number,
            'result_code' => $result_code,
            'result_description' => $result_description,
        ]);
    }

    /**
     * Mark as failed.
     */
    public function markAsFailed($result_code, $result_description)
    {
        $status = self::STATUS_FAILED;
        
        // Specific status based on result code
        if ($result_code == '1032') {
            $status = self::STATUS_CANCELLED;
        } elseif ($result_code == '1037') {
            $status = self::STATUS_EXPIRED;
        }

        $this->update([
            'status' => $status,
            'result_code' => $result_code,
            'result_description' => $result_description,
        ]);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_PAID => 'bg-success',
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_FAILED => 'bg-danger',
            self::STATUS_CANCELLED => 'bg-secondary',
            self::STATUS_EXPIRED => 'bg-info',
            default => 'bg-secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_PAID => __('lang_v1.mpesa_status_paid'),
            self::STATUS_PENDING => __('lang_v1.mpesa_status_pending'),
            self::STATUS_FAILED => __('lang_v1.mpesa_status_failed'),
            self::STATUS_CANCELLED => __('lang_v1.mpesa_status_cancelled'),
            self::STATUS_EXPIRED => __('lang_v1.mpesa_status_expired'),
            default => ucfirst($this->status),
        };
    }
}
