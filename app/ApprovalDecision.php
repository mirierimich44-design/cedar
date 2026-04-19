<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApprovalDecision extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function approval()
    {
        return $this->belongsTo(Approval::class);
    }

    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
