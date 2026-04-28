@extends('layouts.app')
@section('title', __('stock_adjustment.stock_adjustments'))

@section('css')
<style>
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px; }
    .page-toolbar h1 { margin:0; font-size:22px; font-weight:700; color:#111827; }
    table.dataTable thead th {
        background:#f9fafb !important; color:#374151 !important; font-size:11px !important;
        font-weight:700 !important; text-transform:uppercase !important; letter-spacing:.4px !important;
        border-bottom:2px solid #e5e7eb !important; padding:10px 12px !important; white-space:nowrap;
    }
    table.dataTable tbody tr:hover td { background:#f0f4ff !important; }
    table.dataTable tbody td { font-size:13px; color:#374151; padding:10px 12px !important; vertical-align:middle !important; border-bottom:1px solid #f3f4f6 !important; }
</style>
@endsection

@section('content')

<section class="content-header">
    <div class="page-toolbar">
        <h1>@lang('stock_adjustment.stock_adjustments')</h1>
        @if(auth()->user()->can('stock_adjustment.create'))
            <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-lg"
               href="{{ action([\App\Http\Controllers\StockAdjustmentController::class, 'create']) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 5l0 14"/><path d="M5 12l14 0"/>
                </svg>
                @lang('messages.add')
            </a>
        @endif
    </div>
</section>

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
@stop

@section('javascript')
    <script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
@endsection

@cannot('view_purchase_price')
<style>.show_price_with_permission { display:none !important; }</style>
@endcannot
