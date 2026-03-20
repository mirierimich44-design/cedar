@extends('layouts.app')
@section('title', __('Count Products') . ' - ' . $stocktake->ref_no)

@section('content')
<section class="content-header">
    <h1>@lang('Stock Count Entry')
        <small>{{ $stocktake->ref_no }} - {{ optional($stocktake->location)->name }}</small>
    </h1>
</section>

<section class="content">
    <!-- Workflow Steps -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list-ol"></i> @lang('Stocktake Workflow')</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <div class="workflow-step completed">
                        <span class="step-number">1</span>
                        <h5>Create Stocktake</h5>
                        <p class="text-muted">✓ Done</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="workflow-step active">
                        <span class="step-number">2</span>
                        <h5>Print Count Sheets</h5>
                        <a href="{{ route('stocktake.printCountSheet', $stocktake->id) }}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="fa fa-print"></i> Print Sheets
                        </a>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="workflow-step">
                        <span class="step-number">3</span>
                        <h5>Enter Counts</h5>
                        <p class="text-muted">Scan or search below</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="workflow-step">
                        <span class="step-number">4</span>
                        <h5>Verify & Complete</h5>
                        <a href="{{ route('stocktake.printVerificationSheet', $stocktake->id) }}" target="_blank" class="btn btn-warning btn-sm">
                            <i class="fa fa-check-square"></i> Verification Sheet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Search Section -->
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-search"></i> @lang('Find Product')</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div style="position:relative;">
                        <div class="input-group input-group-lg">
                            <span class="input-group-addon"><i class="fa fa-barcode"></i></span>
                            <input type="text"
                                   id="product_search"
                                   class="form-control"
                                   placeholder="Scan barcode or type product name/SKU..."
                                   autofocus>
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="button" id="search_btn">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </span>
                        </div>
                        <p class="help-block"><i class="fa fa-info-circle"></i> Scan barcode or type at least 3 characters to search</p>
                        <!-- Search Results Dropdown — anchored to input width -->
                        <div id="search_results" class="search-results-dropdown" style="display: none;">
                            <ul class="list-group" id="search_results_list"></ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <p class="lead">Products Counted: <span id="counted_products_count" class="badge badge-primary">0</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Counted Products Section -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-clipboard-check"></i> @lang('Counted Products')</h3>
            <div class="box-tools">
                <button type="button" class="btn btn-success" id="save_counts">
                    <i class="fa fa-save"></i> @lang('Save Counts')
                </button>
                <button type="button" class="btn btn-primary" id="complete_stocktake">
                    <i class="fa fa-check"></i> @lang('Complete Stocktake')
                </button>
            </div>
        </div>
        <div class="box-body">
            <div id="empty_state" class="text-center" style="padding: 40px;">
                <i class="fa fa-inbox fa-4x text-muted"></i>
                <h4 class="text-muted">No products counted yet</h4>
                <p class="text-muted">Use the search box above to find and add products</p>
            </div>
            
            <table class="table table-bordered table-striped" id="counted_products_table" style="display: none;">
                <thead>
                    <tr>
                        <th width="120">@lang('SKU')</th>
                        <th>@lang('sale.product')</th>
                        <th width="100">@lang('System Qty')</th>
                        <th width="150">@lang('Counted Qty')</th>
                        <th width="100">@lang('Variance')</th>
                        <th width="60">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody id="counted_products_body">
                </tbody>
            </table>
        </div>
    </div>
</section>

<style>
.workflow-step {
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 8px;
    margin: 5px;
    background: #f9f9f9;
}
.box-success .box-body {
    overflow: visible !important;
}
.box-success {
    overflow: visible !important;
}
.content {
    overflow: visible !important;
}
.content-wrapper {
    overflow: visible !important;
}
.workflow-step.active {
    border-color: #3c8dbc;
    background: #e8f4f8;
}
.workflow-step.completed {
    border-color: #00a65a;
    background: #e8f5e9;
}
.workflow-step .step-number {
    display: inline-block;
    width: 30px;
    height: 30px;
    line-height: 30px;
    background: #333;
    color: #fff;
    border-radius: 50%;
    margin-bottom: 10px;
}
.workflow-step.completed .step-number {
    background: #00a65a;
}
.workflow-step.active .step-number {
    background: #3c8dbc;
}
.search-results-dropdown {
    position: absolute;
    z-index: 9999;
    left: 0;
    right: 0;
    max-height: 450px;
    overflow-y: auto;
    background: #fff;
    border: 2px solid #16a34a;
    border-radius: 10px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    margin-top: 2px;
}
.search-results-dropdown .list-group {
    margin-bottom: 0;
}
.search-results-dropdown .list-group-item {
    cursor: pointer;
    border-left: none;
    border-right: none;
    border-top: none;
    border-bottom: 1px solid #e2e8f0;
}
.search-results-dropdown .list-group-item:first-child {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}
.search-results-dropdown .list-group-item:last-child {
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    border-bottom: none;
}
.search-results-dropdown .list-group-item:hover {
    background: #f0fdf4 !important;
}
.search-results-dropdown .list-group-item.list-group-item-success {
    background: #e8f5e9 !important;
}
#counted_products_table input.counted-qty {
    font-size: 18px;
    font-weight: bold;
    text-align: center;
}
.variance-positive { color: #00a65a; font-weight: bold; }
.variance-negative { color: #dd4b39; font-weight: bold; }
.variance-zero { color: #999; }
</style>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var stocktake_id = {{ $stocktake->id }};
    var location_id = {{ $stocktake->location_id }};
    var countedProducts = {};
    var searchTimeout = null;

    // Focus on search input
    $('#product_search').focus();

    // Search functionality with debounce
    $('#product_search').on('input', function() {
        var query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 3) {
            $('#search_results').hide();
            return;
        }

        searchTimeout = setTimeout(function() {
            searchProducts(query);
        }, 300);
    });

    // Enter key triggers search for barcode
    $('#product_search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var query = $(this).val().trim();
            if (query.length >= 1) {
                searchProducts(query, true);
            }
        }
    });

    $('#search_btn').click(function() {
        var query = $('#product_search').val().trim();
        if (query.length >= 1) {
            searchProducts(query, true);
        }
    });

    function searchProducts(query, isBarcodeScan) {
        $.ajax({
            url: '{{ action([\App\Http\Controllers\StocktakeController::class, "searchProducts"]) }}',
            data: {
                location_id: location_id,
                query: query
            },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    if (isBarcodeScan && response.data.length === 1) {
                        // Auto-add if exact barcode match
                        addProductToCount(response.data[0]);
                        $('#product_search').val('').focus();
                        $('#search_results').hide();
                    } else {
                        showSearchResults(response.data);
                    }
                } else {
                    $('#search_results_list').html('<li class="list-group-item text-muted">No products found</li>');
                    $('#search_results').show();
                }
            }
        });
    }

    function showSearchResults(products) {
        var sep = '<div style="width:1px;height:34px;background:#e2e8f0;flex-shrink:0;"></div>';
        var html = '';
        products.forEach(function(product) {
            var isAdded   = countedProducts[product.variation_id] !== undefined;
            var sysQty    = parseFloat(product.system_qty);
            var sellPrice = parseFloat(product.selling_price)  || 0;
            var costPrice = parseFloat(product.purchase_price) || 0;
            var isOut     = sysQty <= 0;
            var qtyColor  = isOut ? '#ef4444' : '#3b82f6';
            var qtyBg     = isOut ? '#fef2f2' : '#eff6ff';
            var costVal   = costPrice > 0 ? costPrice.toFixed(2) : '—';

            html += '<li class="list-group-item search-result-row' + (isAdded ? ' list-group-item-success' : '') + '" data-product=\'' + JSON.stringify(product) + '\' style="display:flex;align-items:center;padding:9px 20px;border-bottom:1px solid #f1f5f9;cursor:pointer;">';

            // Left: name + SKU
            html += '<div style="flex:1;min-width:0;overflow:hidden;padding-right:12px;">';
            html += '<div style="font-weight:700;font-size:13px;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">';
            html += product.product_name;
            if (product.variation_name && product.variation_name !== 'DUMMY') {
                html += ' <span style="font-weight:400;font-size:12px;color:#64748b;">· ' + product.variation_name + '</span>';
            }
            if (isAdded) html += ' <i class="fa fa-check-circle" style="color:#16a34a;margin-left:4px;"></i>';
            html += '</div>';
            html += '<div style="font-size:11px;color:#94a3b8;margin-top:2px;">SKU: ' + (product.sub_sku || product.sku || '—') + '</div>';
            html += '</div>';

            // Cost
            html += sep;
            html += '<div style="width:72px;flex-shrink:0;text-align:center;padding:3px 6px;border-radius:5px;">';
            html += '<div style="font-size:9px;color:#94a3b8;text-transform:uppercase;font-weight:600;letter-spacing:0.4px;margin-bottom:2px;">Cost</div>';
            html += '<div style="font-size:12px;font-weight:700;color:#64748b;">' + costVal + '</div>';
            html += '</div>';

            // Sell
            html += sep;
            html += '<div style="width:72px;flex-shrink:0;text-align:center;padding:3px 6px;border-radius:5px;background:#f0fdf4;">';
            html += '<div style="font-size:9px;color:#16a34a;text-transform:uppercase;font-weight:600;letter-spacing:0.4px;margin-bottom:2px;">Sell</div>';
            html += '<div style="font-size:12px;font-weight:700;color:#16a34a;">' + sellPrice.toFixed(2) + '</div>';
            html += '</div>';

            // Stock
            html += sep;
            html += '<div style="width:72px;flex-shrink:0;text-align:center;padding:3px 6px;border-radius:5px;background:' + qtyBg + ';">';
            html += '<div style="font-size:9px;color:' + qtyColor + ';text-transform:uppercase;font-weight:600;letter-spacing:0.4px;margin-bottom:2px;">Stock</div>';
            html += '<div style="font-size:12px;font-weight:700;color:' + qtyColor + ';">' + sysQty.toFixed(0) + '</div>';
            html += '</div>';

            html += '</li>';
        });
        $('#search_results_list').html(html);
        $('#search_results').show();
    }

    // Click on search result
    $(document).on('click', '#search_results_list .list-group-item', function() {
        var product = $(this).data('product');
        if (product) {
            addProductToCount(product);
            $('#product_search').val('').focus();
            $('#search_results').hide();
        }
    });

    // Hide search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.box-success').length) {
            $('#search_results').hide();
        }
    });

    function addProductToCount(product) {
        var vid = product.variation_id;
        
        // If already added, focus on its input
        if (countedProducts[vid]) {
            $('input[data-variation-id="' + vid + '"]').focus().select();
            return;
        }

        countedProducts[vid] = product;
        
        var row = '<tr id="row_' + vid + '">' +
            '<td>' + product.sku + '</td>' +
            '<td>' + product.product_name + (product.variation_name && product.variation_name !== 'DUMMY' ? ' <small>(' + product.variation_name + ')</small>' : '') + '</td>' +
            '<td class="text-right system-qty" data-variation-id="' + vid + '">' + parseFloat(product.system_qty).toFixed(2) + '</td>' +
            '<td><input type="number" class="form-control counted-qty" data-variation-id="' + vid + '" step="0.01" min="0" placeholder="0" autofocus></td>' +
            '<td class="text-center variance-display" data-variation-id="' + vid + '">-</td>' +
            '<td class="text-center"><button type="button" class="btn btn-xs btn-danger remove-product" data-variation-id="' + vid + '"><i class="fa fa-times"></i></button></td>' +
        '</tr>';

        $('#counted_products_body').append(row);
        $('#counted_products_table').show();
        $('#empty_state').hide();
        updateProductCount();
        
        // Focus on the new input
        $('input[data-variation-id="' + vid + '"]').focus();
    }

    // Remove product from count
    $(document).on('click', '.remove-product', function() {
        var vid = $(this).data('variation-id');
        delete countedProducts[vid];
        $('#row_' + vid).remove();
        updateProductCount();
        
        if (Object.keys(countedProducts).length === 0) {
            $('#counted_products_table').hide();
            $('#empty_state').show();
        }
    });

    // Calculate variance on input
    $(document).on('input', '.counted-qty', function() {
        var vid = $(this).data('variation-id');
        var counted = parseFloat($(this).val()) || 0;
        var system = parseFloat($('.system-qty[data-variation-id="' + vid + '"]').text()) || 0;
        var variance = counted - system;
        
        var display = $('.variance-display[data-variation-id="' + vid + '"]');
        display.text(variance.toFixed(2));
        display.removeClass('variance-positive variance-negative variance-zero');
        
        if (variance < 0) {
            display.addClass('variance-negative');
        } else if (variance > 0) {
            display.addClass('variance-positive');
        } else {
            display.addClass('variance-zero');
        }
    });

    function updateProductCount() {
        $('#counted_products_count').text(Object.keys(countedProducts).length);
    }

    // Save counts
    $('#save_counts').click(function() {
        var counts = {};
        $('.counted-qty').each(function() {
            var val = $(this).val();
            if (val !== '') {
                counts[$(this).data('variation-id')] = val;
            }
        });

        if (Object.keys(counts).length === 0) {
            toastr.warning('No counts to save. Please add products first.');
            return;
        }

        $.ajax({
            url: '{{ action([\App\Http\Controllers\StocktakeController::class, "saveCounts"], $stocktake->id) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                counts: counts
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function() {
                toastr.error('{{ __("messages.something_went_wrong") }}');
            }
        });
    });

    // Complete stocktake
    $('#complete_stocktake').click(function() {
        swal({
            title: '{{ __("Complete Stocktake?") }}',
            text: '{{ __("This will finalize the stocktake. You cannot edit counts after completion.") }}',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willComplete) => {
            if (willComplete) {
                var counts = {};
                $('.counted-qty').each(function() {
                    var val = $(this).val();
                    if (val !== '') {
                        counts[$(this).data('variation-id')] = val;
                    }
                });

                $.ajax({
                    url: '{{ action([\App\Http\Controllers\StocktakeController::class, "saveCounts"], $stocktake->id) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        counts: counts
                    },
                    success: function() {
                        window.location.href = '{{ action([\App\Http\Controllers\StocktakeController::class, "complete"], $stocktake->id) }}';
                    }
                });
            }
        });
    });
});
</script>
@endsection
