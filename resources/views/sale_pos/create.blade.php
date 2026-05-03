@extends('layouts.app')

@section('title', __('sale.pos_sale'))

@section('content')
    <section class="content no-print">
        <input type="hidden" id="amount_rounding_method" value="{{ $pos_settings['amount_rounding_method'] ?? '' }}">
        @if (!empty($pos_settings['allow_overselling']))
            <input type="hidden" id="is_overselling_allowed">
        @endif
        @if (session('business.enable_rp') == 1)
            <input type="hidden" id="reward_point_enabled">
        @endif
        @php
            $is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
            $is_rp_enabled = session('business.enable_rp') == 1 ? true : false;
        @endphp
        {!! Form::open([
            'url' => action([\App\Http\Controllers\SellPosController::class, 'store']),
            'method' => 'post',
            'id' => 'add_pos_sell_form',
        ]) !!}
        <div class="pos-redesign-container no-print">
            {{-- Left Sidebar - Payment Buttons --}}
            <div class="pos-left-sidebar">
                @include('sale_pos.partials.pos_form_actions')
            </div>

            {{-- Center - Current Sale / Cart --}}
            <div class="pos-center-panel">
                <script>
                    var current_location_id = "{{ !empty($default_location) ? $default_location->id : '' }}";
                </script>
                {!! Form::hidden('location_id', $default_location->id ?? '', [
                    'id' => 'location_id',
                    'data-receipt_printer_type' => !empty($default_location) && !empty($default_location->receipt_printer_type)
                        ? $default_location->receipt_printer_type
                        : 'browser',
                    'data-default_payment_accounts' => !empty($default_location) ? $default_location->default_payment_accounts : '',
                ]) !!}
                {!! Form::hidden('sub_type', isset($sub_type) ? $sub_type : null) !!}
                <input type="hidden" id="item_addition_method" value="{{ $business_details->item_addition_method }}">

                @include('sale_pos.partials.pos_form')
                @include('sale_pos.partials.payment_modal')

                {{-- DDA Prescription Upload Modal --}}
                <div class="modal fade" id="dda_prescription_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header" style="background:#c0392b; color:#fff;">
                                <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Controlled Drug — Prescription Required</h4>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning">
                                    <strong>DDA Alert:</strong> This sale contains a controlled substance (Dangerous Drug). A valid prescription must be uploaded before completing the sale.
                                </div>
                                <div id="dda_drug_names_list" class="mb-10" style="margin-bottom:10px;"></div>
                                <form id="dda_prescription_form" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label>Patient Name <span class="text-danger">*</span></label>
                                        <input type="text" name="patient_name" id="dda_patient_name" class="form-control" placeholder="Full name of patient" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Prescribing Doctor <span class="text-danger">*</span></label>
                                        <input type="text" name="prescriber_name" id="dda_prescriber_name" class="form-control" placeholder="Dr. Full Name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Hospital / Clinic</label>
                                        <input type="text" name="prescriber_hospital" class="form-control" placeholder="Hospital or clinic name">
                                    </div>
                                    <div class="form-group">
                                        <label>Prescription Image <span class="text-danger">*</span></label>
                                        <input type="file" name="prescription_image" id="dda_prescription_image" class="form-control" accept="image/*" required>
                                        <p class="help-block">JPG, PNG or GIF. Max 5MB.</p>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" id="dda_cancel_btn">Cancel Sale</button>
                                <button type="button" class="btn btn-danger" id="dda_upload_and_proceed_btn">
                                    <i class="fa fa-upload"></i> Upload &amp; Proceed to Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @if (empty($pos_settings['disable_suspend']))
                    @include('sale_pos.partials.suspend_note_modal')
                @endif

                @if (empty($pos_settings['disable_recurring_invoice']))
                    @include('sale_pos.partials.recurring_invoice_modal')
                @endif
            </div>

            {{-- Right Panel - Products & Totals --}}
            <div class="pos-right-panel">
                @include('sale_pos.partials.pos_sidebar')
            </div>
        </div>
        {!! Form::close() !!}
    </section>

    <!-- This will be printed -->
    <section class="invoice print_section" id="receipt_section">
    </section>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>
    @if (empty($pos_settings['hide_product_suggestion']) && empty($pos_settings['hide_products_panel']) && isMobile())
        @include('sale_pos.partials.mobile_product_suggestions')
    @endif
    <!-- /.content -->
    <div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

    <div class="modal fade" id="expense_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    @include('sale_pos.partials.configure_search_modal')

    @include('sale_pos.partials.recent_transactions_modal')

    @include('sale_pos.partials.weighing_scale_modal')

@stop
@section('css')
    <!-- include module css -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_css_path']))
                @includeIf($value['module_css_path'])
            @endif
        @endforeach
    @endif
    <style>
        /* ====== POS Mobile Responsive ====== */
        @media (max-width: 768px) {

            /* Stack the 3-panel layout vertically */
            .pos-redesign-container {
                flex-direction: column !important;
                height: auto !important;
                padding-bottom: 60px; /* room for bottom tab bar */
            }

            /* Hide all panels by default on mobile; JS will show active one */
            .pos-left-sidebar,
            .pos-center-panel,
            .pos-right-panel {
                width: 100% !important;
                min-width: unset !important;
                max-width: unset !important;
                height: auto !important;
                display: none !important;
                flex: unset !important;
            }

            /* Active panel is shown */
            .pos-left-sidebar.mobile-active,
            .pos-center-panel.mobile-active,
            .pos-right-panel.mobile-active {
                display: flex !important;
                flex-direction: column;
            }

            /* Bottom Tab Bar */
            #pos-mobile-tabs {
                display: flex !important;
            }
        }

        /* Bottom tab bar — hidden on desktop */
        #pos-mobile-tabs {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: #1e293b;
            height: 56px;
            border-top: 2px solid #334155;
        }
        #pos-mobile-tabs .pos-tab-btn {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 600;
            border: none;
            background: transparent;
            cursor: pointer;
            padding: 4px 0;
            transition: color 0.15s;
        }
        #pos-mobile-tabs .pos-tab-btn i {
            font-size: 18px;
            margin-bottom: 2px;
        }
        #pos-mobile-tabs .pos-tab-btn.active {
            color: #22c55e;
            border-top: 2px solid #22c55e;
        }
    </style>
@stop
@section('javascript')
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/mpesa_pos.js?v=' . $asset_v) }}"></script>
    @include('sale_pos.partials.keyboard_shortcuts')

    <!-- Call restaurant module if defined -->
    @if (in_array('tables', $enabled_modules) ||
            in_array('modifiers', $enabled_modules) ||
            in_array('service_staff', $enabled_modules))
        <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <!-- include module js -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_js_path']))
                @includeIf($value['module_js_path'], ['view_data' => $value['view_data']])
            @endif
        @endforeach
    @endif
    <script type="text/javascript">
        $(document).ready(function() {
            function loadFavorites() {
                var location_id = $('#location_id').val();
                if (!location_id) return;

                $.ajax({
                    url: '/sells/pos/get-featured-products/' + location_id,
                    success: function(data) {
                        if ($(data).find('.product_list').length > 0) {
                            $('#favorites_bar').show();
                            var html = '';
                            $(data).each(function() {
                                var v_id = $(this).data('variation_id');
                                var name = $(this).data('name');
                                if (v_id) {
                                    html += '<button type="button" class="btn btn-xs btn-outline-primary favorite-item" data-variation_id="' + v_id + '" style="margin-right: 5px; border-radius: 20px; padding: 2px 12px;">' + name + '</button>';
                                }
                            });
                            $('#favorites_list').html(html);
                        } else {
                            $('#favorites_bar').hide();
                        }
                    }
                });
            }

            // Initial load
            loadFavorites();

            // Handle favorite toggle
            $(document).on('click', '.favorite-toggle', function(e) {
                e.stopPropagation();
                var variation_id = $(this).data('variation_id');
                var location_id = $('#location_id').val();
                var $this = $(this);

                $.ajax({
                    method: 'POST',
                    url: '/sells/pos/toggle-favorite',
                    data: {
                        variation_id: variation_id,
                        location_id: location_id,
                        _token: $('input[name="_token"]').val()
                    },
                    success: function(result) {
                        if (result.success) {
                            if (result.status == 'added') {
                                $this.find('i').removeClass('fa-star-o tw-text-gray-300').addClass('fa-star tw-text-yellow-500');
                                toastr.success(result.msg);
                            } else {
                                $this.find('i').removeClass('fa-star tw-text-yellow-500').addClass('fa-star-o tw-text-gray-300');
                                toastr.info(result.msg);
                            }
                            loadFavorites();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            // Add favorite product to cart on click
            $(document).on('click', '.favorite-item', function() {
                var v_id = $(this).data('variation_id');
                pos_product_row(v_id);
            });

            // Reload favorites when location changes (if it changes dynamically)
            $('#location_id').change(function() {
                loadFavorites();
            });
            // Handle Customer History Modal
            $(document).on('click', '#view_customer_history', function() {
                var contact_id = $('#customer_id').val();
                if (!contact_id) {
                    toastr.error('Please select a customer first');
                    return;
                }

                $.ajax({
                    url: '/sells/pos/get-customer-history?contact_id=' + contact_id,
                    dataType: 'html',
                    success: function(result) {
                        $('.customer_history_modal').html(result).modal('show');
                    }
                });
            });

            // Handle Repeat Order
            $(document).on('click', '.repeat-order', function() {
                var transaction_id = $(this).data('transaction_id');
                $.ajax({
                    url: '/sells/pos/get-recent-transactions?status=final&transaction_id=' + transaction_id,
                    dataType: 'json',
                    success: function(result) {
                        // Logic to add all items from this transaction to current cart
                        // This might require a new endpoint specifically for fetching items to repeat
                        toastr.info('Repeating order...');
                        // For now, we'll just log and maybe open the original invoice
                    }
                });
            });

            // Credit Limit Enforcement
            $(document).on('click', '#pos-finalize', function(e) {
                var contact_id = $('#customer_id').val();
                var current_total = __read_number($('input#final_total_input'));
                
                if (contact_id) {
                    $.ajax({
                        url: '/contacts/check-credit-limit/' + contact_id + '?amount=' + current_total,
                        dataType: 'json',
                        success: function(result) {
                            if (!result.success) {
                                toastr.error(result.msg);
                                // If blocked, we might want to prevent the default action
                                // But pos-finalize might be a complex trigger.
                            }
                        }
                    });
                }
            });
            // Handle Edit Order from URL (Reports)
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('edit_order_id')) {
                var order_id = urlParams.get('edit_order_id');
                $.ajax({
                    url: '{{ url("pos-customer-orders") }}/' + order_id + '/fetch-details-json',
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            // Open modal first
                            $('#pos-orders').trigger('click');
                            
                            // Trigger edit event
                            $(document).trigger('pos_edit_order', result.order);
                            
                            // Remove parameter from URL without reloading
                            var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.pushState({path:newUrl},'',newUrl);
                        } else {
                            toastr.error(result.msg || 'Order not found');
                        }
                    }
                });
            }
        });

        // ─── DDA Prescription Enforcement ────────────────────────────────────────
        var dda_prescription_uploaded = false;
        var dda_pending_pay_method = null;

        function ddaCheckCart() {
            var dda_rows = $('.product_row[data-is_dda="1"]');
            if (dda_rows.length === 0) return false;

            var drug_names = [];
            dda_rows.each(function () {
                var name = $(this).find('span[style*="font-weight: 700"]').first().text().trim();
                if (name) drug_names.push('<li>' + name + '</li>');
            });

            $('#dda_drug_names_list').html(
                '<strong>Controlled drugs in cart:</strong><ul>' + drug_names.join('') + '</ul>'
            );
            return true;
        }

        // Intercept ALL finalize/payment buttons using capture phase so we fire
        // BEFORE pos.js bubble-phase handlers (which are registered first).
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.pos-finalize, .pos-express-finalize');
            if (!btn) return;
            // Skip suspend — no DDA check needed
            if ($(btn).data('pay_method') === 'suspend') return;
            if (!dda_prescription_uploaded && ddaCheckCart()) {
                e.stopImmediatePropagation();
                e.stopPropagation();
                e.preventDefault();
                dda_pending_pay_method = btn.dataset.pay_method || 'cash';
                $('#dda_prescription_modal').modal('show');
            }
        }, true); // true = capture phase

        // Cancel — clear cart flag too
        $('#dda_cancel_btn').on('click', function () {
            $('#dda_prescription_modal').modal('hide');
            dda_pending_pay_method = null;
        });

        // Upload prescription then proceed to payment
        $('#dda_upload_and_proceed_btn').on('click', function () {
            var $btn = $(this);

            if (!$('#dda_patient_name').val() || !$('#dda_prescriber_name').val() || !$('#dda_prescription_image').val()) {
                toastr.error('Please fill in Patient Name, Doctor Name and upload a prescription image.');
                return;
            }

            var formData = new FormData($('#dda_prescription_form')[0]);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');

            $.ajax({
                url: '{{ route("dda.prescription.pos_upload") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        dda_prescription_uploaded = true;
                        toastr.success('Prescription uploaded for ' + response.patient_name);
                        $('#dda_prescription_modal').modal('hide');

                        // Trigger the correct payment button
                        if (dda_pending_pay_method === 'cash') {
                            $('#cash-finalize').trigger('click');
                        } else {
                            $('#pos-finalize').trigger('click');
                        }
                        dda_pending_pay_method = null;
                    } else {
                        toastr.error(response.msg || 'Upload failed. Please try again.');
                    }
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON && xhr.responseJSON.errors
                        ? Object.values(xhr.responseJSON.errors).join(' ')
                        : 'Upload failed. Please try again.';
                    toastr.error(errors);
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fa fa-upload"></i> Upload &amp; Proceed to Payment');
                }
            });
        });

        // Reset prescription flag when cart is cleared (new sale)
        $(document).on('pos_sale_submitted pos_sale_complete', function () {
            dda_prescription_uploaded = false;
        });
    </script>
@endsection
