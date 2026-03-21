<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoolerRetrieval extends Model
{
    protected $table = 'cooler_retrievals';

    protected $fillable = [
        'business_id', 'agreement_id', 'cooler_id', 'dealer_id',
        'retrieval_date', 'reason', 'reason_notes',
        'authorized_staff_name', 'authorized_staff_id_no', 'authorized_staff_tel',
        'customer_signature_path', 'acknowledgement_date', 'gps_coordinates',
        'retrieval_letter_path', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'retrieval_date'       => 'date',
        'acknowledgement_date' => 'datetime',
    ];

    public static $reasons = [
        'not_purchasing_from_stockist'     => 'Not purchasing from appointed stockist',
        'not_stocking_to_capacity'         => 'Not stocking to capacity',
        'not_displaying_sbck_products_only'=> 'Not displaying SBCK LIMITED products only',
        'unauthorized_rebrand_or_relocation'=> 'Unauthorized rebrand or relocation',
        'business_ownership_change'        => 'Business ownership change',
        'liquidation'                      => 'Liquidation',
        'company_discretion'               => 'Company discretion',
        'other'                            => 'Other',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function agreement()
    {
        return $this->belongsTo(CoolerAgreement::class, 'agreement_id');
    }

    public function cooler()
    {
        return $this->belongsTo(CoolerAsset::class, 'cooler_id');
    }

    public function dealer()
    {
        return $this->belongsTo(CoolerDealer::class, 'dealer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents()
    {
        return $this->morphMany(CoolerDocument::class, 'documentable');
    }

    public function photosBefore()
    {
        return $this->documents()->where('document_type', 'retrieval_photo_before');
    }

    public function photosDuring()
    {
        return $this->documents()->where('document_type', 'retrieval_photo_during');
    }

    public function photosAfter()
    {
        return $this->documents()->where('document_type', 'retrieval_photo_after');
    }

    public function captureCustomerSignature(string $base64Data): string
    {
        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64Data));
        $dir       = public_path("uploads/cooler/retrievals/{$this->id}/");

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'customer_signature_' . time() . '.png';
        file_put_contents($dir . $filename, $imageData);

        $path = "uploads/cooler/retrievals/{$this->id}/{$filename}";
        $this->update([
            'customer_signature_path' => $path,
            'acknowledgement_date'    => now(),
        ]);

        return $path;
    }

    public function getReasonLabelAttribute(): string
    {
        return self::$reasons[$this->reason] ?? ucfirst(str_replace('_', ' ', $this->reason));
    }

    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    public function getStatusBadgeAttribute()
    {
        $classes = ['initiated' => 'warning', 'in_progress' => 'info', 'completed' => 'success'];
        $class   = $classes[$this->status] ?? 'default';
        return "<span class=\"label label-{$class}\">" . ucwords(str_replace('_', ' ', $this->status)) . "</span>";
    }
}
