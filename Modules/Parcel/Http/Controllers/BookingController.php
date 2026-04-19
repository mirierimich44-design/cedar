<?php

namespace Modules\Parcel\Http\Controllers;

use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Parcel\Entities\Parcel;
use Modules\Parcel\Entities\Station;
use Modules\Parcel\Entities\Route;
use Modules\Parcel\Entities\ParcelStatusLog;
use Modules\Parcel\Services\PricingEngine;
use Modules\Parcel\Services\WaybillGeneratorService;
use Modules\Parcel\Services\ParcelMpesaService;
use Modules\Parcel\Services\ParcelSmsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $util;
    protected $pricingEngine;
    protected $waybillGenerator;
    protected $mpesaService;
    protected $smsService;

    public function __construct(
        Util $util,
        PricingEngine $pricingEngine,
        WaybillGeneratorService $waybillGenerator,
        ParcelMpesaService $mpesaService,
        ParcelSmsService $smsService
    ) {
        $this->util = $util;
        $this->pricingEngine = $pricingEngine;
        $this->waybillGenerator = $waybillGenerator;
        $this->mpesaService = $mpesaService;
        $this->smsService = $smsService;
    }

    /**
     * Show the booking form.
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $stations = Station::where('business_id', $business_id)->where('is_active', 1)->pluck('name', 'id');
        
        return view('parcel::booking.create', compact('stations'));
    }

    /**
     * Store a newly created parcel booking.
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        
        $request->validate([
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'origin_station_id' => 'required|exists:parcel_stations,id',
            'destination_station_id' => 'required|exists:parcel_stations,id',
            'weight_kg' => 'required|numeric|min:0.1',
            'payment_method' => 'required|in:mpesa,cash,cod'
        ]);

        try {
            DB::beginTransaction();

            $origin = Station::findOrFail($request->origin_station_id);
            $destination = Station::findOrFail($request->destination_station_id);

            // Find route
            $route = Route::where('origin_station_id', $origin->id)
                ->where('destination_station_id', $destination->id)
                ->first();

            if (!$route) {
                return response()->json(['success' => false, 'msg' => 'No route found between selected stations.']);
            }

            // Calculate price
            $amount = $this->pricingEngine->calculate($route, $request->weight_kg);

            // Generate waybill
            $waybill = $this->waybillGenerator->generate($origin);

            // Create parcel
            $parcel = Parcel::create([
                'business_id' => $business_id,
                'waybill_number' => $waybill,
                'sender_name' => $request->sender_name,
                'sender_phone' => $request->sender_phone,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'origin_station_id' => $origin->id,
                'destination_station_id' => $destination->id,
                'route_id' => $route->id,
                'weight_kg' => $request->weight_kg,
                'description' => $request->description,
                'declared_value' => $request->declared_value ?? 0,
                'charge_amount' => $amount,
                'payment_method' => $request->payment_method,
                'payment_status' => ($request->payment_method === 'cash') ? 'paid' : 'pending',
                'booked_by_user_id' => auth()->user()->id,
                'status' => 'booked'
            ]);

            // Create initial status log
            ParcelStatusLog::create([
                'parcel_id' => $parcel->id,
                'status' => 'booked',
                'station_id' => $origin->id,
                'updated_by_user_id' => auth()->user()->id,
                'notes' => 'Parcel booked at ' . $origin->name
            ]);

            DB::commit();

            // Send booking SMS
            $this->smsService->sendNotification($parcel, 'booked');

            // Handle M-Pesa STK Push if selected
            if ($request->payment_method === 'mpesa') {
                $mpesaResult = $this->mpesaService->initiateBookingPayment($parcel);
                if ($mpesaResult['success']) {
                    return response()->json([
                        'success' => true,
                        'msg' => 'Parcel booked. M-Pesa STK Push initiated.',
                        'parcel_id' => $parcel->id,
                        'waybill' => $waybill,
                        'mpesa_prompt' => true
                    ]);
                }
            }

            return response()->json([
                'success' => true, 
                'msg' => 'Parcel booked successfully. Waybill: ' . $waybill,
                'parcel_id' => $parcel->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Parcel Booking Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }
}
