<?php

namespace Modules\KcbBuni\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\KcbBuni\Entities\KcbBuniSetting;
use Modules\KcbBuni\Entities\KcbBuniTransaction;
use Modules\KcbBuni\Utils\KcbBuniService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KcbBuniGatewayController extends Controller
{
    public function settings()
    {
        if (!auth()->user()->can('kcb_buni.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $settings = KcbBuniSetting::getForBusiness($business_id);

        return view('kcb_buni::settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        if (!auth()->user()->can('kcb_buni.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $validator = Validator::make($request->all(), [
            'app_key'    => 'nullable|string',
            'app_secret' => 'nullable|string',
            'b2b_shortcode' => 'nullable|string|max:50',
            'b2c_shortcode' => 'nullable|string|max:50',
            'environment'   => 'required|in:sandbox,production',
            'is_active'     => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            $settings = KcbBuniSetting::firstOrNew(['business_id' => $business_id]);

            if ($request->filled('app_key')) {
                $settings->app_key = $request->app_key;
            }
            if ($request->filled('app_secret')) {
                $settings->app_secret = $request->app_secret;
            }

            $settings->b2b_shortcode = $request->b2b_shortcode;
            $settings->b2c_shortcode = $request->b2c_shortcode;
            $settings->environment = $request->environment;
            $settings->is_active = $request->boolean('is_active');
            $settings->save();

            return response()->json(['success' => true, 'message' => 'KCB Buni settings saved successfully.']);

        } catch (\Exception $e) {
            Log::error('KCB settings save error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Something went wrong']);
        }
    }

    public function testConnection()
    {
        if (!auth()->user()->can('kcb_buni.manage_settings')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $service = new KcbBuniService($business_id);
            if (!$service->isConfigured()) {
                return response()->json(['success' => false, 'message' => 'KCB Buni not configured.']);
            }

            // Test by getting access token
            $token = $service->getAccessToken();

            if ($token) {
                $settings = $service->getSettings();
                $settings->last_tested_at = now();
                $settings->save();

                return response()->json(['success' => true, 'message' => 'Connected to KCB Buni successfully.']);
            }

            return response()->json(['success' => false, 'message' => 'Connection failed.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function transactions(Request $request)
    {
        if (!auth()->user()->can('kcb_buni.view_transactions')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if ($request->ajax()) {
            $txs = KcbBuniTransaction::byBusiness($business_id)
                ->with('initiatedBy')
                ->orderBy('created_at', 'desc');

            return DataTables::of($txs)
                ->editColumn('amount', fn($row) => number_format($row->amount, 2))
                ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i:s'))
                ->addColumn('status_label', fn($row) =>
                    '<span class="badge ' . $row->getStatusBadgeClass() . '">' . $row->getStatusLabel() . '</span>'
                )
                ->rawColumns(['status_label'])
                ->make(true);
        }

        return view('kcb_buni::transactions');
    }

    public function getBalance(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $service = new KcbBuniService($business_id);
        
        $result = $service->getBalance($request->shortcode);
        return response()->json($result);
    }
}
