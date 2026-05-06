@extends('layouts.app')
@section('title', 'Brands')

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
                    <i class="fas fa-tag"></i>
                </div>
                <div>
                    <h1>@lang('brand.brands')</h1>
                    <p class="pg-subtitle">@lang('brand.manage_your_brands') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                @can('brand.create')
                    <a class="pg-add-btn btn-modal"
                        data-href="{{ action([\App\Http\Controllers\BrandController::class, 'create']) }}"
                        data-container=".brands_modal">
                        <i class="fas fa-plus"></i> @lang('messages.add')
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        @can('brand.view')
            @component('components.widget', ['class' => 'box-primary', 'title' => __('brand.all_your_brands')])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="brands_table">
                        <thead>
                            <tr>
                                <th>@lang('brand.brands')</th>
                                <th>@lang('brand.note')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        @endcan

        <div class="modal fade brands_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    </section>

</div>
@endsection
