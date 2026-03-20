<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobChecklist extends Model
{
    protected $fillable = [
        'job_id',
        'template_item_id',
        'title',
        'description',
        'sort_order',
        'is_completed',
        'completed_at',
        'completed_by',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the job for this checklist item.
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the template item this checklist was created from.
     */
    public function templateItem()
    {
        return $this->belongsTo(JobTemplateItem::class);
    }

    /**
     * Get the user who completed this item.
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Mark as completed.
     */
    public function markCompleted($user_id)
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by' => $user_id,
        ]);

        // Add log entry
        JobLog::create([
            'job_id' => $this->job_id,
            'user_id' => $user_id,
            'log_type' => 'status_change',
            'title' => 'Checklist Item Completed',
            'content' => "Completed: {$this->title}",
            'created_by' => $user_id,
        ]);

        // Update job status if all items completed
        $job = $this->job;
        $allCompleted = $job->checklists()->where('is_completed', false)->count() === 0;

        if ($allCompleted && $job->status === 'in_progress') {
            $job->update(['status' => 'completed']);
        }

        return $this;
    }
}
