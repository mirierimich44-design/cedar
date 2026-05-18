<?php

namespace Modules\Hospital\Entities;

use App\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MortuaryRecord extends Model
{
    use HasFactory;

    protected $table = 'hospital_mortuary_records';

    protected $fillable = [
        'business_id',
        'body_reference',
        'deceased_name',
        'deceased_dob',
        'gender',
        'cause_of_death',
        'date_of_death',
        'time_of_death',
        'brought_by',
        'brought_by_phone',
        'relationship',
        'storage_location',
        'storage_date',
        'release_date',
        'released_to',
        'released_to_phone',
        'notes',
        'status',
    ];

    protected $casts = [
        'date_of_death' => 'date',
        'storage_date'  => 'date',
        'release_date'  => 'date',
        'deceased_dob'  => 'date',
    ];

    /**
     * Get the business that owns this mortuary record.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Generate the next body reference for a given business.
     */
    public static function generateBodyReference(int $business_id): string
    {
        $max = static::where('business_id', $business_id)
            ->where('body_reference', 'like', 'MRT-%')
            ->max('body_reference');

        $next = 1;
        if ($max) {
            $parts = explode('-', $max);
            $next = (int) end($parts) + 1;
        }

        return 'MRT-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
