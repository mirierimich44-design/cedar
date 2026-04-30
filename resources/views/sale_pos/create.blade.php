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
        {{-- Cart backdrop (mobile only) --}}
        <div class="pos-cart-backdrop no-print" id="pos_cart_backdrop"></div>

        <div class="pos-redesign-container no-print">
            {{-- Left Sidebar - Payment Buttons --}}
            <div class="pos-left-sidebar">
                @include('sale_pos.partials.pos_form_actions')
            </div>

            {{-- Center - Current Sale / Cart (slides up as bottom sheet on mobile) --}}
            <div class="pos-center-panel">
                <div class="pos-sheet-handle-bar no-print"></div>
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

        {{-- Mobile Bottom Bar (visible only on phones) --}}
        <div class="pos-mobile-bottom-bar no-print" id="pos_mobile_bottom_bar">
            <div class="pos-mobile-more-drawer" id="pos_mobile_more_drawer">
                <button type="button" class="pos-action-btn" id="mob_mpesa_btn">
                    <i class="fas fa-mobile-alt"></i> M-Pesa
                </button>
                <button type="button" class="pos-action-btn" data-toggle="modal" data-target="#recent_transactions_modal">
                    <i class="fas fa-history"></i> Recent
                </button>
                @if (!Gate::check('disable_quotation') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" class="pos-action-btn" id="mob_quote_btn">
                    <i class="fas fa-file-invoice"></i> Quote
                </button>
                @endif
                @if (!Gate::check('disable_draft') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                <button type="button" class="pos-action-btn" id="mob_draft_btn">
                    <i class="fas fa-save"></i> Draft
                </button>
                @endif
                @if (!Gate::check('disable_suspend_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    @if(empty($pos_settings['disable_suspend']))
                    <button type="button" class="pos-action-btn" id="mob_suspend_btn">
                        <i class="fas fa-pause-circle"></i> Suspend
                    </button>
                    @endif
                @endif
                @if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    @if(empty($pos_settings['disable_credit_sale_button']))
                    <button type="button" class="pos-action-btn" data-toggle="modal" data-target="#credit_sale_customer_modal">
                        <i class="fas fa-handshake"></i> Credit
                    </button>
                    @endif
                @endif
                @if(auth()->user()->can('sell.payments'))
                <button type="button" class="pos-action-btn" data-toggle="modal" data-target="#collect_debt_modal">
                    <i class="fas fa-hand-holding-usd"></i> Collect
                </button>
                @endif
                @if(auth()->user()->can('orders.view') || auth()->user()->can('orders.create'))
                <button type="button" class="pos-action-btn" data-toggle="modal" data-target="#orders_modal">
                    <i class="fas fa-shopping-bag"></i> Orders
                </button>
                @endif
                <button type="button" class="pos-action-btn" data-toggle="modal" data-target="#lost_sale_modal">
                    <i class="fas fa-times-circle"></i> Lost Sale
                </button>
            </div>
            <div class="pos-mobile-cart-row" id="mob_cart_trigger">
                <div class="pos-mobile-cart-info">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="pos-mobile-cart-badge" id="mob_cart_count" style="display:none">0</span>
                    <span id="mob_cart_label">View Cart</span>
                </div>
                <span class="pos-mobile-total-amount" id="mob_total_display">0.00</span>
            </div>
            <div class="pos-mobile-actions">
                <button type="button" class="pos-mobile-pay-btn" id="mob_pay_btn">
                    <i class="fas fa-credit-card"></i> Pay
                </button>
                <button type="button" class="pos-mobile-cash-btn" id="mob_cash_btn">
                    <i class="fas fa-money-bill-wave"></i> Cash
                </button>
                <button type="button" class="pos-mobile-more-btn" id="mob_more_btn">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
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

    {{-- Mobile POS — Bottom-sheet cart --}}
    <script type="text/javascript">
    (function () {
        function isMobileView() { return window.innerWidth <= 768; }

        var cartOpen = false;

        function syncTotal() {
            var txt = $('#total_payable').text().trim();
            if (txt) $('#mob_total_display').text(txt);
        }

        function syncCartCount() {
            var count = $('#pos_table tbody tr').filter(function () {
                return $(this).data('variation_id') || $(this).find('[name^="products["]').length;
            }).length;
            if (count > 0) {
                $('#mob_cart_count').text(count).show();
                $('#mob_cart_label').text(count + ' item' + (count > 1 ? 's' : '') + ' in cart');
            } else {
                $('#mob_cart_count').hide();
                $('#mob_cart_label').text('View Cart');
            }
        }

        function openCart() {
            cartOpen = true;
            $('.pos-center-panel').addClass('pos-mobile-active');
            $('#pos_cart_backdrop').addClass('active');
            syncTotal();
        }

        function closeCart() {
            cartOpen = false;
            $('.pos-center-panel').removeClass('pos-mobile-active');
            $('#pos_cart_backdrop').removeClass('active');
        }

        function closeDrawer() {
            $('#pos_mobile_more_drawer').removeClass('open');
        }

        function initMobile() {
            if (!isMobileView()) return;

            // Override search dropdown to show only product name on mobile
            var acInst = $('#search_product').data('ui-autocomplete');
            if (acInst) {
                acInst._renderItem = function (ul, item) {
                    if (item.auto_added) {
                        return $('<li style="display:none;">').appendTo(ul);
                    }
                    var isOutOfStock = item.enable_stock == 1 && (parseFloat(item.qty_available) || 0) <= 0;
                    var name = item.name || '';
                    if (item.type === 'variable' && item.variation && item.variation !== 'DUMMY') {
                        name += ' <span style="color:#94a3b8;font-weight:400;">· ' + item.variation + '</span>';
                    }
                    var html = '<div style="padding:13px 16px;border-bottom:1px solid #f1f5f9;">' +
                        '<div style="font-size:14px;font-weight:600;color:' + (isOutOfStock ? '#ef4444' : '#1e293b') + ';">' + name + '</div>' +
                        (isOutOfStock ? '<div style="font-size:11px;color:#ef4444;margin-top:2px;">Out of stock</div>' : '') +
                        '</div>';
                    var li = $('<li>').append(html);
                    if (isOutOfStock) li.addClass('ui-state-disabled');
                    return li.appendTo(ul);
                };
            }

            // Auto-load products by triggering sidebar search with empty term
            setTimeout(function () {
                var $sidebarSearch = $('#product_search_sidebar');
                if ($sidebarSearch.length && $sidebarSearch.val() === '') {
                    $sidebarSearch.trigger('input').trigger('keyup');
                }
                // Also click the first category button if products still empty
                setTimeout(function () {
                    if ($('#product_list_body').is(':empty')) {
                        $('.category-filter .category-btn').first().trigger('click');
                    }
                }, 800);
            }, 600);

            // Cart trigger
            $('#mob_cart_trigger').off('click.mob').on('click.mob', function () {
                cartOpen ? closeCart() : openCart();
            });

            // Backdrop closes cart
            $('#pos_cart_backdrop').off('click.mob').on('click.mob', function () {
                closeCart();
            });

            // Bottom bar buttons
            $('#mob_pay_btn').off('click.mob').on('click.mob', function () {
                closeCart();
                setTimeout(function () { $('#pos-finalize').trigger('click'); }, 100);
            });
            $('#mob_cash_btn').off('click.mob').on('click.mob', function () {
                closeCart();
                setTimeout(function () { $('#cash-finalize').trigger('click'); }, 100);
            });
            $('#mob_mpesa_btn').off('click.mob').on('click.mob', function () {
                $('#pos-mpesa-stk').trigger('click');
                closeDrawer();
            });
            $('#mob_quote_btn').off('click.mob').on('click.mob', function () {
                $('#pos-finalize-quotation').trigger('click');
                closeDrawer();
            });
            $('#mob_draft_btn').off('click.mob').on('click.mob', function () {
                $('#pos-draft').trigger('click');
                closeDrawer();
            });
            $('#mob_suspend_btn').off('click.mob').on('click.mob', function () {
                $('.pos-express-finalize[data-pay_method="suspend"]').trigger('click');
                closeDrawer();
            });

            // More drawer
            $('#mob_more_btn').off('click.mob').on('click.mob', function (e) {
                e.stopPropagation();
                $('#pos_mobile_more_drawer').toggleClass('open');
            });
            $(document).off('click.mobDrawer').on('click.mobDrawer', function (e) {
                if (!$(e.target).closest('#pos_mobile_more_drawer, #mob_more_btn').length) {
                    closeDrawer();
                }
            });
            $('#pos_mobile_more_drawer').on('click', '[data-toggle="modal"]', function () {
                closeDrawer();
            });

            // Sync total via MutationObserver
            var totalEl = document.getElementById('total_payable');
            if (totalEl) {
                syncTotal();
                new MutationObserver(syncTotal).observe(totalEl, { childList: true, subtree: true, characterData: true });
            }

            // Sync cart count + auto-open sheet when item added
            var cartBody = document.querySelector('#pos_table tbody');
            if (cartBody) {
                syncCartCount();
                new MutationObserver(function (mutations) {
                    syncCartCount();
                    mutations.forEach(function (m) {
                        if (m.addedNodes.length > 0 && !cartOpen) {
                            openCart();
                        }
                    });
                }).observe(cartBody, { childList: true });
            }
        }

        $(document).ready(function () {
            initMobile();
            var wasMobile = isMobileView();
            $(window).on('resize', function () {
                var nowMobile = isMobileView();
                if (nowMobile && !wasMobile) { initMobile(); }
                if (!nowMobile) { closeCart(); closeDrawer(); }
                wasMobile = nowMobile;
            });
        });
    }());
    </script>
@endsection
