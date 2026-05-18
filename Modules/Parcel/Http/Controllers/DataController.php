<?php

namespace Modules\Parcel\Http\Controllers;

use App\Utils\Util;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Adds Parcel menus
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new \App\Utils\ModuleUtil();
        
        // FIX: do a case-insensitive check so both 'Parcel' and 'parcel' in enabled_modules work
        $enabled_modules = array_map('strtolower', (array) $module_util->allModulesEnabled($business_id));
        $is_parcel_enabled = in_array('parcel', $enabled_modules);

        // Secondary gate: respect modules_statuses.json (framework-level enable/disable)
        if ($is_parcel_enabled) {
            $modules_statuses = json_decode(@file_get_contents(base_path('modules_statuses.json')), true) ?? [];
            if (isset($modules_statuses['Parcel']) && !$modules_statuses['Parcel']) {
                $is_parcel_enabled = false;
            }
        }

        if ($is_parcel_enabled) {
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu) {
                    $menu->dropdown(
                        'Parcel',
                        function ($sub) {
                            $sub->url(
                                action([\Modules\Parcel\Http\Controllers\BookingController::class, 'create']),
                                'Book Parcel',
                                ['icon' => 'fa fa-plus-circle', 'active' => request()->segment(1) == 'parcel' && request()->segment(2) == 'create']
                            );
                            $sub->url(
                                action([\Modules\Parcel\Http\Controllers\ParcelController::class, 'index']),
                                'List Parcels',
                                ['icon' => 'fa fa-list', 'active' => request()->segment(1) == 'parcel' && request()->segment(2) == null]
                            );
                            $sub->url(
                                action([\Modules\Parcel\Http\Controllers\RouteController::class, 'index']),
                                'Stations & Routes',
                                ['icon' => 'fa fa-building', 'active' => request()->segment(1) == 'parcel' && in_array(request()->segment(2), ['stations', 'routes'])]
                            );
                        },
                        ['icon' => 'fa fa-box', 'style' => 'background-color: #f3f4f6; color: #1e293b;']
                    )->order(25);
                }
            );
        }
    }

    /**
     * Defines user permissions for the module.
     *
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'parcel.admin',
                'label' => 'Parcel Admin (Full access)',
                'default' => false,
            ],
            [
                'value' => 'parcel.agent',
                'label' => 'Parcel Agent (Booking & Dispatch)',
                'default' => false,
            ],
            [
                'value' => 'parcel.view_reports',
                'label' => 'View Parcel Reports',
                'default' => false,
            ],
        ];
    }
}
