@extends('layouts.app')
@section('title', __('lang_v1.stock_transfers'))

@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')
<div class="page-modern">

    <section class="content-header no-print"></section>

    <div class="pg-banner no-print">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <h1>@lang('lang_v1.stock_transfers')</h1>
                    <p class="pg-subtitle">@lang('lang_v1.all_stock_transfers') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                @if(auth()->user()->can('stock_transfer.create'))
                    <a class="pg-add-btn"
                        href="{{ action([\App\Http\Controllers\StockTransferController::class, 'create']) }}">
                        <i class="fas fa-plus"></i> @lang('messages.add')
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_stock_transfers')])
            <div class="table-responsive">
                <table class="table table-bordered table-striped ajax_view" id="stock_transfer_table">
                    <thead>
                        <tr>
                            <th>@lang('messages.date')</th>
                            <th>@lang('purchase.ref_no')</th>
                            <th>@lang('lang_v1.location_from')</th>
                            <th>@lang('lang_v1.location_to')</th>
                            <th>@lang('sale.status')</th>
                            <th>@lang('lang_v1.shipping_charges')</th>
                            <th>@lang('stock_adjustment.total_amount')</th>
                            <th>@lang('purchase.additional_notes')</th>
                            <th class="tw-w-full">@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent
    </section>

    @include('stock_transfer.partials.update_status_modal')
    <section id="receipt_section" class="print_section"></section>

</div>
@stop

@section('javascript')
    <script src="{{ asset('js/stock_transfer.js?v=' . $asset_v) }}"></script>
@endsection

@cannot('view_purchase_price')
    <style>.show_price_with_permission { display:none !important; }</style>
@endcannot
