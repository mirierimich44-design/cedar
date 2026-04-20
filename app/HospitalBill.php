<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HospitalBill extends Model
{
    use SoftDeletes;

    protected $table = 'hospital_bills';

    protected $fillable = [
        'business_id', 'location_id', 'bill_number', 'patient_id',
        'patient_name', 'patient_phone', 'patient_dob', 'gender',
        'nhif_number', 'doctor_name', 'visit_date', 'visit_type',
        'diagnosis', 'bill_items',
        'subtotal', 'nhif_amount', 'discount', 'total_amount', 'paid_amount', 'balance',
        'payment_status', 'payment_method', 'mpesa_code',
        'status', 'created_by', 'notes',
    ];

    protected $casts = [
        'bill_items' => 'array',
        'visit_date' => 'date',
    ];

    protected $dates = ['deleted_at'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function patient()
    {
        return $this->belongsTo(Contact::class, 'patient_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateBillNumber(int $business_id): string
    {
        $last = self::where('business_id', $business_id)->max('id');
        $seq  = str_pad(($last ? $last + 1 : 1), 5, '0', STR_PAD_LEFT);
        return 'BIL-' . now()->format('Y') . '-' . $seq;
    }

    public static function visitTypes(): array
    {
        return [
            'outpatient' => 'Outpatient (OPD)',
            'inpatient'  => 'Inpatient (IPD)',
            'emergency'  => 'Emergency',
        ];
    }

    public static function paymentStatusColors(): array
    {
        return [
            'unpaid'  => 'danger',
            'partial' => 'warning',
            'paid'    => 'success',
        ];
    }
}
