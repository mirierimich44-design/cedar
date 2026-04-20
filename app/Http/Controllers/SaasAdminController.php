<?php

namespace App\Http\Controllers;

use App\Business;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SaasAdminController extends Controller
{
    protected $moduleUtil;

    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * List all businesses with their feature status (superadmin only)
     */
    public function index(Request $request)
    {
        if (! auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Superadmin access required.');
        }

        if ($request->ajax()) {
            $query = Business::with('owner')->select('business.*');
            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('saas-admin.features', $row->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-toggle-on"></i> Features</a> ';
                })
                ->addColumn('owner_name', fn($r) => optional($r->owner)->first_name . ' ' . optional($r->owner)->last_name)
                ->addColumn('modules_count', fn($r) => count($r->enabled_modules ?? []) . ' modules')
                ->editColumn('business_type', fn($r) => ucfirst($r->business_type ?? '—'))
                ->editColumn('created_at', fn($r) => $r->created_at ? $r->created_at->format('d M Y') : '')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('saas_admin.index');
    }

    /**
     * Show feature toggles for a specific business
     */
    public function features($business_id)
    {
        if (! auth()->user()->hasRole('Superadmin')) {
            abort(403);
        }

        $business         = Business::findOrFail($business_id);
        $all_modules      = $this->getAllFeatures();
        $enabled_modules  = $business->enabled_modules ?? [];

        return view('saas_admin.features', compact('business', 'all_modules', 'enabled_modules'));
    }

    /**
     * Save feature toggles for a business
     */
    public function updateFeatures(Request $request, $business_id)
    {
        if (! auth()->user()->hasRole('Superadmin')) {
            abort(403);
        }

        $business = Business::findOrFail($business_id);
        $enabled  = $request->input('enabled_modules', []);
        $business->update(['enabled_modules' => $enabled]);

        return redirect()->route('saas-admin.features', $business_id)
            ->with('status', ['success' => 1, 'msg' => 'Features updated for ' . $business->name]);
    }

    /**
     * Full feature list with categories
     */
    private function getAllFeatures(): array
    {
        return [
            'Sales & POS' => [
                'add_sale'   => ['name' => 'Add Sale (Invoice)', 'icon' => 'fa-file-invoice'],
                'pos_sale'   => ['name' => 'POS / Point of Sale', 'icon' => 'fa-cash-register'],
                'subscription' => ['name' => 'Recurring Invoices', 'icon' => 'fa-sync'],
                'types_of_service' => ['name' => 'Types of Service', 'icon' => 'fa-concierge-bell'],
            ],
            'Inventory & Purchasing' => [
                'purchases'        => ['name' => 'Purchases', 'icon' => 'fa-shopping-cart'],
                'stock_transfers'  => ['name' => 'Stock Transfers', 'icon' => 'fa-exchange-alt'],
                'stock_adjustment' => ['name' => 'Stock Adjustments', 'icon' => 'fa-sliders-h'],
            ],
            'Finance' => [
                'expenses' => ['name' => 'Expenses', 'icon' => 'fa-wallet'],
                'account'  => ['name' => 'Accounts / Ledger', 'icon' => 'fa-book'],
            ],
            'Restaurant' => [
                'tables'        => ['name' => 'Table Management', 'icon' => 'fa-utensils'],
                'modifiers'     => ['name' => 'Menu Modifiers', 'icon' => 'fa-sliders-h'],
                'service_staff' => ['name' => 'Service Staff', 'icon' => 'fa-user-tie'],
                'kitchen'       => ['name' => 'Kitchen Display', 'icon' => 'fa-fire'],
                'booking'       => ['name' => 'Bookings / Reservations', 'icon' => 'fa-calendar-check'],
            ],
            'Courier & Logistics' => [
                'parcels' => ['name' => 'Parcel Management', 'icon' => 'fa-box'],
            ],
            'Healthcare' => [
                'hospital_billing' => ['name' => 'Hospital / Clinic Billing', 'icon' => 'fa-hospital'],
            ],
        ];
    }
}
