@extends('layouts.app')
@section('title', __('Lost Sales Report'))

@section('content')

<div class="report-page-modern">
    <div class="rpt-banner">
        <div class="rpt-banner-inner">
            <div class="rpt-banner-title">
                <span class="rpt-banner-icon"><i class="fas fa-times-circle"></i></span>
                <div>
                    <h1>{{ 'Lost Sales Report' }}</h1>
                    <p class="rpt-subtitle">{{ session()->get('business.name') }}</p>
                </div>
            </div>
        </div>
    </div>
<section class="content">
    {{-- Filters --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Filters</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date Range:</label>
                        <input type="text" class="form-control" id="ls_date_range"
                               placeholder="Select date range">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Location:</label>
                        {!! Form::select('location_id', $locations, null, [
                            'class' => 'form-control select2',
                            'id' => 'ls_location',
                            'placeholder' => 'All Locations'
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Product / SKU:</label>
                        <input type="text" class="form-control" id="ls_search_product"
                               placeholder="Search product name or SKU...">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-primary btn-block" id="ls_apply_filter">
                            <i class="fa fa-search"></i> Apply Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row" id="ls_summary_row" style="display:none;">
        <div class="col-md-3">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3 id="ls_total_records">0</h3>
                    <p>Total Lost Sales</p>
                </div>
                <div class="icon"><i class="fa fa-times-circle"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-orange" style="background-color: #f39c12 !important;">
                <div class="inner">
                    <h3 id="ls_total_qty">0</h3>
                    <p>Total Qty Lost</p>
                </div>
                <div class="icon"><i class="fa fa-cubes"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3 id="ls_total_value">0.00</h3>
                    <p>Potential Revenue Lost</p>
                </div>
                <div class="icon"><i class="fa fa-money"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-navy">
                <div class="inner">
                    <h3 id="ls_unique_products">0</h3>
                    <p>Unique Products</p>
                </div>
                <div class="icon"><i class="fa fa-tag"></i></div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-list"></i> Lost Sales Records</h3>
            <div class="box-tools">
                <button type="button" class="btn btn-success btn-sm" id="ls_export_excel">
                    <i class="fa fa-file-excel-o"></i> Export Excel
                </button>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped" id="lost_sales_table">
                <thead>
                    <tr>
                        <th>Date / Time</th>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th class="text-right">Selling Price</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Total Value</th>
                        <th>Location</th>
                        <th>Recorded By</th>
                        <th>Notes</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>
</div>



@endsection

@section('javascript')
<script>
$(document).ready(function() {

    // Date range picker
    $('#ls_date_range').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        }
    });

    // DataTable
    var filterApplied = false;
    var table = $('#lost_sales_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("reports.lost_sales") }}',
            data: function(d) {
                var val = $('#ls_date_range').val();
                var dates = val ? val.split(' - ') : ['', ''];
                d.start_date     = dates[0] || '';
                d.end_date       = dates[1] || '';
                d.location_id    = $('#ls_location').val();
                d.search_product = $('#ls_search_product').val();
            },
            error: function(xhr, error, thrown) {
                var msg = 'Failed to load data.';
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.error) msg = resp.error;
                } catch(e) {}
                toastr.error(msg);
            }
        },
        columns: [
            { data: 'created_at',      name: 'lost_sales.created_at' },
            { data: 'product_name',    name: 'lost_sales.product_name' },
            { data: 'sku',             name: 'lost_sales.sku', defaultContent: '-' },
            { data: 'selling_price',   name: 'lost_sales.selling_price', className: 'text-right' },
            { data: 'quantity',        name: 'lost_sales.quantity', className: 'text-right' },
            { data: 'total_value',     name: 'total_value', orderable: false, className: 'text-right' },
            { data: 'location_name',   name: 'business_locations.name', defaultContent: '-' },
            { data: 'created_by_name', name: 'created_by_name', orderable: false, defaultContent: '-' },
            { data: 'notes',           name: 'lost_sales.notes', defaultContent: '-', orderable: false },
        ],
        order: [[0, 'desc']],
        deferLoading: 0,
        language: {
            emptyTable: 'Click "Apply Filter" to load data.',
            zeroRecords: 'No lost sales found for the selected filters.',
        },
        drawCallback: function(settings) {
            if (filterApplied) updateSummary(settings);
        }
    });

    function updateSummary(settings) {
        var json = settings.json;
        if (!json || !json.data) return;

        var totalRecords = json.recordsFiltered || 0;
        var totalQty = 0, totalValue = 0;
        var products = {};

        json.data.forEach(function(row) {
            totalQty   += parseFloat(row.quantity) || 0;
            totalValue += parseFloat(row.total_value.replace(',', '')) || 0;
            products[row.product_name] = true;
        });

        $('#ls_total_records').text(totalRecords);
        $('#ls_total_qty').text(totalQty.toFixed(2));
        $('#ls_total_value').text(totalValue.toFixed(2));
        $('#ls_unique_products').text(Object.keys(products).length);
        $('#ls_summary_row').show();
    }

    // Apply filter
    $('#ls_apply_filter').click(function() {
        filterApplied = true;
        table.ajax.reload();
    });

    // Enter key on search
    $('#ls_search_product').on('keypress', function(e) {
        if (e.which === 13) { filterApplied = true; table.ajax.reload(); }
    });

    // Export Excel
    $('#ls_export_excel').click(function() {
        var dates = $('#ls_date_range').val().split(' - ');
        var params = $.param({
            start_date: dates[0] || '',
            end_date: dates[1] || '',
            location_id: $('#ls_location').val(),
            search_product: $('#ls_search_product').val(),
            export: 'excel'
        });
        window.location = '{{ route("reports.lost_sales") }}?' + params;
    });
});
</script>



@section('css')
@include('report.partials.report_modern_css')
@endsection
@endsection