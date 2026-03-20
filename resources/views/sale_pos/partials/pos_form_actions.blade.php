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

    {{-- Credit Sale --}}
    @if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
        @if(empty($pos_settings['disable_credit_sale_button']))
        <button type="button" class="pos-action-btn pos-action-credit" id="pos-credit-sale-btn" data-toggle="modal" data-target="#credit_sale_customer_modal" title="@lang('lang_v1.credit_sale')">
            <i class="fas fa-handshake"></i>
            <span class="btn-text">Credit</span>
        </button>
        @endif
    @endif

    {{-- Collect Debt --}}
    @if(auth()->user()->can('sell.payments'))
    <button type="button" class="pos-action-btn pos-action-collect-debt" data-toggle="modal" data-target="#collect_debt_modal" id="pos-collect-debt" title="Collect Customer Debt" style="background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%);">
        <i class="fas fa-hand-holding-usd"></i>
        <span class="btn-text">Collect</span>
    </button>
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
                        url: '/products/list',
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

{{-- Credit Sale Customer Search Modal --}}
@if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
    @if(empty($pos_settings['disable_credit_sale_button']))
    <div class="modal fade" id="credit_sale_customer_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); border: none; padding: 20px;">
                    <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1; font-size: 28px; text-shadow: none;">
                        <span>&times;</span>
                    </button>
                    <h4 class="modal-title" style="color: white; font-weight: 700; font-size: 20px;">
                        <i class="fas fa-handshake"></i> Credit Sale - Select Customer
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
                        <select id="credit_sale_customer_search" class="form-control" style="width: 100%;">
                        </select>
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
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px;">
                        Cancel
                    </button>
                    <button type="button" id="confirm_credit_sale" class="btn btn-primary" disabled style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); border: none; border-radius: 6px; padding: 10px 25px;">
                        <i class="fas fa-check"></i> Complete Credit Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    $(document).ready(function() {
        // Auto-populate customer if already selected on main POS screen
        $('#credit_sale_customer_modal').on('shown.bs.modal', function () {
            var main_customer_id = $('#customer_id').val();
            var main_customer_name = $('#customer_id').select2('data')[0]?.text;

            if (main_customer_id && main_customer_id != 1) { // 1 is usually walk-in
                var newOption = new Option(main_customer_name, main_customer_id, true, true);
                $('#credit_sale_customer_search').append(newOption).trigger('change');
                $('#credit_sale_customer_search').trigger({
                    type: 'select2:select',
                    params: {
                        data: { id: main_customer_id, text: main_customer_name }
                    }
                });
            }
        });

        // Initialize Select2 for customer search
        $('#credit_sale_customer_search').select2({
            ajax: {
                url: '/contacts/customers',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return { results: data };
                }
            },
            placeholder: 'Type customer name or phone number...',
            minimumInputLength: 1,
            allowClear: true,
            dropdownParent: $('#credit_sale_customer_modal'),
            templateResult: function(customer) {
                if (!customer.id) return customer.text;
                var mobile = customer.mobile ? ' - ' + customer.mobile : '';
                return $('<span><strong>' + customer.text + '</strong>' + mobile + '</span>');
            }
        });

        // Listen for new contact added via modal
        $(document).on('contact_added', function(e, contact) {
            if ($('#credit_sale_customer_modal').is(':visible')) {
                var newOption = new Option(contact.name, contact.id, true, true);
                $('#credit_sale_customer_search').append(newOption).trigger('change');
                $('#credit_sale_customer_search').trigger({
                    type: 'select2:select',
                    params: {
                        data: contact
                    }
                });
            }
        });

        // On customer selection
        $('#credit_sale_customer_search').on('select2:select', function(e) {
            var data = e.params.data;
            var customerId = data.id;

            // Fetch customer details
            $.ajax({
                url: '/contacts/' + customerId,
                dataType: 'json',
                success: function(result) {
                    if (result.data && result.data.length > 0) {
                        var customer = result.data[0];
                        $('#credit_customer_name').text(customer.name || '-');
                        $('#credit_customer_phone').text(customer.mobile || customer.landline || '-');
                        $('#credit_customer_balance').text(__currency_trans_from_en(customer.total_due || 0));
                        $('#credit_customer_limit').text(customer.credit_limit ? __currency_trans_from_en(customer.credit_limit) : 'Unlimited');
                        $('#credit_customer_details').removeClass('hide');

                        // Check credit limit
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

        // Clear on modal close
        $('#credit_sale_customer_modal').on('hidden.bs.modal', function() {
            $('#credit_sale_customer_search').val(null).trigger('change');
            $('#credit_customer_details').addClass('hide');
            $('#credit_sale_warning').addClass('hide');
            $('#confirm_credit_sale').prop('disabled', true);
        });

        // Confirm credit sale
        $('#confirm_credit_sale').on('click', function() {
            var customerId = $('#credit_sale_customer_search').val();
            var customerName = $('#credit_sale_customer_search').select2('data')[0]?.text;

            if (customerId) {
                // Set the customer in main POS form
                if ($('#customer_id').length) {
                    var newOption = new Option(customerName, customerId, true, true);
                    $('#customer_id').append(newOption).trigger('change');
                }

                // Close modal and trigger credit sale
                $('#credit_sale_customer_modal').modal('hide');

                // Small delay to ensure customer is set
                setTimeout(function() {
                    // Trigger the express finalize with credit_sale pay method
                    var form = $('form#add_pos_sell_form').length > 0 ? $('form#add_pos_sell_form') : $('form#edit_pos_sell_form');

                    // Set payment method to credit
                    if ($('input[name="payment[0][method]"]').length) {
                        $('input[name="payment[0][method]"]').val('credit');
                    }

                    // Set status
                    $('input#status').val('final');

                    // Submit via AJAX like express finalize
                    var data = form.serialize();
                    data += '&is_credit_sale=1';

                    $.ajax({
                        method: 'POST',
                        url: form.attr('action'),
                        dataType: 'json',
                        data: data,
                        beforeSend: function(xhr) {
                            __disable_submit_button(form.find('button[type="submit"]'));
                        },
                        success: function(result) {
                            if (result.success == 1) {
                                // Reset form
                                reset_pos_form();

                                // Print receipt if enabled
                                if (result.receipt && result.receipt.html_content) {
                                    pos_print(result.receipt);
                                }

                                toastr.success(result.msg || 'Credit sale completed successfully');
                            } else {
                                toastr.error(result.msg || 'Error processing credit sale');
                            }
                            __enable_submit_button(form.find('button[type="submit"]'));
                        },
                        error: function(xhr) {
                            __enable_submit_button(form.find('button[type="submit"]'));
                            toastr.error('Error processing credit sale');
                        }
                    });
                }, 300);
            }
        });
    });
    </script>
    @endif
@endif
