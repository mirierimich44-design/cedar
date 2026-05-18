@extends('layouts.app')
@section('title', 'Report 606 (' . __('lang_v1.purchase') . ')')

@section('content')

<div class="report-page-modern">
    <div class="rpt-banner">
        <div class="rpt-banner-inner">
            <div class="rpt-banner-title">
                <span class="rpt-banner-icon"><i class="fas fa-truck"></i></span>
                <div>
                    <h1>{{ __('lang_v1.purchase') . ' ' . __('report.reports') }}</h1>
                    <p class="rpt-subtitle">{{ session()->get('business.name') }}</p>
                </div>
            </div>
        </div>
    </div>



<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('purchase_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_supplier_id',  __('purchase.supplier') . ':') !!}
                {!! Form::select('purchase_list_filter_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_status',  __('purchase.purchase_status') . ':') !!}
                {!! Form::select('purchase_list_filter_status', $orderStatuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_payment_status',  __('purchase.payment_status') . ':') !!}
                {!! Form::select('purchase_list_filter_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('purchase_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent

    {{-- KPI Summary Cards --}}
    <div class="row" id="purchase_kpi_row" style="display:none;">
        <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="small-box bg-navy">
                <div class="inner">
                    <h4 id="purchase_kpi_total">0.00</h4>
                    <p>Total Purchases</p>
                </div>
                <div class="icon"><i class="fa fa-truck"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h4 id="purchase_kpi_count">0</h4>
                    <p>Purchase Orders</p>
                </div>
                <div class="icon"><i class="fa fa-list-alt"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h4 id="purchase_kpi_tax">0.00</h4>
                    <p>Tax (Input)</p>
                </div>
                <div class="icon"><i class="fa fa-percent"></i></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h4 id="purchase_kpi_discount">0.00</h4>
                    <p>Total Discounts</p>
                </div>
                <div class="icon"><i class="fa fa-tags"></i></div>
            </div>
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary'])
        <div class="table-responsive">
    <table class="table table-bordered table-striped ajax_view" id="purchase_report_table">
        <thead>
            <tr>
                <th>@lang('lang_v1.contact_id')</th>
                <th>@lang('purchase.supplier')</th>
                <th>@lang('purchase.ref_no')</th>
                <th>@lang('purchase.purchase_date') (@lang('lang_v1.year_month'))</th>
                <th>@lang('purchase.purchase_date') (@lang('lang_v1.day'))</th>
                <th>@lang('lang_v1.payment_date') (@lang('lang_v1.year_month'))</th>
                <th>@lang('lang_v1.payment_date') (@lang('lang_v1.day'))</th>
                <th>@lang('sale.total') (@lang('product.exc_of_tax'))</th>
                <th>@lang('sale.discount')</th>
                <th>@lang('sale.tax')</th>
                <th>@lang('sale.total') (@lang('product.inc_of_tax'))</th>
                <th>@lang('lang_v1.payment_method')</th>
            </tr>
        </thead>
    </table>
</div>
    @endcomponent

</section>

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')

<script type="text/javascript">
    $(document).ready(function() {
        //Purchase report table
        purchase_report_table = $('#purchase_report_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader:false,
            ajax: {
                url: '/reports/purchase-report',
                data: function(d) {
                    if ($('#purchase_list_filter_location_id').length) {
                        d.location_id = $('#purchase_list_filter_location_id').val();
                    }
                    if ($('#purchase_list_filter_supplier_id').length) {
                        d.supplier_id = $('#purchase_list_filter_supplier_id').val();
                    }
                    if ($('#purchase_list_filter_payment_status').length) {
                        d.payment_status = $('#purchase_list_filter_payment_status').val();
                    }
                    if ($('#purchase_list_filter_status').length) {
                        d.status = $('#purchase_list_filter_status').val();
                    }

                    var start = '';
                    var end = '';
                    if ($('#purchase_list_filter_date_range').val()) {
                        start = $('input#purchase_list_filter_date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        end = $('input#purchase_list_filter_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                    d.start_date = start;
                    d.end_date = end;

                    d = __datatable_ajax_callback(d);
                },
            },
            columns: [
                { data: 'contact_id', name: 'contacts.contact_id' },
                { data: 'name', name: 'contacts.name' },
                { data: 'ref_no', name: 'ref_no' },
                { data: 'purchase_year_month', name: 'transaction_date' },
                { data: 'purchase_day', name: 'transaction_date' },
                { data: 'payment_year_month', searching: false },
                { data: 'payment_day', searching: false },
                { data: 'total_before_tax', name: 'total_before_tax' },
                { data: 'discount_amount', name: 'discount_amount' },
                { data: 'tax_amount', name: 'tax_amount' },
                { data: 'final_total', name: 'final_total' },
                { data: 'payment_method', name: 'payment_method' },
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#purchase_report_table'));
            }
        });

        $(document).on(
            'change',
            '#purchase_list_filter_location_id, \
                        #purchase_list_filter_supplier_id, #purchase_list_filter_payment_status,\
                         #purchase_list_filter_status',
            function() {
                purchase_report_table.ajax.reload();
                loadPurchaseKpi();
            }
        );
        $('#purchase_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#purchase_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
               purchase_report_table.ajax.reload();
               loadPurchaseKpi();
            }
        );
        $('#purchase_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#purchase_list_filter_date_range').val('');
            purchase_report_table.ajax.reload();
            loadPurchaseKpi();
        });

        function fmt(v) { return parseFloat(v||0).toLocaleString('en-KE', {minimumFractionDigits:2, maximumFractionDigits:2}); }

        function loadPurchaseKpi() {
            var params = {};
            if ($('#purchase_list_filter_date_range').val()) {
                params.start_date = $('input#purchase_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                params.end_date   = $('input#purchase_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
            params.location_id  = $('#purchase_list_filter_location_id').val() || '';
            params.supplier_id  = $('#purchase_list_filter_supplier_id').val() || '';
            params.status       = $('#purchase_list_filter_status').val() || '';

            $.get('/reports/purchase-report-summary', params, function(d) {
                $('#purchase_kpi_total').text(fmt(d.total));
                $('#purchase_kpi_count').text(d.count);
                $('#purchase_kpi_tax').text(fmt(d.tax));
                $('#purchase_kpi_discount').text(fmt(d.discount));
                $('#purchase_kpi_row').show();
            });
        }

        purchase_report_table.on('draw', function() {
            loadPurchaseKpi();
        });
    });
</script>
	
</div>




@section('css')
@include('report.partials.report_modern_css')
@endsection
@endsection