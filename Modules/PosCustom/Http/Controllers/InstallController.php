<?php

namespace Modules\PosCustom\Http\Controllers;
use App\System;
use Composer\Semver\Comparator;
use Defuse\Crypto\File as CryptoFile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use File;

class InstallController extends Controller
{
    public function __construct()
    {
        $this->module_name = 'PosCustom';
        $this->appVersion = config('PosCustom.module_version');
        $this->module_display_name = 'PosCustom';
    }

    /**
     * Install
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $this->installSettings();

        //Check if PosCustom installed or not.
        $is_installed = System::getProperty($this->module_name . '_version');
        if (!empty($is_installed)) {
            abort(404);
        }
        $this->install();
        
        //Check if SellPosController_ori installed or not.
        $path_sp_rename = app_path('Http/Controllers/SellPosController_ori.php');

        if (file_exists($path_sp_rename)) {
            $output = ['success' => 1,
            'msg' => 'PosCustom module installed Succesfully V:' . $this->appVersion . ' !!'
            ];
        }
        else
        {
            $output = ['warning' => 0,
            'msg' => 'SellPosController install failed check permissions'
            ];
        }
        
        return redirect()
            ->action('\App\Http\Controllers\Install\ModulesController@index')
            ->with('status', $output);
    
    }

    /**
     * Initialize all install functions
     */
    private function installSettings()
    {
        config(['app.debug' => true]);
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');
        \Log::info('🧹 Clearing: Cache, Config, View'. $this->module_name . ' Version: ' . $this->appVersion);
    }

    /**
     * Installing PosCustom Module
     */
    public function install()
    {
        try {
            DB::beginTransaction();
            $is_installed = System::getProperty($this->module_name . '_version');
            if (!empty($is_installed)) {
                abort(404);
            }

            DB::statement('SET default_storage_engine=INNODB;');
            Artisan::call('module:migrate', ['module' => "PosCustom", "--force"=> true]);
            Artisan::call('module:publish', ['module' => "PosCustom"]);
            System::addProperty($this->module_name . '_version ', $this->appVersion);

            DB::commit();

            $output = ['success' => 1,
                    'msg' => '99PosCustom module installed succesfully'
                ];
            /**************INI***************** */    
            //*************BEWARE the \ works in windows no in hosting change to / ******************** */
            /** In this section the controller, tailwind/app.css and the view settings will be install for PosCustom*/
            //Set the paths for the files to move or rename
            \Log::info('🏃‍♂️‍➡️ Installing: '. $this->module_name . ' Version: ' . $this->appVersion);

            //Case controller SellPosController
                $path_sp_ori = app_path('Http/Controllers/SellPosController.php');
                $path_sp_rename = app_path('Http/Controllers/SellPosController_ori.php');
                $path_sp_module = base_path('Modules/PosCustom/Http/Controllers/SellPosController_Module.php');
            //Case controller BusinessController
                $path_bs_ori = app_path('Http/Controllers/BusinessController.php');
                $path_bs_rename = app_path('Http/Controllers/BusinessController_ori.php');
                $path_bs_module = base_path('Modules/PosCustom/Http/Controllers/BusinessController_Module.php');
            //Case controller TaxonomyController
                $path_tx_ori = app_path('Http/Controllers/TaxonomyController.php');
                $path_tx_rename = app_path('Http/Controllers/TaxonomyController_ori.php');
                $path_tx_module = base_path('Modules/PosCustom/Http/Controllers/TaxonomyController_Module.php');
            //Case Tailwind/app.css
                $path_css_ori = public_path('css/tailwind/app.css');
                $path_css_rename = public_path('css/tailwind/app_ori.css');
                $path_css_module = base_path('Modules/PosCustom/Resources/tbcopy/tailwind/tailwind.css');
                $path_css_tailcustom = base_path('Modules/PosCustom/Resources/tbcopy/tailwind/tailcpos.css'); /*Tail Custom*/                
            // Case pos_js_custom.js modules are in lower caps PosCustom = poscustom
                $path_js_module = base_path('Modules/PosCustom/Resources/tbcopy/js/pos_js_custom.js');            
                $path_js_copy = public_path('modules/poscustom/js/pos_js_custom.js');
                $path_js_create = public_path('modules/poscustom/js');
            //Case view settings are copying to custom_views  
                //Product Row -- Was move in v6.10 to ProducUtil so its necessary to move to custom views           
                $path_prodrow_module = base_path('Modules/PosCustom/Resources/tbcopy/sale_pos/product_row.blade_Module.php');
                $path_prodrow_copy_cv = base_path('custom_views/sale_pos/product_row.blade.php');
                $path_prodrow_create_cv = base_path('custom_views/sale_pos');            
                //Settings              
                $path_settings_module = base_path('Modules/PosCustom/Resources/tbcopy/business/partials/settings_pos.blade_Module.php');
                $path_settings_copy_cv = base_path('custom_views/business/partials/settings_pos.blade.php');
                $path_settings_create_cv = base_path('custom_views/business/partials');
                //Modifiers
                $path_modifiers_module = base_path('Modules/PosCustom/Resources/tbcopy/modifiers/add_selected_modifiers.blade_Module.php');
                $path_modifiers_copy_cv = base_path('custom_views/restaurant/product_modifier_set/add_selected_modifiers.blade.php');
                $path_modifiers_create_cv = base_path('custom_views/restaurant/product_modifier_set');
                //Taxonomy (Categories with img)
                $path_taxonomy_module = base_path('Modules/PosCustom/Resources/tbcopy/taxonomy');
                $path_taxonomy_copy_cv = base_path('custom_views/taxonomy');
                $path_taxonomy_create_cv = base_path('custom_views/taxonomy');                
                //To create the folder uploads/category_images with 7555 if isnt create from here the app create the folder with 700
                $path_images_categories = public_path('uploads/category_images');
                if (!file_exists($path_images_categories)) 
                    File::makeDirectory($path_images_categories, 0755, true); //Create the folder for category images

            //If exists is because the move is done
            if (!file_exists($path_sp_rename)) { //If not exist Http/Controllers/SellPosController_ori.php
                //Case Controller SellPosController
                    if (file_exists($path_sp_ori)) {
                        File::move($path_sp_ori, $path_sp_rename); 
                        File::copy($path_sp_module, $path_sp_ori);
                        \Log::info('✅ (1) Rename original to SellPosController_ori.php...');
                    }
                //Case Controller BusinessController                    
                    if (file_exists($path_bs_ori)) {
                        File::move($path_bs_ori, $path_bs_rename); 
                        File::copy($path_bs_module, $path_bs_ori);
                        \Log::info('✅ (2) Rename original to BusinessController_ori.php...');
                    }
                //Case Controller Taxonomy                    
                    if (file_exists($path_tx_ori)) {
                        File::move($path_tx_ori, $path_tx_rename); 
                        File::copy($path_tx_module, $path_tx_ori);
                        \Log::info('✅ (3) Rename original to TaxonomyController_ori.php...');
                    }                    
                //Case tailwind/app.css
                    if (file_exists($path_css_ori)) {
                        File::move($path_css_ori, $path_css_rename);
                        File::copy($path_css_module, $path_css_ori);
                        File::copy($path_css_tailcustom, public_path('css/tailwind/tailcpos.css')); /*Tail Custom*/ 
                        \Log::info('✅ (4) Rename original to tailwind/app_ori.css');

                    }
            }

            //Case pos_js_custom.js (Must be copy here to improve compatbility with others modules)
                if (! File::exists($path_js_create)) {
                    File::makeDirectory($path_js_create, 0755, true); //Create the folder recusrsively
                    File::copy($path_js_module, $path_js_copy);
                    \Log::info('✅ (5) copying pos_js_custom.js to modules/poscustom/js ...');
                }else {
                    File::copy($path_js_module, $path_js_copy);
                    \Log::info('✅ (5.) copying pos_js_custom.js to modules/poscustom/js ...'); //The folder exists
                }
                //Catch up any err from above
                if (! File::exists($path_js_copy)) 
                    \Log::emergency('❌ (5) Err copying pos_js_custom.js to modules/poscustom/js...');                            
            //Case Views copying to custom_views 
            //Product_Row
                if (! File::exists($path_prodrow_create_cv)) {
                    File::makeDirectory($path_prodrow_create_cv, 0755, true); //Create the folder recusrsively
                    File::copy($path_prodrow_module, $path_prodrow_copy_cv);
                    \Log::info('✅ (6) copying product_row.blade.php to custom_views ...');
                }else {
                    File::copy($path_prodrow_module, $path_prodrow_copy_cv);
                    \Log::info('✅ (6.) copying product_row.blade.php to custom_views ...'); //The folder exists
                }

                //Catch up any err from above
                if (! File::exists($path_settings_copy_cv)) 
                    \Log::emergency('❌ (6) Err copying product_row.blade.php to custom_views...');

            //Settings
                if (! File::exists($path_settings_create_cv)) {
                    File::makeDirectory($path_settings_create_cv, 0755, true); //Create the folder recusrsively
                    File::copy($path_settings_module, $path_settings_copy_cv);
                    \Log::info('✅ (6) copying settings_pos.blade.php to custom_views ...');
                }else {
                    File::copy($path_settings_module, $path_settings_copy_cv);
                    \Log::info('✅ (6.) copying settings_pos.blade.php to custom_views ...'); //The folder exists
                }

                //Catch up any err from above
                if (! File::exists($path_settings_copy_cv)) 
                    \Log::emergency('❌ (6) Err copying settings_pos.blade.php to custom_views...');

            //Modifiers 
                if (! File::exists($path_modifiers_create_cv)) {
                    File::makeDirectory($path_modifiers_create_cv, 0775, true); //Create the folder recusrsively
                    File::copy($path_modifiers_module, $path_modifiers_copy_cv);
                    \Log::info('✅ (7) copying add_selected_modifiers.blade.php to custom_views ...');
                }else {
                    File::copy($path_modifiers_module, $path_modifiers_copy_cv);
                    \Log::info('✅ (7.) copying add_selected_modifiers.blade.php to custom_views ...');//The folder exists
                }

                //Catch up any err from above
                if (! File::exists($path_modifiers_copy_cv))     
                    \Log::emergency('❌ (7) Err copying add_selected_modifiers.blade.php to custom_views...');
                    
            //Taxonomy 
                if (! File::exists($path_taxonomy_create_cv)) {
                    File::makeDirectory($path_taxonomy_create_cv, 0775, true); //Create the folder recusrsively
                    File::copyDirectory($path_taxonomy_module, $path_taxonomy_copy_cv);
                    \Log::info('✅ (8) copying folder taxonomy to custom_views ...');
                }else {
                    File::copyDirectory($path_taxonomy_module, $path_taxonomy_copy_cv);
                    \Log::info('✅ (8.) copying folder taxonomy to custom_views ...');//The folder exists
                }

                //Catch up any err from above
                if (! File::exists($path_taxonomy_copy_cv))     
                    \Log::emergency('❌ (8) Err copying taxonomy to custom_views...');                    
        /*************END***************** */

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
            //If there is an error return
            return redirect()
            ->action('\App\Http\Controllers\Install\ModulesController@index')
            ->with('status', $output);
        }


        return redirect()
            ->action('\App\Http\Controllers\Install\ModulesController@index')
            ->with('status', $output);
    }

    /**
     * Uninstall
     * @return Response
     */
    public function uninstall()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            System::removeProperty($this->module_name . '_version');

            $output = ['success' => true,
                            'msg' => 'PosCustom module uninstall succesfully'
                        ];
        } catch (\Exception $e) {
            $output = ['success' => false,
                        'msg' => $e->getMessage()
                    ];
        }

        //**************INI**************** */
        /** In this section the controller will be uninstall for PosCustom*/
        //Set the paths for the files to move or rename
        \Log::info('🏃‍♂️‍➡️ Uninstalling: '. $this->module_name . ' Version: ' . $this->appVersion);
        //Controllers
            $path_sp_ori = app_path('Http/Controllers/SellPosController.php');
            $path_sp_rename = app_path('Http/Controllers/SellPosController_ori.php');
            $path_bs_ori = app_path('Http/Controllers/BusinessController.php');
            $path_bs_rename = app_path('Http/Controllers/BusinessController_ori.php');
            $path_tx_ori = app_path('Http/Controllers/TaxonomyController.php');
            $path_tx_rename = app_path('Http/Controllers/TaxonomyController_ori.php');
            //css
            $path_css_ori = public_path('css/tailwind/app.css');
            $path_css_rename = public_path('css/tailwind/app_ori.css');
        //Views must be copy to custom_views
            //Settings              
                $path_settings_del_cv = base_path('custom_views/business/partials/settings_pos.blade.php');
            //Modifiers
                $path_modifiers_del_cv = base_path('custom_views/restaurant/product_modifier_set/add_selected_modifiers.blade.php');
            //Taxonomy Categories
                $path_taxonomy_del_cv = base_path('custom_views/taxonomy');
            //js            
                $path_js_del = public_path('modules/poscustom/js/pos_js_custom.js');            

            
        //Disable and use the original controller
        if (file_exists($path_sp_rename)) { //If exist Http/Controllers/SellPosController_ori.php
            //Case Controller  SellPosController   
            File::move($path_sp_rename, $path_sp_ori);
            //Case Controller  BusinessController   
            File::move($path_bs_rename, $path_bs_ori);
            //Case Controller  TaxonomyController   
            File::move($path_tx_rename, $path_tx_ori);
            //Case tailwind/app.css
            File::move($path_css_rename, $path_css_ori);
            \Log::info('✅ (1) Restore SellPosController.php, BusinessController.php, TaxonomyController.php to original...');
            \Log::info('✅ (2) Restore Tailwind/app.css to original...');
        }

        //Deleting views from custom_views and use the original view settings
        File::delete($path_settings_del_cv, $path_modifiers_del_cv, $path_js_del);
        File::deleteDirectory($path_taxonomy_del_cv);

        if (!file_exists($path_settings_del_cv))  //If not exist settings_pos.blade_ori.php
            \Log::info('✅ (3) View '.$path_settings_del_cv. ' was delete from custom_views...');
        else
            \Log::emergency('❌ (3.) View '.$path_settings_del_cv. ' wasnt delete from custom_views...');

        if (!file_exists($path_modifiers_del_cv))  //If not exist modifiers_pos.blade_ori.php
            \Log::info('✅ (4) View '.$path_modifiers_del_cv. ' was delete from custom_views...');
        else
            \Log::emergency('❌ (4.) View '.$path_modifiers_del_cv. ' wasnt delete from custom_views...');

        if (!file_exists($path_modifiers_del_cv))  //If not exist taxonomy folder
            \Log::info('✅ (5) View '.$path_taxonomy_del_cv. ' was delete from custom_views...');
        else
            \Log::emergency('❌ (5.) View '.$path_taxonomy_del_cv. ' wasnt delete from custom_views...');
            

        //*************END***************** */ 
        
        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * update module
     * @return Response
     */
    public function update()
    {
        //Check if PosCustom_version is same as appVersion then 404
        //If appVersion > crm_version - run update script.
        //Else there is some problem.
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');

            $crm_version = System::getProperty($this->module_name . '_version');

            if (Comparator::greaterThan($this->appVersion, $crm_version)) {
                ini_set('max_execution_time', 0);
                ini_set('memory_limit', '512M');
                $this->installSettings();
                
                DB::statement('SET default_storage_engine=INNODB;');
                Artisan::call('module:migrate', ['module' => "PosCustom", "--force"=> true]);
                Artisan::call('module:publish', ['module' => "PosCustom"]);
                System::setProperty($this->module_name . '_version', $this->appVersion);
            } else {
                abort(404);
            }

            DB::commit();
            
            $output = ['success' => 1,
                        'msg' => 'PosCustom module updated Succesfully to version ' . $this->appVersion . ' !!'
                    ];

            return redirect()->back()->with(['status' => $output]);
        } catch (Exception $e) {
            DB::rollBack();
            die($e->getMessage());
        }
    }
}
