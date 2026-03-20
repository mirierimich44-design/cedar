<!-- Follow-up Modal for POS -->
<div class="modal fade" id="followup_modal" tabindex="-1" role="dialog" aria-labelledby="followupModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="followupModalLabel">
                    <i class="material-icons" style="vertical-align: middle;">follow_the_signs</i>
                    Product Follow-up
                </h4>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#new_followup_tab" aria-controls="new_followup_tab" role="tab" data-toggle="tab">
                            <i class="fa fa-plus"></i> New Follow-up
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#existing_followups_tab" aria-controls="existing_followups_tab" role="tab" data-toggle="tab">
                            <i class="fa fa-list"></i> Existing Follow-ups
                        </a>
                    </li>
                </ul>

                <div class="tab-content" style="padding-top: 15px;">
                    <!-- New Follow-up Tab -->
                    <div role="tabpanel" class="tab-pane active" id="new_followup_tab">
                        <form id="followup_form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer Phone: <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                            <input type="text" id="followup_customer_phone" name="customer_phone"
                                                   class="form-control" placeholder="e.g., 0712345678" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer Name:</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                            <input type="text" id="followup_customer_name" name="customer_name"
                                                   class="form-control" placeholder="Optional">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Search Products: <span class="text-danger">*</span></label>
                                        <div style="position:relative;">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                                <input type="text" id="followup_product_search" class="form-control"
                                                       placeholder="Type to search and add products..." autocomplete="off">
                                            </div>
                                            <div id="fu_search_dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:100000;background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.15);max-height:320px;overflow-y:auto;overflow-x:hidden;margin-top:3px;padding:4px 0;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-info btn-block" id="add_custom_followup_product_btn">
                                            <i class="fa fa-plus"></i> New Product
                                        </button>
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-condensed" id="followup_items_table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th width="100">Quantity</th>
                                                <th width="50">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="followup_items_body">
                                            <tr id="followup_empty_row">
                                                <td colspan="3" class="text-center text-muted">
                                                    No products added
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Comment:</label>
                                        <textarea id="followup_comment" name="comment" class="form-control" rows="2"
                                                  placeholder="Enter comment..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Existing Follow-ups Tab -->
                    <div role="tabpanel" class="tab-pane" id="existing_followups_tab">
                        <div id="existing_followups_list">
                            <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="save_followup_btn">
                    <i class="fa fa-save"></i> Save Follow-up
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #followup_items_table .btn-remove-followup-item { padding: 2px 6px; font-size: 12px; line-height: 1.5; }
    #followup_items_table .btn-remove-followup-item i { font-size: 10px; }
    #fu_search_dropdown::-webkit-scrollbar { width: 5px; }
    #fu_search_dropdown::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<script>
(function() {
    var checkJquery = function() {
        if (typeof jQuery === 'undefined') {
            setTimeout(checkJquery, 100);
            return;
        }
        $(document).ready(function() {
            var followupProducts = {};
            var followupCustomCounter = 0;

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

            // ── Custom product search dropdown (no jQuery UI) ─────────────
            var $fuInput    = $('#followup_product_search');
            var $fuDropdown = $('#fu_search_dropdown');
            var fuTimer     = null;
            var fuXhr       = null;

            function renderFuDropdown(products) {
                $fuDropdown.empty();
                if (!products.length) {
                    $fuDropdown.append($('<div>').css({ padding:'14px', textAlign:'center', color:'#94a3b8', fontSize:'13px' }).text('No products found')).show();
                    return;
                }
                $.each(products, function(i, p) {
                    $fuDropdown.append(buildProductRow(p, function(product) {
                        addProductToFollowup(product);
                        $fuDropdown.hide().empty();
                        $fuInput.val('').focus();
                    }));
                });
                $fuDropdown.show();
            }

            $fuInput.on('input', function() {
                var term = $(this).val().trim();
                clearTimeout(fuTimer);
                if (term.length < 2) { if (fuXhr) fuXhr.abort(); $fuDropdown.hide().empty(); return; }
                fuTimer = setTimeout(function() {
                    if (fuXhr) fuXhr.abort();
                    $fuDropdown.show().html('<div style="padding:14px;text-align:center;color:#94a3b8;font-size:13px;"><i class="fa fa-spinner fa-spin"></i> Searching...</div>');
                    fuXhr = $.ajax({
                        url: '{{ action([\App\Http\Controllers\FollowupController::class, "searchProducts"]) }}',
                        dataType: 'json',
                        data: { query: term, location_id: $('#location_id').val() },
                        success: function(data) { renderFuDropdown(Array.isArray(data) ? data : []); },
                        error: function(xhr) { if (xhr.statusText !== 'abort') $fuDropdown.hide().empty(); }
                    });
                }, 300);
            });

            $(document).on('click.fuDropdown', function(e) {
                if (!$(e.target).closest('#followup_product_search, #fu_search_dropdown').length) $fuDropdown.hide().empty();
            });

            function addProductToFollowup(product) {
                var vid = product.variation_id;

                if (followupProducts[vid]) {
                    var currentQty = parseFloat($('#followup_qty_' + vid).val()) || 1;
                    $('#followup_qty_' + vid).val(currentQty + 1);
                    return;
                }

                followupProducts[vid] = product;
                $('#followup_empty_row').hide();

                var row = '<tr id="followup_row_' + vid + '">' +
                    '<td>' + product.name +
                        (product.type == 'variable' ? ' <small>(' + product.variation + ')</small>' : '') +
                        (product.sub_sku ? '<br><small class="text-muted">' + product.sub_sku + '</small>' : '') +
                    '</td>' +
                    '<td><input type="number" class="form-control input-sm followup-qty" id="followup_qty_' + vid + '" value="1" min="1" step="any"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-remove-followup-item" data-vid="' + vid + '"><i class="fa fa-times"></i></button></td>' +
                '</tr>';

                $('#followup_items_body').append(row);
            }

            // Redirect to new product page
            $('#add_custom_followup_product_btn').click(function() {
                window.open('{{ action([\App\Http\Controllers\ProductController::class, "create"]) }}', '_blank');
            });



            // Remove product from followup
            $(document).on('click', '.btn-remove-followup-item', function() {
                var vid = $(this).data('vid');
                delete followupProducts[vid];
                $('#followup_row_' + vid).remove();

                if (Object.keys(followupProducts).length === 0) {
                    $('#followup_empty_row').show();
                }
            });

            // Clear form function
            function clearFollowupForm() {
                followupProducts = {};
                followupCustomCounter = 0;
                $('#followup_form')[0].reset();
                $('#followup_items_body tr:not(#followup_empty_row)').remove();
                $('#followup_empty_row').show();
                $('#followup_product_search').val('');
                $('#followup_custom_product_row').hide();
                $('#followup_custom_product_name').val('');
                $('#followup_custom_product_qty').val('1');
            }

            // Save follow-up
            $('#save_followup_btn').click(function() {
                if (Object.keys(followupProducts).length === 0) {
                    toastr.warning('Please add at least one product');
                    return;
                }

                var phone = $('#followup_customer_phone').val().trim();
                if (!phone) {
                    toastr.warning('Please enter customer phone');
                    return;
                }

                var products = [];
                Object.keys(followupProducts).forEach(function(vid) {
                    var product = followupProducts[vid];
                    products.push({
                        product_id: product.product_id || null,
                        variation_id: product.variation_id || null,
                        is_custom: product.is_custom || false,
                        custom_product_name: product.custom_product_name || null,
                        quantity: parseFloat($('#followup_qty_' + vid).val()) || 1
                    });
                });

                $.ajax({
                    url: '{{ action([\App\Http\Controllers\FollowupController::class, "store"]) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        location_id: $('#location_id').val(),
                        customer_phone: phone,
                        customer_name: $('#followup_customer_name').val(),
                        comment: $('#followup_comment').val(),
                        products: products
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            $('#followup_modal').modal('hide');
                            clearFollowupForm();
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong');
                    }
                });
            });

            // Load existing followups when tab is shown
            $('a[href="#existing_followups_tab"]').on('shown.bs.tab', function() {
                $.ajax({
                    url: '{{ action([\App\Http\Controllers\FollowupController::class, "getFollowupsForPos"]) }}',
                    data: {
                        location_id: $('#location_id').val()
                    },
                    success: function(response) {
                        $('#existing_followups_list').html(response);
                    }
                });
            });

            // Reset form when modal is closed
            $('#followup_modal').on('hidden.bs.modal', function() {
                clearFollowupForm();
                if (fuXhr) fuXhr.abort();
                $fuDropdown.hide().empty();
                $('#followup_modal a[href="#new_followup_tab"]').tab('show');
            });
        });
    };
    checkJquery();
})();
</script>
