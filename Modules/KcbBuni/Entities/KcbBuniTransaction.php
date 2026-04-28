<?php

namespace Modules\KcbBuni\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class KcbBuniTransaction extends Model
{
    protected $table = 'kcb_buni_transactions';

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED    = 'failed';
    const STATUS_REVERSED  = 'reversed';

    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function scopeByBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function markAsCompleted($confirmation_code, $desc = null)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'confirmation_code' => $confirmation_code,
            'status_description' => $desc ?: 'Transaction successful',
        ]);
    }

    public function markAsFailed($desc = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'status_description' => $desc ?: 'Transaction failed',
        ]);
    }

    public function getStatusLabel()
    {
        return ucfirst($this->status);
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'bg-success',
            self::STATUS_PENDING   => 'bg-warning',
            self::STATUS_FAILED    => 'bg-danger',
            self::STATUS_REVERSED  => 'bg-info',
            default                => 'bg-secondary',
        };
    }
}
