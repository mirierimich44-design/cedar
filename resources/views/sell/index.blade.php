@extends('layouts.app')
@section('title', __('lang_v1.all_sales'))

@section('css')
<style>
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px; }
    .page-toolbar h1 { margin:0; font-size:22px; font-weight:700; color:#111827; }
    .page-toolbar .date-badge { font-size:13px; font-weight:400; color:#6b7280; margin-left:8px; }
    table.dataTable thead th {
        background:#f9fafb !important;
        color:#374151 !important;
        font-size:11px !important;
        font-weight:700 !important;
        text-transform:uppercase !important;
        letter-spacing:.4px !important;
        border-bottom:2px solid #e5e7eb !important;
        padding:10px 12px !important;
        white-space:nowrap;
    }
    table.dataTable tbody tr { transition:background .15s; }
    table.dataTable tbody tr:hover td { background:#f0f4ff !important; }
    table.dataTable tbody td { font-size:13px; color:#374151; padding:10px 12px !important; vertical-align:middle !important; border-bottom:1px solid #f3f4f6 !important; }
    table.dataTable tfoot tr td { background:#f9fafb; font-size:12px; font-weight:700; padding:10px 12px !important; border-top:2px solid #e5e7eb !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius:6px !important; font-size:12px !important; }
    .dataTables_wrapper .dataTables_info { font-size:12px; color:#9ca3af; }
</style>
@endsection

@section('content')

<section class="content-header no-print">
    <div class="page-toolbar">
        <div>
            <h1>@lang('sale.sells')
                <span class="date-badge" id="sell_list_selected_range">
                    {{ @format_date(\Carbon\Carbon::now()->subDays(29)) }} ~ {{ @format_date(\Carbon\Carbon::now()) }}
                </span>
            </h1>
        </div>
        @can('direct_sell.access')
            <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-lg"
               href="{{ action([\App\Http\Controllers\SellController::class, 'create']) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 5l0 14"/><path d="M5 12l14 0"/>
                </svg>
                @lang('messages.add')
            </a>
        @endcan
    </div>
</section>

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
