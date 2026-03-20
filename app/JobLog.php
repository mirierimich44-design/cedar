<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobLog extends Model
{
    protected $fillable = [
        'job_id',
        'user_id',
        'log_type',
        'title',
        'content',
        'old_status',
        'new_status',
        'hours_logged',
        'logged_at',
        'location_coordinates',
        'created_by',
    ];

    protected $casts = [
        'hours_logged' => 'decimal:2',
        'logged_at' => 'datetime',
    ];

    /**
     * Get the job for this log.
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the user who created this log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the images associated with this log.
     */
    public function images()
    {
        return $this->hasMany(JobImage::class);
    }

    /**
     * Get the creator of this log.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include time logs.
     */
    public function scopeTimeLogs($query)
    {
        return $query->where('log_type', 'time_log');
    }

    /**
     * Scope a query to only include notes.
     */
    public function scopeNotes($query)
    {
        return $query->where('log_type', 'note');
    }

    /**
     * Scope a query to only include status changes.
     */
    public function scopeStatusChanges($query)
    {
        return $query->where('log_type', 'status_change');
    }

    /**
     * Create a status change log.
     */
    public static function createStatusChange($job_id, $user_id, $old_status, $new_status, $notes = null)
    {
        return self::create([
            'job_id' => $job_id,
            'user_id' => $user_id,
            'log_type' => 'status_change',
            'title' => 'Status Changed',
            'content' => $notes ?? "Status changed from {$old_status} to {$new_status}",
            'old_status' => $old_status,
            'new_status' => $new_status,
            'created_by' => $user_id,
        ]);
    }

    /**
     * Create a time log entry.
     */
    public static function createTimeLog($job_id, $user_id, $hours, $notes = null, $location = null)
    {
        return self::create([
            'job_id' => $job_id,
            'user_id' => $user_id,
            'log_type' => 'time_log',
            'title' => 'Time Logged',
            'content' => $notes ?? "Logged {$hours} hours",
            'hours_logged' => $hours,
            'logged_at' => now(),
            'location_coordinates' => $location,
            'created_by' => $user_id,
        ]);
    }

    /**
     * Create a note log entry.
     */
    public static function createNote($job_id, $user_id, $title, $content, $is_comment = false)
    {
        return self::create([
            'job_id' => $job_id,
            'user_id' => $user_id,
            'log_type' => $is_comment ? 'comment' : 'note',
            'title' => $title,
            'content' => $content,
            'created_by' => $user_id,
        ]);
    }
}
