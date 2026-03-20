<?php

namespace App\Http\Controllers;

use App\LostSale;
use App\BusinessLocation;
use App\VariationLocationDetails;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LostSaleController extends Controller
{
    /**
     * Record a lost sale from POS.
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 403);
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            LostSale::create([
                'business_id'  => $business_id,
                'location_id'  => $request->location_id ?: null,
                'product_id'   => $request->product_id ?: null,
                'variation_id' => $request->variation_id ?: null,
                'product_name' => $request->product_name,
                'sku'          => $request->sku,
                'selling_price'=> $request->selling_price ?? 0,
                'quantity'     => $request->quantity ?? 1,
                'notes'        => $request->notes,
                'created_by'   => auth()->user()->id,
            ]);

            return response()->json(['success' => true, 'msg' => 'Lost sale recorded.']);
        } catch (\Exception $e) {
            \Log::error('Lost sale record error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'Failed to record lost sale.'], 500);
        }
    }

    /**
     * Search products for the Lost Sale modal on POS.
     * No stocktake.view permission required — any authenticated POS user can search.
     */
    public function search(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['data' => []], 403);
        }

        $business_id  = $request->session()->get('user.business_id');
        $location_id  = $request->input('location_id');
        $search_query = $request->input('query');

        if (empty($search_query)) {
            return response()->json(['data' => []]);
        }

        // Use DB::table with LEFT JOIN so products appear even without location_details entries
        $products = \DB::table('products')
            ->join('variations', 'products.id', '=', 'variations.product_id')
            ->leftJoin('variation_location_details', function ($join) use ($location_id) {
                $join->on('variations.id', '=', 'variation_location_details.variation_id');
                if (!empty($location_id)) {
                    $join->where('variation_location_details.location_id', '=', $location_id);
                }
            })
            ->where('products.business_id', $business_id)
            ->where(function ($q) use ($search_query) {
                $q->where('products.name', 'LIKE', "%{$search_query}%")
                  ->orWhere('products.sku', 'LIKE', "%{$search_query}%")
                  ->orWhere('variations.sub_sku', 'LIKE', "%{$search_query}%");
            })
            ->select([
                'products.id as product_id',
                'products.name as product_name',
                'products.sku',
                'variations.id as variation_id',
                'variations.name as variation_name',
                'variations.sub_sku',
                'variations.sell_price_inc_tax as selling_price',
                \DB::raw('COALESCE(variation_location_details.qty_available, 0) as system_qty'),
            ])
            ->distinct()
            ->orderBy('products.name')
            ->limit(20)
            ->get();

        return response()->json(['data' => $products]);
    }

    /**
     * Lost Sales Report page.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $locations   = BusinessLocation::forDropdown($business_id, true);

        if ($request->ajax()) {
            try {
                $query = LostSale::query()
                    ->where('lost_sales.business_id', $business_id)
                    ->leftJoin('business_locations', 'lost_sales.location_id', '=', 'business_locations.id')
                    ->leftJoin('users', 'lost_sales.created_by', '=', 'users.id')
                    ->select([
                        'lost_sales.id',
                        'lost_sales.created_at',
                        'lost_sales.product_name',
                        'lost_sales.sku',
                        'lost_sales.selling_price',
                        'lost_sales.quantity',
                        'lost_sales.notes',
                        'business_locations.name as location_name',
                        \DB::raw("CONCAT(users.first_name, ' ', COALESCE(users.last_name, '')) as created_by_name"),
                    ]);

                if ($request->filled('location_id')) {
                    $query->where('lost_sales.location_id', $request->location_id);
                }

                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $query->whereDate('lost_sales.created_at', '>=', $request->start_date)
                          ->whereDate('lost_sales.created_at', '<=', $request->end_date);
                }

                if ($request->filled('search_product')) {
                    $kw = $request->search_product;
                    $query->where(function ($q) use ($kw) {
                        $q->where('lost_sales.product_name', 'like', "%$kw%")
                          ->orWhere('lost_sales.sku', 'like', "%$kw%");
                    });
                }

                return DataTables::of($query)
                    ->editColumn('created_at', function ($r) {
                        return \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i');
                    })
                    ->editColumn('selling_price', function ($r) {
                        return number_format($r->selling_price, 2);
                    })
                    ->editColumn('quantity', function ($r) {
                        return number_format($r->quantity, 2);
                    })
                    ->addColumn('total_value', function ($r) {
                        return number_format($r->selling_price * $r->quantity, 2);
                    })
                    ->rawColumns([])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('Lost sales report error: ' . $e->getMessage());
                return response()->json([
                    'draw'            => intval($request->draw),
                    'recordsTotal'    => 0,
                    'recordsFiltered' => 0,
                    'data'            => [],
                    'error'           => 'Failed to load data: ' . $e->getMessage(),
                ], 200);
            }
        }

        return view('report.lost_sales', compact('locations'));
    }
}
