@extends('layouts.app')
@section('title', __('lang_v1.all_sales'))

@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')
<div class="page-modern">

    <section class="content-header no-print"></section>

    <div class="pg-banner no-print">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <h1>@lang('sale.sells')
                        <span style="font-size:13px;font-weight:400;opacity:.8;margin-left:8px;" id="sell_list_selected_range">
                            {{ @format_date(\Carbon\Carbon::now()->subDays(29)) }} ~ {{ @format_date(\Carbon\Carbon::now()) }}
                        </span>
                    </h1>
                    <p class="pg-subtitle">@lang('lang_v1.all_sales') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                @can('direct_sell.access')
                    <a class="pg-add-btn"
                        href="{{ action([\App\Http\Controllers\SellController::class, 'create']) }}">
                        <i class="fas fa-plus"></i> @lang('messages.add')
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <section class="content no-print">

        @component('components.filters', ['title' => __('report.filters')])
            @include('sell.partials.sell_list_filters')
            @if ($payment_types)
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('payment_method', __('lang_v1.payment_method') . ':') !!}
                        {!! Form::select('payment_method', $payment_types, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                    </div>
                </div>
            @endif
            @if (!empty($sources))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_source', __('lang_v1.sources') . ':') !!}
                        {!! Form::select('sell_list_filter_source', $sources, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                    </div>
                </div>
            @endif
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_sales')])
            @if (auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only') || auth()->user()->can('view_commission_agent_sell'))
                @php $custom_labels = json_decode(session('business.custom_labels'), true); @endphp
                <div class="table-responsive">
                    <table class="table table-hover" id="sell_table" style="width:100%">
                        <thead>
                            <tr>
                                <th>@lang('messages.action')</th>
                                <th>@lang('messages.date')</th>
                                <th>@lang('sale.invoice_no')</th>
                                <th>@lang('sale.customer_name')</th>
                                <th>@lang('lang_v1.contact_no')</th>
                                <th>@lang('sale.location')</th>
                                <th>@lang('sale.payment_status')</th>
                                <th>@lang('lang_v1.payment_method')</th>
                                <th>@lang('sale.total_amount')</th>
                                <th>@lang('sale.total_paid')</th>
                                <th>@lang('lang_v1.sell_due')</th>
                                <th>@lang('lang_v1.sell_return_due')</th>
                                <th>@lang('lang_v1.shipping_status')</th>
                                <th>@lang('lang_v1.total_items')</th>
                                <th>@lang('lang_v1.types_of_service')</th>
                                <th>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1') }}</th>
                                <th>{{ $custom_labels['sell']['custom_field_1'] ?? '' }}</th>
                                <th>{{ $custom_labels['sell']['custom_field_2'] ?? '' }}</th>
                                <th>{{ $custom_labels['sell']['custom_field_3'] ?? '' }}</th>
                                <th>{{ $custom_labels['sell']['custom_field_4'] ?? '' }}</th>
                                <th>@lang('lang_v1.added_by')</th>
                                <th>@lang('sale.sell_note')</th>
                                <th>@lang('sale.staff_note')</th>
                                <th>@lang('sale.shipping_details')</th>
                                <th>@lang('restaurant.table')</th>
                                <th>@lang('restaurant.service_staff')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="text-center">
                                <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                                <td class="footer_payment_status_count"></td>
                                <td class="payment_method_count"></td>
                                <td class="footer_sale_total"></td>
                                <td class="footer_total_paid"></td>
                                <td class="footer_total_remaining"></td>
                                <td class="footer_total_sell_return_due"></td>
                                <td colspan="2"></td>
                                <td class="service_type_count"></td>
                                <td colspan="7"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        @endcomponent

    </section>

    <div class="modal fade payment_modal" tabindex="-1" role="dialog"></div>
    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog"></div>
    <section class="invoice print_section" id="receipt_section"></section>

</div>
@stop

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    var startLast30 = moment().subtract(29, 'days');
    var endLast = moment();

    function updateDateRangeHeading(start, end) {
        if (start && end) {
            $('#sell_list_selected_range').text(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
        } else {
            $('#sell_list_selected_range').text(
                moment().subtract(29,'days').format(moment_date_format) + ' ~ ' + moment().format(moment_date_format)
            );
        }
    }

    $('#sell_list_filter_date_range').daterangepicker(
        $.extend(true, {}, dateRangeSettings, { startDate: startLast30, endDate: endLast }),
        function(start, end) { updateDateRangeHeading(start, end); sell_table.ajax.reload(); }
    );
    $('#sell_list_filter_date_range').on('cancel.daterangepicker', function() {
        $('#sell_list_filter_date_range').val('');
        updateDateRangeHeading(null, null);
        sell_table.ajax.reload();
    });

    sell_table = $('#sell_table').DataTable({
        processing: true,
        serverSide: true,
        fixedHeader: false,
        aaSorting: [[1, 'desc']],
        ajax: {
            url: '/sells',
            data: function(d) {
                if ($('#sell_list_filter_date_range').val()) {
                    d.start_date = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    d.end_date   = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                d.is_direct_sale  = 1;
                d.location_id     = $('#sell_list_filter_location_id').val();
                d.customer_id     = $('#sell_list_filter_customer_id').val();
                d.payment_status  = $('#sell_list_filter_payment_status').val();
                d.created_by      = $('#created_by').val();
                d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                d.service_staffs  = $('#service_staffs').val();
                if ($('#shipping_status').length)         d.shipping_status  = $('#shipping_status').val();
                if ($('#sell_list_filter_source').length) d.source           = $('#sell_list_filter_source').val();
                if ($('#only_subscriptions').is(':checked')) d.only_subscriptions = 1;
                if ($('#payment_method').length)          d.payment_method   = $('#payment_method').val();
                d = __datatable_ajax_callback(d);
            }
        },
        scrollY: '70vh', scrollX: true, scrollCollapse: true,
        columns: [
            { data: 'action',               name: 'action',               orderable: false, searchable: false },
            { data: 'transaction_date',     name: 'transaction_date' },
            { data: 'invoice_no',           name: 'invoice_no' },
            { data: 'conatct_name',         name: 'conatct_name' },
            { data: 'mobile',               name: 'contacts.mobile' },
            { data: 'business_location',    name: 'bl.name' },
            { data: 'payment_status',       name: 'payment_status' },
            { data: 'payment_methods',      orderable: false, searchable: false },
            { data: 'final_total',          name: 'final_total' },
            { data: 'total_paid',           name: 'total_paid',           searchable: false },
            { data: 'total_remaining',      name: 'total_remaining' },
            { data: 'return_due',           orderable: false,             searchable: false },
            { data: 'shipping_status',      name: 'shipping_status' },
            { data: 'total_items',          name: 'total_items',          searchable: false },
            { data: 'types_of_service_name',name: 'tos.name',             @if(empty($is_types_service_enabled)) visible: false @endif },
            { data: 'service_custom_field_1',name:'service_custom_field_1',@if(empty($is_types_service_enabled)) visible: false @endif },
            { data: 'custom_field_1',       name: 'transactions.custom_field_1', @if(empty($custom_labels['sell']['custom_field_1'])) visible: false @endif },
            { data: 'custom_field_2',       name: 'transactions.custom_field_2', @if(empty($custom_labels['sell']['custom_field_2'])) visible: false @endif },
            { data: 'custom_field_3',       name: 'transactions.custom_field_3', @if(empty($custom_labels['sell']['custom_field_3'])) visible: false @endif },
            { data: 'custom_field_4',       name: 'transactions.custom_field_4', @if(empty($custom_labels['sell']['custom_field_4'])) visible: false @endif },
            { data: 'added_by',             name: 'u.first_name' },
            { data: 'additional_notes',     name: 'additional_notes' },
            { data: 'staff_note',           name: 'staff_note' },
            { data: 'shipping_details',     name: 'shipping_details' },
            { data: 'table_name',           name: 'tables.name',          @if(empty($is_tables_enabled)) visible: false @endif },
            { data: 'waiter',               name: 'ss.first_name',        @if(empty($is_service_staff_enabled)) visible: false @endif },
        ],
        fnDrawCallback: function() { __currency_convert_recursively($('#sell_table')); },
        footerCallback: function(row, data) {
            var totals = { sale: 0, paid: 0, remaining: 0, return_due: 0 };
            data.forEach(function(r) {
                totals.sale       += parseFloat($(r.final_total).data('orig-value'))            || 0;
                totals.paid       += parseFloat($(r.total_paid).data('orig-value'))             || 0;
                totals.remaining  += parseFloat($(r.total_remaining).data('orig-value'))        || 0;
                totals.return_due += parseFloat($(r.return_due).find('.sell_return_due').data('orig-value')) || 0;
            });
            $('.footer_sale_total').html(__currency_trans_from_en(totals.sale));
            $('.footer_total_paid').html(__currency_trans_from_en(totals.paid));
            $('.footer_total_remaining').html(__currency_trans_from_en(totals.remaining));
            $('.footer_total_sell_return_due').html(__currency_trans_from_en(totals.return_due));
            $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
            $('.service_type_count').html(__count_status(data, 'types_of_service_name'));
            $('.payment_method_count').html(__count_status(data, 'payment_methods'));
        },
        createdRow: function(row) { $(row).find('td:eq(6)').attr('class', 'clickable_td'); }
    });

    $(document).on('change',
        '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #sell_list_filter_source, #payment_method',
        function() { sell_table.ajax.reload(); }
    );
    $('#only_subscriptions').on('ifChanged', function() { sell_table.ajax.reload(); });
});
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection
