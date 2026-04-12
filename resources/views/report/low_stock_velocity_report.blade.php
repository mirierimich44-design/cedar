@extends('layouts.app')
@section('title', __('report.low_stock_alert'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('report.low_stock_alert')}}
        <small class="tw-text-sm tw-text-gray-500">@lang('report.low_stock_alert_desc')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getLowStockVelocityReport']), 'method' => 'get', 'id' => 'low_stock_report_filter_form' ]) !!}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('category_id', __('category.category') . ':') !!}
                        {!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('brand', __('product.brand') . ':') !!}
                        {!! Form::select('brand', $brands, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('stock_status', __('report.stock_status') . ':') !!}
                        {!! Form::select('stock_status', ['' => __('messages.all'), 'out_of_stock' => __('report.out_of_stock'), 'critical' => __('report.critical'), 'low' => __('report.low'), 'ok' => __('report.ok')], null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('report.out_of_stock')</span>
                    <span class="info-box-number" id="out_of_stock_count">0</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-orange">
                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('report.critical')</span>
                    <span class="info-box-number" id="critical_count">0</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('report.low')</span>
                    <span class="info-box-number" id="low_count">0</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">@lang('report.ok')</span>
                    <span class="info-box-number" id="ok_count">0</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="low_stock_velocity_table">
                        <thead>
                            <tr>
                                <th>@lang('sale.product')</th>
                                <th>SKU</th>
                                <th>@lang('report.location')</th>
                                <th>@lang('report.current_stock')</th>
                                <th>@lang('report.alert_qty')</th>
                                <th>@lang('report.sold_1d')</th>
                                <th>@lang('report.sold_7d')</th>
                                <th>@lang('report.sold_30d')</th>
                                <th>@lang('report.sold_90d')</th>
                                <th>@lang('report.daily_avg')</th>
                                <th>@lang('report.days_until_stockout')</th>
                                <th>@lang('report.status')</th>
                                <th>Recommendation</th>
                            </tr>
                        </thead>
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
        var low_stock_table = $('#low_stock_velocity_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\App\Http\Controllers\ReportController::class, 'getLowStockVelocityReport']) }}",
                data: function (d) {
                    d.location_id = $('select[name="location_id"]').val();
                    d.category = $('select[name="category"]').val();
                    d.brand = $('select[name="brand"]').val();
                    d.stock_status = $('select[name="stock_status"]').val();
                },
                dataSrc: function(json) {
                    // Update summary counts
                    if (json.summary) {
                        $('#out_of_stock_count').text(json.summary.out_of_stock || 0);
                        $('#critical_count').text(json.summary.critical || 0);
                        $('#low_count').text(json.summary.low || 0);
                        $('#ok_count').text(json.summary.ok || 0);
                    }
                    return json.data;
                }
            },
            columns: [
                { data: 'product_name', name: 'product_name' },
                { data: 'sku', name: 'sku' },
                { data: 'location_name', name: 'location_name' },
                { data: 'current_stock', name: 'current_stock', className: 'text-right' },
                { data: 'alert_quantity', name: 'alert_quantity', className: 'text-right' },
                { data: 'sold_1d', name: 'sold_1d', className: 'text-right' },
                { data: 'sold_7d', name: 'sold_7d', className: 'text-right' },
                { data: 'sold_30d', name: 'sold_30d', className: 'text-right' },
                { data: 'sold_90d', name: 'sold_90d', className: 'text-right' },
                { data: 'daily_avg', name: 'daily_avg', className: 'text-right' },
                { data: 'days_until_stockout', name: 'days_until_stockout', className: 'text-right' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'recommendation', name: 'recommendation', orderable: false, searchable: false }
            ],
            order: [[10, 'asc']], // Order by days until stockout
            pageLength: 25,
            createdRow: function(row, data, dataIndex) {
                // Apply row styling based on status
                if (data.status_raw === 'Out of Stock') {
                    $(row).addClass('bg-danger');
                } else if (data.status_raw === 'Critical') {
                    $(row).addClass('bg-warning');
                } else if (data.status_raw === 'Low') {
                    $(row).css('background-color', '#fff3cd');
                }
            }
        });

        // Reload on filter change
        $('select[name="location_id"], select[name="category"], select[name="brand"], select[name="stock_status"]').change(function() {
            low_stock_table.ajax.reload();
        });
    });
</script>
@endsection
