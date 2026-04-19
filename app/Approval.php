<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Approval extends Model
{
    protected $guarded = ['id'];

    // ── Relationships ──────────────────────────────────────────────

    public function approvable()
    {
        return $this->morphTo();
    }

    public function flow()
    {
        return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function decisions()
    {
        return $this->hasMany(ApprovalDecision::class)->orderBy('step_number');
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeApproved($q)  { return $q->where('status', 'approved'); }
    public function scopeRejected($q)  { return $q->where('status', 'rejected'); }

    // ── Helpers ────────────────────────────────────────────────────

    /**
     * Who needs to act on the current step?
     */
    public function currentApprovers()
    {
        if (!$this->flow) return collect();

        $step = $this->flow->steps()->where('order', $this->current_step)->first();
        if (!$step) return collect();

        if ($step->user_id) {
            return User::where('id', $step->user_id)->get();
        }
        if ($step->role_id) {
            return User::where('business_id', $this->business_id)
                ->whereHas('roles', fn($q) => $q->where('id', $step->role_id))
                ->get();
        }
        return collect();
    }

    /**
     * Can this user act on the current step?
     */
    public function canBeActedOnBy(User $user): bool
    {
        if ($this->status !== 'pending') return false;
        return $this->currentApprovers()->contains('id', $user->id);
    }

    public function totalSteps(): int
    {
        return $this->flow ? $this->flow->steps()->count() : 1;
    }

    public function progressPercent(): int
    {
        $total = $this->totalSteps();
        return $total > 0 ? (int)(($this->current_step - 1) / $total * 100) : 0;
    }
}
