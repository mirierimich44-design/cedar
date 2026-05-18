@extends('layouts.app')
@section('title', __('lang_v1.supplier_report'))

@section('content')

<div class="report-page-modern">
    <div class="rpt-banner">
        <div class="rpt-banner-inner">
            <div class="rpt-banner-title">
                <span class="rpt-banner-icon"><i class="fas fa-truck-loading"></i></span>
                <div>
                    <h1>{{ __('lang_v1.supplier_report') }}</h1>
                    <p class="rpt-subtitle">{{ session()->get('business.name') }}</p>
                </div>
            </div>
        </div>
    </div>
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('supplier_report_location_id', __('purchase.business_location') . ':') !!}
                {!! Form::select('supplier_report_location_id', $locations, null, [
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

    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.supplier_report')])
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="supplier_report_table">
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
                        <th>@lang('report.vat_amount')</th>
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
</section>
</div>



@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize date range picker
    $('#supplier_report_date_range').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'DD/MM/YYYY',
            cancelLabel: 'Clear'
        }
    });

    $('#supplier_report_date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        supplier_report_table.ajax.reload();
    });

    $('#supplier_report_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        supplier_report_table.ajax.reload();
    });

    // Initialize DataTable
    var supplier_report_table = $('#supplier_report_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ action([\App\Http\Controllers\ReportController::class, "getSupplierReport"]) }}',
            data: function(d) {
                d.location_id = $('#supplier_report_location_id').val();
                var dateRange = $('#supplier_report_date_range').val();
                if (dateRange) {
                    var dates = dateRange.split(' - ');
                    d.start_date = dates[0];
                    d.end_date = dates[1];
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

            $('.footer_total_purchase').html(__currency_trans_from_en(totalPurchase, true));
            $('.footer_total_return').html(__currency_trans_from_en(totalReturn, true));
            $('.footer_total_due').html(__currency_trans_from_en(totalDue, true));
            $('.footer_total_paid').html(__currency_trans_from_en(totalPaid, true));
            @if($show_vat)
            $('.footer_total_vat').html(__currency_trans_from_en(totalVat, true));
            @endif
        }
    });

    // Reload on filter change
    $('#supplier_report_location_id').change(function() {
        supplier_report_table.ajax.reload();
    });
});
</script>



@section('css')
@include('report.partials.report_modern_css')
@endsection
@endsection