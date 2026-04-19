<?php

namespace Modules\Parcel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Parcel\Entities\Parcel;
use Modules\Parcel\Entities\Station;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParcelController extends Controller
{
    public function index(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        // Filters
        $status    = $request->get('status');
        $station   = $request->get('station_id');
        $date_from = $request->get('date_from', Carbon::now()->subDays(30)->toDateString());
        $date_to   = $request->get('date_to', Carbon::now()->toDateString());
        $search    = $request->get('search');

        $query = Parcel::where('business_id', $business_id)
            ->with(['originStation', 'destinationStation'])
            ->whereBetween('created_at', [$date_from . ' 00:00:00', $date_to . ' 23:59:59']);

        if ($status)  $query->where('status', $status);
        if ($station) $query->where(fn($q) => $q->where('origin_station_id', $station)->orWhere('destination_station_id', $station));
        if ($search)  $query->where(fn($q) => $q->where('waybill_number', 'like', "%$search%")
                                                  ->orWhere('sender_name', 'like', "%$search%")
                                                  ->orWhere('recipient_name', 'like', "%$search%")
                                                  ->orWhere('recipient_phone', 'like', "%$search%"));

        $parcels  = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $stations = Station::where('business_id', $business_id)->where('is_active', true)->orderBy('name')->get();

        // Summary stats for the date range
        $stats = Parcel::where('business_id', $business_id)
            ->whereBetween('created_at', [$date_from . ' 00:00:00', $date_to . ' 23:59:59'])
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status="delivered" THEN 1 ELSE 0 END) as delivered'),
                DB::raw('SUM(CASE WHEN status="in_transit" THEN 1 ELSE 0 END) as in_transit'),
                DB::raw('SUM(CASE WHEN status="failed" THEN 1 ELSE 0 END) as failed'),
                DB::raw('SUM(CASE WHEN payment_status="paid" THEN charge_amount ELSE 0 END) as revenue')
            )->first();

        return view('parcel::index', compact('parcels', 'stations', 'stats', 'date_from', 'date_to', 'status', 'station', 'search'));
    }

    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $parcel = Parcel::where('business_id', $business_id)
            ->with(['originStation', 'destinationStation', 'statusLogs'])
            ->findOrFail($id);
        return view('parcel::show', compact('parcel'));
    }

    public function stationIndex(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $stations = Station::where('business_id', $business_id)->orderBy('name')->get();
        return view('parcel::stations.index', compact('stations'));
    }

    public function storeStation(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'town'   => 'required|string|max:255',
            'county' => 'nullable|string|max:255',
        ]);
        Station::create([
            'business_id' => $request->session()->get('user.business_id'),
            'name'        => $request->name,
            'town'        => $request->town,
            'county'      => $request->county,
            'contact'     => $request->contact,
            'is_active'   => true,
        ]);
        return redirect()->back()->with('status', ['success' => 1, 'msg' => 'Station added.']);
    }
}
