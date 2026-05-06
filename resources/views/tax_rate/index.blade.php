@extends('layouts.app')
@section('title', __('tax_rate.tax_rates'))

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
                    <i class="fas fa-percent"></i>
                </div>
                <div>
                    <h1>@lang('tax_rate.tax_rates')</h1>
                    <p class="pg-subtitle">@lang('tax_rate.manage_your_tax_rates') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                @can('tax_rate.create')
                    <a class="pg-add-btn btn-modal"
                        data-href="{{ action([\App\Http\Controllers\TaxRateController::class, 'create']) }}"
                        data-container=".tax_rate_modal">
                        <i class="fas fa-plus"></i> @lang('tax_rate.add_tax_rate')
                    </a>
                    <a class="pg-glass-btn btn-modal"
                        data-href="{{ action([\App\Http\Controllers\GroupTaxController::class, 'create']) }}"
                        data-container=".tax_group_modal">
                        <i class="fas fa-plus"></i> @lang('tax_rate.add_tax_group')
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('tax_rate.all_your_tax_rates')])
            @can('tax_rate.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tax_rates_table">
                        <thead>
                            <tr>
                                <th>@lang('tax_rate.name')</th>
                                <th>@lang('tax_rate.rate')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        @endcomponent

        @component('components.widget', ['class' => 'box-primary'])
            @slot('title')
                @lang('tax_rate.tax_groups') ( @lang('lang_v1.combination_of_taxes') ) @show_tooltip(__('tooltip.tax_groups'))
            @endslot
            @can('tax_rate.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tax_groups_table">
                        <thead>
                            <tr>
                                <th>@lang('tax_rate.name')</th>
                                <th>@lang('tax_rate.rate')</th>
                                <th>@lang('tax_rate.sub_taxes')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        @endcomponent

        <div class="modal fade tax_rate_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
        <div class="modal fade tax_group_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    </section>

</div>
@endsection
