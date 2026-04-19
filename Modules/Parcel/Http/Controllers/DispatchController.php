<?php

namespace Modules\Parcel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Parcel\Entities\Parcel;
use Modules\Parcel\Entities\ParcelStatusLog;
use Modules\Parcel\Services\ParcelSmsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DispatchController extends Controller
{
    protected $smsService;

    public function __construct(ParcelSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Update parcel status.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'parcel_id' => 'required|exists:parcels,id',
            'status' => 'required|in:booked,in_transit,arrived,collected,failed',
            'station_id' => 'nullable|exists:parcel_stations,id',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $parcel = Parcel::findOrFail($request->parcel_id);
            $old_status = $parcel->status;
            $parcel->status = $request->status;

            if ($request->status === 'collected') {
                $parcel->payment_status = 'paid';
            }

            $parcel->save();

            // Create status log
            ParcelStatusLog::create([
                'parcel_id' => $parcel->id,
                'status' => $request->status,
                'station_id' => $request->station_id,
                'updated_by_user_id' => auth()->user()->id,
                'notes' => $request->notes
            ]);

            DB::commit();

            // Send notification if status changed
            if ($old_status !== $request->status) {
                $this->smsService->sendNotification($parcel, $request->status);
            }

            return response()->json([
                'success' => true,
                'msg' => 'Parcel status updated to ' . $request->status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Parcel Status Update Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'Something went wrong.']);
        }
    }

    /**
     * Bulk update status for dispatch.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'parcel_ids' => 'required|array',
            'status' => 'required|in:in_transit,arrived',
            'station_id' => 'required|exists:parcel_stations,id'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->parcel_ids as $id) {
                $parcel = Parcel::findOrFail($id);
                $old_status = $parcel->status;
                $parcel->status = $request->status;
                $parcel->save();

                ParcelStatusLog::create([
                    'parcel_id' => $parcel->id,
                    'status' => $request->status,
                    'station_id' => $request->station_id,
                    'updated_by_user_id' => auth()->user()->id,
                    'notes' => $request->notes ?? 'Bulk status update'
                ]);

                if ($old_status !== $request->status) {
                    $this->smsService->sendNotification($parcel, $request->status);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Bulk status update successful.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }
}
