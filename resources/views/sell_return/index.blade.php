@extends('layouts.app')
@section('title', __('lang_v1.sell_return'))

@section('css')
<style>
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px; }
    .page-toolbar h1 { margin:0; font-size:22px; font-weight:700; color:#111827; }
    table.dataTable thead th {
        background:#f9fafb !important; color:#374151 !important; font-size:11px !important;
        font-weight:700 !important; text-transform:uppercase !important; letter-spacing:.4px !important;
        border-bottom:2px solid #e5e7eb !important; padding:10px 12px !important; white-space:nowrap;
    }
    table.dataTable tbody tr:hover td { background:#f0f4ff !important; }
    table.dataTable tbody td { font-size:13px; color:#374151; padding:10px 12px !important; vertical-align:middle !important; border-bottom:1px solid #f3f4f6 !important; }
    table.dataTable tfoot td { background:#f9fafb; font-size:12px; font-weight:700; padding:10px 12px !important; border-top:2px solid #e5e7eb !important; }
</style>
@endsection

@section('content')

<section class="content-header no-print">
    <div class="page-toolbar">
        <h1>@lang('lang_v1.sell_return')</h1>
    </div>
</section>

<section class="content no-print">

    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sell_list_filter_location_id', __('purchase.business_location') . ':') !!}
                {!! Form::select('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sell_list_filter_customer_id', __('contact.customer') . ':') !!}
                {!! Form::select('sell_list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']) !!}
            </div>
        </div>
        @can('access_sell_return')
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('created_by', __('report.user') . ':') !!}
                {!! Form::select('created_by', $sales_representative, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
            </div>
        </div>
        @endcan
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.sell_return')])
        @include('sell_return.partials.sell_return_list')
    @endcomponent

    <div class="modal fade payment_modal"      tabindex="-1" role="dialog"></div>
    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog"></div>

</section>
@stop

@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
$(document).ready(function() {
    $('#sell_list_filter_date_range').daterangepicker(dateRangeSettings, function(start, end) {
        $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
        sell_return_table.ajax.reload();
    });
    $('#sell_list_filter_date_range').on('cancel.daterangepicker', function() {
        $('#sell_list_filter_date_range').val('');
        sell_return_table.ajax.reload();
    });

    sell_return_table = $('#sell_return_table').DataTable({
        processing: true, serverSide: true, fixedHeader: false,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '/sell-return',
            data: function(d) {
                if ($('#sell_list_filter_date_range').val()) {
                    d.start_date = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    d.end_date   = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                if ($('#sell_list_filter_location_id').length) d.location_id = $('#sell_list_filter_location_id').val();
                d.customer_id = $('#sell_list_filter_customer_id').val();
                if ($('#created_by').length) d.created_by = $('#created_by').val();
            }
        },
        columnDefs: [{ targets: [7, 8], orderable: false, searchable: false }],
        columns: [
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'invoice_no',       name: 'invoice_no' },
            { data: 'parent_sale',      name: 'T1.invoice_no' },
            { data: 'name',             name: 'contacts.name' },
            { data: 'business_location',name: 'bl.name' },
            { data: 'payment_status',   name: 'payment_status' },
            { data: 'final_total',      name: 'final_total' },
            { data: 'payment_due',      name: 'payment_due' },
            { data: 'action',           name: 'action' }
        ],
        fnDrawCallback: function() {
            var total_sell = sum_table_col($('#sell_return_table'), 'final_total');
            $('#footer_sell_return_total').text(total_sell);
            $('#footer_payment_status_count_sr').html(__sum_status_html($('#sell_return_table'), 'payment-status-label'));
            var total_due = sum_table_col($('#sell_return_table'), 'payment_due');
            $('#footer_total_due_sr').text(total_due);
            __currency_convert_recursively($('#sell_return_table'));
        },
        createdRow: function(row) { $(row).find('td:eq(2)').attr('class', 'clickable_td'); }
    });

    $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #created_by', function() {
        sell_return_table.ajax.reload();
    });

    $(document).on('click', 'a.delete_sell_return', function(e) {
        e.preventDefault();
        swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(function(willDelete) {
            if (willDelete) {
                $.ajax({
                    method: 'DELETE', url: $(e.currentTarget).attr('href'), dataType: 'json',
                    success: function(result) {
                        if (result.success) { toastr.success(result.msg); sell_return_table.ajax.reload(); }
                        else { toastr.error(result.msg); }
                    }
                });
            }
        });
    });
});
</script>
@endsection
