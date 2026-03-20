<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobSignature extends Model
{
    protected $fillable = [
        'job_id',
        'signature_type',
        'signature_data',
        'signer_name',
        'signer_email',
        'signer_phone',
        'location_coordinates',
        'signed_at',
        'created_by',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    /**
     * Get the job for this signature.
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the creator of this signature record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include worker signatures.
     */
    public function scopeWorker($query)
    {
        return $query->where('signature_type', 'worker');
    }

    /**
     * Scope a query to only include manager signatures.
     */
    public function scopeManager($query)
    {
        return $query->where('signature_type', 'manager');
    }

    /**
     * Scope a query to only include customer signatures.
     */
    public function scopeCustomer($query)
    {
        return $query->where('signature_type', 'customer');
    }

    /**
     * Check if job has all required signatures.
     */
    public static function jobHasRequiredSignatures($job_id, $requiredTypes = ['worker', 'customer'])
    {
        $existingTypes = self::where('job_id', $job_id)
            ->whereIn('signature_type', $requiredTypes)
            ->pluck('signature_type')
            ->toArray();

        return count(array_intersect($requiredTypes, $existingTypes)) === count($requiredTypes);
    }

    /**
     * Generate signature image file.
     */
    public function generateImage()
    {
        $signatureData = $this->signature_data;

        // Remove data URI prefix if present
        if (strpos($signatureData, 'data:image/') === 0) {
            $signatureData = substr($signatureData, strpos($signatureData, ',') + 1);
        }

        $imageData = base64_decode($signatureData);
        $filename = "signature_{$this->job_id}_{$this->signature_type}_{$this->id}.png";
        $filepath = "uploads/job_signatures/{$filename}";

        // Ensure directory exists
        $fullPath = public_path($filepath);
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($fullPath, $imageData);

        return $filepath;
    }

    /**
     * Get the signature image URL.
     */
    public function getImageUrlAttribute()
    {
        $filepath = "uploads/job_signatures/signature_{$this->job_id}_{$this->signature_type}_{$this->id}.png";
        if (file_exists(public_path($filepath))) {
            return asset($filepath);
        }

        // Generate if doesn't exist
        $filepath = $this->generateImage();
        return asset($filepath);
    }
}
