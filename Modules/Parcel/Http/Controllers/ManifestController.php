<?php

namespace Modules\Parcel\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Parcel\Entities\Parcel;
use Modules\Parcel\Entities\Station;
use Modules\Parcel\Entities\Route as ParcelRoute;

class ManifestController extends Controller
{
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $date        = $request->get('date', Carbon::today()->toDateString());
        $route_id    = $request->get('route_id');

        $query = Parcel::where('business_id', $business_id)
            ->whereDate('created_at', $date)
            ->with(['originStation', 'destinationStation', 'statusLogs'])
            ->orderBy('created_at', 'asc');

        if ($route_id) {
            $query->where('route_id', $route_id);
        }

        $parcels = $query->get();

        $routes   = ParcelRoute::where('business_id', $business_id)
            ->where('is_active', true)
            ->with(['origin', 'destination'])
            ->get();

        $stations = Station::where('business_id', $business_id)->where('is_active', true)->get();

        // Summary
        $summary = [
            'total'      => $parcels->count(),
            'weight'     => $parcels->sum('weight_kg'),
            'revenue'    => $parcels->where('payment_status', 'paid')->sum('charge_amount'),
            'cod'        => $parcels->where('payment_method', 'cod')->count(),
            'pending_payment' => $parcels->where('payment_status', 'pending')->count(),
        ];

        return view('parcel::manifest.index', compact('parcels', 'routes', 'stations', 'date', 'route_id', 'summary'));
    }
}
