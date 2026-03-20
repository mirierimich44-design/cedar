<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobNotification extends Model
{
    protected $fillable = [
        'job_id',
        'business_id',
        'user_id',
        'contact_email',
        'contact_phone',
        'notification_type',
        'title',
        'message',
        'channel',
        'is_read',
        'read_at',
        'is_sent',
        'sent_at',
        'error_message',
        'created_by',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_sent' => 'boolean',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the job for this notification.
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the business for this notification.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user for this notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the creator of this notification.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope a query to only include unsent notifications.
     */
    public function scopeUnsent($query)
    {
        return $query->where('is_sent', false);
    }

    /**
     * Scope a query to only include sent notifications.
     */
    public function scopeSent($query)
    {
        return $query->where('is_sent', true);
    }

    /**
     * Scope a query for a specific notification type.
     */
    public function scopeType($query, $type)
    {
        return $query->where('notification_type', $type);
    }

    /**
     * Mark as read.
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark as sent.
     */
    public function markAsSent()
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark with error.
     */
    public function markWithErrors($errorMessage)
    {
        $this->update([
            'is_sent' => false,
            'error_message' => $errorMessage,
        ]);

        return $this;
    }

    /**
     * Create a job assignment notification.
     */
    public static function createAssignment($job_id, $user_id, $job_title, $user_name)
    {
        return self::create([
            'job_id' => $job_id,
            'business_id' => \App\Job::find($job_id)->business_id,
            'user_id' => $user_id,
            'notification_type' => 'job_assigned',
            'title' => 'New Job Assigned',
            'message' => "You have been assigned to a new job: {$job_title}",
            'channel' => 'in_app',
            'created_by' => $user_id,
        ]);
    }

    /**
     * Create a job completion notification.
     */
    public static function createCompletion($job_id, $user_id, $job_title)
    {
        $job = \App\Job::find($job_id);

        return self::create([
            'job_id' => $job_id,
            'business_id' => $job->business_id,
            'user_id' => $job->created_by,
            'notification_type' => 'job_completed',
            'title' => 'Job Completed',
            'message' => "Job '{$job_title}' has been completed by {$job->assignedUser->name ?? 'a worker'}",
            'channel' => 'in_app',
            'created_by' => $user_id,
        ]);
    }

    /**
     * Create a requisition notification.
     */
    public static function createRequisition($job_id, $requisition_id, $status, $item_name)
    {
        $job = \App\Job::find($job_id);
        $title = $status === 'approved' ? 'Requisition Approved' : 'Requisition Rejected';
        $type = $status === 'approved' ? 'requisition_approved' : 'requisition_rejected';

        return self::create([
            'job_id' => $job_id,
            'business_id' => $job->business_id,
            'user_id' => $job->assigned_to,
            'notification_type' => $type,
            'title' => $title,
            'message' => "Your requisition for {$item_name} has been {$status}",
            'channel' => 'in_app',
            'created_by' => $job->created_by,
        ]);
    }
}
