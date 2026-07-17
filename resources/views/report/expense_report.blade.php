@extends('layouts.app')
@section('title', __('report.expense_report'))

@section('content')

<div class="report-page-modern">
    {{-- Page Header Banner --}}
    <div class="rpt-banner">
        <div class="rpt-banner-inner">
            <div class="rpt-banner-title">
                <span class="rpt-banner-icon"><i class="fas fa-receipt"></i></span>
                <div>
                    <h1>{{ __('report.expense_report') }}</h1>
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
    <div style="padding:0 12px;margin-bottom:20px;">
        <div class="rpt-card">
            <div class="rpt-card-header">
                <span class="rpt-card-header-icon"><i class="fas fa-filter"></i></span>
                <h3>@lang('report.filters')</h3>
            </div>
            <div class="rpt-card-body rpt-filters">
                {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getExpenseReport']), 'method' => 'get' ]) !!}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('category_id', __('category.category').':') !!}
                            {!! Form::select('category', $categories, null, ['placeholder' => __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('trending_product_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'trending_product_date_range', 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm tw-text-white pull-right">@lang('report.apply_filters')</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    {{-- KPI Summary Cards --}}
    @php
        $expense_total_sum = $expenses->sum('total_expense');
        $expense_category_count = $expenses->count();
    @endphp
    <div style="padding:0 12px;margin-bottom:20px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
            <div class="rpt-kpi" style="background:linear-gradient(135deg,#dc2626,#f87171);">
                <div class="rpt-kpi-circle"></div>
                <p class="rpt-kpi-label">Total Expenses</p>
                <h3 class="rpt-kpi-value"><span class="display_currency" data-currency_symbol="true">{{ $expense_total_sum }}</span></h3>
            </div>
            <div class="rpt-kpi" style="background:linear-gradient(135deg,#d97706,#f59e0b);">
                <div class="rpt-kpi-circle"></div>
                <p class="rpt-kpi-label">Categories</p>
                <h3 class="rpt-kpi-value">{{ $expense_category_count }}</h3>
            </div>
            @if($expense_category_count > 0)
            <div class="rpt-kpi" style="background:linear-gradient(135deg,#2563eb,#60a5fa);">
                <div class="rpt-kpi-circle"></div>
                <p class="rpt-kpi-label">Avg per Category</p>
                <h3 class="rpt-kpi-value"><span class="display_currency" data-currency_symbol="true">{{ round($expense_total_sum / $expense_category_count, 2) }}</span></h3>
            </div>
            @endif
        </div>
    </div>

    {{-- Chart --}}
    <div style="padding:0 12px;margin-bottom:20px;">
        <div class="rpt-card">
            <div class="rpt-card-header rch-purple">
                <span class="rpt-card-header-icon"><i class="fas fa-chart-bar"></i></span>
                <h3>@lang('report.expense_report')</h3>
            </div>
            <div class="rpt-card-body">
                {!! $chart->container() !!}
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div style="padding:0 12px;margin-bottom:20px;">
        <div class="rpt-card">
            <div class="rpt-card-header rch-red">
                <span class="rpt-card-header-icon"><i class="fas fa-table"></i></span>
                <h3>@lang('expense.expense_categories')</h3>
            </div>
            <div class="rpt-card-body">
                <table class="table table-striped" id="expense_report_table">
                    <thead>
                        <tr>
                            <th>@lang('expense.expense_categories')</th>
                            <th>@lang('report.total_expense')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total_expense = 0; @endphp
                        @foreach($expenses as $expense)
                            <tr>
                                <td>{{ $expense['category'] ?? __('report.others') }}</td>
                                <td><span class="display_currency" data-currency_symbol="true">{{ $expense['total_expense'] }}</span></td>
                            </tr>
                            @php $total_expense += $expense['total_expense']; @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="footer-total">
                            <td>@lang('sale.total')</td>
                            <td><span class="display_currency" data-currency_symbol="true">{{ $total_expense }}</span></td>
                        </tr>
                    </tfoot>
                </table>
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
    {!! $chart->script() !!}
@endsection
