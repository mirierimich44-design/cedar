<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApprovalFlow extends Model
{
    protected $guarded = ['id'];

    public function steps()
    {
        return $this->hasMany(ApprovalFlowStep::class)->orderBy('order');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
}
