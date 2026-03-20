<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobRequisition extends Model
{
    protected $fillable = [
        'job_id',
        'product_id',
        'item_name',
        'sku',
        'unit',
        'quantity_requested',
        'quantity_approved',
        'quantity_issued',
        'unit_price',
        'total_cost',
        'status',
        'reason',
        'approved_at',
        'approved_by',
        'issued_at',
        'issued_by',
        'created_by',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:3',
        'quantity_approved' => 'decimal:3',
        'quantity_issued' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'approved_at' => 'datetime',
        'issued_at' => 'datetime',
    ];

    /**
     * Get the job for this requisition.
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the product for this requisition.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who approved this requisition.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who issued this requisition.
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the creator of this requisition.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include pending requisitions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved requisitions.
     */
    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'partially_issued', 'fully_issued']);
    }

    /**
     * Scope a query to only include rejected requisitions.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if requisition is fully issued.
     */
    public function isFullyIssued()
    {
        return $this->status === 'fully_issued' ||
            ($this->quantity_issued && $this->quantity_approved && $this->quantity_issued >= $this->quantity_approved);
    }

    /**
     * Check if requisition is partially issued.
     */
    public function isPartiallyIssued()
    {
        return $this->quantity_issued > 0 && !$this->isFullyIssued();
    }

    /**
     * Calculate remaining quantity to issue.
     */
    public function getRemainingQuantityAttribute()
    {
        if (!$this->quantity_approved) {
            return $this->quantity_requested;
        }
        return $this->quantity_approved - $this->quantity_issued;
    }

    /**
     * Approve the requisition.
     */
    public function approve($user_id, $approved_quantity = null)
    {
        $quantity = $approved_quantity ?? $this->quantity_requested;

        $this->update([
            'status' => 'approved',
            'quantity_approved' => $quantity,
            'approved_at' => now(),
            'approved_by' => $user_id,
        ]);

        // Log the approval
        JobLog::create([
            'job_id' => $this->job_id,
            'user_id' => $user_id,
            'log_type' => 'system',
            'title' => 'Requisition Approved',
            'content' => "Approved {$quantity} {$this->unit} of {$this->item_name}",
            'created_by' => $user_id,
        ]);

        return $this;
    }

    /**
     * Reject the requisition.
     */
    public function reject($user_id, $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'quantity_approved' => 0,
            'approved_at' => now(),
            'approved_by' => $user_id,
        ]);

        // Log the rejection
        JobLog::create([
            'job_id' => $this->job_id,
            'user_id' => $user_id,
            'log_type' => 'system',
            'title' => 'Requisition Rejected',
            'content' => $reason ?? "Requisition for {$this->item_name} was rejected",
            'created_by' => $user_id,
        ]);

        return $this;
    }

    /**
     * Issue items from requisition.
     */
    public function issue($user_id, $quantity)
    {
        if ($quantity > $this->remaining_quantity) {
            throw new \Exception('Cannot issue more than the approved quantity');
        }

        $newIssued = $this->quantity_issued + $quantity;
        $newStatus = 'partially_issued';

        if ($newIssued >= $this->quantity_approved) {
            $newStatus = 'fully_issued';
        }

        $this->update([
            'quantity_issued' => $newIssued,
            'status' => $newStatus,
            'issued_at' => $newStatus === 'fully_issued' ? now() : null,
            'issued_by' => $user_id,
        ]);

        // Log the issue
        JobLog::create([
            'job_id' => $this->job_id,
            'user_id' => $user_id,
            'log_type' => 'system',
            'title' => 'Items Issued',
            'content' => "Issued {$quantity} {$this->unit} of {$this->item_name}",
            'created_by' => $user_id,
        ]);

        return $this;
    }
}
