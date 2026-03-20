<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MpesaC2bPayment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mpesa_c2b_payments';

    /**
     * Status constants.
     */
    const STATUS_RECEIVED = 'received';
    const STATUS_MATCHED = 'matched';
    const STATUS_USED = 'used';
    const STATUS_UNMATCHED = 'unmatched';

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
        'raw_response' => 'array',
        'amount' => 'decimal:4',
        'org_account_balance' => 'decimal:4',
        'trans_time' => 'datetime',
    ];

    /**
     * Get the business.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    /**
     * Scope for unmatched payments.
     */
    public function scopeUnmatched($query)
    {
        return $query->whereIn('status', [self::STATUS_RECEIVED, self::STATUS_UNMATCHED]);
    }

    /**
     * Scope for available payments (not used).
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', '!=', self::STATUS_USED);
    }

    /**
     * Scope by business.
     */
    public function scopeByBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    /**
     * Get customer full name.
     */
    public function getFullNameAttribute()
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', $parts) ?: 'N/A';
    }

    /**
     * Get formatted phone.
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = $this->msisdn;
        if (strlen($phone) === 12 && strpos($phone, '254') === 0) {
            return '+' . $phone;
        }
        return $phone;
    }

    /**
     * Match to a transaction.
     */
    public function matchTo($type, $id)
    {
        $this->update([
            'status' => self::STATUS_MATCHED,
            'matched_to_type' => $type,
            'matched_to_id' => $id,
        ]);
    }

    /**
     * Mark as used.
     */
    public function markAsUsed()
    {
        $this->update([
            'status' => self::STATUS_USED,
        ]);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_USED => 'bg-success',
            self::STATUS_MATCHED => 'bg-info',
            self::STATUS_RECEIVED => 'bg-warning',
            self::STATUS_UNMATCHED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_USED => __('lang_v1.mpesa_c2b_used'),
            self::STATUS_MATCHED => __('lang_v1.mpesa_c2b_matched'),
            self::STATUS_RECEIVED => __('lang_v1.mpesa_c2b_received'),
            self::STATUS_UNMATCHED => __('lang_v1.mpesa_c2b_unmatched'),
            default => ucfirst($this->status),
        };
    }

    /**
     * Create from Safaricom callback data.
     */
    public static function createFromCallback($business_id, array $data)
    {
        // Parse trans_time from YYYYMMDDHHmmss format
        $trans_time = null;
        if (!empty($data['TransTime'])) {
            try {
                $trans_time = Carbon::createFromFormat('YmdHis', $data['TransTime']);
            } catch (\Exception $e) {
                $trans_time = now();
            }
        }

        return self::create([
            'business_id' => $business_id,
            'transaction_type' => $data['TransactionType'] ?? null,
            'trans_id' => $data['TransID'],
            'trans_time' => $trans_time,
            'amount' => $data['TransAmount'] ?? 0,
            'business_shortcode' => $data['BusinessShortCode'] ?? null,
            'bill_ref_number' => $data['BillRefNumber'] ?? null,
            'org_account_balance' => $data['OrgAccountBalance'] ?? null,
            'msisdn' => $data['MSISDN'] ?? '',
            'first_name' => $data['FirstName'] ?? null,
            'middle_name' => $data['MiddleName'] ?? null,
            'last_name' => $data['LastName'] ?? null,
            'raw_response' => $data,
            'status' => self::STATUS_RECEIVED,
        ]);
    }
}
