<?php

namespace Modules\Parcel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Parcel\Entities\Route as ParcelRoute;
use Modules\Parcel\Entities\Station;
use Modules\Parcel\Entities\PricingRule;
use Modules\Parcel\Services\PricingEngine;

class RouteController extends Controller
{
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $routes = ParcelRoute::where('business_id', $business_id)
            ->with(['origin', 'destination', 'pricing_rules'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('parcel::routes.index', compact('routes'));
    }

    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $stations = Station::where('business_id', $business_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');
        return view('parcel::routes.create', compact('stations'));
    }

    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $request->validate([
            'origin_station_id'      => 'required|exists:parcel_stations,id',
            'destination_station_id' => 'required|exists:parcel_stations,id|different:origin_station_id',
            'base_price_per_kg'      => 'required|numeric|min:0',
            'min_price'              => 'required|numeric|min:0',
        ]);

        $route = ParcelRoute::create([
            'business_id'            => $business_id,
            'origin_station_id'      => $request->origin_station_id,
            'destination_station_id' => $request->destination_station_id,
            'base_price_per_kg'      => $request->base_price_per_kg,
            'min_price'              => $request->min_price,
            'estimated_hours'        => $request->estimated_hours,
            'is_active'              => true,
        ]);

        // Also create reverse route if requested
        if ($request->boolean('create_reverse')) {
            ParcelRoute::firstOrCreate(
                ['business_id' => $business_id, 'origin_station_id' => $request->destination_station_id, 'destination_station_id' => $request->origin_station_id],
                ['base_price_per_kg' => $request->base_price_per_kg, 'min_price' => $request->min_price, 'estimated_hours' => $request->estimated_hours, 'is_active' => true]
            );
        }

        // Save pricing rules if provided
        if (!empty($request->rules)) {
            foreach ($request->rules as $rule) {
                if (!empty($rule['weight_max_kg'])) {
                    PricingRule::create([
                        'route_id'       => $route->id,
                        'weight_min_kg'  => $rule['weight_min_kg'] ?? 0,
                        'weight_max_kg'  => $rule['weight_max_kg'],
                        'price_per_kg'   => $rule['price_per_kg'] ?? 0,
                        'flat_fee'       => $rule['flat_fee'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('parcel.routes.index')
            ->with('status', ['success' => 1, 'msg' => 'Route created successfully.']);
    }

    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $route = ParcelRoute::where('business_id', $business_id)->findOrFail($id);
        PricingRule::where('route_id', $id)->delete();
        $route->delete();
        return redirect()->route('parcel.routes.index')
            ->with('status', ['success' => 1, 'msg' => 'Route deleted.']);
    }

    /** AJAX: calculate price for a given route + weight */
    public function calculatePrice(Request $request)
    {
        $route = ParcelRoute::find($request->route_id);
        if (!$route) return response()->json(['price' => 0]);
        $price = app(PricingEngine::class)->calculate($route, (float)$request->weight_kg);
        return response()->json(['price' => round($price, 2)]);
    }
}
