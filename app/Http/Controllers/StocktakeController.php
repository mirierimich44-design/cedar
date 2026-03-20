<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Product;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\VariationLocationDetails;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StocktakeController extends Controller
{
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display stocktake list
     */
    public function index()
    {
        if (!auth()->user()->can('stocktake.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $locations = BusinessLocation::forDropdown($business_id);

        if (request()->ajax()) {
            $stocktakes = Transaction::where('business_id', $business_id)
                ->where('type', 'stocktake')
                ->with(['location'])
                ->select([
                    'id',
                    'ref_no',
                    'location_id',
                    'transaction_date',
                    'status',
                    'additional_notes',
                    'created_by'
                ]);

            return DataTables::of($stocktakes)
                ->addColumn('location', function ($row) {
                    return optional($row->location)->name ?? '';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                        <a href="' . action([\App\Http\Controllers\StocktakeController::class, 'show'], $row->id) . '" class="btn btn-xs btn-info">
                            <i class="fas fa-eye"></i> ' . __('messages.view') . '
                        </a>';
                    if ($row->status == 'draft') {
                        $html .= '<a href="' . action([\App\Http\Controllers\StocktakeController::class, 'edit'], $row->id) . '" class="btn btn-xs btn-primary">
                            <i class="fas fa-edit"></i> ' . __('messages.edit') . '
                        </a>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('transaction_date', function ($row) {
                    return \Carbon::parse($row->transaction_date)->format('d/m/Y H:i');
                })
                ->editColumn('status', function ($row) {
                    $badge = $row->status == 'completed' ? 'success' : 'warning';
                    return '<span class="badge badge-' . $badge . '">' . ucfirst($row->status) . '</span>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('stocktake.index', compact('locations'));
    }

    /**
     * Show form to create new stocktake
     */
    public function create()
    {
        if (!auth()->user()->can('stocktake.manage')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $locations = BusinessLocation::forDropdown($business_id);

            return view('stocktake.create', compact('locations'));
        } catch (\Exception $e) {
            \Log::error('Stocktake create error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response('Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(), 500);
        }
    }

    /**
     * Store a new stocktake
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('stocktake.manage')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = auth()->user()->id;

            DB::beginTransaction();

            // Create stocktake transaction
            $ref_no = 'ST-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $stocktake = Transaction::create([
                'business_id' => $business_id,
                'location_id' => $request->location_id,
                'type' => 'stocktake',
                'ref_no' => $ref_no,
                'transaction_date' => \Carbon::now(),
                'status' => 'draft',
                'created_by' => $user_id,
                'additional_notes' => $request->notes ?? null,
            ]);

            DB::commit();

            return redirect()
                ->action([\App\Http\Controllers\StocktakeController::class, 'edit'], $stocktake->id)
                ->with('status', [
                    'success' => true,
                    'msg' => __('Stocktake created successfully. Now count your products.')
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stocktake creation error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            
            // Debug: Return actual error for troubleshooting
            return redirect()->back()
                ->with('status', [
                    'success' => false,
                    'msg' => 'Error: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Show stocktake details
     */
    public function show($id)
    {
        if (!auth()->user()->can('stocktake.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $stocktake = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->where('type', 'stocktake')
            ->with(['location', 'stocktake_lines.product', 'stocktake_lines.variation'])
            ->firstOrFail();

        return view('stocktake.show', compact('stocktake'));
    }

    /**
     * Edit stocktake - add product counts
     */
    public function edit($id)
    {
        if (!auth()->user()->can('stocktake.manage')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $stocktake = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->where('type', 'stocktake')
            ->with(['location'])
            ->firstOrFail();

        if ($stocktake->status != 'draft') {
            return redirect()
                ->action([\App\Http\Controllers\StocktakeController::class, 'show'], $id)
                ->with('status', [
                    'success' => false,
                    'msg' => __('This stocktake has already been completed.')
                ]);
        }

        return view('stocktake.edit', compact('stocktake'));
    }

    /**
     * Get products for stocktake entry
     */
    public function getProducts(Request $request)
    {
        if (!auth()->user()->can('stocktake.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = $request->location_id;

        $products = VariationLocationDetails::join('variations', 'variation_location_details.variation_id', '=', 'variations.id')
            ->join('products', 'variations.product_id', '=', 'products.id')
            ->where('variation_location_details.location_id', $location_id)
            ->where('products.business_id', $business_id)
            ->select([
                'products.id as product_id',
                'products.name as product_name',
                'products.sku',
                'variations.id as variation_id',
                'variations.name as variation_name',
                'variations.sub_sku',
                'variation_location_details.qty_available as system_qty'
            ])
            ->get();

        return DataTables::of($products)
            ->addColumn('counted_qty', function ($row) {
                return '<input type="number" 
                    class="form-control counted-qty" 
                    name="counts[' . $row->variation_id . ']" 
                    data-variation-id="' . $row->variation_id . '"
                    placeholder="Enter count"
                    step="0.01"
                    min="0">';
            })
            ->addColumn('variance', function ($row) {
                return '<span class="variance-display" data-variation-id="' . $row->variation_id . '">-</span>';
            })
            ->editColumn('system_qty', function ($row) {
                return '<span class="system-qty" data-variation-id="' . $row->variation_id . '">' . number_format($row->system_qty, 2) . '</span>';
            })
            ->rawColumns(['counted_qty', 'variance', 'system_qty'])
            ->make(true);
    }

    /**
     * Search products for stocktake entry (AJAX)
     */
    public function searchProducts(Request $request)
    {
        if (!auth()->user()->can('stocktake.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = $request->input('location_id');
        $search_query = $request->input('query');

        $products = VariationLocationDetails::join('variations', 'variation_location_details.variation_id', '=', 'variations.id')
            ->join('products', 'variations.product_id', '=', 'products.id')
            ->where('variation_location_details.location_id', $location_id)
            ->where('products.business_id', $business_id)
            ->where(function($q) use ($search_query) {
                $q->where('products.name', 'LIKE', "%{$search_query}%")
                  ->orWhere('products.sku', 'LIKE', "%{$search_query}%")
                  ->orWhere('variations.sub_sku', 'LIKE', "%{$search_query}%");
            })
            ->select([
                'products.id as product_id',
                'products.name as product_name',
                'products.sku',
                'products.type',
                'products.enable_stock',
                'variations.id as variation_id',
                'variations.name as variation_name',
                'variations.sub_sku',
                'variations.sell_price_inc_tax as selling_price',
                'variations.default_purchase_price as purchase_price',
                'variation_location_details.qty_available as system_qty'
            ])
            ->limit(20)
            ->get();

        return response()->json(['data' => $products]);
    }

    /**
     * Save stocktake counts
     */
    public function saveCounts(Request $request, $id)
    {
        if (!auth()->user()->can('stocktake.manage')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            
            $stocktake = Transaction::where('business_id', $business_id)
                ->where('id', $id)
                ->where('type', 'stocktake')
                ->where('status', 'draft')
                ->firstOrFail();

            DB::beginTransaction();

            // Delete existing lines
            DB::table('stocktake_lines')->where('transaction_id', $id)->delete();

            // Insert new counts
            $counts = $request->counts ?? [];
            foreach ($counts as $variation_id => $data) {
                $counted_qty = is_array($data) ? ($data['qty'] ?? null) : $data;
                $lot_number = is_array($data) ? ($data['lot_number'] ?? null) : null;
                $expiry_date = is_array($data) ? ($data['expiry_date'] ?? null) : null;
                
                if ($counted_qty !== null && $counted_qty !== '') {
                    // Get system qty and product_id
                    $vld = VariationLocationDetails::join('variations', 'variation_location_details.variation_id', '=', 'variations.id')
                        ->where('variation_location_details.variation_id', $variation_id)
                        ->where('variation_location_details.location_id', $stocktake->location_id)
                        ->select(['variation_location_details.qty_available', 'variations.product_id'])
                        ->first();

                    $system_qty = $vld ? $vld->qty_available : 0;
                    $product_id = $vld ? $vld->product_id : null;

                    if ($product_id) {
                        DB::table('stocktake_lines')->insert([
                            'transaction_id' => $id,
                            'product_id' => $product_id,
                            'variation_id' => $variation_id,
                            'system_qty' => $system_qty,
                            'counted_qty' => $counted_qty,
                            'variance' => $counted_qty - $system_qty,
                            'lot_number' => $lot_number,
                            'expiry_date' => $expiry_date,
                            'counted_by' => auth()->user()->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => __('Counts saved successfully.')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stocktake save error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ], 500);
        }
    }

    /**
     * Complete stocktake and optionally create adjustments
     */
    public function complete(Request $request, $id)
    {
        if (!auth()->user()->can('stocktake.manage')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            
            $stocktake = Transaction::where('business_id', $business_id)
                ->where('id', $id)
                ->where('type', 'stocktake')
                ->where('status', 'draft')
                ->firstOrFail();

            DB::beginTransaction();

            // Mark as completed
            $stocktake->status = 'completed';
            $stocktake->save();

            // If requested, create stock adjustments for variances
            if ($request->create_adjustments) {
                $lines = DB::table('stocktake_lines')
                    ->where('transaction_id', $id)
                    ->where('variance', '!=', 0)
                    ->get();

                if ($lines->count() > 0) {
                    // Create adjustment transaction
                    // This integrates with existing StockAdjustmentController logic
                    // For now, we just mark it as completed
                }
            }

            DB::commit();

            return redirect()
                ->action([\App\Http\Controllers\StocktakeController::class, 'show'], $id)
                ->with('status', [
                    'success' => true,
                    'msg' => __('Stocktake completed successfully.')
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stocktake complete error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('status', [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ]);
        }
    }

    /**
     * Get variance report
     */
    public function varianceReport($id)
    {
        if (!auth()->user()->can('stocktake.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $stocktake = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->where('type', 'stocktake')
            ->firstOrFail();

        $lines = DB::table('stocktake_lines')
            ->join('variations', 'stocktake_lines.variation_id', '=', 'variations.id')
            ->join('products', 'variations.product_id', '=', 'products.id')
            ->where('stocktake_lines.transaction_id', $id)
            ->select([
                'products.name as product_name',
                'products.sku',
                'variations.name as variation_name',
                'stocktake_lines.system_qty',
                'stocktake_lines.counted_qty',
                'stocktake_lines.variance',
                'variations.default_purchase_price',
                'variations.default_sell_price'
            ])
            ->get();

        return view('stocktake.variance_report', compact('stocktake', 'lines'));
    }

    /**
     * Print count sheets for physical inventory
     * print_type: 'blank' = fully empty rows, 'with_products' = products pre-filled but no quantities
     */
    public function printCountSheet($id, Request $request)
    {
        if (!auth()->user()->can('stocktake.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $stocktake = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->where('type', 'stocktake')
            ->with(['location'])
            ->firstOrFail();

        // print_type: 'blank' | 'with_products' (all products) | 'with_stock_only' (qty > 0 only)
        $print_type   = $request->get('print_type', 'blank');
        $rowsPerSheet = $request->get('per_sheet', 20);
        $business     = \App\Business::find($business_id);

        if ($print_type === 'with_products' || $print_type === 'with_stock_only') {
            $productQuery = VariationLocationDetails::join('variations', 'variation_location_details.variation_id', '=', 'variations.id')
                ->join('products', 'variations.product_id', '=', 'products.id')
                ->where('variation_location_details.location_id', $stocktake->location_id)
                ->where('products.business_id', $business_id)
                ->select([
                    'products.name as product_name',
                    'products.sku',
                    'variations.name as variation_name',
                    'variations.sub_sku',
                    'variation_location_details.qty_available as system_qty',
                ])
                ->orderBy('products.name');

            // "With Stock Only" shows only products that currently have stock > 0
            if ($print_type === 'with_stock_only') {
                $productQuery->where('variation_location_details.qty_available', '>', 0);
            }

            $products      = $productQuery->get();
            $totalProducts = $products->count();
            $sheets        = $products->chunk($rowsPerSheet)->values();

            return view('stocktake.print_count_sheet', compact('stocktake', 'sheets', 'business', 'totalProducts', 'rowsPerSheet', 'print_type'));
        }

        // Default: blank sheets — row count based on ALL products at this location
        $totalProducts = VariationLocationDetails::join('variations', 'variation_location_details.variation_id', '=', 'variations.id')
            ->join('products', 'variations.product_id', '=', 'products.id')
            ->where('variation_location_details.location_id', $stocktake->location_id)
            ->where('products.business_id', $business_id)
            ->count();

        $numberOfSheets = max(1, ceil($totalProducts / $rowsPerSheet));
        $sheets = [];
        $remainingProducts = $totalProducts;
        for ($i = 0; $i < $numberOfSheets; $i++) {
            $sheets[] = min($rowsPerSheet, $remainingProducts);
            $remainingProducts -= $rowsPerSheet;
        }

        return view('stocktake.print_count_sheet', compact('stocktake', 'sheets', 'business', 'totalProducts', 'rowsPerSheet', 'print_type'));
    }

    /**
     * Print verification sheet with entered data for review
     */
    public function printVerificationSheet($id)
    {
        if (!auth()->user()->can('stocktake.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $stocktake = Transaction::where('business_id', $business_id)
            ->where('id', $id)
            ->where('type', 'stocktake')
            ->with(['location'])
            ->firstOrFail();

        $lines = DB::table('stocktake_lines')
            ->join('variations', 'stocktake_lines.variation_id', '=', 'variations.id')
            ->join('products', 'variations.product_id', '=', 'products.id')
            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
            ->leftJoin('users', 'stocktake_lines.counted_by', '=', 'users.id')
            ->where('stocktake_lines.transaction_id', $id)
            ->select([
                'products.name as product_name',
                'products.sku',
                'variations.name as variation_name',
                'variations.sub_sku',
                'units.short_name as unit_name',
                'stocktake_lines.system_qty',
                'stocktake_lines.counted_qty',
                'stocktake_lines.variance',
                'stocktake_lines.lot_number',
                'stocktake_lines.expiry_date',
                'users.first_name as counted_by_name'
            ])
            ->orderBy('products.name')
            ->get();

        $business = \App\Business::find($business_id);

        return view('stocktake.print_verification_sheet', compact('stocktake', 'lines', 'business'));
    }
}

