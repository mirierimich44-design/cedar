<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApprovalFlowStep extends Model
{
    protected $guarded = ['id'];

    public function flow()
    {
        return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedRole()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }
}
