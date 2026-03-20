@extends('layouts.app')
@section('title', __('lang_v1.customer_credit_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.customer_credit_report')</h1>
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ccr_customer_id', __('contact.customer') . ':') !!}
                        {!! Form::select('ccr_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'ccr_customer_id', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ccr_location_id', __('purchase.business_location') . ':') !!}
                        {!! Form::select('ccr_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'ccr_location_id', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ccr_date_filter', __('report.date_range') . ':') !!}
                        {!! Form::text('ccr_date_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'ccr_date_filter', 'readonly']); !!}
                    </div>
                </div>

            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="customer_credit_report_tbl">
                    <thead>
                        <tr>
                            <th>@lang('contact.customer')</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('sale.total_paid')</th>
                            <th>@lang('sale.total_remaining')</th>
                            <th>@lang('sale.payment_status')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            <td colspan="3"><strong>@lang('sale.total'):</strong></td>
                            <td><span class="display_currency" id="footer_total_amount" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="footer_total_paid" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="footer_total_due" data-currency_symbol="true"></span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    // Initialize daterangepicker
    if ($('#ccr_date_filter').length) {
        $('#ccr_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#ccr_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                customer_credit_report_tbl.ajax.reload();
            }
        );
        $('#ccr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#ccr_date_filter').val('');
            customer_credit_report_tbl.ajax.reload();
        });
    }

    // Initialize DataTable
    var customer_credit_report_tbl = $('#customer_credit_report_tbl').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/reports/customer-credit',
            data: function(d) {
                d.customer_id = $('#ccr_customer_id').val();
                d.location_id = $('#ccr_location_id').val();
                
                var start = '';
                var end = '';
                if ($('#ccr_date_filter').val()) {
                    start = $('input#ccr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#ccr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;
            }
        },
        columns: [
            {data: 'customer_name', name: 'contacts.name'},
            {data: 'invoice_no', name: 'transactions.invoice_no'},
            {data: 'transaction_date', name: 'transactions.transaction_date'},
            {data: 'final_total', name: 'transactions.final_total'},
            {data: 'total_paid', name: 'total_paid'},
            {data: 'total_due', name: 'total_due'},
            {data: 'payment_status', name: 'transactions.payment_status'},
        ],
        fnDrawCallback: function(oSettings) {
            var total_amount = sum_table_col($('#customer_credit_report_tbl'), 'final_total');
            $('#footer_total_amount').text(__currency_trans_from_en(total_amount, true)).attr('data-orig-value', total_amount);
            
            var total_paid = sum_table_col($('#customer_credit_report_tbl'), 'total_paid');
            $('#footer_total_paid').text(__currency_trans_from_en(total_paid, true)).attr('data-orig-value', total_paid);
            
            var total_due = sum_table_col($('#customer_credit_report_tbl'), 'total_due');
            $('#footer_total_due').text(__currency_trans_from_en(total_due, true)).attr('data-orig-value', total_due);
            
            __currency_convert_recursively($('#customer_credit_report_tbl'));
        }
    });

    // Reload table on filter change
    $(document).on('change', '#ccr_customer_id, #ccr_location_id', function() {
        customer_credit_report_tbl.ajax.reload();
    });
});
</script>
@endsection
