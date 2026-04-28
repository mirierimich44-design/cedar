<?php

namespace Modules\CameraBarcodeScanner\Http\Controllers;

use Illuminate\Routing\Controller;
use App\System;

class DataController extends Controller
{
    /**
     * Get POS screen view data
     * This method is called by the system to get module-specific POS view components
     * Returns view component that will be automatically injected into POS screen
     *
     * @param  array  $arguments
     * @return array
     */
    public function get_pos_screen_view($arguments = [])
    {
        // Check if module is installed - try both possible property names
        $is_installed = System::getProperty('camerabarcodescanner_version') 
                     ?? System::getProperty('CameraBarcodeScanner_version');
        
        if (empty($is_installed)) {
            \Log::info('CameraBarcodeScanner: Module not installed, skipping injection');
            return []; // Module not installed, don't inject anything
        }

        \Log::info('CameraBarcodeScanner: Module detected as installed, returning POS screen data');

        // Return structure - ModuleUtil already keys by module name, so return array directly
        return [
            'module_name' => 'CameraBarcodeScanner',
            'template_path' => 'camerabarcodescanner::components.camera_scanner',
            'template_data' => [],
            'module_js_path' => 'camerabarcodescanner::components.camera_scanner_js',
            'module_css_path' => null, // No CSS needed, using existing styles
            'view_data' => []
        ];
    }
}

