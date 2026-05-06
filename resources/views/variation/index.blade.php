@extends('layouts.app')
@section('title', __('product.variations'))

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
                    <i class="fas fa-palette"></i>
                </div>
                <div>
                    <h1>@lang('product.variations')</h1>
                    <p class="pg-subtitle">@lang('lang_v1.manage_product_variations') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                <a class="pg-add-btn btn-modal"
                    data-href="{{ action([\App\Http\Controllers\VariationTemplateController::class, 'create']) }}"
                    data-container=".variation_modal">
                    <i class="fas fa-plus"></i> @lang('messages.add')
                </a>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_variations')])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="variation_table">
                    <thead>
                        <tr>
                            <th>@lang('product.variations')</th>
                            <th>@lang('lang_v1.values')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent

        <div class="modal fade variation_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    </section>

</div>
@endsection
