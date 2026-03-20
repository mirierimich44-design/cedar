<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Job extends Model
{
    protected $table = 'job_cards';

    public $timestamps = true;

    protected $fillable = [
        'business_id',
        'location_id',
        'contact_id',
        'category_id',
        'template_id',
        'ref_no',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'assigned_at',
        'started_at',
        'completed_at',
        'approved_at',
        'assigned_to',
        'estimated_hours',
        'actual_hours',
        'estimated_cost',
        'actual_cost',
        'worker_notes',
        'admin_notes',
        'location_coordinates',
        'location_address',
        'completed_by',
        'approved_by',
        'requires_approval',
        'is_paid',
        'rating',
        'rating_feedback',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'requires_approval' => 'boolean',
        'is_paid' => 'boolean',
    ];

    /**
     * Get the business that owns the job.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the business location for this job.
     */
    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    /**
     * Get the contact (customer) for this job.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the category for this job.
     */
    public function category()
    {
        return $this->belongsTo(JobCategory::class);
    }

    /**
     * Get the template this job was created from.
     */
    public function template()
    {
        return $this->belongsTo(JobTemplate::class);
    }

    /**
     * Get the user assigned to this job.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this job.
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who completed this job.
     */
    public function completedByUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get the user who approved this job.
     */
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the checklists for this job.
     */
    public function checklists()
    {
        return $this->hasMany(JobChecklist::class)->orderBy('sort_order');
    }

    /**
     * Get the logs for this job.
     */
    public function logs()
    {
        return $this->hasMany(JobLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the requisitions for this job.
     */
    public function requisitions()
    {
        return $this->hasMany(JobRequisition::class);
    }

    /**
     * Get the images for this job.
     */
    public function images()
    {
        return $this->hasMany(JobImage::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the signatures for this job.
     */
    public function signatures()
    {
        return $this->hasMany(JobSignature::class);
    }

    /**
     * Get the notifications for this job.
     */
    public function notifications()
    {
        return $this->hasMany(JobNotification::class);
    }

    /**
     * Scope a query to only include jobs for a specific business.
     */
    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('jobs.business_id', $business_id);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('jobs.status', $status);
    }

    /**
     * Scope a query to filter by priority.
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('jobs.priority', $priority);
    }

    /**
     * Scope a query to only include jobs assigned to a user.
     */
    public function scopeAssignedTo($query, $user_id)
    {
        return $query->where('jobs.assigned_to', $user_id);
    }

    /**
     * Scope a query to only include jobs for a contact.
     */
    public function scopeForContact($query, $contact_id)
    {
        return $query->where('jobs.contact_id', $contact_id);
    }

    /**
     * Scope a query to only include jobs due soon or overdue.
     */
    public function scopeDue($query)
    {
        return $query->where('jobs.due_date', '<=', now()->addDays(7))
            ->whereNotIn('jobs.status', ['completed', 'approved', 'cancelled']);
    }

    /**
     * Scope a query to only include overdue jobs.
     */
    public function scopeOverdue($query)
    {
        return $query->where('jobs.due_date', '<', now())
            ->whereNotIn('jobs.status', ['completed', 'approved', 'cancelled']);
    }

    /**
     * Scope a query to only include jobs that need approval.
     */
    public function scopeNeedsApproval($query)
    {
        return $query->where('jobs.status', 'completed')
            ->where('jobs.requires_approval', true)
            ->whereNull('jobs.approved_at');
    }

    /**
     * Get completion percentage based on checklists.
     */
    public function getCompletionPercentageAttribute()
    {
        if ($this->checklists->isEmpty()) {
            return $this->status === 'completed' || $this->status === 'approved' ? 100 : 0;
        }

        $total = $this->checklists->count();
        $completed = $this->checklists->where('is_completed', true)->count();

        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    /**
     * Check if job is overdue.
     */
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast()
            && !in_array($this->status, ['completed', 'approved', 'cancelled']);
    }

    /**
     * Generate a unique reference number.
     */
    public static function generateRefNo($business_id)
    {
        $prefix = 'JB-';
        $lastRef = self::where('business_id', $business_id)
            ->orderBy('id', 'desc')
            ->value('ref_no');

        if ($lastRef) {
            $number = (int) str_replace($prefix, '', $lastRef) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create job from template.
     */
    public static function createFromTemplate($template, $data)
    {
        $job = self::create(array_merge($data, [
            'template_id' => $template->id,
            'category_id' => $data['category_id'] ?? $template->category_id,
            'estimated_hours' => $data['estimated_hours'] ?? $template->estimated_hours,
            'estimated_cost' => $data['estimated_cost'] ?? $template->estimated_cost,
            'requires_approval' => $data['requires_approval'] ?? $template->requires_approval,
        ]));

        // Copy template items to job checklists
        foreach ($template->items as $item) {
            JobChecklist::create([
                'job_id' => $job->id,
                'template_item_id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'sort_order' => $item->sort_order,
            ]);
        }

        return $job;
    }
}
