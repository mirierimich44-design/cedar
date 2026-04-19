<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Hospital\HospitalAsset;
use App\Hospital\HospitalAssetMaintenance;
use Illuminate\Http\Request;
use DB;

class HospitalAssetController extends Controller
{
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $assets = HospitalAsset::where('business_id', $business_id)
            ->orderBy('next_service_date', 'asc')
            ->get();
            
        return view('hospital.assets.index', compact('assets'));
    }

    public function create()
    {
        return view('hospital.assets.create');
    }

    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        HospitalAsset::create([
            'business_id' => $business_id,
            'name' => $request->name,
            'asset_code' => $request->asset_code,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'category' => $request->category,
            'purchase_date' => $request->purchase_date,
            'purchase_price' => $request->purchase_price,
            'warranty_expiry' => $request->warranty_expiry,
            'next_service_date' => $request->next_service_date,
            'status' => 'active'
        ]);

        return redirect()->action([HospitalAssetController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Asset registered successfully']);
    }

    public function addMaintenance($id)
    {
        $asset = HospitalAsset::findOrFail($id);
        return view('hospital.assets.maintenance', compact('asset'));
    }

    public function storeMaintenance(Request $request)
    {
        DB::transaction(function() use ($request) {
            HospitalAssetMaintenance::create([
                'asset_id' => $request->asset_id,
                'service_date' => $request->service_date,
                'service_type' => $request->service_type,
                'details' => $request->details,
                'cost' => $request->cost,
                'performed_by' => $request->performed_by
            ]);

            HospitalAsset::where('id', $request->asset_id)->update([
                'next_service_date' => $request->next_service_date,
                'status' => $request->status
            ]);
        });

        return redirect()->action([HospitalAssetController::class, 'index'])
            ->with('status', ['success' => 1, 'msg' => 'Maintenance record updated']);
    }
}
