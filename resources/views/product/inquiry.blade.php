@extends('layouts.app')
@section('title', __('lang_v1.product_inquiry'))

@section('content')

<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        @lang('lang_v1.product_inquiry')
        <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">
            @lang('lang_v1.search_products_for_inquiry')
        </small>
    </h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        {{-- Search bar --}}
        <div class="row">
            <div class="col-sm-8 col-xs-12">
                <div class="form-group">
                    <label for="inquiry_search">@lang('lang_v1.search_product'):</label>
                    <div class="input-group">
                        <input type="text" id="inquiry_search" class="form-control"
                            placeholder="@lang('lang_v1.search_product_placeholder')"
                            autocomplete="off" autocorrect="off" spellcheck="false">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" id="inquiry_clear_btn">
                                <i class="fa fa-times"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xs-12">
                <div class="form-group">
                    <label for="inquiry_location">@lang('purchase.business_location'):</label>
                    {!! Form::select('location_id', $business_locations, null, [
                        'class' => 'form-control select2',
                        'id' => 'inquiry_location',
                        'style' => 'width:100%',
                        'placeholder' => __('lang_v1.all_locations'),
                    ]) !!}
                </div>
            </div>
        </div>

        {{-- Loading indicator --}}
        <div id="inquiry_loading" style="display:none;" class="text-center tw-py-6">
            <i class="fa fa-spinner fa-spin fa-2x tw-text-indigo-500"></i>
        </div>

        {{-- No results --}}
        <div id="inquiry_no_results" style="display:none;" class="text-center tw-py-8 tw-text-gray-500">
            <i class="fa fa-search fa-3x tw-mb-3 tw-text-gray-300"></i>
            <p class="tw-text-lg">@lang('lang_v1.no_products_found')</p>
        </div>

        {{-- Results container --}}
        <div id="inquiry_results" class="row"></div>
    @endcomponent
</section>

@stop

@section('javascript')
<script>
(function () {
    var canViewStock = @json($can_view_stock);
    var canViewCost  = @json($can_view_purchase_price);
    var searchTimer  = null;
    var currentXhr   = null;

    var INQLANG = {
        stock:         "{{ __('lang_v1.stock') }}",
        selling_price: "{{ __('lang_v1.selling_price') }}",
        cost_price:    "{{ __('lang_v1.cost_price') }}"
    };

    function formatMoney(amount) {
        var precision = parseInt($('#p_precision').val() || 2);
        return parseFloat(amount || 0).toFixed(precision);
    }

    function getStockBadge(qty, enableStock) {
        if (!enableStock) {
            return '<span class="tw-inline-block tw-px-2 tw-py-0.5 tw-rounded tw-bg-gray-100 tw-text-gray-500 tw-text-xs">N/A</span>';
        }
        qty = parseFloat(qty || 0);
        if (qty > 10) {
            return '<span class="tw-inline-block tw-px-2 tw-py-0.5 tw-rounded tw-bg-green-100 tw-text-green-700 tw-text-xs tw-font-semibold">' + qty + '</span>';
        } else if (qty > 0) {
            return '<span class="tw-inline-block tw-px-2 tw-py-0.5 tw-rounded tw-bg-yellow-100 tw-text-yellow-700 tw-text-xs tw-font-semibold">' + qty + '</span>';
        } else {
            return '<span class="tw-inline-block tw-px-2 tw-py-0.5 tw-rounded tw-bg-red-100 tw-text-red-700 tw-text-xs tw-font-semibold">0</span>';
        }
    }

    function buildCard(item) {
        var variationLabel = (item.variation && item.variation !== 'DUMMY') ? item.variation : '';
        var title = item.name + (variationLabel ? ' - ' + variationLabel : '');

        var rows = '';

        if (canViewStock) {
            rows += '<div class="tw-flex tw-justify-between tw-items-center tw-py-1 tw-border-b tw-border-gray-100">' +
                        '<span class="tw-text-gray-500 tw-text-xs tw-uppercase tw-tracking-wide">' + INQLANG.stock + '</span>' +
                        '<span>' + getStockBadge(item.qty_available, item.enable_stock) + '</span>' +
                    '</div>';
        }

        rows += '<div class="tw-flex tw-justify-between tw-items-center tw-py-1' + (canViewCost ? ' tw-border-b tw-border-gray-100' : '') + '">' +
                    '<span class="tw-text-gray-500 tw-text-xs tw-uppercase tw-tracking-wide">' + INQLANG.selling_price + '</span>' +
                    '<span class="tw-font-semibold tw-text-gray-800">' + formatMoney(item.selling_price) + '</span>' +
                '</div>';

        if (canViewCost) {
            rows += '<div class="tw-flex tw-justify-between tw-items-center tw-py-1">' +
                        '<span class="tw-text-gray-500 tw-text-xs tw-uppercase tw-tracking-wide">' + INQLANG.cost_price + '</span>' +
                        '<span class="tw-font-semibold tw-text-orange-600">' + formatMoney(item.purchase_price) + '</span>' +
                    '</div>';
        }

        return '<div class="col-xs-12 col-sm-6 col-md-4 tw-mb-3">' +
                    '<div class="tw-bg-white tw-border tw-border-gray-200 tw-rounded-xl tw-shadow-sm tw-p-4 tw-h-full">' +
                        '<div class="tw-flex tw-items-start tw-justify-between tw-mb-3">' +
                            '<div class="tw-min-w-0 tw-flex-1">' +
                                '<p class="tw-font-bold tw-text-gray-800 tw-text-sm tw-leading-tight tw-mb-0.5 tw-break-words">' + title + '</p>' +
                                '<p class="tw-text-gray-400 tw-text-xs tw-font-mono">' + (item.sub_sku || '') + '</p>' +
                            '</div>' +
                            (item.unit ? '<span class="tw-bg-indigo-50 tw-text-indigo-600 tw-text-xs tw-px-2 tw-py-0.5 tw-rounded-full tw-font-medium tw-shrink-0 tw-ml-2">' + item.unit + '</span>' : '') +
                        '</div>' +
                        '<div>' + rows + '</div>' +
                    '</div>' +
                '</div>';
    }

    function doSearch(term) {
        var locationId = $('#inquiry_location').val();

        if (currentXhr) {
            currentXhr.abort();
        }

        $('#inquiry_results').empty();
        $('#inquiry_no_results').hide();

        if (!term || term.length < 1) {
            $('#inquiry_loading').hide();
            return;
        }

        $('#inquiry_loading').show();

        currentXhr = $.ajax({
            url: '/products/list',
            method: 'GET',
            data: {
                term: term,
                location_id: locationId || null,
                search_fields: ['name', 'sku'],
            },
            success: function (data) {
                $('#inquiry_loading').hide();
                var items = typeof data === 'string' ? JSON.parse(data) : data;

                if (!items || items.length === 0) {
                    $('#inquiry_no_results').show();
                    return;
                }

                var html = '';
                $.each(items, function (i, item) {
                    html += buildCard(item);
                });
                $('#inquiry_results').html(html);
            },
            error: function (xhr) {
                if (xhr.statusText !== 'abort') {
                    $('#inquiry_loading').hide();
                    $('#inquiry_no_results').show();
                }
            }
        });
    }

    $(document).ready(function () {
        $('#inquiry_search').on('input', function () {
            var term = $(this).val().trim();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                doSearch(term);
            }, 350);
        });

        $('#inquiry_clear_btn').on('click', function () {
            $('#inquiry_search').val('').trigger('focus');
            $('#inquiry_results').empty();
            $('#inquiry_no_results').hide();
        });

        $('#inquiry_location').on('change', function () {
            var term = $('#inquiry_search').val().trim();
            if (term) {
                doSearch(term);
            }
        });
    });
}());
</script>
@endsection
