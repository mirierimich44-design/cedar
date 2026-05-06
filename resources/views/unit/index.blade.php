@extends('layouts.app')
@section('title', __('unit.units'))

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
                    <i class="fas fa-ruler-combined"></i>
                </div>
                <div>
                    <h1>@lang('unit.units')</h1>
                    <p class="pg-subtitle">@lang('unit.manage_your_units') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                @can('unit.create')
                    <a class="pg-add-btn btn-modal"
                        data-href="{{ action([\App\Http\Controllers\UnitController::class, 'create']) }}"
                        data-container=".unit_modal">
                        <i class="fas fa-plus"></i> @lang('messages.add')
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        @can('unit.view')
            @component('components.widget', ['class' => 'box-primary', 'title' => __('unit.all_your_units')])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="unit_table">
                        <thead>
                            <tr>
                                <th>@lang('unit.name')</th>
                                <th>@lang('unit.short_name')</th>
                                <th>@lang('unit.allow_decimal') @show_tooltip(__('tooltip.unit_allow_decimal'))</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        @endcan

        <div class="modal fade unit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    </section>

</div>
@endsection
