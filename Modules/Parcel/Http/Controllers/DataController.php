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
        
        $is_parcel_enabled = (bool)$module_util->isModuleEnabled('parcel', $business_id);

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
                                action([\Modules\Parcel\Http\Controllers\ParcelController::class, 'index']), // TODO: StationController
                                'Stations',
                                ['icon' => 'fa fa-building', 'active' => request()->segment(1) == 'parcel' && request()->segment(2) == 'stations']
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
