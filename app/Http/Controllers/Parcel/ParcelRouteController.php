<?php

namespace App\Http\Controllers\Parcel;

use App\Http\Controllers\Controller;
use App\ParcelRoute;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ParcelRouteController extends Controller
{
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $routes = ParcelRoute::where('business_id', $business_id)->get();
            return DataTables::of($routes)
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-xs btn-primary edit-route-btn" data-id="' . $row->id . '"><i class="fa fa-edit"></i></button> '
                        . '<button class="btn btn-xs btn-danger delete-route-btn" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                })
                ->editColumn('is_active', fn($r) => $r->is_active
                    ? '<span class="label label-success">Active</span>'
                    : '<span class="label label-danger">Inactive</span>')
                ->editColumn('base_price', fn($r) => 'KES ' . number_format($r->base_price, 2))
                ->editColumn('price_per_kg', fn($r) => 'KES ' . number_format($r->price_per_kg, 2))
                ->editColumn('min_price', fn($r) => 'KES ' . number_format($r->min_price, 2))
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }

        return view('parcel.routes.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_town'   => 'required|string|max:100',
            'to_town'     => 'required|string|max:100',
            'base_price'  => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'min_price'   => 'required|numeric|min:0',
        ]);

        $business_id = request()->session()->get('user.business_id');
        $route = ParcelRoute::create(array_merge($request->all(), ['business_id' => $business_id]));

        return response()->json(['success' => true, 'data' => $route, 'msg' => 'Route created.']);
    }

    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $route = ParcelRoute::where('business_id', $business_id)->findOrFail($id);
        return response()->json($route);
    }

    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $route = ParcelRoute::where('business_id', $business_id)->findOrFail($id);
        $route->update($request->all());
        return response()->json(['success' => true, 'data' => $route, 'msg' => 'Route updated.']);
    }

    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        ParcelRoute::where('business_id', $business_id)->findOrFail($id)->delete();
        return response()->json(['success' => true, 'msg' => 'Route deleted.']);
    }

    /**
     * Calculate price for a given route + weight (AJAX helper used in booking form)
     */
    public function calculatePrice(Request $request)
    {
        $route = ParcelRoute::find($request->route_id);
        if (! $route) return response()->json(['price' => 0]);

        $price = $route->calculatePrice(
            (float) $request->weight_kg,
            $request->service_type ?? 'standard'
        );

        return response()->json([
            'price'           => $price,
            'base_price'      => $route->base_price,
            'price_per_kg'    => $route->price_per_kg,
            'min_price'       => $route->min_price,
            'transit_days'    => $route->transit_days,
        ]);
    }
}
