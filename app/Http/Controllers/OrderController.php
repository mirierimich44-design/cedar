<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\PosOrder;
use App\PosOrderLine;
use App\Product;
use App\Variation;
use App\Utils\ProductUtil;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;

    /**
     * Constructor
     *
     * @param ProductUtil $productUtil
     * @return void
     */
    public function __construct(ProductUtil $productUtil)
    {
        $this->productUtil = $productUtil;
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('orders.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $orders = PosOrder::forBusiness($business_id)
                ->leftJoin('contacts', 'pos_orders.contact_id', '=', 'contacts.id')
                ->leftJoin('business_locations', 'pos_orders.location_id', '=', 'business_locations.id')
                ->leftJoin('users', 'pos_orders.created_by', '=', 'users.id')
                ->with(['orderLines.product', 'orderLines.variation'])
                ->select([
                    'pos_orders.*',
                    'contacts.name as contact_name',
                    'business_locations.name as location_name',
                    'users.first_name as user_first_name'
                ]);

            if (!empty($request->location_id)) {
                $orders->where('pos_orders.location_id', $request->location_id);
            }

            if (!empty($request->status)) {
                $orders->where('pos_orders.status', $request->status);
            }

            return DataTables::of($orders)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">';
                    $html .= '<button type="button" class="btn btn-info btn-xs view-order" data-id="' . $row->id . '"><i class="fa fa-eye"></i></button>';
                    if (auth()->user()->can('orders.update')) {
                        $html .= '<a href="' . action([\App\Http\Controllers\OrderController::class, 'edit'], [$row->id]) . '" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></a>';
                    }
                    if (auth()->user()->can('orders.delete')) {
                        $html .= '<button type="button" class="btn btn-danger btn-xs delete-order" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->editColumn('status', function ($row) {
                    $statusClasses = [
                        'pending' => 'bg-yellow',
                        'processing' => 'bg-blue',
                        'completed' => 'bg-green',
                        'cancelled' => 'bg-red',
                    ];
                    $class = $statusClasses[$row->status] ?? 'bg-gray';
                    return '<span class="label ' . $class . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('items_count', function ($row) {
                    return $row->orderLines->count();
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->contact_name ?: '-';
                })
                ->addColumn('location_name_display', function ($row) {
                    return $row->location_name ?: '-';
                })
                ->addColumn('created_by_name', function ($row) {
                    return $row->user_first_name ?: '-';
                })
                ->filterColumn('customer_name', function ($query, $keyword) {
                    $query->where('contacts.name', 'like', "%{$keyword}%");
                })
                ->filterColumn('location_name_display', function ($query, $keyword) {
                    $query->where('business_locations.name', 'like', "%{$keyword}%");
                })
                ->filterColumn('created_by_name', function ($query, $keyword) {
                    $query->where('users.first_name', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $locations = BusinessLocation::forDropdown($business_id);
        $statuses = [
            'pending' => __('lang_v1.pending'),
            'processing' => __('lang_v1.processing'),
            'completed' => __('lang_v1.completed'),
            'cancelled' => __('lang_v1.cancelled'),
        ];

        return view('orders.index', compact('locations', 'statuses'));
    }

    /**
     * Store a new order from POS.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('orders.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = auth()->user()->id;

            DB::beginTransaction();

            // Check if an order already exists for this location and business for today (Consolidated Daily Order)
            $today = \Carbon::now()->format('Y-m-d');
            $existingOrder = PosOrder::where('business_id', $business_id)
                ->where('location_id', $request->location_id)
                ->whereDate('created_at', $today)
                ->where('status', '!=', 'cancelled') // strict consolidation, maybe exclude cancelled?
                ->first();

            if ($existingOrder) {
                $order = $existingOrder;
                // Update timestamp to show latest activity? Optional. 
                // $order->touch(); 
            } else {
                // Generate reference number
                $ref_count = PosOrder::forBusiness($business_id)->count() + 1;
                $ref_no = 'ORD-' . str_pad($ref_count, 5, '0', STR_PAD_LEFT);

                $order = PosOrder::create([
                    'business_id' => $business_id,
                    'location_id' => $request->location_id,
                    'contact_id' => $request->contact_id,
                    'created_by' => $user_id,
                    'ref_no' => $ref_no,
                    'status' => 'pending',
                    'notes' => $request->notes,
                ]);
            }

            // Add order lines (Append to existing or new)
            if (!empty($request->products)) {
                foreach ($request->products as $product) {
                    // Check if this product/variation already exists in the order to just increment quantity?
                    // Implementation Plan said "Append", but usually consolidation implies merging same items too.
                    // For now, let's append as separate lines to preserve individual scan history if needed, 
                    // OR merge if exact match. Merging is cleaner for a "Daily Summary".
                    
                    $productId = $product['product_id'] ?? null;
                    $variationId = $product['variation_id'] ?? null;
                    $isCustom = !empty($product['is_custom']) || empty($productId);
                    $customName = $isCustom ? ($product['custom_product_name'] ?? 'Custom Item') : null;
                    $quantity = $product['quantity'] ?? 1;
                    $unitPrice = $product['unit_price'] ?? null;

                    if ($isCustom) {
                         $lineData = [
                            'order_id' => $order->id,
                            'product_id' => null,
                            'variation_id' => null,
                            'custom_product_name' => $customName,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                        ];
                        PosOrderLine::create($lineData);
                    } else {
                        // Try to find existing line for same product/variation in this order
                        $existingLine = PosOrderLine::where('order_id', $order->id)
                            ->where('product_id', $productId)
                            ->where('variation_id', $variationId)
                            ->first();

                        if ($existingLine) {
                            $existingLine->quantity += $quantity;
                            // Update price if it changed? usually weighted average or just keep last? 
                            // Let's keep the incoming price if distinct, but here we are merging. 
                            // Determine behavior: Simple increment.
                            $existingLine->save();
                        } else {
                            $lineData = [
                                'order_id' => $order->id,
                                'product_id' => $productId,
                                'variation_id' => $variationId,
                                'custom_product_name' => null,
                                'quantity' => $quantity,
                                'unit_price' => $unitPrice,
                            ];
                            PosOrderLine::create($lineData);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => $existingOrder ? __('lang_v1.order_updated_successfully') : __('lang_v1.order_created_successfully'),
                'order_id' => $order->id,
                'ref_no' => $order->ref_no,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('Order Store Error - File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            \Log::error($request->all());
            return response()->json([
                'success' => false,
                'msg' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Update an existing order.
     */
    /**
     * Show the form for editing the specified order.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('orders.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $order = PosOrder::forBusiness($business_id)
            ->with(['location', 'contact', 'orderLines.product', 'orderLines.variation'])
            ->findOrFail($id);

        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('orders.edit', compact('order', 'business_locations'));
    }

    /**
     * Update an existing order.
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('orders.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            DB::beginTransaction();

            $order = PosOrder::forBusiness($business_id)->findOrFail($id);
            $old_status = $order->status;

            $order->update([
                'notes' => $request->notes,
                // 'contact_id' => $request->contact_id, // Usually contact doesn't change for daily order? or maybe it does. kept as is.
                'status' => $request->status ?? $order->status,
            ]);

            // Handle Admin Edit Form Submit
            if ($request->has('order_lines')) {
                $submittedLines = $request->input('order_lines');
                $submittedLineIds = array_keys($submittedLines);
                
                // Delete lines that were removed in the UI
                $linesToDelete = PosOrderLine::where('order_id', $order->id)
                                             ->whereNotIn('id', $submittedLineIds)
                                             ->get();
                foreach($linesToDelete as $lineToDelete) {
                    $lineToDelete->delete();
                }
                
                foreach ($submittedLines as $lineId => $data) {
                    $line = PosOrderLine::find($lineId);
                    if ($line && $line->order_id == $order->id) {
                        if (isset($data['quantity'])) $line->quantity = $data['quantity'];
                        if (isset($data['unit_price'])) $line->unit_price = $data['unit_price'];
                        $line->save();
                    }
                }
                
                // Recalculate order total amount
                $order->total_amount = PosOrderLine::where('order_id', $order->id)
                    ->get()
                    ->sum(function ($line) {
                        return $line->quantity * $line->unit_price;
                    });
                $order->save();
            } else {
                // If order_lines is completely empty (all items removed)
                PosOrderLine::where('order_id', $order->id)->delete();
                $order->total_amount = 0;
                $order->save();
            }

            // Deduct stock if marked as completed
            if ($old_status !== 'completed' && $order->status === 'completed') {
                $order->load('orderLines.product'); // refresh relation
                foreach ($order->orderLines as $line) {
                    if ($line->product && $line->product->enable_stock == 1) {
                        $this->productUtil->decreaseProductQuantity(
                            $line->product_id,
                            $line->variation_id,
                            $order->location_id,
                            $line->quantity,
                            0
                        );
                    }
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'msg' => __('lang_v1.order_updated_successfully'),
                    'order_id' => $order->id,
                    'ref_no' => $order->ref_no,
                ]);
            } else {
                 return redirect()->action([\App\Http\Controllers\ReportController::class, 'getOrdersReport'])
                    ->with('status', ['success' => 1, 'msg' => __('lang_v1.order_updated_successfully')]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('Order Update Error - File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Error: ' . $e->getMessage(),
                ]);
            } else {
                return back()->with('status', ['success' => 0, 'msg' => __('messages.something_went_wrong')]);
            }
        }
    }

    /**
     * Get order details.
     */
    public function show($id)
    {
        if (!auth()->user()->can('orders.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $order = PosOrder::forBusiness($business_id)
            ->with(['location', 'contact', 'createdBy', 'orderLines.product', 'orderLines.variation'])
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    public function getOrderDetailsJson($id)
    {
        if (!auth()->user()->can('orders.view') && !auth()->user()->can('orders.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $order = PosOrder::forBusiness($business_id)
            ->with(['orderLines.product', 'orderLines.variation'])
            ->findOrFail($id);

        $lines = $order->orderLines->groupBy(function($line) {
            return $line->variation_id ?? ('custom_' . $line->custom_product_name);
        })->map(function($group) {
            $first = $group->first();
            return [
                'product_id' => $first->product_id,
                'variation_id' => $first->variation_id,
                'is_custom' => $first->product_id ? false : true,
                'name' => $first->product ? $first->product->name : ($first->custom_product_name ?? ''),
                'variation' => $first->variation ? $first->variation->name : '',
                'sub_sku' => $first->product ? $first->product->sub_sku : '',
                'quantity' => $group->sum('quantity'),
                'unit_price' => $first->unit_price
            ];
        })->values();

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'notes' => $order->notes,
                'contact_id' => $order->contact_id,
                'lines' => $lines
            ]
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        if (!auth()->user()->can('orders.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $order = PosOrder::forBusiness($business_id)->findOrFail($id);
            $old_status = $order->status;
            $order->status = $request->status;
            $order->save();

            // Deduct stock if marked as completed
            if ($old_status !== 'completed' && $order->status === 'completed') {
                foreach ($order->orderLines as $line) {
                    if ($line->product->enable_stock == 1) {
                        $this->productUtil->decreaseProductQuantity(
                            $line->product_id,
                            $line->variation_id,
                            $order->location_id,
                            $line->quantity,
                            0
                        );
                    }
                }
            }

            return response()->json([
                'success' => true,
                'msg' => __('lang_v1.status_updated_successfully'),
            ]);
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ]);
        }
    }

    /**
     * Delete an order.
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('orders.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $order = PosOrder::forBusiness($business_id)->findOrFail($id);
            $order->delete();

            return response()->json([
                'success' => true,
                'msg' => __('lang_v1.order_deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ]);
        }
    }

    /**
     * Search products for order (AJAX).
     */
    public function searchProducts(Request $request)
    {
        if (!auth()->user()->can('orders.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = $request->location_id;
        $query = $request->input('query');

        $search_fields = ['name', 'sku', 'sub_sku'];
        $result = $this->productUtil->filterProduct($business_id, $query, $location_id, null, null, [], $search_fields);

        return response()->json($result);
    }

    /**
     * Get orders for POS modal (simple list).
     */
    public function getOrdersForPos(Request $request)
    {
        if (!auth()->user()->can('orders.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = $request->location_id;

        $orders = PosOrder::forBusiness($business_id)
            ->where('location_id', $location_id)
            ->whereIn('status', ['pending', 'processing'])
            ->with(['contact', 'orderLines.product', 'orderLines.variation'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('sale_pos.partials.orders_list', compact('orders'));
    }

    /**
     * Print order as invoice (opens in new window).
     */
    public function printOrder($id)
    {
        if (!auth()->user()->can('orders.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $order = PosOrder::forBusiness($business_id)
            ->with(['location', 'createdBy', 'orderLines.product', 'orderLines.variation'])
            ->findOrFail($id);

        $business = \App\Business::find($business_id);

        return view('orders.print', compact('order', 'business'));
    }

    /**
     * Get order receipt HTML for printing in same window (like POS receipt).
     */
    public function getOrderReceipt($id)
    {
        if (!auth()->user()->can('orders.view')) {
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized action.',
            ]);
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $order = PosOrder::forBusiness($business_id)
                ->with(['location', 'createdBy', 'orderLines.product', 'orderLines.variation'])
                ->findOrFail($id);

            $business = \App\Business::find($business_id);

            $html = view('orders.receipt', compact('order', 'business'))->render();

            return response()->json([
                'success' => true,
                'html_content' => $html,
                'print_title' => 'Shop Order - ' . $order->ref_no,
            ]);
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ]);
        }
    }
}
