@extends('layouts.app')
@section('title', __('lang_v1.customer_groups'))

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
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h1>@lang('lang_v1.customer_groups')</h1>
                    <p class="pg-subtitle">@lang('lang_v1.all_your_customer_groups') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                @can('customer.create')
                    <a class="pg-add-btn btn-modal"
                        data-href="{{ action([\App\Http\Controllers\CustomerGroupController::class, 'create']) }}"
                        data-container=".customer_groups_modal">
                        <i class="fas fa-plus"></i> @lang('messages.add')
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_your_customer_groups')])
            @can('customer.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="customer_groups_table">
                        <thead>
                            <tr>
                                <th>@lang('lang_v1.customer_group_name')</th>
                                <th>@lang('lang_v1.calculation_percentage')</th>
                                <th>@lang('lang_v1.selling_price_group')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        @endcomponent

        <div class="modal fade customer_groups_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    </section>

</div>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).on('change', '#price_calculation_type', function() {
        var price_calculation_type = $(this).val();
        if (price_calculation_type == 'percentage') {
            $('.percentage-field').removeClass('hide');
            $('.selling_price_group-field').addClass('hide');
        } else {
            $('.percentage-field').addClass('hide');
            $('.selling_price_group-field').removeClass('hide');
        }
    });
</script>
@endsection
