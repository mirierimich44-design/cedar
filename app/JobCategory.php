<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'parent_id',
        'name',
        'short_code',
        'description',
        'color',
        'icon',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the business that owns the category.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(JobCategory::class, 'parent_id');
    }

    /**
     * Get the sub-categories.
     */
    public function subCategories()
    {
        return $this->hasMany(JobCategory::class, 'parent_id');
    }

    /**
     * Get the jobs for this category.
     */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'category_id');
    }

    /**
     * Get the templates for this category.
     */
    public function templates()
    {
        return $this->hasMany(JobTemplate::class, 'category_id');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include root categories.
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get categories for dropdown.
     */
    public static function forDropdown($business_id, $prepend_none = true)
    {
        $categories = JobCategory::where('business_id', $business_id)
            ->active()
            ->orderBy('name')
            ->get();

        $dropdown = $categories->pluck('name', 'id');

        if ($prepend_none) {
            $dropdown->prepend(__('job.select_category'), '');
        }

        return $dropdown;
    }
}
