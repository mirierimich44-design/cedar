@extends('layouts.app')
@section('title', __('report.trending_products'))

@section('content')

<div class="report-page-modern">
    {{-- Page Header Banner --}}
    <div class="rpt-banner">
        <div class="rpt-banner-inner">
            <div class="rpt-banner-title">
                <span class="rpt-banner-icon"><i class="fas fa-fire"></i></span>
                <div>
                    <h1>{{ __('report.trending_products') }}</h1>
                    <p class="rpt-subtitle">{{ session()->get('business.name') }}</p>
                </div>
            </div>
            <div class="rpt-banner-actions no-print">
                <button class="rpt-glass-btn" onclick="window.print();">
                    <i class="fas fa-print"></i> @lang('messages.print')
                </button>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div style="padding:0 12px;margin-bottom:20px;" class="no-print">
        <div class="rpt-card">
            <div class="rpt-card-header">
                <span class="rpt-card-header-icon"><i class="fas fa-filter"></i></span>
                <h3>@lang('report.filters')</h3>
            </div>
            <div class="rpt-card-body rpt-filters">
                {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getTrendingProducts']), 'method' => 'get' ]) !!}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('category_id', __('product.category') . ':') !!}
                            {!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                            {!! Form::select('sub_category', array(), null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sub_category_id']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('brand', __('product.brand') . ':') !!}
                            {!! Form::select('brand', $brands, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('unit', __('product.unit') . ':') !!}
                            {!! Form::select('unit', $units, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('trending_product_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'trending_product_date_range', 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('limit', __('lang_v1.no_of_products') . ':') !!} @show_tooltip(__('tooltip.no_of_products_for_trending_products'))
                            {!! Form::number('limit', 5, ['placeholder' => __('lang_v1.no_of_products'), 'class' => 'form-control', 'min' => 1]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('product_type', __('product.product_type') . ':') !!}
                            {!! Form::select('product_type', ['single' => __('lang_v1.single'), 'variable' => __('lang_v1.variable'), 'combo' => __('lang_v1.combo')], request()->input('product_type'), ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white pull-right">@lang('report.apply_filters')</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div style="padding:0 12px;margin-bottom:20px;">
        <div class="rpt-card">
            <div class="rpt-card-header rch-amber">
                <span class="rpt-card-header-icon"><i class="fas fa-chart-bar"></i></span>
                <h3>@lang('report.top_trending_products') @show_tooltip(__('tooltip.top_trending_products'))</h3>
            </div>
            <div class="rpt-card-body">
                {!! $chart->container() !!}
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div style="padding:0 12px;margin-bottom:20px;">
        <div class="rpt-card">
            <div class="rpt-card-header rch-theme">
                <span class="rpt-card-header-icon"><i class="fas fa-table"></i></span>
                <h3>@lang('report.trending_products')</h3>
            </div>
            <div class="rpt-card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>@lang('product.product_name')</th>
                                <th>@lang('report.total_unit_sold')</th>
                                <th>@lang('product.default_purchase_price')</th>
                                <th>@lang('product.default_sell_price')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->product }}</td>
                                    <td>{{ @format_quantity($product->total_unit_sold) }} {{ $product->unit }}</td>
                                    <td><span class="display_currency" data-currency_symbol="true">{{ $product->cost_price }}</span></td>
                                    <td><span class="display_currency" data-currency_symbol="true">{{ $product->selling_price }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
@include('report.partials.report_modern_css')
@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
    {!! $chart->script() !!}
@endsection
