@extends('layouts.app')
@section('title', __('report.dead_stock'))

@section('content')

<div class="report-page-modern">
    <div class="rpt-banner">
        <div class="rpt-banner-inner">
            <div class="rpt-banner-title">
                <span class="rpt-banner-icon"><i class="fas fa-box-open"></i></span>
                <div>
                    <h1>{{ __('report.dead_stock') }}</h1>
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
              {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getDeadStockReport']), 'method' => 'get', 'id' => 'dead_stock_report_filter_form' ]) !!}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('category_id', __('category.category') . ':') !!}
                        {!! Form::select('category_id', $categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('brand_id', __('product.brand') . ':') !!}
                        {!! Form::select('brand_id', $brands, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('risk_level', 'Risk Level:') !!}
                        {!! Form::select('risk_level', ['all' => 'All (Slow & Dead)', 'slow' => 'Slow Movers (31-90 Days)', 'dead' => 'Dead Stock (> 90 Days)'], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'risk_level']); !!}
                    </div>
                </div>
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dead_stock_table">
                        <thead>
                            <tr>
                                <th>@lang('sale.product')</th>
                                <th>SKU</th>
                                <th>@lang('report.location')</th>
                                <th>@lang('report.current_stock')</th>
                                <th>Stock Value</th>
                                <th>Last Sale Date</th>
                                <th>Days Since Sale</th>
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

</div>



@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var dead_stock_table = $('#dead_stock_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\App\Http\Controllers\ReportController::class, 'getDeadStockReport']) }}",
                data: function (d) {
                    d.location_id = $('select[name="location_id"]').val();
                    d.category_id = $('select[name="category_id"]').val();
                    d.brand_id = $('select[name="brand_id"]').val();
                    d.risk_level = $('select[name="risk_level"]').val();
                }
            },
            columns: [
                { data: 'product_name', name: 'product_name' },
                { data: 'sku', name: 'sku' },
                { data: 'location_name', name: 'location_name' },
                { data: 'current_stock', name: 'current_stock', className: 'text-right' },
                { data: 'stock_value', name: 'stock_value', className: 'text-right' },
                { data: 'last_sale_date', name: 'last_sale_date' },
                { data: 'days_since_sale', name: 'days_since_sale', className: 'text-right' },
                { data: 'recommendation', name: 'recommendation', orderable: false, searchable: false }
            ]
        });

        $('select[name="location_id"], select[name="category_id"], select[name="brand_id"], select[name="risk_level"]').change(function() {
            dead_stock_table.ajax.reload();
        });
    });
</script>



@section('css')
@include('report.partials.report_modern_css')
@endsection
@endsection