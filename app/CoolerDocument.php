<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoolerDocument extends Model
{
    use SoftDeletes;

    protected $table = 'cooler_documents';

    protected $fillable = [
        'documentable_type', 'documentable_id', 'document_type',
        'file_path', 'file_name', 'file_size', 'mime_type', 'thumbnail_path',
        'uploaded_by', 'verified_at', 'verified_by', 'verification_notes',
        'expires_at', 'status', 'metadata',
    ];

    protected $casts = [
        'metadata'    => 'array',
        'verified_at' => 'datetime',
        'expires_at'  => 'date',
        'file_size'   => 'integer',
    ];

    public static $typeLabels = [
        'id_copy'                      => 'ID Copy',
        'kra_pin_certificate'          => 'KRA PIN Certificate',
        'passport_photo'               => 'Passport Photo',
        'county_business_permit'       => 'County Business Permit',
        'certificate_of_registration'  => 'Certificate of Registration',
        'certificate_of_incorporation' => 'Certificate of Incorporation',
        'tax_certificate'              => 'Tax Certificate',
        'signed_agreement'             => 'Signed Agreement',
        'retrieval_photo_before'       => 'Before Retrieval Photo',
        'retrieval_photo_during'       => 'During Retrieval Photo',
        'retrieval_photo_after'        => 'After Retrieval Photo',
        'acknowledgement_signature'    => 'Acknowledgement Signature',
        'signed_retrieval_letter'      => 'Signed Retrieval Letter',
        'other'                        => 'Other',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getUrlAttribute(): string
    {
        return asset($this->file_path);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail_path) {
            return asset($this->thumbnail_path);
        }
        return $this->url;
    }

    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->document_type] ?? ucwords(str_replace('_', ' ', $this->document_type));
    }

    public function isImage(): bool
    {
        return in_array($this->mime_type, ['image/jpeg', 'image/png', 'image/heic', 'image/webp']);
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expires_at && $this->expires_at->isFuture() && $this->expires_at->diffInDays(now()) <= $days;
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->isExpired()) {
            return '<span class="label label-danger">Expired</span>';
        }
        $classes = ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'];
        $class   = $classes[$this->status] ?? 'default';
        return "<span class=\"label label-{$class}\">" . ucfirst($this->status) . "</span>";
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', now())
            ->whereDate('expires_at', '<=', now()->addDays($days));
    }
}
