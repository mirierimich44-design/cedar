@extends('layouts.app')
@section('title', __('business.business_locations'))

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
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <h1>@lang('business.business_locations')</h1>
                    <p class="pg-subtitle">@lang('business.manage_your_business_locations') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                <a class="pg-add-btn btn-modal"
                    data-href="{{ action([\App\Http\Controllers\BusinessLocationController::class, 'create']) }}"
                    data-container=".location_add_modal">
                    <i class="fas fa-plus"></i> @lang('messages.add')
                </a>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('business.all_your_business_locations')])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="business_location_table">
                    <thead>
                        <tr>
                            <th>@lang('invoice.name')</th>
                            <th>@lang('lang_v1.location_id')</th>
                            <th>@lang('business.landmark')</th>
                            <th>@lang('business.city')</th>
                            <th>@lang('business.zip_code')</th>
                            <th>@lang('business.state')</th>
                            <th>@lang('business.country')</th>
                            <th>@lang('lang_v1.price_group')</th>
                            <th>@lang('invoice.invoice_scheme')</th>
                            <th>@lang('lang_v1.invoice_layout_for_pos')</th>
                            <th>@lang('lang_v1.invoice_layout_for_sale')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent

        <div class="modal fade location_add_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
        <div class="modal fade location_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    </section>

</div>
@endsection
