{{-- Camera Barcode Scanner JavaScript - Auto-injected into POS screen --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function() {
    'use strict';
    
    // Wait for DOM to be ready
    $(document).ready(function() {
        console.log('CameraBarcodeScanner: Script loaded, checking POS screen...');
        
        // Check if we're on POS screen (search_product input exists)
        if ($('#search_product').length === 0) {
            console.log('CameraBarcodeScanner: Not on POS screen (#search_product not found), skipping initialization');
            return; // Not on POS screen, skip initialization
        }
        
        console.log('CameraBarcodeScanner: POS screen detected, waiting for form to load...');
        
        // Wait a bit for POS form to fully load - increased timeout for reliability
        setTimeout(function() {
            console.log('CameraBarcodeScanner: Starting injection process...');
            // Auto-inject camera button into POS form next to search input
            injectCameraButton();
            
            // Initialize scanner functionality (also injects modal)
            initializeCameraScanner();
        }, 1000);
    });
    
    /**
     * Inject camera button into POS form automatically
     * Button is added next to the search_product input group
     */
    function injectCameraButton() {
        // Check if button already exists
        if ($('.camera_scanner_btn').length > 0) {
            return; // Already injected
        }
        
        // Find the input group containing search_product - try multiple selectors
        let $searchInputGroup = $('#search_product').closest('.input-group');
        
        // If not found, try finding by parent form-group
        if ($searchInputGroup.length === 0) {
            $searchInputGroup = $('#search_product').closest('.form-group').find('.input-group');
        }
        
        // If still not found, try finding by parent that contains input-group
        if ($searchInputGroup.length === 0) {
            $searchInputGroup = $('#search_product').parent('.input-group');
        }
        
        if ($searchInputGroup.length === 0) {
            console.error('CameraBarcodeScanner: Could not find search_product input group. Trying alternative injection method...');
            // Alternative: try to inject directly after the input
            const $searchInput = $('#search_product');
            if ($searchInput.length > 0) {
                // Create button and insert after input's parent
                const $cameraBtn = $('<button>', {
                    type: 'button',
                    class: 'btn btn-default bg-white btn-flat camera_scanner_btn',
                    'data-toggle': 'modal',
                    'data-target': '#camera_scanner_modal',
                    title: '{{ __("camerabarcodescanner::lang.camera_scanner") }}',
                    html: '<i class="fa fa-camera text-primary fa-lg"></i>',
                    style: 'margin-left: 5px;'
                });
                
                // Try to insert after the input or its parent
                $searchInput.after($cameraBtn);
                console.log('CameraBarcodeScanner: Camera button injected using alternative method');
                return;
            }
            console.error('CameraBarcodeScanner: Could not inject camera button - search_product input not found');
            return;
        }
        
        // Find the input-group-btn span (where other buttons are)
        let $buttonContainer = $searchInputGroup.find('.input-group-btn').last();
        
        // If no button container exists, find the input and add button container after it
        if ($buttonContainer.length === 0) {
            const $input = $searchInputGroup.find('input#search_product');
            if ($input.length > 0) {
                // Check if there's already a span after the input
                $buttonContainer = $input.next('.input-group-btn');
                if ($buttonContainer.length === 0) {
                    // Create new button container
                    $buttonContainer = $('<span>', { class: 'input-group-btn' });
                    $input.after($buttonContainer);
                }
            }
        }
        
        // Create and inject camera button
        const $cameraBtn = $('<button>', {
            type: 'button',
            class: 'btn btn-default bg-white btn-flat camera_scanner_btn',
            'data-toggle': 'modal',
            'data-target': '#camera_scanner_modal',
            title: '{{ __("camerabarcodescanner::lang.camera_scanner") }}',
            html: '<i class="fa fa-camera text-primary fa-lg"></i>'
        });
        
        // The POS form has structure: input-group > input-group-btn (before) > input#search_product > span.input-group-btn (after)
        // We want to insert into the span.input-group-btn that comes AFTER the input
        
        // Find the input-group-btn that comes after the search_product input
        const $afterBtnContainer = $('#search_product').next('.input-group-btn');
        
        if ($afterBtnContainer.length > 0) {
            // Insert before the weighing scale button if it exists
            const $weighingBtn = $afterBtnContainer.find('#weighing_scale_btn');
            if ($weighingBtn.length > 0) {
                $cameraBtn.insertBefore($weighingBtn);
                console.log('CameraBarcodeScanner: Camera button inserted before weighing scale button');
            } else {
                // Insert before quick add product button if exists
                const $quickAddBtn = $afterBtnContainer.find('.pos_add_quick_product');
                if ($quickAddBtn.length > 0) {
                    $cameraBtn.insertBefore($quickAddBtn);
                    console.log('CameraBarcodeScanner: Camera button inserted before quick add button');
                } else {
                    // Just prepend to the button container (so it appears first)
                    $afterBtnContainer.prepend($cameraBtn);
                    console.log('CameraBarcodeScanner: Camera button prepended to button container');
                }
            }
        } else if ($buttonContainer.length > 0) {
            // Fallback: use the found button container
            $buttonContainer.append($cameraBtn);
            console.log('CameraBarcodeScanner: Camera button appended to found container');
        } else {
            // Last resort: create button container and insert after input
            const $newContainer = $('<span>', { class: 'input-group-btn' });
            $newContainer.append($cameraBtn);
            $('#search_product').after($newContainer);
            console.log('CameraBarcodeScanner: Camera button injected with new container');
        }
        
        console.log('CameraBarcodeScanner: Camera button injected successfully at', new Date().toISOString());
    }
    
    // Scanner variables
    let html5QrcodeScanner = null;
    let isScanning = false;
    let targetInputSelector = '#search_product'; // Default target input
    
    /**
     * Inject camera scanner modal into body if not exists
     */
    function injectCameraModal() {
        // Check if modal already exists
        if ($('#camera_scanner_modal').length > 0) {
            return; // Already exists
        }
        
        // Inject modal HTML
        const modalHTML = `
<div class="modal fade" id="camera_scanner_modal" tabindex="-1" role="dialog" aria-labelledby="camera_scanner_modal_label">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="camera_scanner_modal_label">
                    <i class="fa fa-camera"></i> {{ __("camerabarcodescanner::lang.camera_scanner") }}
                </h4>
            </div>
            <div class="modal-body">
                <div id="camera_scanner_container" style="width: 100%; text-align: center;">
                    <div id="camera_scanner_camera" style="width: 100%; display: inline-block;"></div>
                    <div id="camera_scanner_result" style="margin-top: 15px; display: none;">
                        <div class="alert alert-success">
                            <strong>{{ __("camerabarcodescanner::lang.scan_success") }}:</strong> 
                            <span id="scanned_barcode"></span>
                        </div>
                    </div>
                </div>
                <div id="camera_scanner_status" style="margin-top: 10px; text-align: center;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="stop_camera_btn" style="display: none;">
                    <i class="fa fa-stop"></i> {{ __("camerabarcodescanner::lang.stop_camera") }}
                </button>
                <button type="button" class="btn btn-default" id="start_camera_btn">
                    <i class="fa fa-play"></i> {{ __("camerabarcodescanner::lang.start_camera") }}
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    {{ __("messages.close") }}
                </button>
            </div>
        </div>
    </div>
</div>`;
        
        // Append to body
        $('body').append(modalHTML);
        console.log('CameraBarcodeScanner: Modal injected successfully');
    }
    
    /**
     * Initialize camera scanner functionality
     */
    function initializeCameraScanner() {
        // Inject modal first
        injectCameraModal();
        // Initialize when modal is shown
        $('#camera_scanner_modal').on('shown.bs.modal', function() {
            // Determine which input field to use
            const $barcodeInput = $('#search_product_barcode_type_check');
            if ($barcodeInput.length && $barcodeInput.is(':visible') && !$barcodeInput.is(':disabled')) {
                targetInputSelector = '#search_product_barcode_type_check';
            } else {
                targetInputSelector = '#search_product';
            }
        });
        
        // Clean up when modal is hidden
        $('#camera_scanner_modal').on('hidden.bs.modal', function() {
            stopScanner();
            $('#camera_scanner_result').hide();
            $('#scanned_barcode').text('');
        });
        
        // Start camera button
        $(document).on('click', '#start_camera_btn', function() {
            startScanner();
        });
        
        // Stop camera button
        $(document).on('click', '#stop_camera_btn', function() {
            stopScanner();
        });
    }
    
    function startScanner() {
        if (isScanning) {
            return;
        }
        
        // Check if browser supports camera
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showError('{{ __("camerabarcodescanner::lang.camera_not_supported") }}');
            return;
        }
        
        try {
            // Create scanner instance
            html5QrcodeScanner = new Html5Qrcode("camera_scanner_camera");
            
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                supportedScanTypes: [
                    Html5QrcodeScanType.SCAN_TYPE_CAMERA
                ],
                formatsToSupport: [
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.UPC_A,
                    Html5QrcodeSupportedFormats.UPC_E,
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.CODE_93,
                    Html5QrcodeSupportedFormats.CODABAR,
                    Html5QrcodeSupportedFormats.ITF,
                    Html5QrcodeSupportedFormats.QR_CODE,
                    Html5QrcodeSupportedFormats.AZTEC,
                    Html5QrcodeSupportedFormats.DATA_MATRIX
                ]
            };
            
            // Start scanning
            html5QrcodeScanner.start(
                { facingMode: "environment" }, // Use back camera by default
                config,
                onScanSuccess,
                onScanError
            ).then(() => {
                isScanning = true;
                $('#start_camera_btn').hide();
                $('#stop_camera_btn').show();
                $('#camera_scanner_status').html('<div class="alert alert-info"><i class="fa fa-info-circle"></i> {{ __("camerabarcodescanner::lang.scanning") }}</div>');
            }).catch((err) => {
                console.error('Failed to start scanner:', err);
                showError('{{ __("camerabarcodescanner::lang.failed_to_start") }}');
            });
            
        } catch (error) {
            console.error('Scanner initialization error:', error);
            showError('{{ __("camerabarcodescanner::lang.scanner_error") }}');
        }
    }
    
    function stopScanner() {
        if (html5QrcodeScanner && isScanning) {
            html5QrcodeScanner.stop().then(() => {
                isScanning = false;
                $('#start_camera_btn').show();
                $('#stop_camera_btn').hide();
                $('#camera_scanner_status').html('');
                html5QrcodeScanner.clear();
            }).catch((err) => {
                console.error('Failed to stop scanner:', err);
            });
        }
    }
    
    function onScanSuccess(decodedText, decodedResult) {
        // Hide scanning status
        $('#camera_scanner_status').html('');
        
        // Show success message
        $('#scanned_barcode').text(decodedText);
        $('#camera_scanner_result').show();
        
        // Auto-fill the search input field
        const $targetInput = $(targetInputSelector);
        if ($targetInput.length) {
            $targetInput.val(decodedText).trigger('input').focus();
            
            // Trigger search - try multiple methods
            if (typeof window.searchProduct === 'function') {
                window.searchProduct(decodedText);
            } else if (typeof window.pos_search_product === 'function') {
                window.pos_search_product(decodedText);
            } else if (typeof pos_search_product === 'function') {
                pos_search_product(decodedText);
            } else {
                // Trigger input event to trigger any existing listeners
                $targetInput.trigger('input').trigger('change');
                
                // Try triggering keydown/enter to simulate user input
                setTimeout(function() {
                    const e = $.Event('keydown', { keyCode: 13 });
                    $targetInput.trigger(e);
                }, 100);
            }
        }
        
        // Stop scanner after successful scan
        setTimeout(function() {
            stopScanner();
            $('#camera_scanner_modal').modal('hide');
        }, 1000);
    }
    
    function onScanError(errorMessage) {
        // Suppress too frequent error messages
        // Only show error if it's not a regular scan attempt message
        if (!errorMessage.includes('No MultiFormat Readers') && 
            !errorMessage.includes('NotFoundException') &&
            !errorMessage.includes('No QR code')) {
            // Don't show every error, only significant ones
        }
    }
    
    function showError(message) {
        $('#camera_scanner_status').html(
            '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + message + '</div>'
        );
    }
    
    // Expose function to change target input (if needed by other modules)
    window.setCameraScannerTarget = function(selector) {
        targetInputSelector = selector;
    };
    
    console.log('CameraBarcodeScanner: Module initialized and ready');
})();
</script>

