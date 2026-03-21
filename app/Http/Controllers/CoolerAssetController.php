<?php

namespace App\Http\Controllers;

use App\CoolerAsset;
use App\CoolerDealer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CoolerAssetController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('cooler.asset.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $assets = CoolerAsset::where('cooler_assets.business_id', $business_id)
                ->leftJoin('cooler_dealers as cd', 'cooler_assets.current_dealer_id', '=', 'cd.id')
                ->select([
                    'cooler_assets.id', 'cooler_assets.asset_type', 'cooler_assets.asset_number',
                    'cooler_assets.serial_number', 'cooler_assets.cooler_tag', 'cooler_assets.status',
                    'cooler_assets.deployment_date', 'cooler_assets.replacement_value',
                    'cd.outlet_name as dealer_outlet', 'cd.name as dealer_name',
                ]);

            return DataTables::of($assets)
                ->addColumn('status_badge', fn ($row) => (new CoolerAsset($row->toArray()))->status_badge)
                ->addColumn('dealer', fn ($row) => $row->dealer_outlet ? "{$row->dealer_outlet} ({$row->dealer_name})" : '<em class="text-muted">Unassigned</em>')
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">';
                    if (auth()->user()->can('cooler.asset.view')) {
                        $html .= '<a href="' . route('cooler.assets.show', $row->id) . '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a> ';
                    }
                    if (auth()->user()->can('cooler.asset.update')) {
                        $html .= '<a href="' . route('cooler.assets.edit', $row->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a> ';
                    }
                    if (auth()->user()->can('cooler.asset.delete')) {
                        $html .= '<button class="btn btn-xs btn-danger btn-delete-cooler-asset" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['status_badge', 'dealer', 'action'])
                ->make(true);
        }

        $statusCounts = CoolerAsset::forBusiness($business_id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('cooler.assets.index', compact('statusCounts'));
    }

    public function create(Request $request)
    {
        if (!auth()->user()->can('cooler.asset.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $statuses    = array_combine(CoolerAsset::$statuses, array_map(fn($s) => ucwords(str_replace('_', ' ', $s)), CoolerAsset::$statuses));

        return view('cooler.assets.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('cooler.asset.create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'asset_type'        => 'required|string|max:100',
            'asset_number'      => 'required|string|max:100',
            'serial_number'     => 'nullable|string|max:100',
            'cooler_tag'        => 'nullable|string|max:100',
            'status'            => 'required|in:' . implode(',', CoolerAsset::$statuses),
            'replacement_value' => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
        ]);

        $business_id = $request->session()->get('user.business_id');

        CoolerAsset::create(array_merge($request->only([
            'asset_type', 'asset_number', 'serial_number', 'cooler_tag',
            'status', 'replacement_value', 'notes',
        ]), [
            'business_id' => $business_id,
            'created_by'  => auth()->id(),
        ]));

        return redirect()->route('cooler.assets.index')
            ->with('success', __('Cooler asset added successfully.'));
    }

    public function show(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.asset.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $asset       = CoolerAsset::forBusiness($business_id)
            ->with(['currentDealer', 'agreements.dealer', 'retrievals.dealer'])
            ->findOrFail($id);

        return view('cooler.assets.show', compact('asset'));
    }

    public function edit(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.asset.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $asset       = CoolerAsset::forBusiness($business_id)->findOrFail($id);
        $statuses    = array_combine(CoolerAsset::$statuses, array_map(fn($s) => ucwords(str_replace('_', ' ', $s)), CoolerAsset::$statuses));

        return view('cooler.assets.edit', compact('asset', 'statuses'));
    }

    public function update(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.asset.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $asset       = CoolerAsset::forBusiness($business_id)->findOrFail($id);

        $request->validate([
            'asset_type'        => 'required|string|max:100',
            'asset_number'      => 'required|string|max:100',
            'serial_number'     => 'nullable|string|max:100',
            'cooler_tag'        => 'nullable|string|max:100',
            'status'            => 'required|in:' . implode(',', CoolerAsset::$statuses),
            'replacement_value' => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
        ]);

        $asset->update($request->only([
            'asset_type', 'asset_number', 'serial_number', 'cooler_tag',
            'status', 'replacement_value', 'notes',
        ]));

        return redirect()->route('cooler.assets.show', $id)
            ->with('success', __('Cooler asset updated successfully.'));
    }

    public function destroy(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.asset.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $asset       = CoolerAsset::forBusiness($business_id)->findOrFail($id);
        $asset->delete();

        return response()->json(['success' => true]);
    }
}
