<?php

namespace Modules\PosCustom\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Utils\ModuleUtil;
use Faker\Provider\File;
use Illuminate\Support\Facades\Storage;
use Menu;

class DataController extends Controller
{
    
    /**
     * Defines user permissions for the module.
     *
     * @return array
     */
    public function user_permissions()
    {
        return [
            /**
             * #JCN 2023 Add show quick access category bar
             * #JCN 2025 Add Customer display
            */
            [
                'value' => 'poscustom.pos_display',
                'label' => __('poscustom::lang.pos_display'),
                'default' => true,
                // 'is_radio' => true,
                'radio_input_name' => 'pos_display',
            ],
            [
                'value' => 'poscustom.category_bar',
                'label' => __('poscustom::lang.category_bar'),
                'default' => true,
                // 'is_radio' => true,
                'radio_input_name' => 'pos_category_bar_enable',
            ],
            [
                //Hide units in POS Screen
                'value' => 'poscustom.enable_units',
                'label' => __('poscustom::lang.enable_units'),
                'default' => false,
                // 'is_radio' => true,
                'radio_input_name' => 'pos_enable_units',
            ],
            
        ];
    }


    /**
     * Defines module as a superadmin package.
     * @return Array
     */
    public function superadmin_package()
    {
        return [
            [
                'name' => 'PosCustom_module',
                'label' => __('PosCustom::lang.PosCustom_module'),
                'default' => false
            ]
        ];
    }

    /**
     * Add PosCustom menu
     * @return null 
     * #JCN 2025 i quit the menu because all the action is in the POS Menu boutton
     */
/*     public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        $is_PosCustom_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'PosCustom_module', 'superadmin_package');

        if ($is_PosCustom_enabled && (auth()->user()->can('superadmin') || auth()->user()->can('poscustom.enabled')) ) {

            Menu::modify('admin-sidebar-menu', function ($menu) {
                $menu->url(
                         action([\Modules\PosCustom\Http\Controllers\SellPosController::class, 'create']),
                        __('PosCustom::lang.titulo_PosCustom'),
                        //['icon' => 'fa fas fa-podcast', 'active' => request()->segment(1) == 'PosCustom', 'style' => config('app.env') == 'demo' ? 'background-color: #ff851b;' : '']
                        ['icon' => 'fa fas fa-laptop','active' => request()->segment(1) == 'poscustom', 'style' => 'background-color: #58D68D !important; ']
                    )
                ->order(95);    
            });
        }
    } */
}
