<?php

namespace Modules\InventoryEmptying\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Utils\ModuleUtil;
use Menu;

class DataController extends Controller
{    
    public function superadmin_package()
    {
        return [
            [
                'name' => 'inventoryemptying_module',
                'label' => __('inventoryemptying::app.remove_inv'),
                'default' => false
            ]
        ];
    }

    /**
      * Defines user permissions for the module.
      * @return array
      */
    public function user_permissions()
    {
        return [
            [
                'value' => 'inventoryemptying.view',
                'label' => __('inventoryemptying::app.view'),
                'default' => false
            ],
            [
                'value' => 'inventoryemptying.empty',
                'label' => __('inventoryemptying::app.remove_inv'),
                'default' => false
            ],
      
        ];
    }

    /**
    * Function to add module taxonomies
    * @return array
    */
    public function addTaxonomies()
    {
        $business_id = request()->session()->get('user.business_id');

        $module_util = new ModuleUtil();
        if (!(auth()->user()->can('superadmin') || $module_util->hasThePermissionInSubscription($business_id, 'inventoryemptying_module'))) {
            return ['wallet' => []];
        }
        
        return [
            'wallet' => [
                'taxonomy_label' =>  __('inventoryemptying::app.manage'),
                'heading' => __('inventoryemptying::app.manage'),
                'sub_heading' => __('inventoryemptying::app.manage'),
                'enable_taxonomy_code' => false,
                'enable_sub_taxonomy' => false,
                'navbar' => 'inventoryemptying::layouts.nav'
            ]
        ];
    }

    /**
     * Adds Repair menus
     * @return null
     */
    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        $is_inventoryemptying_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'inventoryemptying_module');

        $background_color = '';
        if (config('app.env') == 'demo') {
            $background_color = '#2e97bf !important';
        }

        if ($is_inventoryemptying_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('inventoryemptying.view'))) {

            $menuparent = Menu::instance('admin-sidebar-menu');
    
            $menuparent->url (
                action('\Modules\InventoryEmptying\Http\Controllers\InventoryEmptyingController@index'),
                __('inventoryemptying::app.empting_inventory'), 
            
            //                 function ($sub) use ($background_color){
            //                 '';
            //                     // $sub->url(action('\Modules\InventoryEmptying\Http\Controllers\InventoryEmptyingController@dashboard'),
            //                     // __('inventoryemptying::app.main facade'),
            //                     // ['icon' => 'fas fa fa-boxes', 
            //                     // 'active' => request()->segment(2) == 'dashboard',
            //                     //  'style' => 'background-color:'.$background_color]);


            // },
            
            ['icon' => 'fa fa-minus-circle']
            )->order(23);
        }
    }


}
