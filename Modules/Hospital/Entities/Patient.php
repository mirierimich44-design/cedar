<?php

namespace Modules\Hospital\Entities;

use App\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'hospital_patients';

    protected $fillable = [
        'business_id',
        'patient_no',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'phone',
        'email',
        'address',
        'blood_group',
        'allergies',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    /**
     * Get the business that owns the patient.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the visits for this patient.
     */
    public function visits()
    {
        return $this->hasMany(HospitalVisit::class, 'patient_id');
    }

    /**
     * Accessor: full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Generate the next patient number for a given business.
     */
    public static function generatePatientNo(int $business_id): string
    {
        $max = static::where('business_id', $business_id)
            ->where('patient_no', 'like', 'PT-%')
            ->max('patient_no');

        $next = 1;
        if ($max) {
            $parts = explode('-', $max);
            $next = (int) end($parts) + 1;
        }

        return 'PT-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
