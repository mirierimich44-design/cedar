<?php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LabOrder extends Model
{
    use HasFactory;

    protected $table = 'hospital_lab_orders';

    protected $fillable = [
        'visit_id',
        'business_id',
        'test_name',
        'test_code',
        'ordered_by',
        'ordered_at',
        'status',
        'result_value',
        'result_unit',
        'reference_range',
        'result_notes',
        'resulted_at',
    ];

    protected $casts = [
        'ordered_at'  => 'datetime',
        'resulted_at' => 'datetime',
    ];

    /**
     * Get the visit that owns this lab order.
     */
    public function visit()
    {
        return $this->belongsTo(HospitalVisit::class, 'visit_id');
    }
}
