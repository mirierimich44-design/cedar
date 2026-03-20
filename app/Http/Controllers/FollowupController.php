<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Followup;
use App\Product;
use App\Variation;
use App\Utils\ProductUtil;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FollowupController extends Controller
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
     * Display a listing of follow-ups.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('followups.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $followups = Followup::forBusiness($business_id)
                ->leftJoin('products', 'followups.product_id', '=', 'products.id')
                ->leftJoin('variations', 'followups.variation_id', '=', 'variations.id')
                ->leftJoin('business_locations', 'followups.location_id', '=', 'business_locations.id')
                ->leftJoin('users', 'followups.created_by', '=', 'users.id')
                ->select([
                    'followups.*',
                    'products.name as product_name_raw',
                    'variations.name as variation_name',
                    'business_locations.name as location_name_raw',
                    'users.first_name as user_first_name'
                ]);

            if (!empty($request->location_id)) {
                $followups->where('followups.location_id', $request->location_id);
            }

            if (!empty($request->status)) {
                $followups->where('followups.status', $request->status);
            }

            return DataTables::of($followups)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">';
                    if (auth()->user()->can('followups.update')) {
                        $html .= '<button type="button" class="btn btn-primary btn-xs edit-followup-status" data-id="' . $row->id . '" data-status="' . $row->status . '"><i class="fa fa-edit"></i></button>';
                    }
                    if (auth()->user()->can('followups.delete')) {
                        $html .= '<button type="button" class="btn btn-danger btn-xs delete-followup" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                    }
                    $html .= '</div>';
                    // Recipient: Location mobile or Business Owner's contact number (Internal)
                    $business = \App\Business::find($row->business_id);
                    $location = \App\BusinessLocation::find($row->location_id);
                    $recipient_number = !empty($location->mobile) ? $location->mobile : ($business->owner ? $business->owner->contact_number : null);
                    
                    if (!empty($recipient_number)) {
                        $html .= ' <a target="_blank" href="https://wa.me/' . $recipient_number . '?text=' . urlencode(__('lang_v1.followup_reminder_message', ['product' => $row->product_name_raw, 'customer' => $row->customer_name])) . '" class="btn btn-success btn-xs"><i class="fab fa-whatsapp"></i></a>';
                    }
                    return $html;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->editColumn('status', function ($row) {
                    $statusClasses = [
                        'pending' => 'bg-yellow',
                        'contacted' => 'bg-blue',
                        'resolved' => 'bg-green',
                    ];
                    $class = $statusClasses[$row->status] ?? 'bg-gray';
                    return '<span class="label ' . $class . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('product_name', function ($row) {
                    $name = $row->product_name_raw ?: '-';
                    if ($row->variation_name && $row->variation_name !== 'DUMMY') {
                        $name .= ' (' . $row->variation_name . ')';
                    }
                    return $name;
                })
                ->addColumn('location_name', function ($row) {
                    return $row->location_name_raw ?: '-';
                })
                ->addColumn('created_by_name', function ($row) {
                    return $row->user_first_name ?: '-';
                })
                ->filterColumn('product_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('products.name', 'like', "%{$keyword}%")
                          ->orWhere('variations.name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('location_name', function ($query, $keyword) {
                    $query->where('business_locations.name', 'like', "%{$keyword}%");
                })
                ->filterColumn('created_by_name', function ($query, $keyword) {
                    $query->where('users.first_name', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $locations = BusinessLocation::forDropdown($business_id);
        $statuses = Followup::statusOptions();

        return view('followups.index', compact('locations', 'statuses'));
    }

    /**
     * Store a new follow-up from POS (supports multiple products).
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('followups.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'customer_phone' => 'required|string|max:50',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer',
            'products.*.variation_id' => 'required|integer',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = auth()->user()->id;

            DB::beginTransaction();

            $followup_ids = [];
            $product_names = [];

            foreach ($request->products as $product) {
                $followup = Followup::create([
                    'business_id' => $business_id,
                    'location_id' => $request->location_id,
                    'product_id' => $product['product_id'],
                    'variation_id' => $product['variation_id'],
                    'customer_phone' => $request->customer_phone,
                    'customer_name' => $request->customer_name,
                    'quantity' => $product['quantity'] ?? 1,
                    'comment' => $request->comment,
                    'status' => 'pending',
                    'created_by' => $user_id,
                ]);

                $followup_ids[] = $followup->id;

                $prod = \App\Product::find($product['product_id']);
                if ($prod) {
                    $product_names[] = $prod->name . ' x' . ($product['quantity'] ?? 1);
                }
            }

            DB::commit();

            // WhatsApp Alert Logic
            try {
                $location = \App\BusinessLocation::find($request->location_id);
                $business = \App\Business::find($business_id);

                // Recipient: Location mobile or Business Owner's contact number
                $recipient_number = !empty($location->mobile) ? $location->mobile : ($business->owner ? $business->owner->contact_number : null);

                if (!empty($recipient_number)) {
                    $message = "New Follow-up Created!\n";
                    $message .= "Customer: " . ($request->customer_name ?? 'N/A') . "\n";
                    $message .= "Phone: " . $request->customer_phone . "\n";
                    $message .= "Products: " . implode(', ', $product_names) . "\n";
                    $message .= "Comment: " . ($request->comment ?? 'N/A');

                    // Using NotificationUtil to send/log alert
                    $notificationUtil = new \App\Utils\NotificationUtil();

                    // Automated WhatsApp Alert via AfricasTalking
                    $notificationUtil->sendAfricasTalkingWhatsapp($recipient_number, $message);
                    
                    // We can also provide the click-to-send link as a backup in logs
                    $wa_link = $notificationUtil->getWhatsappNotificationLink([
                        'mobile_number' => $recipient_number,
                        'whatsapp_text' => $message
                    ]);

                    \Log::info('WhatsApp Alert Link (Backup): ' . $wa_link);

                    // If they have an automated SMS service configured, we can also send an SMS as an alert
                    $business_details = [
                        'sms_settings' => $business->sms_settings,
                        'mobile_number' => $recipient_number,
                        'sms_body' => $message
                    ];
                    $notificationUtil->sendSms($business_details);
                }
            } catch (\Exception $e) {
                \Log::emergency('Followup WhatsApp Alert Error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'msg' => __('lang_v1.followup_created_successfully'),
                'followup_ids' => $followup_ids,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ]);
        }
    }

    /**
     * Get follow-ups for POS modal (simple list).
     */
    public function getFollowupsForPos(Request $request)
    {
        if (!auth()->user()->can('followups.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $location_id = $request->location_id;

        $followups = Followup::forBusiness($business_id)
            ->where('location_id', $location_id)
            ->whereIn('status', ['pending', 'contacted'])
            ->with(['product', 'variation', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('sale_pos.partials.followups_list', compact('followups'));
    }

    /**
     * Update follow-up status.
     */
    public function updateStatus(Request $request, $id)
    {
        if (!auth()->user()->can('followups.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $followup = Followup::forBusiness($business_id)->findOrFail($id);
            $followup->status = $request->status;
            if ($request->has('comment')) {
                $followup->comment = $request->comment;
            }
            $followup->save();

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
     * Delete a follow-up.
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('followups.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $followup = Followup::forBusiness($business_id)->findOrFail($id);
            $followup->delete();

            return response()->json([
                'success' => true,
                'msg' => __('lang_v1.followup_deleted_successfully'),
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
     * Search products for follow-up (AJAX).
     */
    public function searchProducts(Request $request)
    {
        if (!auth()->user()->can('followups.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $query = $request->input('query');
        $location_id = $request->input('location_id');

        $search_fields = ['name', 'sku', 'sub_sku'];
        $result = $this->productUtil->filterProduct($business_id, $query, $location_id, null, null, [], $search_fields);

        return response()->json($result);
    }

    /**
     * Get followup receipt HTML for printing in same window (like POS receipt).
     */
    public function getFollowupReceipt($id)
    {
        if (!auth()->user()->can('followups.view')) {
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized action.',
            ]);
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $followup = Followup::forBusiness($business_id)
                ->with(['location', 'product', 'variation', 'createdBy'])
                ->findOrFail($id);

            $business = \App\Business::find($business_id);

            $html = view('followups.receipt', compact('followup', 'business'))->render();

            return response()->json([
                'success' => true,
                'html_content' => $html,
                'print_title' => 'Follow-up - ' . $followup->customer_phone,
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
