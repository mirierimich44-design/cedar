<!-- Shop Orders Modal for POS -->
<div class="modal fade" id="orders_modal" tabindex="-1" role="dialog" aria-labelledby="ordersModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="ordersModalLabel">
                    <i class="material-icons" style="vertical-align: middle;">shopping_cart</i>
                    Shop Orders
                </h4>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#new_order_tab" aria-controls="new_order_tab" role="tab" data-toggle="tab">
                            <i class="fa fa-plus"></i> New Order
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#existing_orders_tab" aria-controls="existing_orders_tab" role="tab" data-toggle="tab">
                            <i class="fa fa-list"></i> Existing Orders
                        </a>
                    </li>
                </ul>

                <div class="tab-content" style="padding-top: 15px;">
                    <!-- New Order Tab -->
                    <div role="tabpanel" class="tab-pane active" id="new_order_tab">
                        <form id="order_form">
                            <input type="hidden" id="editing_order_id" value="">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Search Products:</label>
                                        <div style="position:relative;">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                                <input type="text" id="order_product_search" class="form-control"
                                                       placeholder="Type to search products..." autocomplete="off">
                                            </div>
                                            <div id="ord_search_dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:100000;background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.15);max-height:320px;overflow-y:auto;overflow-x:hidden;margin-top:3px;padding:4px 0;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-info btn-block" id="add_custom_product_btn">
                                            <i class="fa fa-plus"></i> New Product
                                        </button>
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-condensed" id="order_items_table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th width="100">Quantity</th>
                                                <th width="50">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="order_items_body">
                                            <tr id="order_empty_row">
                                                <td colspan="3" class="text-center text-muted">
                                                    No products added
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </form>
                    </div>

                    <!-- Existing Orders Tab -->
                    <div role="tabpanel" class="tab-pane" id="existing_orders_tab">
                        <div id="existing_orders_list">
                            <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save_order_btn">
                    <i class="fa fa-save"></i> Save Order
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #order_items_table .remove-order-item { padding: 2px 6px; font-size: 12px; line-height: 1.5; }
    #order_items_table .remove-order-item i { font-size: 10px; }
    #ord_search_dropdown::-webkit-scrollbar { width: 5px; }
    #ord_search_dropdown::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<script>
(function() {
    var checkJquery = function() {
        if (typeof jQuery === 'undefined') {
            setTimeout(checkJquery, 100);
            return;
        }
        $(document).ready(function() {
            var orderProducts = {};
            var customProductCounter = 0;

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

            // ── Custom product search dropdown ────────────────────────────
            var $ordInput    = $('#order_product_search');
            var $ordDropdown = $('#ord_search_dropdown');
            var ordTimer = null, ordXhr = null;

            function renderOrdDropdown(products) {
                $ordDropdown.empty();
                if (!products.length) {
                    $ordDropdown.append($('<div>').css({ padding:'14px', textAlign:'center', color:'#94a3b8', fontSize:'13px' }).text('No products found')).show();
                    return;
                }
                $.each(products, function(i, p) {
                    $ordDropdown.append(buildProductRow(p, function(product) {
                        addProductToOrder(product);
                        $ordDropdown.hide().empty();
                        $ordInput.val('').focus();
                    }));
                });
                $ordDropdown.show();
            }

            $ordInput.on('input', function() {
                var term = $(this).val().trim();
                clearTimeout(ordTimer);
                if (term.length < 2) { if (ordXhr) ordXhr.abort(); $ordDropdown.hide().empty(); return; }
                ordTimer = setTimeout(function() {
                    if (ordXhr) ordXhr.abort();
                    $ordDropdown.show().html('<div style="padding:14px;text-align:center;color:#94a3b8;font-size:13px;"><i class="fa fa-spinner fa-spin"></i> Searching...</div>');
                    ordXhr = $.ajax({
                        url: '{{ action([\App\Http\Controllers\OrderController::class, "searchProducts"]) }}',
                        dataType: 'json',
                        data: { query: term, location_id: $('#location_id').val() },
                        success: function(data) { renderOrdDropdown(Array.isArray(data) ? data : []); },
                        error: function(xhr) { if (xhr.statusText !== 'abort') $ordDropdown.hide().empty(); }
                    });
                }, 300);
            });

            $(document).on('click.ordDropdown', function(e) {
                if (!$(e.target).closest('#order_product_search, #ord_search_dropdown').length) $ordDropdown.hide().empty();
            });

            function addProductToOrder(product) {
                var vid = product.variation_id;

                if (orderProducts[vid]) {
                    var currentQty = parseFloat($('#order_qty_' + vid).val()) || 1;
                    $('#order_qty_' + vid).val(currentQty + 1);
                    return;
                }

                orderProducts[vid] = product;
                $('#order_empty_row').hide();

                var row = '<tr id="order_row_' + vid + '">' +
                    '<td>' + product.name +
                        (product.type == 'variable' ? ' <small>(' + product.variation + ')</small>' : '') +
                        (product.sub_sku ? '<br><small class="text-muted">' + product.sub_sku + '</small>' : '') +
                    '</td>' +
                    '<td><input type="number" class="form-control input-sm order-qty" id="order_qty_' + vid + '" value="1" min="1" step="any"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-danger remove-order-item" data-vid="' + vid + '"><i class="fa fa-times"></i></button></td>' +
                '</tr>';

                $('#order_items_body').append(row);
            }

            // Redirect to new product page
            $('#add_custom_product_btn').click(function() {
                window.open('{{ action([\App\Http\Controllers\ProductController::class, "create"]) }}', '_blank');
            });



            // Remove product from order
            $(document).on('click', '.remove-order-item', function() {
                var vid = $(this).data('vid');
                delete orderProducts[vid];
                $('#order_row_' + vid).remove();

                if (Object.keys(orderProducts).length === 0) {
                    $('#order_empty_row').show();
                }
            });

            // Clear form function
            function clearOrderForm() {
                orderProducts = {};
                customProductCounter = 0;
                $('#editing_order_id').val('');
                $('#save_order_btn').html('<i class="fa fa-save"></i> Save Order').removeClass('btn-warning').addClass('btn-primary');
                $('#order_items_body tr:not(#order_empty_row)').remove();
                $('#order_empty_row').show();

                $('#order_product_search').val('');
                $('#custom_product_row').hide();
                $('#custom_product_name').val('');
                $('#custom_product_qty').val('1');
                $('#custom_product_price').val('');
            }

            // Save order
            $('#save_order_btn').click(function() {
                if (Object.keys(orderProducts).length === 0) {
                    toastr.warning('Please add at least one product');
                    return;
                }

                var products = [];
                Object.keys(orderProducts).forEach(function(vid) {
                    var product = orderProducts[vid];
                    products.push({
                        product_id: product.product_id || null,
                        variation_id: product.variation_id || null,
                        is_custom: product.is_custom || false,
                        custom_product_name: product.custom_product_name || null,
                        quantity: parseFloat($('#order_qty_' + vid).val()) || 1
                    });
                });

                var editing_order_id = $('#editing_order_id').val();
                var url = '{{ action([\App\Http\Controllers\OrderController::class, "store"]) }}';
                var method = 'POST';

                if (editing_order_id) {
                    url = '{{ url("pos-customer-orders") }}/' + editing_order_id;
                    method = 'PUT';
                }

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        _token: '{{ csrf_token() }}',
                        location_id: $('#location_id').val(),
                        products: products
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            $('#orders_modal').modal('hide');
                            clearOrderForm();
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong');
                    }
                });
            });

            // Listen for edit order event
            $(document).on('pos_edit_order', function(e, data) {
                clearOrderForm();

                $('#editing_order_id').val(data.id);
                // Note: contact_id update might be needed in POS UI if it changed

                if (data.lines && data.lines.length > 0) {
                    data.lines.forEach(function(line) {
                        var product = {
                           name: line.name,
                           type: line.variation ? 'variable' : 'single',
                           variation: line.variation,
                           sub_sku: line.sub_sku,
                           product_id: line.product_id,
                           variation_id: line.variation_id,
                           selling_price: line.unit_price,
                           is_custom: line.is_custom
                        };

                        var vid = product.is_custom ? 'custom_' + (++customProductCounter) : product.variation_id;
                        orderProducts[vid] = product;
                        $('#order_empty_row').hide();

                        var nameDisplay = product.name + (product.variation ? ' <small>(' + product.variation + ')</small>' : '') + (product.sub_sku ? '<br><small class="text-muted">' + product.sub_sku + '</small>' : '');
                        if (product.is_custom) {
                            nameDisplay = product.name + '<br><small class="text-muted text-warning">(Custom Item)</small>';
                        }

                        var row = '<tr id="order_row_' + vid + '">' +
                            '<td>' + nameDisplay + '</td>' +
                            '<td><input type="number" class="form-control input-sm order-qty" id="order_qty_' + vid + '" value="' + line.quantity + '" min="1" step="any"></td>' +
                            '<td class="text-center"><button type="button" class="btn btn-danger remove-order-item" data-vid="' + vid + '"><i class="fa fa-times"></i></button></td>' +
                        '</tr>';

                        $('#order_items_body').append(row);
                    });
                }

                $('#save_order_btn').html('<i class="fas fa-sync"></i> Update Order').removeClass('btn-primary').addClass('btn-warning');
                
                // Switch to new order tab
                $('#orders_modal a[href="#new_order_tab"]').tab('show');
            });

            // Load existing orders when tab is shown
            $('a[href="#existing_orders_tab"]').on('shown.bs.tab', function() {
                $.ajax({
                    url: '{{ action([\App\Http\Controllers\OrderController::class, "getOrdersForPos"]) }}',
                    data: {
                        location_id: $('#location_id').val()
                    },
                    success: function(response) {
                        $('#existing_orders_list').html(response);
                    }
                });
            });

            // Reset form when modal is closed
            $('#orders_modal').on('hidden.bs.modal', function() {
                clearOrderForm();
                if (ordXhr) ordXhr.abort();
                $ordDropdown.hide().empty();
                $('#orders_modal a[href="#new_order_tab"]').tab('show');
            });
        });
    };
    checkJquery();
})();
</script>
