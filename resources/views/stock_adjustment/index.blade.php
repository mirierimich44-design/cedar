@extends('layouts.app')
@section('title', __('stock_adjustment.stock_adjustments'))

@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')
<div class="page-modern">

    <section class="content-header"></section>

    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <div>
                    <h1>@lang('stock_adjustment.stock_adjustments')</h1>
                    <p class="pg-subtitle">@lang('stock_adjustment.all_stock_adjustments') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                @if(auth()->user()->can('stock_adjustment.create'))
                    <a class="pg-add-btn"
                        href="{{ action([\App\Http\Controllers\StockAdjustmentController::class, 'create']) }}">
                        <i class="fas fa-plus"></i> @lang('messages.add')
                    </a>
                @endif
            </div>
        </div>
    </div>

    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('stock_adjustment.all_stock_adjustments')])
            <div class="table-responsive">
                <table class="table table-hover ajax_view" id="stock_adjustment_table" style="width:100%">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('purchase.ref_no')</th>
                            <th>@lang('business.location')</th>
                            <th>@lang('stock_adjustment.adjustment_type')</th>
                            <th>@lang('stock_adjustment.total_amount')</th>
                            <th>@lang('stock_adjustment.total_amount_recovered')</th>
                            <th>@lang('stock_adjustment.reason_for_stock_adjustment')</th>
                            <th>@lang('lang_v1.added_by')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent
    </section>

</div>
@stop

@section('javascript')
    <script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
@endsection

@cannot('view_purchase_price')
<style>.show_price_with_permission { display:none !important; }</style>
@endcannot
