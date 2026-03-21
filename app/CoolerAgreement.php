<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoolerAgreement extends Model
{
    protected $table = 'cooler_agreements';

    protected $fillable = [
        'business_id', 'cooler_id', 'dealer_id', 'agreement_date', 'sales_volume_target', 'status',
        'termination_reason', 'termination_date',
        'company_signatory_name', 'company_signature_path', 'company_signed_at',
        'rsm_tsm_signatory_name', 'rsm_tsm_signature_path', 'rsm_tsm_signed_at',
        'dealer_signatory_name', 'dealer_signature_path', 'dealer_signed_at',
        'distributor_signatory_name', 'distributor_signature_path', 'distributor_signed_at',
        'generated_pdf_path', 'created_by',
    ];

    protected $casts = [
        'agreement_date'    => 'date',
        'termination_date'  => 'date',
        'company_signed_at' => 'datetime',
        'rsm_tsm_signed_at' => 'datetime',
        'dealer_signed_at'  => 'datetime',
        'distributor_signed_at' => 'datetime',
        'sales_volume_target'   => 'decimal:2',
    ];

    public static $signatoryTypes = ['company_legal', 'rsm_tsm', 'dealer', 'distributor'];

    public function business()
    {
        return $this->belongsTo(Business::class);
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

    public function retrievals()
    {
        return $this->hasMany(CoolerRetrieval::class, 'agreement_id');
    }

    public function documents()
    {
        return $this->morphMany(CoolerDocument::class, 'documentable');
    }

    public function isFullySigned(): bool
    {
        return $this->company_signature_path
            && $this->rsm_tsm_signature_path
            && $this->dealer_signature_path
            && $this->distributor_signature_path;
    }

    public function getSignedCount(): int
    {
        $count = 0;
        if ($this->company_signature_path)     $count++;
        if ($this->rsm_tsm_signature_path)     $count++;
        if ($this->dealer_signature_path)      $count++;
        if ($this->distributor_signature_path) $count++;
        return $count;
    }

    public function captureSignature(string $type, string $base64Data, string $signatoryName): string
    {
        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64Data));
        $dir       = public_path("uploads/cooler/agreements/{$this->id}/signatures/");

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = "{$type}_" . time() . ".png";
        file_put_contents($dir . $filename, $imageData);

        $path = "uploads/cooler/agreements/{$this->id}/signatures/{$filename}";

        $this->update([
            "{$type}_signature_path" => $path,
            "{$type}_signatory_name" => $signatoryName,
            "{$type}_signed_at"      => now(),
        ]);

        return $path;
    }

    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    public function getStatusBadgeAttribute()
    {
        $classes = ['draft' => 'default', 'active' => 'success', 'terminated' => 'danger'];
        $class   = $classes[$this->status] ?? 'default';
        return "<span class=\"label label-{$class}\">" . ucfirst($this->status) . "</span>";
    }
}
