<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HospitalVisit extends Model
{
    use HasFactory;

    protected $table = 'hospital_visits';

    // Status constants
    const STATUS_TRIAGE       = 'triage';
    const STATUS_CONSULTATION = 'consultation';
    const STATUS_LAB          = 'lab';
    const STATUS_PHARMACY     = 'pharmacy';
    const STATUS_DISCHARGED   = 'discharged';
    const STATUS_ADMITTED     = 'admitted';
    const STATUS_DECEASED     = 'deceased';

    protected $fillable = [
        'business_id',
        'patient_id',
        'visit_no',
        'visited_at',
        'visit_type',
        'chief_complaint',
        'triage_category',
        'triage_notes',
        'bp_systolic',
        'bp_diastolic',
        'temperature',
        'pulse_rate',
        'respiratory_rate',
        'oxygen_saturation',
        'weight_kg',
        'height_cm',
        'assigned_doctor',
        'status',
        'admission_date',
        'discharge_date',
        'ward',
        'bed_number',
    ];

    protected $casts = [
        'visited_at'     => 'datetime',
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
    ];

    /**
     * Get the patient that owns the visit.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the lab orders for this visit.
     */
    public function labOrders()
    {
        return $this->hasMany(LabOrder::class, 'visit_id');
    }

    /**
     * Return the Bootstrap badge colour class for the triage category.
     */
    public function getTriageBadgeClass(): string
    {
        return match ($this->triage_category) {
            'green'  => 'success',
            'yellow' => 'warning',
            'orange' => 'warning',
            'red'    => 'danger',
            'black'  => 'dark',
            default  => 'secondary',
        };
    }

    /**
     * Generate the next visit number for a given business.
     */
    public static function generateVisitNo(int $business_id): string
    {
        $max = static::where('business_id', $business_id)
            ->where('visit_no', 'like', 'VN-%')
            ->max('visit_no');

        $next = 1;
        if ($max) {
            $parts = explode('-', $max);
            $next = (int) end($parts) + 1;
        }

        return 'VN-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
