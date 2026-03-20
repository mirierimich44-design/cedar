<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobTemplateItem extends Model
{
    protected $fillable = [
        'job_template_id',
        'title',
        'description',
        'sort_order',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the template for this item.
     */
    public function template()
    {
        return $this->belongsTo(JobTemplate::class, 'job_template_id');
    }

    /**
     * Get the checklist items created from this template item.
     */
    public function checklistItems()
    {
        return $this->hasMany(JobChecklist::class, 'template_item_id');
    }

    /**
     * Scope a query to only include required items.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
