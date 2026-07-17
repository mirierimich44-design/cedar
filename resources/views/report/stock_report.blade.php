@extends('layouts.app')
@section('title', __('report.stock_report'))

@section('content')
@php
    $themeMap = [
        'green'=>['dark'=>'#065f46','main'=>'#059669'],'green-light'=>['dark'=>'#065f46','main'=>'#059669'],
        'blue'=>['dark'=>'#1e3a5f','main'=>'#2563eb'],'blue-light'=>['dark'=>'#1e40af','main'=>'#3b82f6'],
        'red'=>['dark'=>'#991b1b','main'=>'#dc2626'],'purple'=>['dark'=>'#581c87','main'=>'#7c3aed'],
        'primary'=>['dark'=>'#312e81','main'=>'#4f46e5'],'yellow'=>['dark'=>'#92400e','main'=>'#d97706'],
        'orange'=>['dark'=>'#9a3412','main'=>'#ea580c'],'sky'=>['dark'=>'#075985','main'=>'#0284c7'],
    ];
    $t = $themeMap[session('business.theme_color','primary')] ?? $themeMap['primary'];
@endphp

<div class="report-page-modern">
    {{-- Page Header Banner --}}
    <div style="background:linear-gradient(135deg,{{ $t['dark'] }},{{ $t['main'] }});padding:28px 28px 22px;border-radius:0 0 18px 18px;margin:-15px -15px 24px;">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <span style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-boxes" style="color:white;font-size:20px;"></i>
                </span>
                <div>
                    <h1 style="color:#fff;font-size:22px;font-weight:700;margin:0;">{{ __('report.stock_report') }}</h1>
                    <p style="color:rgba(255,255,255,0.7);font-size:13px;margin:2px 0 0;">{{ session()->get('business.name') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div style="padding:0 12px;margin-bottom:20px;">
        <div style="background:#fff;border-radius:14px;box-shadow:0 1px 6px rgba(0,0,0,0.06);overflow:hidden;">
            <div style="background:linear-gradient(135deg,#475569,#334155);padding:14px 16px;display:flex;align-items:center;gap:10px;">
                <span style="width:30px;height:30px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-filter" style="color:white;font-size:14px;"></i>
                </span>
                <h3 style="color:white;font-weight:700;font-size:15px;margin:0;">@lang('report.filters')</h3>
            </div>
            <div style="padding:16px;">
                {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getStockReport']), 'method' => 'get', 'id' => 'stock_report_filter_form' ]) !!}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('category_id', __('category.category') . ':') !!}
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
                    @if($show_manufacturing_data)
                        <div class="col-md-3">
                            <div class="form-group">
                                <br>
                                <div class="checkbox">
                                    <label>
                                      {!! Form::checkbox('only_mfg', 1, false, ['class' => 'input-icheck', 'id' => 'only_mfg_products']) !!} {{ __('manufacturing::lang.only_mfg_products') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    {{-- Stock Value KPI Cards --}}
    @can('view_product_stock_value')
    <div style="padding:0 12px;margin-bottom:20px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
            <div class="kpi-card" style="background:linear-gradient(135deg,#2563eb,#60a5fa);border-radius:14px;overflow:hidden;position:relative;padding:20px;">
                <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.08);border-radius:50%;"></div>
                <p style="font-size:12px;font-weight:500;color:rgba(255,255,255,0.8);margin:0;">@lang('report.closing_stock') (@lang('lang_v1.by_purchase_price'))</p>
                <h3 id="closing_stock_by_pp" style="margin:6px 0 0;font-size:22px;font-weight:700;color:#fff;font-family:ui-monospace,monospace;"></h3>
            </div>
            <div class="kpi-card" style="background:linear-gradient(135deg,#059669,#10b981);border-radius:14px;overflow:hidden;position:relative;padding:20px;">
                <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.08);border-radius:50%;"></div>
                <p style="font-size:12px;font-weight:500;color:rgba(255,255,255,0.8);margin:0;">@lang('report.closing_stock') (@lang('lang_v1.by_sale_price'))</p>
                <h3 id="closing_stock_by_sp" style="margin:6px 0 0;font-size:22px;font-weight:700;color:#fff;font-family:ui-monospace,monospace;"></h3>
            </div>
            <div class="kpi-card" style="background:linear-gradient(135deg,#7c3aed,#a78bfa);border-radius:14px;overflow:hidden;position:relative;padding:20px;">
                <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.08);border-radius:50%;"></div>
                <p style="font-size:12px;font-weight:500;color:rgba(255,255,255,0.8);margin:0;">@lang('lang_v1.potential_profit')</p>
                <h3 id="potential_profit" style="margin:6px 0 0;font-size:22px;font-weight:700;color:#fff;font-family:ui-monospace,monospace;"></h3>
            </div>
            <div class="kpi-card" style="background:linear-gradient(135deg,#d97706,#f59e0b);border-radius:14px;overflow:hidden;position:relative;padding:20px;">
                <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:rgba(255,255,255,0.08);border-radius:50%;"></div>
                <p style="font-size:12px;font-weight:500;color:rgba(255,255,255,0.8);margin:0;">@lang('lang_v1.profit_margin')</p>
                <h3 id="profit_margin" style="margin:6px 0 0;font-size:22px;font-weight:700;color:#fff;font-family:ui-monospace,monospace;"></h3>
            </div>
        </div>
    </div>
    @endcan

    {{-- Stock Table --}}
    <div style="padding:0 12px;margin-bottom:20px;">
        <div style="background:#fff;border-radius:14px;box-shadow:0 1px 6px rgba(0,0,0,0.06);overflow:hidden;">
            <div style="background:linear-gradient(135deg,{{ $t['dark'] }},{{ $t['main'] }});padding:14px 16px;display:flex;align-items:center;gap:10px;">
                <span style="width:30px;height:30px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-table" style="color:white;font-size:14px;"></i>
                </span>
                <h3 style="color:white;font-weight:700;font-size:15px;margin:0;">@lang('report.stock_report')</h3>
            </div>
            <div style="padding:16px;">
                @include('report.partials.stock_report_table')
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
@includeIf('report.partials.report_modern_css')
@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection
