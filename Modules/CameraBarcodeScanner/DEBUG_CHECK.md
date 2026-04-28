# Camera Barcode Scanner - Debug Checklist

## Issue: Module not injecting into POS screen

### Steps to Debug:

1. **Check Module Installation**
   ```php
   // Run in tinker or check database
   \App\System::getProperty('camerabarcodescanner_version');
   // Should return version number like "1.0.0"
   ```

2. **Check Browser Console**
   Open POS screen and check browser console for:
   - `CameraBarcodeScanner: Script loaded, checking POS screen...`
   - `CameraBarcodeScanner: POS screen detected, waiting for form to load...`
   - `CameraBarcodeScanner: Starting injection process...`
   - `CameraBarcodeScanner: Camera button injected successfully`

3. **Check Laravel Logs**
   Check `storage/logs/laravel.log` for:
   - `CameraBarcodeScanner: Module detected as installed, returning POS screen data`
   - Or: `CameraBarcodeScanner: Module not installed, skipping injection`

4. **Verify View Path**
   The view should be loaded from:
   `Modules/CameraBarcodeScanner/Resources/views/components/camera_scanner_js.blade.php`
   
   Check if view exists and is accessible:
   ```php
   view('camerabarcodescanner::components.camera_scanner_js')->render();
   ```

5. **Check POS Screen HTML**
   View page source on `/pos/create` and search for:
   - `camera_scanner_js` - Should see the script tag
   - `CameraBarcodeScanner` - Should see module initialization

6. **Verify Module Hook**
   Check if DataController method is being called:
   - Add `\Log::info('DataController::get_pos_screen_view called');` at start of method
   - Check logs to see if it's being invoked

### Common Issues:

1. **Module not installed**
   - Solution: Install module from `/manage-modules`

2. **View path wrong**
   - Check ServiceProvider view namespace registration
   - Verify view file exists

3. **JavaScript not loading**
   - Check browser console for errors
   - Verify jQuery is loaded before our script
   - Check if script tag is in HTML

4. **Button not injecting**
   - Check if `#search_product` exists
   - Verify input group structure matches expectations
   - Check console for injection errors

5. **Timing issue**
   - Script might be loading before POS form is ready
   - Try increasing setTimeout delay
   - Check if using document.ready is sufficient

### Quick Fix Test:

Add this directly to POS view temporarily to test:
```html
<script>
console.log('Test: CameraBarcodeScanner module check');
console.log('jQuery loaded:', typeof $ !== 'undefined');
console.log('search_product exists:', $('#search_product').length > 0);
</script>
```

