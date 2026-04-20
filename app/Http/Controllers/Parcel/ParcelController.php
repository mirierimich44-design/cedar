<?php

namespace App\Http\Controllers\Parcel;

use App\BusinessLocation;
use App\Http\Controllers\Controller;
use App\Parcel;
use App\ParcelCheckpoint;
use App\ParcelRoute;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ParcelController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = Parcel::where('parcels.business_id', $business_id)
                ->select('parcels.*');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('from_town')) {
                $query->where('from_town', 'like', '%' . $request->from_town . '%');
            }
            if ($request->filled('to_town')) {
                $query->where('to_town', 'like', '%' . $request->to_town . '%');
            }
            if ($request->filled('date_range')) {
                [$start, $end] = explode(' - ', $request->date_range);
                $query->whereBetween('created_at', [
                    \Carbon\Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay(),
                    \Carbon\Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay(),
                ]);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $html  = '<div class="btn-group">';
                    $html .= '<a href="' . route('parcels.show', $row->id) . '" class="btn btn-xs btn-info" title="View"><i class="fa fa-eye"></i></a> ';
                    $html .= '<a href="' . route('parcels.edit', $row->id) . '" class="btn btn-xs btn-primary" title="Edit"><i class="fa fa-edit"></i></a> ';
                    $html .= '<a href="' . route('parcels.waybill', $row->id) . '" target="_blank" class="btn btn-xs btn-default" title="Print Waybill"><i class="fa fa-print"></i></a> ';
                    $html .= '<button class="btn btn-xs btn-success update-status-btn" data-id="' . $row->id . '" data-status="' . $row->status . '" title="Update Status"><i class="fa fa-refresh"></i></button>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('status_badge', fn($r) => $r->status_badge)
                ->editColumn('total_amount', fn($r) => 'KES ' . number_format($r->total_amount, 2))
                ->editColumn('payment_status', function ($r) {
                    $colors = ['unpaid' => 'danger', 'partial' => 'warning', 'paid' => 'success'];
                    return '<span class="label label-' . ($colors[$r->payment_status] ?? 'default') . '">' . ucfirst($r->payment_status) . '</span>';
                })
                ->rawColumns(['action', 'status_badge', 'payment_status'])
                ->make(true);
        }

        $status_list    = Parcel::statusList();
        $business_locations = BusinessLocation::forDropdown($business_id);

        // Summary counts
        $counts = Parcel::where('business_id', $business_id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('parcel.index', compact('status_list', 'business_locations', 'counts'));
    }

    // ── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $routes      = ParcelRoute::getDropdown($business_id);
        $locations   = BusinessLocation::forDropdown($business_id);
        $parcel_types = Parcel::parcelTypes();

        $kenyan_towns = $this->kenyanTowns();

        return view('parcel.create', compact('routes', 'locations', 'parcel_types', 'kenyan_towns'));
    }

    // ── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'sender_name'    => 'required|string|max:191',
            'sender_phone'   => 'required|string|max:20',
            'receiver_name'  => 'required|string|max:191',
            'receiver_phone' => 'required|string|max:20',
            'from_town'      => 'required|string|max:100',
            'to_town'        => 'required|string|max:100',
            'weight_kg'      => 'required|numeric|min:0.001',
        ]);

        try {
            $business_id = request()->session()->get('user.business_id');

            // Calculate freight charge from route if given
            $freight = (float) $request->freight_charge;
            if ($request->filled('route_id') && $freight == 0) {
                $route = ParcelRoute::find($request->route_id);
                if ($route) {
                    $freight = $route->calculatePrice((float) $request->weight_kg, $request->service_type ?? 'standard');
                }
            }

            $insurance = (float) $request->input('declared_value', 0) * 0.01; // 1% of declared value
            $pickup_ch = $request->pickup_type === 'home_pickup' ? 200 : 0;
            $delivery_ch = $request->delivery_type === 'home_delivery' ? 200 : 0;
            $total = $freight + $insurance + $pickup_ch + $delivery_ch;

            $parcel = Parcel::create([
                'business_id'         => $business_id,
                'location_id'         => $request->location_id,
                'waybill_number'      => Parcel::generateWaybill($business_id),
                'route_id'            => $request->route_id ?: null,
                'from_town'           => $request->from_town,
                'to_town'             => $request->to_town,
                'sender_name'         => $request->sender_name,
                'sender_phone'        => $request->sender_phone,
                'sender_id_number'    => $request->sender_id_number,
                'sender_town'         => $request->sender_town,
                'receiver_name'       => $request->receiver_name,
                'receiver_phone'      => $request->receiver_phone,
                'receiver_id_number'  => $request->receiver_id_number,
                'receiver_town'       => $request->to_town,
                'receiver_address'    => $request->receiver_address,
                'parcel_type'         => $request->parcel_type ?? 'package',
                'parcel_description'  => $request->parcel_description,
                'weight_kg'           => $request->weight_kg,
                'declared_value'      => $request->declared_value ?? 0,
                'pieces'              => $request->pieces ?? 1,
                'dimensions'          => $request->dimensions,
                'service_type'        => $request->service_type ?? 'standard',
                'pickup_type'         => $request->pickup_type ?? 'drop_off',
                'delivery_type'       => $request->delivery_type ?? 'depot_pickup',
                'freight_charge'      => $freight,
                'insurance_charge'    => $insurance,
                'pickup_charge'       => $pickup_ch,
                'delivery_charge'     => $delivery_ch,
                'total_amount'        => $total,
                'paid_amount'         => (float) $request->paid_amount,
                'payment_method'      => $request->payment_method ?? 'cash',
                'payment_by'          => $request->payment_by ?? 'sender',
                'mpesa_code'          => $request->mpesa_code,
                'payment_status'      => $this->calcPayStatus((float) $request->paid_amount, $total),
                'status'              => 'booked',
                'created_by'          => auth()->id(),
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes'               => $request->notes,
                'special_instructions' => $request->special_instructions,
            ]);

            // Log first checkpoint
            ParcelCheckpoint::create([
                'parcel_id'       => $parcel->id,
                'location'        => $request->from_town,
                'checkpoint_type' => 'booked',
                'status_note'     => 'Parcel booked by ' . auth()->user()->first_name,
                'scanned_by'      => auth()->id(),
            ]);

            $output = ['success' => 1, 'msg' => 'Parcel booked. Waybill: ' . $parcel->waybill_number];
        } catch (\Exception $e) {
            \Log::emergency('ParcelController store: ' . $e->getMessage());
            $output = ['success' => 0, 'msg' => 'Error: ' . $e->getMessage()];
        }

        return redirect()->route('parcels.show', $parcel->id ?? 0)
            ->with('status', $output);
    }

    // ── Show ─────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $parcel      = Parcel::where('business_id', $business_id)
            ->with(['route', 'creator', 'checkpoints.scannedByUser'])
            ->findOrFail($id);

        $status_list = Parcel::statusList();
        return view('parcel.show', compact('parcel', 'status_list'));
    }

    // ── Edit ─────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $business_id  = request()->session()->get('user.business_id');
        $parcel       = Parcel::where('business_id', $business_id)->findOrFail($id);
        $routes       = ParcelRoute::getDropdown($business_id);
        $locations    = BusinessLocation::forDropdown($business_id);
        $parcel_types = Parcel::parcelTypes();
        $kenyan_towns = $this->kenyanTowns();
        return view('parcel.edit', compact('parcel', 'routes', 'locations', 'parcel_types', 'kenyan_towns'));
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $parcel      = Parcel::where('business_id', $business_id)->findOrFail($id);

        $freight     = (float) $request->freight_charge;
        $insurance   = (float) $request->insurance_charge;
        $pickup_ch   = (float) $request->pickup_charge;
        $delivery_ch = (float) $request->delivery_charge;
        $total       = $freight + $insurance + $pickup_ch + $delivery_ch;
        $paid        = (float) $request->paid_amount;

        $parcel->update(array_merge($request->except(['_token', '_method']), [
            'freight_charge'  => $freight,
            'total_amount'    => $total,
            'paid_amount'     => $paid,
            'payment_status'  => $this->calcPayStatus($paid, $total),
        ]));

        return redirect()->route('parcels.show', $parcel->id)
            ->with('status', ['success' => 1, 'msg' => 'Parcel updated.']);
    }

    // ── Update Status (AJAX) ─────────────────────────────────────────────────

    public function updateStatus(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $parcel      = Parcel::where('business_id', $business_id)->findOrFail($id);
        $new_status  = $request->status;

        $parcel->update(['status' => $new_status]);

        // Map parcel status → checkpoint type
        $cpMap = [
            'collected'        => 'collected_from_sender',
            'in_transit'       => 'dispatched',
            'at_depot'         => 'arrived_at_depot',
            'out_for_delivery' => 'out_for_delivery',
            'delivered'        => 'delivered',
            'returned'         => 'returned_to_sender',
        ];

        if (isset($cpMap[$new_status])) {
            ParcelCheckpoint::create([
                'parcel_id'       => $parcel->id,
                'location'        => $request->input('location', $parcel->from_town),
                'checkpoint_type' => $cpMap[$new_status],
                'status_note'     => $request->input('note', ''),
                'scanned_by'      => auth()->id(),
                'vehicle_reg'     => $request->input('vehicle_reg'),
            ]);
        }

        if ($new_status === 'delivered') {
            $parcel->update([
                'actual_delivery_date' => now(),
                'delivered_to'         => $request->input('delivered_to', $parcel->receiver_name),
            ]);
        }

        return response()->json(['success' => true, 'msg' => 'Status updated to ' . Parcel::statusList()[$new_status]]);
    }

    // ── Waybill print ────────────────────────────────────────────────────────

    public function waybill($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $parcel      = Parcel::where('business_id', $business_id)->with('route')->findOrFail($id);
        $business    = request()->session()->get('business');
        return view('parcel.waybill', compact('parcel', 'business'));
    }

    // ── Manifest (by route/date) ─────────────────────────────────────────────

    public function manifest(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $routes      = ParcelRoute::getDropdown($business_id);
        $parcels     = collect();

        if ($request->filled('from_town') || $request->filled('to_town') || $request->filled('date')) {
            $query = Parcel::where('business_id', $business_id);
            if ($request->filled('from_town')) $query->where('from_town', $request->from_town);
            if ($request->filled('to_town'))   $query->where('to_town', $request->to_town);
            if ($request->filled('date'))      $query->whereDate('created_at', $request->date);
            if ($request->filled('status'))    $query->where('status', $request->status);
            $parcels = $query->get();
        }

        $kenyan_towns = $this->kenyanTowns();
        $status_list  = Parcel::statusList();

        return view('parcel.manifest', compact('routes', 'parcels', 'kenyan_towns', 'status_list'));
    }

    // ── Track (public / internal) ─────────────────────────────────────────────

    public function track(Request $request)
    {
        $parcel = null;
        if ($request->filled('waybill')) {
            $parcel = Parcel::with('checkpoints.scannedByUser')
                ->where('waybill_number', strtoupper(trim($request->waybill)))
                ->first();
        }
        return view('parcel.track', compact('parcel'));
    }

    // ── Bulk update status ────────────────────────────────────────────────────

    public function bulkScan(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $waybills    = array_filter(array_map('trim', explode("\n", $request->waybills)));
        $results     = [];

        foreach ($waybills as $wb) {
            $parcel = Parcel::where('business_id', $business_id)->where('waybill_number', strtoupper($wb))->first();
            if ($parcel) {
                $parcel->update(['status' => $request->status]);
                ParcelCheckpoint::create([
                    'parcel_id'       => $parcel->id,
                    'location'        => $request->location,
                    'checkpoint_type' => 'arrived_at_depot',
                    'status_note'     => 'Bulk scan at ' . $request->location,
                    'scanned_by'      => auth()->id(),
                ]);
                $results[] = ['waybill' => $wb, 'success' => true];
            } else {
                $results[] = ['waybill' => $wb, 'success' => false, 'msg' => 'Not found'];
            }
        }

        return response()->json(['results' => $results]);
    }

    // ── Reports ──────────────────────────────────────────────────────────────

    public function reports(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $routes      = ParcelRoute::getDropdown($business_id);
        $status_list = Parcel::statusList();

        $query = Parcel::where('business_id', $business_id);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($request->start_date)->startOfDay(),
                \Carbon\Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        $summary = [
            'total'    => $query->count(),
            'revenue'  => $query->sum('total_amount'),
            'paid'     => $query->sum('paid_amount'),
            'by_route' => Parcel::where('business_id', $business_id)
                ->selectRaw('from_town, to_town, count(*) as count, sum(total_amount) as revenue')
                ->groupBy('from_town', 'to_town')
                ->get(),
            'by_status' => Parcel::where('business_id', $business_id)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
        ];

        return view('parcel.reports', compact('routes', 'status_list', 'summary'));
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function calcPayStatus(float $paid, float $total): string
    {
        if ($total <= 0) return 'paid';
        if ($paid >= $total) return 'paid';
        if ($paid > 0)   return 'partial';
        return 'unpaid';
    }

    private function kenyanTowns(): array
    {
        return [
            'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika',
            'Malindi', 'Kitale', 'Garissa', 'Kakamega', 'Nyeri', 'Machakos',
            'Meru', 'Embu', 'Lamu', 'Voi', 'Nanyuki', 'Muranga', 'Kirinyaga',
            'Kericho', 'Bungoma', 'Kisii', 'Homabay', 'Migori', 'Siaya',
            'Bomet', 'Narok', 'Kajiado', 'Athi River', 'Limuru', 'Ruiru',
            'Mwingi', 'Kitui', 'Makueni', 'Wajir', 'Marsabit', 'Isiolo',
            'Mandera', 'Kwale', 'Kilifi', 'Tana River', 'Taita', 'Nyahururu',
            'Laikipia', 'Samburu', 'Trans Nzoia', 'Uasin Gishu', 'Elgeyo',
            'Nandi', 'Baringo', 'Turkana', 'West Pokot', 'Samburu',
        ];
    }
}
