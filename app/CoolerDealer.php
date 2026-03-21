<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoolerDealer extends Model
{
    use SoftDeletes;

    protected $table = 'cooler_dealers';

    protected $fillable = [
        'business_id', 'contact_id', 'name', 'id_number', 'kra_pin', 'postal_address', 'phone',
        'outlet_name', 'channel', 'building', 'road', 'area', 'years_in_business',
        'brands_stocked', 'compliance_score', 'status', 'created_by', 'agent_id',
    ];

    protected $casts = [
        'brands_stocked'   => 'array',
        'compliance_score' => 'decimal:2',
    ];

    public static $channels = ['retail', 'wholesale', 'supermarket', 'kiosk'];

    public static $brandOptions = [
        'Pepsi', 'Mountain Dew', 'Miranda', '7UP', 'Aquafina', 'Lipton', 'Sting', 'Evervess',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function scopeForAgent($query, $agent_id)
    {
        return $query->where('agent_id', $agent_id);
    }

    public function agreements()
    {
        return $this->hasMany(CoolerAgreement::class, 'dealer_id');
    }

    public function activeAgreement()
    {
        return $this->hasOne(CoolerAgreement::class, 'dealer_id')->where('status', 'active');
    }

    public function coolers()
    {
        return $this->hasMany(CoolerAsset::class, 'current_dealer_id');
    }

    public function retrievals()
    {
        return $this->hasMany(CoolerRetrieval::class, 'dealer_id');
    }

    public function complianceLogs()
    {
        return $this->hasMany(CoolerComplianceLog::class, 'dealer_id');
    }

    public function documents()
    {
        return $this->morphMany(CoolerDocument::class, 'documentable');
    }

    public function getDocumentByType(string $type)
    {
        return $this->documents()->where('document_type', $type)->latest()->first();
    }

    public function hasRequiredDocuments(): bool
    {
        $personal = ['id_copy', 'kra_pin_certificate', 'passport_photo'];
        $legal    = ['county_business_permit', 'certificate_of_registration', 'certificate_of_incorporation', 'tax_certificate'];

        $uploaded = $this->documents()->pluck('document_type')->toArray();

        foreach ($personal as $type) {
            if (!in_array($type, $uploaded)) {
                return false;
            }
        }

        // At least one legal document required
        $hasLegal = count(array_intersect($legal, $uploaded)) > 0;

        return $hasLegal;
    }

    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getStatusBadgeAttribute()
    {
        $classes = ['active' => 'success', 'inactive' => 'default', 'suspended' => 'danger'];
        $class   = $classes[$this->status] ?? 'default';
        return "<span class=\"label label-{$class}\">" . ucfirst($this->status) . "</span>";
    }

    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([$this->building, $this->road, $this->area]));
    }
}
