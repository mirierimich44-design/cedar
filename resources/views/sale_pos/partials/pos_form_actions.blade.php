@php
	$is_mobile = isMobile();
@endphp



<div class="pos-sidebar-buttons">
    {{-- Primary Action: Multiple PAY --}}
    <button type="button" class="pos-action-btn pos-action-primary pos-finalize" id="pos-finalize" data-pay_method="cash" title="@lang('lang_v1.tooltip_finalize_multi_payment')">
        <i class="fas fa-credit-card"></i>
        <span class="btn-text">Multiple Pay</span>
    </button>

    {{-- Express CASH --}}
    <button type="button" class="pos-action-btn pos-action-cash pos-express-finalize" data-pay_method="cash" id="cash-finalize" title="@lang('lang_v1.tooltip_cash')">
        <i class="fas fa-money-bill-wave"></i>
        <span class="btn-text">Cash</span>
    </button>

    {{-- Express M-PESA --}}
    <button type="button" class="pos-action-btn pos-action-mpesa" id="pos-mpesa-stk" data-target="#mpesa_details_modal" data-toggle="modal" title="M-Pesa STK Push">
        <i class="fas fa-mobile-alt"></i>
        <span class="btn-text">M-Pesa</span>
    </button>

    {{-- Quotation --}}
    @if (!Gate::check('disable_quotation') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
    <button type="button" class="pos-action-btn pos-action-quote" id="pos-finalize-quotation" title="@lang('lang_v1.quotation')">
        <i class="fas fa-file-invoice"></i>
        <span class="btn-text">Quote</span>
    </button>
    @endif

    {{-- Recent Transactions --}}
    <button type="button" class="pos-action-btn pos-action-recent" data-toggle="modal" data-target="#recent_transactions_modal" id="recent-transactions">
        <i class="fas fa-history"></i>
        <span class="btn-text">Recent</span>
    </button>

    {{-- Draft --}}
    @if (!Gate::check('disable_draft') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
    <button type="button" class="pos-action-btn pos-action-draft @if ($pos_settings['disable_draft'] != 0) hide @endif" id="pos-draft" title="@lang('sale.draft')">
        <i class="fas fa-save"></i>
        <span class="btn-text">Draft</span>
    </button>
    @endif

    {{-- Suspend --}}
    @if (!Gate::check('disable_suspend_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
        @if(empty($pos_settings['disable_suspend']))
        <button type="button" class="pos-action-btn pos-action-suspend pos-express-finalize" data-pay_method="suspend" title="@lang('sale.suspend')">
            <i class="fas fa-pause-circle"></i>
            <span class="btn-text">Suspend</span>
        </button>
        @endif
    @endif

    {{-- Credit Button --}}
    @if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
        @if(empty($pos_settings['disable_credit_sale_button']))
        <button type="button" class="pos-action-btn pos-action-credit" id="pos-credit-sale-btn" title="Credit Options">
            <i class="fas fa-handshake"></i>
            <span class="btn-text">Credit</span>
        </button>
        @endif
    @endif

    {{-- Orders --}}
    @if(auth()->user()->can('orders.view') || auth()->user()->can('orders.create'))
    <button type="button" class="pos-action-btn pos-action-orders" data-toggle="modal" data-target="#orders_modal" id="pos-orders">
        <i class="fas fa-shopping-bag"></i>
        <span class="btn-text">Orders</span>
    </button>
    @endif

    {{-- Follow-ups --}}
    @if(auth()->user()->can('followups.create'))
    <button type="button" class="pos-action-btn pos-action-followup" data-toggle="modal" data-target="#followup_modal" id="pos-followup">
        <i class="fas fa-user-clock"></i>
        <span class="btn-text">Follow Up</span>
    </button>
    @endif

    {{-- Lost Sales --}}
    <button type="button" class="pos-action-btn pos-action-lost-sale" data-toggle="modal" data-target="#lost_sale_modal" id="pos-lost-sale" title="Lost Sales Lookup" style="background: linear-gradient(135deg, #b91c1c 0%, #ef4444 100%);">
        <i class="fas fa-times-circle"></i>
        <span class="btn-text">Lost Sale</span>
    </button>
</div>

{{-- Include Orders Modal --}}
@if(auth()->user()->can('orders.view') || auth()->user()->can('orders.create'))
    @include('sale_pos.partials.orders_modal')
@endif

{{-- Include Follow-up Modal --}}
@if(auth()->user()->can('followups.create'))
    @include('sale_pos.partials.followup_modal')
@endif

{{-- Lost Sale Modal --}}
<style>
    /* ── Lost Sale Items Table ── */
    #ls_items_table td { vertical-align: middle !important; }
    .ls-remove-btn { padding: 2px 7px; font-size: 12px; }
    /* ── Custom search dropdown ── */
    #ls_search_dropdown::-webkit-scrollbar { width: 5px; }
    #ls_search_dropdown::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
    #ls_search_dropdown::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<div class="modal fade" id="lost_sale_modal" tabindex="-1" role="dialog" aria-labelledby="lostSaleModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #b91c1c 0%, #ef4444 100%); border: none; padding: 20px;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1; font-size: 28px; text-shadow: none;">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title" id="lostSaleModalLabel" style="color: white; font-weight: 700; font-size: 20px;">
                    <i class="fas fa-times-circle"></i> Lost Sale Lookup
                </h4>
            </div>
            <div class="modal-body" style="padding: 25px;">

                {{-- Search bar --}}
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; color: #374151; margin-bottom: 8px; display: block;">
                        <i class="fas fa-search" style="color: #b91c1c;"></i> Search &amp; Add Products
                    </label>
                    <div style="position: relative;">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                            <input type="text" id="lost_sale_product_input" class="form-control"
                                   placeholder="Type product name, SKU or barcode to add..."
                                   autocomplete="off">
                        </div>
                        {{-- Fully custom dropdown — no jQuery UI, 100% our HTML --}}
                        <div id="ls_search_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:100000; background:#fff; border:1px solid #e2e8f0; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.15); max-height:340px; overflow-y:auto; overflow-x:hidden; margin-top:4px; padding:4px 0;"></div>
                    </div>
                    <small class="text-muted">Type at least 2 characters — click a product to add it to the list below</small>
                </div>

                {{-- Products list table --}}
                <table class="table table-bordered table-condensed" id="ls_items_table">
                    <thead style="background:#f3f4f6;">
                        <tr>
                            <th>Product</th>
                            <th style="width:80px;">SKU</th>
                            <th style="width:80px;">Price</th>
                            <th style="width:90px;">Qty</th>
                            <th>Notes</th>
                            <th style="width:40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="ls_items_body">
                        <tr id="ls_empty_row">
                            <td colspan="6" class="text-center text-muted" style="padding:20px;">
                                <i class="fa fa-info-circle"></i> No products added yet — search above to add
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- Success message --}}
                <div id="ls_record_success" style="display:none; padding:10px 14px; background:#d1fae5; border-radius:6px; color:#065f46; font-size:13px; margin-top:10px;">
                    <i class="fa fa-check-circle"></i> <span id="ls_success_msg">Lost sales recorded.</span>
                </div>

            </div>
            <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 15px 25px; background: #f8fafc;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px;">Close</button>
                <button type="button" id="ls_record_all_btn" class="btn btn-danger" style="border-radius:6px;" disabled>
                    <i class="fa fa-save"></i> Record All Lost Sales
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
(function() {
    var checkJquery = function() {
        if (typeof jQuery === 'undefined') {
            setTimeout(checkJquery, 100);
            return;
        }
        $(document).ready(function() {
            var lsItems   = {};
            var lsCounter = 0;
            var lsTimer   = null;
            var lsXhr     = null;

            // ── Custom search dropdown (no jQuery UI) ─────────────────────
            var $input    = $('#lost_sale_product_input');
            var $dropdown = $('#ls_search_dropdown');

            // ── Shared uniform product row builder ───────────────────────
            function buildProductRow(p, onClickFn) {
                var name     = p.name || p.product_name || '—';
                var sku      = p.sub_sku || p.sku || '—';
                var varText  = (p.variation && p.variation !== 'DUMMY') ? ' · ' + p.variation
                             : (p.variation_name && p.variation_name !== 'DUMMY') ? ' · ' + p.variation_name : '';
                var cost     = parseFloat(p.purchase_price || 0);
                var sell     = parseFloat(p.selling_price  || 0);
                var qty      = parseFloat(p.qty_available  || p.system_qty || 0);
                var hasStock = (p.enable_stock == 1);
                var isOut    = hasStock && qty <= 0;

                var $row = $('<div>').css({ display:'flex', alignItems:'center', padding:'9px 20px', borderBottom:'1px solid #f1f5f9', cursor:'pointer', background:'#fff' });

                var $left = $('<div>').css({ flex:'1', minWidth:'0', overflow:'hidden', paddingRight:'12px' });
                $('<div>').css({ fontWeight:'700', fontSize:'13px', color:'#1e293b', whiteSpace:'nowrap', overflow:'hidden', textOverflow:'ellipsis' }).text(name + varText).appendTo($left);
                $('<div>').css({ fontSize:'11px', color:'#94a3b8', marginTop:'2px' }).text('SKU: ' + sku).appendTo($left);
                $row.append($left);

                function sep() { return $('<div>').css({ width:'1px', height:'34px', background:'#e2e8f0', flexShrink:'0' }); }

                function dataCol(label, value, textColor, bg) {
                    var $c = $('<div>').css({ width:'72px', flexShrink:'0', textAlign:'center', padding:'3px 6px', borderRadius:'5px', background: bg || 'transparent' });
                    $('<div>').css({ fontSize:'9px', color: textColor || '#94a3b8', textTransform:'uppercase', fontWeight:'600', letterSpacing:'0.4px', marginBottom:'2px' }).text(label).appendTo($c);
                    $('<div>').css({ fontSize:'12px', fontWeight:'700', color: textColor || '#475569' }).text(value).appendTo($c);
                    return $c;
                }

                var costVal  = cost > 0 ? cost.toFixed(2) : '—';
                var stockVal = hasStock ? qty.toFixed(0) : '—';
                var stockClr = hasStock ? (isOut ? '#ef4444' : '#3b82f6') : '#cbd5e1';
                var stockBg  = hasStock ? (isOut ? '#fef2f2' : '#eff6ff') : 'transparent';

                $row.append(sep()).append(dataCol('Cost',  costVal,         '#64748b', ''));
                $row.append(sep()).append(dataCol('Sell',  sell.toFixed(2), '#16a34a', '#f0fdf4'));
                $row.append(sep()).append(dataCol('Stock', stockVal,        stockClr,  stockBg));

                $row.on('mouseenter', function() { $(this).css('background', isOut ? '#fff8f8' : '#f8fafc'); })
                    .on('mouseleave', function() { $(this).css('background', '#fff'); })
                    .on('click', function() { onClickFn(p); });
                return $row;
            }

            function renderLsDropdown(products) {
                $dropdown.empty();
                if (!products.length) {
                    $dropdown.append($('<div>').css({ padding:'14px', textAlign:'center', color:'#94a3b8', fontSize:'13px' }).text('No products found')).show();
                    return;
                }
                $.each(products, function(i, p) {
                    $dropdown.append(buildProductRow(p, function(product) {
                        addLostSaleItem(product);
                        $dropdown.hide().empty();
                        $input.val('').focus();
                    }));
                });
                $dropdown.show();
            }

            $input.on('input', function() {
                var term = $(this).val().trim();
                clearTimeout(lsTimer);

                if (term.length < 2) {
                    if (lsXhr) { lsXhr.abort(); lsXhr = null; }
                    $dropdown.hide().empty();
                    return;
                }

                lsTimer = setTimeout(function() {
                    if (lsXhr) { lsXhr.abort(); }

                    $dropdown.show().html(
                        '<div style="padding:14px;text-align:center;color:#94a3b8;font-size:13px;">' +
                        '<i class="fa fa-spinner fa-spin"></i> Searching...</div>'
                    );

                    lsXhr = $.ajax({
                        url: '{{ url("products/list") }}',
                        dataType: 'json',
                        data: {
                            term:           term,
                            location_id:    $('input#location_id').val(),
                            not_for_selling: 0
                        },
                        success: function(data) {
                            renderLsDropdown(Array.isArray(data) ? data : []);
                        },
                        error: function(xhr) {
                            if (xhr.statusText !== 'abort') {
                                $dropdown.hide().empty();
                            }
                        }
                    });
                }, 300);
            });

            // Close dropdown when clicking anywhere outside the search area
            $(document).on('click.lsDropdown', function(e) {
                if (!$(e.target).closest('#lost_sale_product_input, #ls_search_dropdown').length) {
                    $dropdown.hide().empty();
                }
            });

            // ── Add product to table ──────────────────────────────────────
            function addLostSaleItem(product) {
                var key = product.variation_id ? 'v' + product.variation_id : 'c' + (++lsCounter);

                // If already in list, just bump qty by 1
                if (product.variation_id && lsItems['v' + product.variation_id]) {
                    var $qtyInput = $('#ls_qty_' + product.variation_id);
                    $qtyInput.val(parseFloat($qtyInput.val() || 1) + 1);
                    return;
                }

                lsItems[key] = product;
                $('#ls_empty_row').hide();
                $('#ls_record_all_btn').prop('disabled', false);
                $('#ls_record_success').hide();

                var name = product.name || '-';
                if (product.variation && product.variation !== 'DUMMY') {
                    name += ' <small class="text-muted">(' + product.variation + ')</small>';
                }
                var sku  = product.sub_sku || '-';
                var price = parseFloat(product.selling_price || 0).toFixed(2);
                var qtyId   = product.variation_id ? 'ls_qty_' + product.variation_id   : 'ls_qty_c'   + lsCounter;
                var notesId = product.variation_id ? 'ls_notes_' + product.variation_id : 'ls_notes_c' + lsCounter;

                var row = '<tr id="ls_row_' + key + '">' +
                    '<td>' + name + '</td>' +
                    '<td><small>' + sku + '</small></td>' +
                    '<td style="color:#16a34a;font-weight:700;">' + price + '</td>' +
                    '<td><input type="number" id="' + qtyId + '" class="form-control input-sm ls-qty" value="1" min="1" step="any" style="width:70px;"></td>' +
                    '<td><input type="text" id="' + notesId + '" class="form-control input-sm ls-notes" placeholder="optional"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-xs ls-remove-btn" data-key="' + key + '"><i class="fa fa-times"></i></button></td>' +
                '</tr>';

                $('#ls_items_body').append(row);
                // Store key on the row for easy access
                $('#ls_row_' + key).data('key', key);
            }

            // ── Remove a row ─────────────────────────────────────────────
            $(document).on('click', '.ls-remove-btn', function() {
                var key = $(this).data('key');
                delete lsItems[key];
                $('#ls_row_' + key).remove();
                if (Object.keys(lsItems).length === 0) {
                    $('#ls_empty_row').show();
                    $('#ls_record_all_btn').prop('disabled', true);
                }
            });

            // ── Record All ───────────────────────────────────────────────
            $('#ls_record_all_btn').on('click', function() {
                var keys = Object.keys(lsItems);
                if (keys.length === 0) return;

                var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Recording...');
                var location_id = $('input#location_id').val();
                var token = '{{ csrf_token() }}';
                var promises = [];
                var failed = 0;

                keys.forEach(function(key) {
                    var product = lsItems[key];
                    var vid     = product.variation_id;
                    var qty     = parseFloat(vid ? $('#ls_qty_' + vid).val()   : $('#ls_qty_'   + key.replace('v','').replace('c','')).val())   || 1;
                    var notes   = vid ? $('#ls_notes_' + vid).val() : $('#ls_notes_' + key.replace('v','').replace('c','')).val();

                    // Get correct qty/notes input by row
                    var $row = $('#ls_row_' + key);
                    qty   = parseFloat($row.find('.ls-qty').val())   || 1;
                    notes = $row.find('.ls-notes').val();

                    var def = $.ajax({
                        url: '{{ route("lost_sales.store") }}',
                        method: 'POST',
                        data: {
                            _token:        token,
                            location_id:   location_id,
                            product_id:    product.product_id   || null,
                            variation_id:  product.variation_id || null,
                            product_name:  product.name,
                            sku:           product.sub_sku || '',
                            selling_price: product.selling_price || 0,
                            quantity:      qty,
                            notes:         notes
                        }
                    });
                    def.fail(function() { failed++; });
                    promises.push(def);
                });

                $.when.apply($, promises).always(function() {
                    $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Record All Lost Sales');
                    if (failed === 0) {
                        var count = keys.length;
                        $('#ls_success_msg').text(count + ' lost sale' + (count > 1 ? 's' : '') + ' recorded successfully.');
                        $('#ls_record_success').show();
                        // Clear the table
                        lsItems = {};
                        $('#ls_items_body tr:not(#ls_empty_row)').remove();
                        $('#ls_empty_row').show();
                        $('#ls_record_all_btn').prop('disabled', true);
                        $('#lost_sale_product_input').val('').focus();
                    } else {
                        toastr.error(failed + ' item(s) failed to record. Please try again.');
                    }
                });
            });

            // ── Reset on modal open ──────────────────────────────────────
            $('#lost_sale_modal').on('show.bs.modal', function() {
                lsItems = {};
                lsCounter = 0;
                if (lsXhr) { lsXhr.abort(); lsXhr = null; }
                clearTimeout(lsTimer);
                $input.val('');
                $dropdown.hide().empty();
                $('#ls_items_body tr:not(#ls_empty_row)').remove();
                $('#ls_empty_row').show();
                $('#ls_record_all_btn').prop('disabled', true);
                $('#ls_record_success').hide();
            });

            $('#lost_sale_modal').on('shown.bs.modal', function() {
                $input.focus();
            });
        });
    };
    checkJquery();
})();
</script>

@php
    $is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
    $is_rp_enabled = session('business.enable_rp') == 1 ? true : false;
@endphp

@if (isset($transaction))
    @include('sale_pos.partials.edit_discount_modal', [
        'sales_discount' => $transaction->discount_amount,
        'discount_type' => $transaction->discount_type,
        'rp_redeemed' => $transaction->rp_redeemed,
        'rp_redeemed_amount' => $transaction->rp_redeemed_amount,
        'max_available' => !empty($redeem_details['points']) ? $redeem_details['points'] : 0,
        'is_discount_enabled' => $is_discount_enabled,
        'is_rp_enabled' => $is_rp_enabled
    ])
@else
    @include('sale_pos.partials.edit_discount_modal', [
        'sales_discount' => $business_details->default_sales_discount,
        'discount_type' => 'percentage',
        'rp_redeemed' => 0,
        'rp_redeemed_amount' => 0,
        'max_available' => 0,
        'is_discount_enabled' => $is_discount_enabled,
        'is_rp_enabled' => $is_rp_enabled
    ])
@endif

@if (isset($transaction))
    @include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $transaction->tax_id])
@else
    @include('sale_pos.partials.edit_order_tax_modal', [
        'selected_tax' => $business_details->default_sales_tax,
    ])
@endif

@include('sale_pos.partials.edit_shipping_modal')

{{-- Credit Options Modal --}}
@if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
    @if(empty($pos_settings['disable_credit_sale_button']))

    {{-- Step 1: Credit Options Chooser --}}
    <div class="modal fade" id="credit_options_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document" style="max-width:340px; margin:80px auto;">
            <div class="modal-content" style="border-radius:14px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.25);">
                <div class="modal-header" style="background:linear-gradient(135deg,#7c3aed 0%,#a855f7 100%); border:none; padding:18px 20px;">
                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:1;font-size:26px;text-shadow:none;">&times;</button>
                    <h4 class="modal-title" style="color:white;font-weight:700;font-size:17px;">
                        <i class="fas fa-handshake"></i> Credit Options
                    </h4>
                </div>
                <div class="modal-body" style="padding:16px 14px; display:flex; flex-direction:column; gap:10px;">

                    {{-- Option 1: Complete Credit Sale --}}
                    <button type="button" id="copt_complete_credit_sale" class="btn btn-block" style="padding:14px 16px; background:#f5f3ff; border:2px solid #7c3aed; border-radius:10px; text-align:left; display:flex; align-items:center; gap:12px; cursor:pointer;">
                        <span style="width:36px;height:36px;background:linear-gradient(135deg,#7c3aed,#a855f7);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-check-circle" style="color:white;font-size:16px;"></i>
                        </span>
                        <span style="text-align:left;">
                            <strong style="display:block;color:#1e293b;font-size:14px;">Complete Credit Sale</strong>
                            <small style="color:#64748b;font-size:11px;">Sell now, collect payment later</small>
                        </span>
                    </button>

                    {{-- Option 2: Collect Payment --}}
                    @if(auth()->user()->can('sell.payments'))
                    <button type="button" id="copt_collect_debt" class="btn btn-block" style="padding:14px 16px; background:#f0f9ff; border:2px solid #0369a1; border-radius:10px; text-align:left; display:flex; align-items:center; gap:12px; cursor:pointer;">
                        <span style="width:36px;height:36px;background:linear-gradient(135deg,#0369a1,#0ea5e9);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-hand-holding-usd" style="color:white;font-size:16px;"></i>
                        </span>
                        <span style="text-align:left;">
                            <strong style="display:block;color:#1e293b;font-size:14px;">Collect Payment</strong>
                            <small style="color:#64748b;font-size:11px;">Receive payment from a customer</small>
                        </span>
                    </button>
                    @endif

                    {{-- Option 3: Add Customer --}}
                    <button type="button" id="copt_add_customer" class="btn btn-block" style="padding:14px 16px; background:#f0fdf4; border:2px solid #059669; border-radius:10px; text-align:left; display:flex; align-items:center; gap:12px; cursor:pointer;"
                        data-add-customer-href="{{action([\App\Http\Controllers\ContactController::class, 'create'], ['type' => 'customer'])}}">
                        <span style="width:36px;height:36px;background:linear-gradient(135deg,#059669,#10b981);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-user-plus" style="color:white;font-size:16px;"></i>
                        </span>
                        <span style="text-align:left;">
                            <strong style="display:block;color:#1e293b;font-size:14px;">Add Customer</strong>
                            <small style="color:#64748b;font-size:11px;">Create a new customer profile</small>
                        </span>
                    </button>

                </div>
            </div>
        </div>
    </div>

    {{-- Step 2: Credit Sale Customer Search Modal --}}
    <div class="modal fade" id="credit_sale_customer_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); border: none; padding: 20px;">
                    <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1; font-size: 28px; text-shadow: none;">
                        <span>&times;</span>
                    </button>
                    <h4 class="modal-title" style="color: white; font-weight: 700; font-size: 20px;">
                        <i class="fas fa-handshake"></i> Credit Sale — Select Customer
                    </h4>
                </div>
                <div class="modal-body" style="padding: 25px;">
                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <label style="font-weight: 600; color: #374151; margin-bottom: 0;">
                                <i class="fas fa-search" style="color: #7c3aed;"></i> Search Customer (Name or Phone)
                            </label>
                            <button type="button" class="btn btn-xs btn-primary btn-modal"
                                data-href="{{action([\App\Http\Controllers\ContactController::class, 'create'], ['type' => 'customer'])}}"
                                data-container=".contact_modal">
                                <i class="fa fa-plus"></i> Add New
                            </button>
                        </div>
                        <select id="credit_sale_customer_search" class="form-control" style="width: 100%;"></select>
                    </div>

                    <div id="credit_customer_details" class="hide" style="margin-top: 20px; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: #64748b;">Customer:</span>
                            <strong id="credit_customer_name" style="color: #1e293b;"></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: #64748b;">Phone:</span>
                            <strong id="credit_customer_phone" style="color: #1e293b;"></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: #64748b;">Current Balance:</span>
                            <strong id="credit_customer_balance" style="color: #dc2626;"></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Credit Limit:</span>
                            <strong id="credit_customer_limit" style="color: #059669;"></strong>
                        </div>
                    </div>

                    <div id="credit_sale_warning" class="hide" style="margin-top: 15px; padding: 12px; background: #fef2f2; border-radius: 8px; border: 1px solid #fecaca; color: #dc2626;">
                        <i class="fas fa-exclamation-triangle"></i> <span id="credit_warning_message"></span>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 15px 25px; background: #f8fafc;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                    <button type="button" id="confirm_credit_sale" class="btn btn-primary" disabled style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); border: none; border-radius: 6px; padding: 10px 25px;">
                        <i class="fas fa-check"></i> Complete Credit Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    (function() {
        function initCredit() {
            if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
                setTimeout(initCredit, 200);
                return;
            }
            var $ = jQuery;

            // ── Open credit options popup ────────────────────────────────
            $(document).on('click', '#pos-credit-sale-btn', function() {
                $('#credit_options_modal').modal('show');
            });

            // ── Option 1: Complete Credit Sale ───────────────────────────
            $(document).on('click', '#copt_complete_credit_sale', function() {
                $('#credit_options_modal').modal('hide');

                var finalTotal = parseFloat($('#final_total_input').val()) || 0;
                if (finalTotal <= 0) {
                    toastr.warning('Please add products to the cart first.');
                    return;
                }

                var customerId = $('#customer_id').val();
                if (customerId && customerId != '1') {
                    // Customer already selected on POS — finalize immediately
                    setTimeout(function() { doCompleteCreditSale(customerId, null); }, 300);
                } else {
                    // No customer selected — ask user to pick one
                    setTimeout(function() { $('#credit_sale_customer_modal').modal('show'); }, 300);
                }
            });

            // ── Option 2: Collect Debt ───────────────────────────────────
            $(document).on('click', '#copt_collect_debt', function() {
                $('#credit_options_modal').modal('hide');
                setTimeout(function() {
                    $('#collect_debt_modal').modal('show');
                }, 300);
            });

            // ── Option 3: Add Customer ───────────────────────────────────
            $(document).on('click', '#copt_add_customer', function() {
                $('#credit_options_modal').modal('hide');
                var href = $(this).data('add-customer-href');
                setTimeout(function() {
                    $.ajax({
                        url: href,
                        success: function(result) {
                            $('.contact_modal').html(result).modal('show');
                        }
                    });
                }, 300);
            });

            // ── Auto-populate customer on credit sale modal open ─────────
            $('#credit_sale_customer_modal').on('shown.bs.modal', function() {
                var main_id   = $('#customer_id').val();
                var main_data = $('#customer_id').select2('data');
                var main_name = (main_data && main_data[0]) ? main_data[0].text : '';

                if (main_id && main_id != '1') {
                    var opt = new Option(main_name, main_id, true, true);
                    $('#credit_sale_customer_search').append(opt).trigger('change');
                    $('#credit_sale_customer_search').trigger({
                        type: 'select2:select',
                        params: { data: { id: main_id, text: main_name } }
                    });
                }
            });

            // ── Init Select2 for credit customer search ──────────────────
            $('#credit_sale_customer_search').select2({
                ajax: {
                    url: '{{ url("contacts/customers") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) { return { q: params.term, page: params.page }; },
                    processResults: function(data) { return { results: data }; }
                },
                placeholder: 'Type customer name or phone number...',
                minimumInputLength: 1,
                allowClear: true,
                dropdownParent: $('#credit_sale_customer_modal'),
                templateResult: function(c) {
                    if (!c.id) return c.text;
                    return $('<span><strong>' + c.text + '</strong>' + (c.mobile ? ' - ' + c.mobile : '') + '</span>');
                }
            });

            // ── On customer selection — fetch details ────────────────────
            $('#credit_sale_customer_search').on('select2:select', function(e) {
                var customerId = e.params.data.id;
                $.ajax({
                    url: '{{ url("contacts") }}/' + customerId,
                    dataType: 'json',
                    success: function(result) {
                        if (result.data && result.data.length > 0) {
                            var customer = result.data[0];
                            $('#credit_customer_name').text(customer.name || '-');
                            $('#credit_customer_phone').text(customer.mobile || customer.landline || '-');
                            $('#credit_customer_balance').text(__currency_trans_from_en(customer.total_due || 0));
                            $('#credit_customer_limit').text(customer.credit_limit ? __currency_trans_from_en(customer.credit_limit) : 'Unlimited');
                            $('#credit_customer_details').removeClass('hide');

                            var currentTotal = parseFloat($('#final_total_input').val()) || 0;
                            var totalDue = parseFloat(customer.total_due) || 0;
                            var creditLimit = customer.credit_limit ? parseFloat(customer.credit_limit) : null;

                            if (creditLimit !== null && (totalDue + currentTotal) > creditLimit) {
                                $('#credit_warning_message').text('This sale will exceed the customer credit limit!');
                                $('#credit_sale_warning').removeClass('hide');
                            } else {
                                $('#credit_sale_warning').addClass('hide');
                            }
                            $('#confirm_credit_sale').prop('disabled', false);
                        }
                    }
                });
            });

            // ── Clear on credit modal close ──────────────────────────────
            $('#credit_sale_customer_modal').on('hidden.bs.modal', function() {
                $('#credit_sale_customer_search').val(null).trigger('change');
                $('#credit_customer_details').addClass('hide');
                $('#credit_sale_warning').addClass('hide');
                $('#confirm_credit_sale').prop('disabled', true);
            });

            // ── New contact added — update credit modal search ───────────
            $(document).on('contact_added', function(e, contact) {
                if ($('#credit_sale_customer_modal').is(':visible')) {
                    var opt = new Option(contact.name, contact.id, true, true);
                    $('#credit_sale_customer_search').append(opt).trigger('change');
                    $('#credit_sale_customer_search').trigger({ type: 'select2:select', params: { data: contact } });
                }
            });

            // ── Shared credit sale submitter ──────────────────────────────
            // Uses the existing POS submit flow (pos_form_obj / jQuery validate)
            // exactly like .pos-express-finalize[data-pay_method="credit_sale"] does.
            function doCompleteCreditSale(customerId, customerName) {
                var form = $('form#add_pos_sell_form').length
                    ? $('form#add_pos_sell_form')
                    : $('form#edit_pos_sell_form');

                // Ensure the customer is set on the main POS form
                if (customerId && $('#customer_id').val() != customerId) {
                    var opt = new Option(customerName || customerId, customerId, true, true);
                    $('#customer_id').append(opt).trigger('change');
                }

                // Set the hidden is_credit_sale flag that pos.js already reads
                if ($('#is_credit_sale').length) {
                    $('#is_credit_sale').val(1);
                }

                // Trigger the normal POS form submit — goes through pos.js
                // submitHandler which builds products, handles receipt & reset
                form.submit();
            }

            // ── Confirm & submit credit sale (from customer modal) ────────
            $(document).on('click', '#confirm_credit_sale', function() {
                var customerId   = $('#credit_sale_customer_search').val();
                var sel2data     = $('#credit_sale_customer_search').select2('data');
                var customerName = (sel2data && sel2data[0]) ? sel2data[0].text : '';

                if (!customerId) { toastr.warning('Please select a customer.'); return; }

                $('#credit_sale_customer_modal').modal('hide');
                setTimeout(function() { doCompleteCreditSale(customerId, customerName); }, 300);
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCredit);
        } else {
            setTimeout(initCredit, 100);
        }
    })();
    </script>
    @endif
@endif

{{-- ════════════════════════════════════════════════════════════════
     COLLECT DEBT MODAL
     ════════════════════════════════════════════════════════════════ --}}
@if(auth()->user()->can('sell.payments'))
<div class="modal fade" id="collect_debt_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden;">

            {{-- Header --}}
            <div class="modal-header" style="background:linear-gradient(135deg,#0369a1 0%,#0ea5e9 100%); border:none; padding:20px;">
                <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1; font-size:28px; text-shadow:none;">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title" style="color:white; font-weight:700; font-size:20px;">
                    <i class="fas fa-hand-holding-usd"></i> Collect Payment
                </h4>
            </div>

            <div class="modal-body" style="padding:25px;">

                {{-- Step 1: Customer Search --}}
                <div class="form-group" style="margin-bottom:15px;">
                    <label style="font-weight:600; color:#374151; margin-bottom:8px; display:block;">
                        <i class="fas fa-search" style="color:#0369a1;"></i> Search Customer (Name or Phone)
                    </label>
                    <div style="position:relative;">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                            <input type="text" id="cd_customer_input" class="form-control"
                                   placeholder="Type customer name or phone number..."
                                   autocomplete="off">
                        </div>
                        <div id="cd_customer_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:100000; background:#fff; border:1px solid #e2e8f0; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.15); max-height:300px; overflow-y:auto; margin-top:4px; padding:4px 0;"></div>
                    </div>
                    <small class="text-muted">Type at least 2 characters to search</small>
                </div>

                {{-- Step 2: Customer card --}}
                <div id="cd_customer_card" class="hide" style="margin-bottom:15px; padding:16px; background:#f0f9ff; border-radius:10px; border:1px solid #bae6fd;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <span style="color:#64748b;"><i class="fas fa-user" style="width:16px;"></i> Name:</span>
                        <strong id="cd_cust_name" style="color:#1e293b;"></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <span style="color:#64748b;"><i class="fas fa-phone" style="width:16px;"></i> Phone:</span>
                        <strong id="cd_cust_phone" style="color:#1e293b;"></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:#64748b;"><i class="fas fa-wallet" style="width:16px;"></i> Outstanding:</span>
                        <strong id="cd_cust_balance" style="color:#dc2626; font-size:16px;"></strong>
                    </div>
                </div>

                {{-- Zero-balance notice --}}
                <div id="cd_zero_balance_banner" class="hide" style="padding:12px 16px; background:#fef3c7; border-radius:8px; color:#92400e; margin-bottom:12px;">
                    <i class="fas fa-info-circle"></i> This customer has no outstanding balance.
                </div>

                {{-- Step 3+4: Amount + Payment (shown after customer selected and has balance) --}}
                <div id="cd_payment_section" class="hide">

                    {{-- Amount row --}}
                    <div class="form-group" style="margin-bottom:8px;">
                        <label style="font-weight:600; color:#374151; margin-bottom:6px; display:block;">Amount to Collect</label>
                        <div style="display:flex; border:2px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                            <span style="background:#f1f5f9; padding:10px 14px; font-weight:700; color:#64748b; border-right:2px solid #e2e8f0;">KES</span>
                            <input type="number" id="cd_amount" class="form-control"
                                   step="0.01" min="0.01"
                                   style="border:none; padding:10px 14px; font-size:16px; font-weight:700; outline:none; flex:1; box-shadow:none;">
                        </div>
                        <div id="cd_remaining_info" style="margin-top:5px; font-size:12px; color:#64748b;"></div>
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="cursor:pointer; font-size:13px; color:#374151; font-weight:500;">
                            <input type="checkbox" id="cd_full_balance_chk" checked style="margin-right:5px;">
                            Pay full outstanding balance
                        </label>
                    </div>

                    {{-- Payment method tabs --}}
                    <ul class="nav nav-tabs" id="cd_payment_tabs" style="margin-bottom:15px; border-bottom:2px solid #e2e8f0;">
                        <li class="active" id="cd_tab_cash_li">
                            <a href="#cd_cash_tab" data-toggle="tab" style="font-weight:600; color:#374151; border-radius:6px 6px 0 0;">
                                <i class="fas fa-money-bill-wave" style="color:#16a34a;"></i> Cash
                            </a>
                        </li>
                        <li id="cd_tab_mpesa_li">
                            <a href="#cd_mpesa_tab" data-toggle="tab" style="font-weight:600; color:#374151; border-radius:6px 6px 0 0;">
                                <i class="fas fa-mobile-alt" style="color:#00B09B;"></i> M-Pesa
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        {{-- ── Cash Tab ── --}}
                        <div id="cd_cash_tab" class="tab-pane active" style="padding:5px 0;">
                            <div class="form-group">
                                <label style="font-weight:600; color:#374151; font-size:13px;">Note (optional)</label>
                                <input type="text" id="cd_cash_note" class="form-control" placeholder="e.g. Cash received at counter">
                            </div>
                            <button type="button" id="cd_cash_submit_btn" class="btn btn-success btn-block"
                                    style="padding:12px; font-weight:700; border-radius:8px; font-size:15px;">
                                <i class="fas fa-check"></i> Record Cash Payment
                            </button>
                        </div>

                        {{-- ── M-Pesa Tab ── --}}
                        <div id="cd_mpesa_tab" class="tab-pane" style="padding:5px 0;">

                            {{-- Mode toggle --}}
                            <div style="display:flex; gap:8px; margin-bottom:15px;">
                                <button type="button" id="cd_mpesa_stk_mode_btn" class="btn btn-sm btn-primary"
                                        style="flex:1; border-radius:6px; font-weight:600;">
                                    <i class="fas fa-paper-plane"></i> STK Push
                                </button>
                                <button type="button" id="cd_mpesa_manual_mode_btn" class="btn btn-sm btn-default"
                                        style="flex:1; border-radius:6px; font-weight:600;">
                                    <i class="fas fa-keyboard"></i> Manual
                                </button>
                            </div>

                            {{-- STK section --}}
                            <div id="cd_stk_section">

                                {{-- STK input --}}
                                <div id="cd_stk_input_area">
                                    <div class="form-group">
                                        <label style="font-weight:600; color:#374151; font-size:13px;">Customer Phone</label>
                                        <div style="display:flex; border:2px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                                            <span style="background:#f1f5f9; padding:10px 12px; font-weight:700; color:#64748b; border-right:2px solid #e2e8f0;">+254</span>
                                            <input type="tel" id="cd_stk_phone" maxlength="9" placeholder="712345678"
                                                   style="flex:1; border:none; padding:10px 12px; font-size:15px; font-weight:600; outline:none;">
                                        </div>
                                    </div>
                                    <button type="button" id="cd_send_stk_btn" class="btn btn-block"
                                            style="padding:12px; background:linear-gradient(135deg,#00B09B 0%,#96C93D 100%); color:white; border:none; border-radius:8px; font-weight:700; font-size:14px;">
                                        <i class="fas fa-paper-plane"></i> Send STK Push
                                    </button>
                                </div>

                                {{-- Waiting state --}}
                                <div id="cd_stk_waiting" style="display:none; text-align:center; padding:20px 10px;">
                                    <div style="width:50px; height:50px; background:#fef3c7; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                                        <i class="fas fa-spinner fa-spin" style="color:#f59e0b; font-size:20px;"></i>
                                    </div>
                                    <h4 style="font-weight:700; color:#1e293b; margin-bottom:6px;">Waiting for PIN...</h4>
                                    <p style="color:#64748b; margin-bottom:12px;">Customer should check their phone</p>
                                    <span id="cd_stk_countdown" style="display:inline-block; background:#fef3c7; color:#d97706; font-size:22px; font-weight:800; padding:10px 20px; border-radius:8px;">2:00</span>
                                    <div style="margin-top:12px;">
                                        <button type="button" id="cd_stk_cancel_btn" class="btn btn-sm btn-default">Cancel</button>
                                    </div>
                                </div>

                                {{-- STK success state --}}
                                <div id="cd_stk_success" style="display:none; text-align:center; padding:15px 10px;">
                                    <div style="width:50px; height:50px; background:#d1fae5; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                                        <i class="fas fa-check" style="color:#10b981; font-size:20px;"></i>
                                    </div>
                                    <h4 style="font-weight:700; color:#10b981; margin-bottom:4px;">Payment Received!</h4>
                                    <p style="color:#64748b; margin-bottom:4px;">Ref: <strong id="cd_stk_receipt" style="font-family:monospace; letter-spacing:1px;"></strong></p>
                                    <button type="button" id="cd_stk_confirm_btn" class="btn btn-success btn-block"
                                            style="border-radius:8px; font-weight:700; padding:12px; margin-top:12px;">
                                        <i class="fas fa-check-double"></i> Confirm &amp; Record
                                    </button>
                                </div>

                                {{-- STK error / timeout state --}}
                                <div id="cd_stk_error" style="display:none; text-align:center; padding:15px 10px;">
                                    <div style="width:50px; height:50px; background:#fee2e2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 10px;">
                                        <i class="fas fa-times" style="color:#ef4444; font-size:20px;"></i>
                                    </div>
                                    <h4 style="font-weight:700; color:#ef4444; margin-bottom:4px;">No Response</h4>
                                    <p id="cd_stk_err_msg" style="color:#64748b; margin-bottom:12px;"></p>
                                    <div style="display:flex; gap:8px;">
                                        <button type="button" id="cd_stk_retry_btn" class="btn btn-primary" style="flex:1; border-radius:6px; font-weight:600;">Retry</button>
                                        <button type="button" id="cd_stk_fallback_manual_btn" class="btn btn-default" style="flex:1; border-radius:6px; font-weight:600;">Enter Manually</button>
                                    </div>
                                </div>

                            </div>{{-- /cd_stk_section --}}

                            {{-- Manual section --}}
                            <div id="cd_manual_section" style="display:none;">
                                <div class="form-group">
                                    <label style="font-weight:600; color:#374151; font-size:13px;">M-Pesa Reference No.</label>
                                    <input type="text" id="cd_manual_ref" class="form-control"
                                           placeholder="e.g. QZK92PX..."
                                           style="font-family:monospace; font-weight:700; text-transform:uppercase; letter-spacing:1px;">
                                </div>
                                <div class="form-group">
                                    <label style="font-weight:600; color:#374151; font-size:13px;">Note (optional)</label>
                                    <input type="text" id="cd_manual_note" class="form-control" placeholder="optional">
                                </div>
                                <button type="button" id="cd_manual_submit_btn" class="btn btn-block"
                                        style="padding:12px; background:linear-gradient(135deg,#00B09B 0%,#96C93D 100%); color:white; border:none; border-radius:8px; font-weight:700; font-size:14px;">
                                    <i class="fas fa-check"></i> Record M-Pesa Payment
                                </button>
                            </div>

                        </div>{{-- /cd_mpesa_tab --}}
                    </div>{{-- /tab-content --}}

                </div>{{-- /cd_payment_section --}}

                {{-- Success banner --}}
                <div id="cd_success_banner" style="display:none; margin-top:15px; padding:12px 16px; background:#d1fae5; border-radius:8px; color:#065f46; font-weight:600; text-align:center;">
                    <i class="fas fa-check-circle"></i> <span id="cd_success_msg"></span>
                </div>

            </div>{{-- /modal-body --}}

            <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:15px 25px; background:#f8fafc;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
(function() {
    function initCollectDebt() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
            setTimeout(initCollectDebt, 200);
            return;
        }
        var $ = jQuery;

        // ── State ────────────────────────────────────────────────────────
        var cds = {
            customerId: null,
            balance: 0,
            phone: '',
            mpesaTxnId: null,
            pollInterval: null,
            countdownInterval: null
        };

        // ── Helpers ──────────────────────────────────────────────────────
        function stopTimers() {
            if (cds.pollInterval)     { clearInterval(cds.pollInterval);     cds.pollInterval     = null; }
            if (cds.countdownInterval){ clearInterval(cds.countdownInterval); cds.countdownInterval = null; }
        }

        function formatPhoneLocal(phone) {
            if (!phone) return '';
            phone = String(phone).replace(/\D/g, '');
            if (phone.startsWith('254')) phone = phone.substring(3);
            if (phone.startsWith('0'))   phone = phone.substring(1);
            return phone;
        }

        // ── Full reset ───────────────────────────────────────────────────
        function cdReset() {
            cds.customerId = null;
            cds.balance = 0;
            cds.phone = '';
            cds.mpesaTxnId = null;
            stopTimers();

            if (cdXhr) { cdXhr.abort(); cdXhr = null; }
            clearTimeout(cdTimer);
            $cdInput.val('');
            $cdDrop.hide().empty();
            cds.customerId = null;
            $('#cd_customer_card, #cd_payment_section, #cd_zero_balance_banner').addClass('hide');
            $('#cd_success_banner').hide();

            // amount
            $('#cd_amount').val('');
            $('#cd_full_balance_chk').prop('checked', true);
            $('#cd_remaining_info').text('');

            // cash
            $('#cd_cash_note').val('');

            // tabs back to Cash
            $('#cd_tab_cash_li').addClass('active'); $('#cd_tab_mpesa_li').removeClass('active');
            $('#cd_cash_tab').addClass('active');    $('#cd_mpesa_tab').removeClass('active');

            // stk / manual
            stkReset();
            cdShowStk(true);
        }

        // ── STK reset ────────────────────────────────────────────────────
        function stkReset() {
            stopTimers();
            cds.mpesaTxnId = null;
            $('#cd_stk_input_area').show();
            $('#cd_stk_waiting, #cd_stk_success, #cd_stk_error').hide();
            $('#cd_stk_receipt').text('');
            $('#cd_stk_countdown').text('2:00');
            $('#cd_stk_phone').val(formatPhoneLocal(cds.phone));
        }

        // ── STK / Manual toggle ──────────────────────────────────────────
        function cdShowStk(show) {
            if (show) {
                $('#cd_stk_section').show(); $('#cd_manual_section').hide();
                $('#cd_mpesa_stk_mode_btn').removeClass('btn-default').addClass('btn-primary');
                $('#cd_mpesa_manual_mode_btn').removeClass('btn-primary').addClass('btn-default');
            } else {
                $('#cd_stk_section').hide(); $('#cd_manual_section').show();
                $('#cd_mpesa_stk_mode_btn').removeClass('btn-primary').addClass('btn-default');
                $('#cd_mpesa_manual_mode_btn').removeClass('btn-default').addClass('btn-primary');
            }
        }

        // ── Custom customer search (same pattern as Lost Sale / POS search) ──
        var cdTimer  = null;
        var cdXhr    = null;
        var $cdInput = $('#cd_customer_input');
        var $cdDrop  = $('#cd_customer_dropdown');

        function cdRenderDropdown(customers) {
            $cdDrop.empty();
            if (!customers.length) {
                $cdDrop.append($('<div>').css({ padding:'14px', textAlign:'center', color:'#94a3b8', fontSize:'13px' }).text('No customers found')).show();
                return;
            }
            $.each(customers, function(i, c) {
                var $row = $('<div>').css({ display:'flex', alignItems:'center', padding:'10px 16px', borderBottom:'1px solid #f1f5f9', cursor:'pointer', background:'#fff' });
                var $info = $('<div>').css({ flex:'1' });
                $('<div>').css({ fontWeight:'700', fontSize:'13px', color:'#1e293b' }).text(c.text || c.name || '—').appendTo($info);
                if (c.mobile) {
                    $('<div>').css({ fontSize:'11px', color:'#64748b', marginTop:'2px' }).text(c.mobile).appendTo($info);
                }
                $row.append($info);
                $row.on('mouseenter', function() { $(this).css('background','#f0f9ff'); })
                    .on('mouseleave', function() { $(this).css('background','#fff'); })
                    .on('click', function() {
                        $cdDrop.hide().empty();
                        $cdInput.val(c.text || c.name || '');
                        cdLoadCustomer(c.id);
                    });
                $cdDrop.append($row);
            });
            $cdDrop.show();
        }

        $cdInput.on('input', function() {
            var term = $(this).val().trim();
            clearTimeout(cdTimer);
            if (term.length < 2) {
                if (cdXhr) { cdXhr.abort(); cdXhr = null; }
                $cdDrop.hide().empty();
                return;
            }
            cdTimer = setTimeout(function() {
                if (cdXhr) { cdXhr.abort(); }
                $cdDrop.show().html('<div style="padding:14px;text-align:center;color:#94a3b8;font-size:13px;"><i class="fa fa-spinner fa-spin"></i> Searching...</div>');
                cdXhr = $.ajax({
                    url: '{{ url("contacts/customers") }}',
                    dataType: 'json',
                    data: { q: term },
                    success: function(data) {
                        cdRenderDropdown(Array.isArray(data) ? data : []);
                    },
                    error: function(xhr) {
                        if (xhr.statusText !== 'abort') { $cdDrop.hide().empty(); }
                    }
                });
            }, 300);
        });

        // Close dropdown when clicking outside
        $(document).on('click.cdDropdown', function(e) {
            if (!$(e.target).closest('#cd_customer_input, #cd_customer_dropdown').length) {
                $cdDrop.hide().empty();
            }
        });

        function cdLoadCustomer(cid) {
            $.ajax({
                url: '{{ url("contacts/credit-info") }}/' + cid,
                dataType: 'json',
                success: function(r) {
                    cds.customerId = r.id;
                    cds.balance    = parseFloat(r.sell_due) || 0;
                    cds.phone      = r.mobile || '';

                    $('#cd_cust_name').text(r.name || '—');
                    $('#cd_cust_phone').text(r.mobile || '—');
                    $('#cd_cust_balance').text(r.sell_due_formatted || ('KES ' + cds.balance.toFixed(2)));
                    $('#cd_customer_card').removeClass('hide');
                    $('#cd_success_banner').hide();

                    if (cds.balance <= 0) {
                        $('#cd_zero_balance_banner').removeClass('hide');
                        $('#cd_payment_section').addClass('hide');
                    } else {
                        $('#cd_zero_balance_banner').addClass('hide');
                        $('#cd_payment_section').removeClass('hide');
                        $('#cd_amount').val(cds.balance.toFixed(2));
                        $('#cd_full_balance_chk').prop('checked', true);
                        $('#cd_remaining_info').text('');
                        $('#cd_stk_phone').val(formatPhoneLocal(cds.phone));
                        stkReset();
                    }
                },
                error: function() { toastr.error('Failed to load customer info'); }
            });
        }

        // ── Full-balance checkbox ────────────────────────────────────────
        $('#cd_full_balance_chk').on('change', function() {
            if ($(this).is(':checked')) {
                $('#cd_amount').val(cds.balance.toFixed(2));
                $('#cd_remaining_info').text('');
            }
        });

        // ── Amount input — show remaining ────────────────────────────────
        $('#cd_amount').on('input', function() {
            var val = parseFloat($(this).val()) || 0;
            if (val >= cds.balance) {
                $('#cd_full_balance_chk').prop('checked', true);
                $('#cd_remaining_info').text('');
            } else {
                $('#cd_full_balance_chk').prop('checked', false);
                $('#cd_remaining_info').html('<span style="color:#f59e0b;"><i class="fas fa-info-circle"></i> Remaining after this payment: <strong>KES ' + (cds.balance - val).toFixed(2) + '</strong></span>');
            }
        });

        // ── Mode toggle buttons ──────────────────────────────────────────
        $('#cd_mpesa_stk_mode_btn').on('click',    function() { cdShowStk(true);  });
        $('#cd_mpesa_manual_mode_btn').on('click', function() { cdShowStk(false); });
        $('#cd_stk_fallback_manual_btn').on('click', function() { cdShowStk(false); stkReset(); });
        $('#cd_stk_retry_btn').on('click',  stkReset);
        $('#cd_stk_cancel_btn').on('click', stkReset);

        // ── Phone — digits only ──────────────────────────────────────────
        $('#cd_stk_phone').on('input', function() { this.value = this.value.replace(/[^0-9]/g, ''); });

        // ── Submit helper ────────────────────────────────────────────────
        function cdSubmitPayment(method, ref, note, $btn) {
            var amount = parseFloat($('#cd_amount').val()) || 0;
            if (!cds.customerId) { toastr.error('No customer selected'); return; }
            if (amount <= 0)     { toastr.error('Enter a valid amount');  return; }

            if ($btn) $btn.prop('disabled', true).prepend('<i class="fas fa-spinner fa-spin" style="margin-right:6px;"></i>');

            var payload = {
                _token:           $('meta[name="csrf-token"]').attr('content'),
                contact_id:       cds.customerId,
                amount:           amount,
                method:           method,
                note:             note || '',
                due_payment_type: 'sell'
            };
            if (method === 'custom_pay_1' && ref) {
                payload.transaction_no_1 = ref;
            }

            $.ajax({
                url:    '{{ url("payments/pay-contact-due") }}',
                method: 'POST',
                data:   payload,
                success: function(r) {
                    if ($btn) $btn.prop('disabled', false).find('.fa-spinner').remove();
                    if (r.success) {
                        var amtStr = 'KES ' + amount.toFixed(2);
                        $('#cd_success_msg').text('Payment of ' + amtStr + ' recorded successfully!');
                        $('#cd_success_banner').show();

                        // Update local balance
                        cds.balance = Math.max(0, cds.balance - amount);
                        $('#cd_cust_balance').text('KES ' + cds.balance.toFixed(2));

                        if (cds.balance <= 0) {
                            $('#cd_payment_section').addClass('hide');
                            $('#cd_zero_balance_banner').removeClass('hide');
                        } else {
                            $('#cd_amount').val(cds.balance.toFixed(2));
                            $('#cd_full_balance_chk').prop('checked', true);
                            $('#cd_remaining_info').text('');
                        }
                        // Reset payment inputs
                        $('#cd_cash_note').val('');
                        $('#cd_manual_ref, #cd_manual_note').val('');
                        stkReset();
                    } else {
                        toastr.error(r.msg || 'Payment failed');
                    }
                },
                error: function() {
                    if ($btn) $btn.prop('disabled', false).find('.fa-spinner').remove();
                    toastr.error('Error recording payment');
                }
            });
        }

        // ── Cash submit ──────────────────────────────────────────────────
        $('#cd_cash_submit_btn').on('click', function() {
            var amount = parseFloat($('#cd_amount').val()) || 0;
            if (amount <= 0) { toastr.error('Enter a valid amount'); return; }
            cdSubmitPayment('cash', null, $('#cd_cash_note').val(), $(this));
        });

        // ── STK Push ─────────────────────────────────────────────────────
        $('#cd_send_stk_btn').on('click', function() {
            var phone  = $('#cd_stk_phone').val().trim();
            var amount = parseFloat($('#cd_amount').val()) || 0;

            if (!phone || phone.length < 9) { toastr.error('Enter a valid 9-digit phone number'); return; }
            if (amount <= 0)               { toastr.error('Enter a valid amount'); return; }

            if (phone.startsWith('0')) phone = phone.substring(1);
            phone = '254' + phone;

            $('#cd_stk_input_area').hide();
            $('#cd_stk_waiting').show();
            $('#cd_stk_countdown').text('2:00');

            $.ajax({
                url:    '{{ url("mpesa/stk-push") }}',
                method: 'POST',
                data: {
                    _token:    $('meta[name="csrf-token"]').attr('content'),
                    phone:     phone,
                    amount:    Math.ceil(amount),
                    reference: ('Debt-' + (cds.customerId || '')).substring(0, 12)
                },
                success: function(r) {
                    if (r.success) {
                        cds.mpesaTxnId = r.mpesa_transaction_id;
                        toastr.success('STK push sent!');
                        startStkPoll();
                        startCountdown();
                    } else {
                        stkShowError(r.message || 'Failed to send STK push');
                    }
                },
                error: function(xhr) {
                    stkShowError((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Request failed');
                }
            });
        });

        function startStkPoll() {
            cds.pollInterval = setInterval(function() {
                if (!cds.mpesaTxnId) { stopTimers(); return; }
                $.post('/mpesa/check-payment-status', {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    mpesa_transaction_id: cds.mpesaTxnId
                }, function(r) {
                    if (r.is_paid) {
                        stopTimers();
                        stkShowSuccess(r.receipt_number);
                    } else if (r.status === 'failed' || r.status === 'cancelled') {
                        stopTimers();
                        stkShowError(r.result_description || 'Payment failed or cancelled');
                    }
                });
            }, 3000);
        }

        function startCountdown() {
            var sec = 120;
            cds.countdownInterval = setInterval(function() {
                sec--;
                $('#cd_stk_countdown').text(Math.floor(sec / 60) + ':' + (sec % 60 < 10 ? '0' : '') + (sec % 60));
                if (sec <= 0) {
                    stopTimers();
                    stkShowError('No response received. Please enter the reference manually.');
                }
            }, 1000);
        }

        function stkShowSuccess(receipt) {
            stopTimers();
            $('#cd_stk_waiting').hide();
            $('#cd_stk_receipt').text(receipt || 'CONFIRMED');
            $('#cd_stk_success').show();
            toastr.success('Payment received via M-Pesa!');
        }

        function stkShowError(msg) {
            stopTimers();
            $('#cd_stk_waiting').hide();
            $('#cd_stk_err_msg').text(msg);
            $('#cd_stk_error').show();
        }

        // ── STK Confirm & Record ─────────────────────────────────────────
        $('#cd_stk_confirm_btn').on('click', function() {
            var receipt = $('#cd_stk_receipt').text();
            cdSubmitPayment('custom_pay_1', receipt, '', $(this));
        });

        // ── Manual M-Pesa submit ─────────────────────────────────────────
        $('#cd_manual_submit_btn').on('click', function() {
            var ref = $('#cd_manual_ref').val().trim().toUpperCase();
            if (!ref) { toastr.error('Enter the M-Pesa reference number'); return; }
            cdSubmitPayment('custom_pay_1', ref, $('#cd_manual_note').val(), $(this));
        });

        // ── Manual ref — uppercase on input ─────────────────────────────
        $('#cd_manual_ref').on('input', function() { this.value = this.value.toUpperCase(); });

        // ── Modal lifecycle ──────────────────────────────────────────────
        $('#collect_debt_modal').on('show.bs.modal',   cdReset);
        $('#collect_debt_modal').on('hidden.bs.modal', function() { stopTimers(); });
        $('#collect_debt_modal').on('shown.bs.modal',  function() {
            $cdInput.focus();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCollectDebt);
    } else {
        setTimeout(initCollectDebt, 100);
    }
})();
</script>
@endif
