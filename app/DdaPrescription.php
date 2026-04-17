<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DdaPrescription extends Model
{
    protected $guarded = ['id'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path && file_exists(public_path('uploads/dda/' . $this->image_path))) {
            return asset('uploads/dda/' . $this->image_path);
        }
        return null;
    }
}
