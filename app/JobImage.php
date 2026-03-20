<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobImage extends Model
{
    protected $fillable = [
        'job_id',
        'job_log_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'thumbnail_path',
        'caption',
        'location_coordinates',
        'taken_at',
        'created_by',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    /**
     * Get the job for this image.
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the log associated with this image.
     */
    public function log()
    {
        return $this->belongsTo(JobLog::class, 'job_log_id');
    }

    /**
     * Get the creator of this image.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the full URL for the image.
     */
    public function getUrlAttribute()
    {
        return asset('uploads/job_images/' . $this->file_path);
    }

    /**
     * Get the full URL for the thumbnail.
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path) {
            return asset('uploads/job_images/' . $this->thumbnail_path);
        }
        return $this->url;
    }

    /**
     * Get file size in human readable format.
     */
    public function getHumanFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Delete the image file from storage.
     */
    public function deleteImageFiles()
    {
        $imagePath = public_path('uploads/job_images/' . $this->file_path);
        $thumbnailPath = $this->thumbnail_path ? public_path('uploads/job_images/' . $this->thumbnail_path) : null;

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        if ($thumbnailPath && file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
    }

    /**
     * Override delete to remove files.
     */
    public function delete()
    {
        $this->deleteImageFiles();
        return parent::delete();
    }
}
