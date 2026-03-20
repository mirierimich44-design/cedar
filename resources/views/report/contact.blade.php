@extends('layouts.app')
@section('title', __('report.customer') . ' & ' . __('report.supplier') . ' ' . __('report.reports'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('report.customer')}} & {{ __('report.supplier')}} {{ __('report.reports')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @php
                    $active_tab = $active_tab ?? 'customer';
                @endphp
                <ul class="nav nav-tabs">
                    <li class="{{ $active_tab == 'customer' ? 'active' : '' }}">
                        <a href="#customer_report_tab" data-toggle="tab" aria-expanded="{{ $active_tab == 'customer' ? 'true' : 'false' }}">
                            <i class="fa fa-user"></i> @lang('report.customer') @lang('report.reports')
                        </a>
                    </li>
                    <li class="{{ $active_tab == 'supplier' ? 'active' : '' }}">
                        <a href="#supplier_report_tab" data-toggle="tab" aria-expanded="{{ $active_tab == 'supplier' ? 'true' : 'false' }}">
                            <i class="fa fa-truck"></i> @lang('report.supplier') @lang('report.reports')
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane {{ $active_tab == 'customer' ? 'active' : '' }}" id="customer_report_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @component('components.filters', ['title' => __('report.filters')])
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('customer_report_location_id', __('purchase.business_location') . ':') !!}
                                            {!! Form::select('customer_report_location_id', $business_locations, null, [
                                                'class' => 'form-control select2',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('customer_report_customer_group', __('lang_v1.customer_group') . ':') !!}
                                            {!! Form::select('customer_report_customer_group', $customer_group, null, [
                                                'class' => 'form-control select2',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('customer_report_date_range', __('report.date_range') . ':') !!}
                                            {!! Form::text('customer_report_date_range', null, [
                                                'class' => 'form-control',
                                                'id' => 'customer_report_date_range',
                                                'placeholder' => __('lang_v1.select_a_date_range'),
                                                'readonly'
                                            ]) !!}
                                        </div>
                                    </div>
                                @endcomponent
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                @component('components.widget', ['class' => 'box-primary'])
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="customer_report_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('contact.contact_id')</th>
                                                    <th>@lang('contact.name')</th>
                                                    <th>@lang('contact.mobile')</th>
                                                    <th>@lang('lang_v1.customer_group')</th>
                                                    <th>@lang('lang_v1.total_sales')</th>
                                                    <th>@lang('lang_v1.total_sales_return')</th>
                                                    <th>@lang('lang_v1.total_sale_due')</th>
                                                    <th>@lang('lang_v1.total_paid')</th>
                                                    @if($show_vat)
                                                    <th>@lang('lang_v1.vat_amount') (16%)</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr class="bg-gray font-17 text-center footer-total">
                                                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                                    <td class="footer_total_sales"></td>
                                                    <td class="footer_total_return"></td>
                                                    <td class="footer_total_due"></td>
                                                    <td class="footer_total_paid"></td>
                                                    @if($show_vat)
                                                    <td class="footer_total_vat"></td>
                                                    @endif
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @endcomponent
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane {{ $active_tab == 'supplier' ? 'active' : '' }}" id="supplier_report_tab">
                        <div class="row">
                            <div class="col-md-12">
                                @component('components.filters', ['title' => __('report.filters')])
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('supplier_report_location_id', __('purchase.business_location') . ':') !!}
                                            {!! Form::select('supplier_report_location_id', $business_locations, null, [
                                                'class' => 'form-control select2',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('supplier_report_date_range', __('report.date_range') . ':') !!}
                                            {!! Form::text('supplier_report_date_range', null, [
                                                'class' => 'form-control',
                                                'id' => 'supplier_report_date_range',
                                                'placeholder' => __('lang_v1.select_a_date_range'),
                                                'readonly'
                                            ]) !!}
                                        </div>
                                    </div>
                                @endcomponent
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                @component('components.widget', ['class' => 'box-primary'])
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="supplier_report_table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('contact.contact_id')</th>
                                                    <th>@lang('business.business_name')</th>
                                                    <th>@lang('contact.name')</th>
                                                    <th>@lang('contact.mobile')</th>
                                                    <th>@lang('lang_v1.total_purchases')</th>
                                                    <th>@lang('lang_v1.total_purchase_return')</th>
                                                    <th>@lang('lang_v1.purchase_due')</th>
                                                    <th>@lang('lang_v1.total_paid')</th>
                                                    @if($show_vat)
                                                    <th>@lang('lang_v1.vat_amount') (16%)</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr class="bg-gray font-17 text-center footer-total">
                                                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                                                    <td class="footer_total_purchase"></td>
                                                    <td class="footer_total_return"></td>
                                                    <td class="footer_total_due"></td>
                                                    <td class="footer_total_paid"></td>
                                                    @if($show_vat)
                                                    <td class="footer_total_vat"></td>
                                                    @endif
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @endcomponent
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Customer Report logic
    if ($('#customer_report_date_range').length) {
        $('#customer_report_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#customer_report_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                customer_report_table.ajax.reload();
            }
        );
        $('#customer_report_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#customer_report_date_range').val('');
            customer_report_table.ajax.reload();
        });
    }

    var customer_report_table = $('#customer_report_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ action([\App\Http\Controllers\ReportController::class, "getCustomerReport"]) }}',
            data: function(d) {
                d.location_id = $('#customer_report_location_id').val();
                d.customer_group_id = $('#customer_report_customer_group').val();
                var start = $('#customer_report_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('#customer_report_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                if ($('#customer_report_date_range').val()) {
                    d.start_date = start;
                    d.end_date = end;
                }
            }
        },
        columns: [
            { data: 'contact_id', name: 'contacts.contact_id' },
            { data: 'name', name: 'contacts.name' },
            { data: 'mobile', name: 'contacts.mobile' },
            { data: 'customer_group', name: 'cg.name' },
            { data: 'total_invoice', name: 'total_invoice' },
            { data: 'total_sell_return', name: 'total_sell_return' },
            { data: 'total_due', name: 'total_due' },
            { data: 'total_paid', name: 'total_paid' },
            @if($show_vat)
            { data: 'vat_amount', name: 'vat_amount' },
            @endif
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            var totalSales = 0, totalReturn = 0, totalDue = 0, totalPaid = 0, totalVat = 0;
            
            data.forEach(function(row) {
                totalSales += parseFloat(row.total_invoice_raw || 0);
                totalReturn += parseFloat(row.total_sell_return_raw || 0);
                totalDue += parseFloat(row.total_due_raw || 0);
                totalPaid += parseFloat(row.total_paid_raw || 0);
                @if($show_vat)
                totalVat += parseFloat(row.vat_amount_raw || 0);
                @endif
            });

            $(api.column(4).footer()).html(__currency_trans_from_en(totalSales, true));
            $(api.column(5).footer()).html(__currency_trans_from_en(totalReturn, true));
            $(api.column(6).footer()).html(__currency_trans_from_en(totalDue, true));
            $(api.column(7).footer()).html(__currency_trans_from_en(totalPaid, true));
            @if($show_vat)
            $(api.column(8).footer()).html(__currency_trans_from_en(totalVat, true));
            @endif
        }
    });

    $('#customer_report_location_id, #customer_report_customer_group').change(function() {
        customer_report_table.ajax.reload();
    });

    // Supplier Report logic
    if ($('#supplier_report_date_range').length) {
        $('#supplier_report_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#supplier_report_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                supplier_report_table.ajax.reload();
            }
        );
        $('#supplier_report_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#supplier_report_date_range').val('');
            supplier_report_table.ajax.reload();
        });
    }

    var supplier_report_table = $('#supplier_report_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ action([\App\Http\Controllers\ReportController::class, "getSupplierReport"]) }}',
            data: function(d) {
                d.location_id = $('#supplier_report_location_id').val();
                var start = $('#supplier_report_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('#supplier_report_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                if ($('#supplier_report_date_range').val()) {
                    d.start_date = start;
                    d.end_date = end;
                }
            }
        },
        columns: [
            { data: 'contact_id', name: 'contacts.contact_id' },
            { data: 'supplier_business_name', name: 'contacts.supplier_business_name' },
            { data: 'name', name: 'contacts.name' },
            { data: 'mobile', name: 'contacts.mobile' },
            { data: 'total_purchase', name: 'total_purchase' },
            { data: 'total_purchase_return', name: 'total_purchase_return' },
            { data: 'total_due', name: 'total_due' },
            { data: 'total_paid', name: 'total_paid' },
            @if($show_vat)
            { data: 'vat_amount', name: 'vat_amount' },
            @endif
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            var totalPurchase = 0, totalReturn = 0, totalDue = 0, totalPaid = 0, totalVat = 0;
            
            data.forEach(function(row) {
                totalPurchase += parseFloat(row.total_purchase_raw || 0);
                totalReturn += parseFloat(row.total_purchase_return_raw || 0);
                totalDue += parseFloat(row.total_due_raw || 0);
                totalPaid += parseFloat(row.total_paid_raw || 0);
                @if($show_vat)
                totalVat += parseFloat(row.vat_amount_raw || 0);
                @endif
            });

            $(api.column(4).footer()).html(__currency_trans_from_en(totalPurchase, true));
            $(api.column(5).footer()).html(__currency_trans_from_en(totalReturn, true));
            $(api.column(6).footer()).html(__currency_trans_from_en(totalDue, true));
            $(api.column(7).footer()).html(__currency_trans_from_en(totalPaid, true));
            @if($show_vat)
            $(api.column(8).footer()).html(__currency_trans_from_en(totalVat, true));
            @endif
        }
    });

    $('#supplier_report_location_id').change(function() {
        supplier_report_table.ajax.reload();
    });
});
</script>
@endsection