<?php

namespace Modules\CameraBarcodeScanner\Http\Controllers;

use App\System;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function __construct()
    {
        $this->module_name = 'camerabarcodescanner';
        $this->appVersion = config('camerabarcodescanner.module_version', '1.0.0');
        $this->module_display_name = 'Camera Barcode Scanner';
    }

    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $this->installSettings();

        //Check if installed or not.
        $is_installed = System::getProperty($this->module_name . '_version');

        // If not installed, install it directly (like Exchange module)
        if (empty($is_installed)) {
            try {
                DB::statement('SET default_storage_engine=INNODB;');
                
                // No database migrations needed for this module
                // Just save version
                System::addProperty($this->module_name . '_version', $this->appVersion);
                
                \Log::info('CameraBarcodeScanner module installed successfully');
            } catch (\Exception $e) {
                \Log::error('CameraBarcodeScanner installation error: ' . $e->getMessage());
                throw $e;
            }
        }

        $output = [
            'success' => 1,
            'msg' => 'Camera Barcode Scanner module installed successfully',
        ];

        return redirect()
            ->action([\App\Http\Controllers\Install\ModulesController::class, 'index'])
            ->with('status', $output);
    }

    private function installSettings()
    {
        config(['app.debug' => true]);
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
    }

    /**
     * Installing Camera Barcode Scanner Module
     */
    public function install()
    {
        $is_installed = System::getProperty($this->module_name . '_version');
        if (!empty($is_installed)) {
            abort(404);
        }

        try {
            DB::beginTransaction();
            
            $this->installSettings();

            // No database migrations needed for this module
            // Artisan::call('module:migrate', ['module' => "CameraBarcodeScanner"]);

            DB::statement('SET default_storage_engine=INNODB;');

            System::addProperty($this->module_name . '_version', $this->appVersion);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => 'Camera Barcode Scanner module installed successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
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

            $output = [
                'success' => true,
                'msg' => __("lang_v1.success")
            ];
        } catch (\Exception $e) {
            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * update module
     * @return Response
     */
    public function update()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');

            $module_version = System::getProperty($this->module_name . '_version');

            if (empty($module_version) || version_compare($this->appVersion, $module_version, '>')) {
                ini_set('max_execution_time', 0);
                ini_set('memory_limit', '512M');
                $this->installSettings();

                DB::statement('SET default_storage_engine=INNODB;');
                
                System::setProperty($this->module_name . '_version', $this->appVersion);
            } else {
                abort(404);
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => 'Camera Barcode Scanner module updated successfully to version ' . $this->appVersion . ' !!'
            ];

            return redirect()->back()->with(['status' => $output]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
            
            return redirect()->back()->with(['status' => $output]);
        }
    }
}

