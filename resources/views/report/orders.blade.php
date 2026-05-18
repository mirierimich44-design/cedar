@extends('layouts.app')
@section('title', __('lang_v1.pos_orders_report'))

@section('content')

<div class="report-page-modern">
    <div class="rpt-banner">
        <div class="rpt-banner-inner">
            <div class="rpt-banner-title">
                <span class="rpt-banner-icon"><i class="fas fa-clipboard-list"></i></span>
                <div>
                    <h1>{{ __('lang_v1.pos_orders_report') }}</h1>
                    <p class="rpt-subtitle">{{ session()->get('business.name') }}</p>
                </div>
            </div>
        </div>
    </div>



<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('order_location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('order_location_id', $locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('order_status',  __('sale.status') . ':') !!}
                        {!! Form::select('order_status', ['' => __('lang_v1.all')] + $statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('order_contact_id',  __('contact.customer') . ':') !!}
                        {!! Form::select('order_contact_id', [], null, ['class' => 'form-control select2', 'id' => 'order_contact_id', 'placeholder' => __('lang_v1.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('order_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('order_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="orders_report_table">
                        <thead>
                            <tr>
                                <th>@lang('lang_v1.date')</th>
                                <th>@lang('purchase.ref_no')</th>
                                <th>@lang('purchase.business_location')</th>
                                <th>@lang('contact.customer')</th>
                                 <th>@lang('lang_v1.items_count')</th>
                                <th>Products</th>
                                <th>@lang('sale.status')</th>
                                <th>@lang('lang_v1.initiated_by')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

</div>



@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready( function(){
            $('#order_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#order_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    orders_report_table.ajax.reload();
                }
            );
            $('#order_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#order_date_range').val('');
                orders_report_table.ajax.reload();
            });

            //Customer filter
            $('#order_contact_id').select2({
                ajax: {
                    url: '/contacts/customers',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data,
                        };
                    },
                },
                minimumInputLength: 1,
                escapeMarkup: function(m) {
                    return m;
                },
                templateResult: function(data) {
                    if (!data.id) {
                        return data.text;
                    }
                    var html = data.text + ' (' + data.contact_id + ')';
                    return html;
                },
            });

            orders_report_table = $('#orders_report_table').DataTable({
                processing: true,
                serverSide: true,
                dom: '<"row margin-bottom-20 text-center"<"col-sm-2"l><"col-sm-7"B><"col-sm-3"f> r>tip',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fa fa-files-o" aria-hidden="true"></i> ' + LANG.copy,
                        className: 'btn-sm',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-text-o" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'btn-sm',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> ' + LANG.export_to_excel,
                        className: 'btn-sm',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf" aria-hidden="true"></i> ' + LANG.export_to_pdf,
                        className: 'btn-sm',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'btn-sm',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true,
                        },
                        footer: true,
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'btn-sm',
                    }
                ],
                ajax: {
                    url: '{{action([\App\Http\Controllers\ReportController::class, "getOrdersReport"])}}',
                    data: function(d) {
                        d.location_id = $('#order_location_id').val();
                        d.status = $('#order_status').val();
                        d.contact_id = $('#order_contact_id').val();
                        var start = '';
                        var end = '';
                        if ($('#order_date_range').val()) {
                            start = $('input#order_date_range')
                                .data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            end = $('input#order_date_range')
                                .data('daterangepicker')
                                .endDate.format('YYYY-MM-DD');
                        }
                        d.start_date = start;
                        d.end_date = end;
                    }
                },
                columns: [
                    { data: 'created_at', name: 'created_at' },
                    { data: 'ref_no', name: 'ref_no' },
                    { data: 'location_name', name: 'location_name', searchable: false },
                    { data: 'customer_name', name: 'customer_name', searchable: false },
                    { data: 'items_count', name: 'items_count', searchable: false },
                    { data: 'products', name: 'products', searchable: false },
                    { data: 'status', name: 'status' },
                    { data: 'created_by_name', name: 'created_by_name', searchable: false },
                    { data: 'action', name: 'action', searchable: false, orderable: false }
                ]
            });

            $(document).on('change', '#order_location_id, #order_status, #order_contact_id', function() {
                orders_report_table.ajax.reload();
            });
        });
    </script>



@section('css')
@include('report.partials.report_modern_css')
@endsection
@endsection