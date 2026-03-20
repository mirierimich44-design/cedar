<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'category_id',
        'name',
        'description',
        'estimated_hours',
        'estimated_cost',
        'requires_approval',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'estimated_hours' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the business that owns the template.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the category for this template.
     */
    public function category()
    {
        return $this->belongsTo(JobCategory::class);
    }

    /**
     * Get the creator of this template.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the items/checklists for this template.
     */
    public function items()
    {
        return $this->hasMany(JobTemplateItem::class)->orderBy('sort_order');
    }

    /**
     * Get the jobs created from this template.
     */
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get templates for dropdown.
     */
    public static function forDropdown($business_id, $prepend_none = true)
    {
        $templates = JobTemplate::where('business_id', $business_id)
            ->active()
            ->with('category')
            ->orderBy('name')
            ->get();

        $dropdown = $templates->mapWithKeys(function ($template) {
            $name = $template->category
                ? "{$template->category->name} - {$template->name}"
                : $template->name;
            return [$template->id => $name];
        });

        if ($prepend_none) {
            $dropdown->prepend(__('job.select_template'), '');
        }

        return $dropdown;
    }

    /**
     * Duplicate this template.
     */
    public function duplicate()
    {
        $newTemplate = $this->replicate();
        $newTemplate->name = $this->name . ' (Copy)';
        $newTemplate->save();

        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $newItem->job_template_id = $newTemplate->id;
            $newItem->save();
        }

        return $newTemplate;
    }
}
