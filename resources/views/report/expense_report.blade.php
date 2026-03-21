@extends('layouts.app')
@section('title', __('report.expense_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('report.expense_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row no-print">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getExpenseReport']), 'method' => 'get' ]) !!}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('category_id', __('category.category').':') !!}
                        {!! Form::select('category', $categories, null, ['placeholder' =>
                        __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('trending_product_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', null , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'trending_product_date_range', 'readonly']); !!}
                    </div>
                </div>
                <div class="col-sm-12">
                  <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm tw-text-white pull-right">@lang('report.apply_filters')</button>
                </div> 
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
    {{-- KPI Summary Cards --}}
    @php
        $expense_total_sum = $expenses->sum('total_expense');
        $expense_category_count = $expenses->count();
    @endphp
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h4><span class="display_currency" data-currency_symbol="true">{{ $expense_total_sum }}</span></h4>
                    <p>Total Expenses</p>
                </div>
                <div class="icon"><i class="fa fa-money"></i></div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h4>{{ $expense_category_count }}</h4>
                    <p>Categories</p>
                </div>
                <div class="icon"><i class="fa fa-tags"></i></div>
            </div>
        </div>
        @if($expense_category_count > 0)
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h4><span class="display_currency" data-currency_symbol="true">{{ round($expense_total_sum / $expense_category_count, 2) }}</span></h4>
                    <p>Avg per Category</p>
                </div>
                <div class="icon"><i class="fa fa-bar-chart"></i></div>
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        <div class="col-xs-12">
            @component('components.widget', ['class' => 'box-primary'])
                {!! $chart->container() !!}
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        @component('components.widget', ['class' => 'box-primary'])
            <table class="table" id="expense_report_table">
                <thead>
                    <tr>
                        <th>@lang( 'expense.expense_categories' )</th>
                        <th>@lang( 'report.total_expense' )</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_expense = 0;
                    @endphp
                    @foreach($expenses as $expense)
                        <tr>
                            <td>{{$expense['category'] ?? __('report.others')}}</td>
                            <td><span class="display_currency" data-currency_symbol="true">{{$expense['total_expense']}}</span></td>
                        </tr>
                        @php
                            $total_expense += $expense['total_expense'];
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>@lang('sale.total')</td>
                        <td><span class="display_currency" data-currency_symbol="true">{{$total_expense}}</span></td>
                    </tr>
                </tfoot>
            </table>
        @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
    {!! $chart->script() !!}
@endsection