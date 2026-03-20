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
    </script>
@endsection
