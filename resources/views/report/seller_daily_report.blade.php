@extends('layouts.app')
@section('title', __('Seller Daily Report'))

@section('content')
<section class="content-header">
    <h1>Seller Daily Report
        <small>Products sold by each seller per day</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" id="accordion">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-filter"></i> Filters</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date Range:</label>
                                <input type="text" class="form-control" id="date_range" placeholder="Select date range">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Seller:</label>
                                {!! Form::select('user_id', $users, null, ['class' => 'form-control select2', 'id' => 'user_id', 'placeholder' => 'All Sellers']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Location:</label>
                                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'id' => 'location_id', 'placeholder' => 'All Locations']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" id="apply_filter">
                                    <i class="fa fa-search"></i> Apply Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Sales Details by Seller & Product</h3>
                    <div class="box-tools">
                        <button class="btn btn-success btn-sm" id="export_excel">
                            <i class="fa fa-file-excel-o"></i> Export Excel
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="seller_daily_table" style="width: 100%;">
                        <thead>
                            <tr style="background: #f1f5f9;">
                                <th>Date</th>
                                <th>Seller</th>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Qty Sold</th>
                                <th>Total (KES)</th>
                                <th># Sales</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr style="background: #e2e8f0; font-weight: bold;">
                                <th colspan="4">TOTAL</th>
                                <th id="total_qty">0</th>
                                <th id="total_amount">0</th>
                                <th id="total_sales">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Date range picker
    $('#date_range').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear', format: 'YYYY-MM-DD' }
    });
    $('#date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });
    $('#date_range').on('cancel.daterangepicker', function() {
        $(this).val('');
    });

    // DataTable
    var table = $('#seller_daily_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/reports/seller-daily-report-data',
            data: function(d) {
                var dates = $('#date_range').val().split(' - ');
                d.start_date = dates[0] || '';
                d.end_date = dates[1] || '';
                d.user_id = $('#user_id').val();
                d.location_id = $('#location_id').val();
            }
        },
        columns: [
            { data: 'sale_date', name: 'sale_date' },
            { data: 'seller_name', name: 'seller_name' },
            { data: 'product_name', name: 'product_name' },
            { data: 'sku', name: 'sku' },
            { data: 'total_qty', name: 'total_qty', className: 'text-right' },
            { data: 'total_price', name: 'total_price', className: 'text-right' },
            { data: 'num_transactions', name: 'num_transactions', className: 'text-center' }
        ],
        order: [[0, 'desc']],
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();
            var totalQty = api.column(4).data().reduce(function(a, b) {
                return parseFloat(a) + parseFloat(b.replace(/,/g, '') || 0);
            }, 0);
            var totalAmount = api.column(5).data().reduce(function(a, b) {
                return parseFloat(a) + parseFloat(b.replace(/,/g, '') || 0);
            }, 0);
            var totalSales = api.column(6).data().reduce(function(a, b) {
                return parseInt(a) + parseInt(b || 0);
            }, 0);
            $('#total_qty').html(totalQty.toFixed(2));
            $('#total_amount').html(totalAmount.toLocaleString('en', {minimumFractionDigits: 2}));
            $('#total_sales').html(totalSales);
        },
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print']
    });

    $('#apply_filter').click(function() {
        table.ajax.reload();
    });

    $('#export_excel').click(function() {
        table.button('.buttons-excel').trigger();
    });
});
</script>
@endsection
