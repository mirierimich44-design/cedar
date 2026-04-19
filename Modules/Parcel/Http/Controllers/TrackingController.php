<?php

namespace Modules\Parcel\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Parcel\Entities\Parcel;

class TrackingController extends Controller
{
    /**
     * Show tracking info for a waybill.
     * Publicly accessible.
     */
    public function show($waybill)
    {
        $parcel = Parcel::where('waybill_number', $waybill)
            ->with(['origin', 'destination', 'status_logs.station'])
            ->first();

        if (!$parcel) {
            return view('parcel::tracking.not_found', compact('waybill'));
        }

        return view('parcel::tracking.show', compact('parcel'));
    }
}
