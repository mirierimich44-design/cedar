<?php

namespace Modules\Parcel\Services;

use Modules\Parcel\Entities\Parcel;
use Modules\Parcel\Entities\Station;
use Carbon\Carbon;

class WaybillGeneratorService
{
    /**
     * Generate a unique waybill number.
     * Format: FS-[ORIGIN_CODE]-[YYYYMMDD]-[SEQUENCE]
     *
     * @param Station $origin
     * @return string
     */
    public function generate(Station $origin): string
    {
        $today = Carbon::today()->format('Ymd');
        $prefix = 'FS-' . strtoupper(substr($origin->name, 0, 3)) . '-' . $today . '-';

        // Count existing parcels for this origin and day to get sequence
        $count = Parcel::where('waybill_number', 'like', $prefix . '%')->count();
        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        $waybill = $prefix . $sequence;

        // Double check for uniqueness
        while (Parcel::where('waybill_number', $waybill)->exists()) {
            $count++;
            $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            $waybill = $prefix . $sequence;
        }

        return $waybill;
    }
}
