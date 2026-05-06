@extends('layouts.app')
@section('title', __('purchase.purchases'))

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
                <i class="fas fa-box-open"></i>
            </div>
            <div>
                <h1>@lang('purchase.purchases')</h1>
                <p class="pg-subtitle">@lang('purchase.all_purchases') &middot; {{ session('business.name') }}</p>
            </div>
        </div>
        <div class="pg-banner-actions">
            @can('purchase.create')
                <a class="pg-add-btn"
                    href="{{ action([\App\Http\Controllers\PurchaseController::class, 'create']) }}">
                    <i class="fas fa-plus"></i> @lang('messages.add')
                </a>
            @endcan
        </div>
    </div>
</div>

<section class="content no-print">

    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_location_id', __('purchase.business_location') . ':') !!}
                {!! Form::select('purchase_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_supplier_id', __('purchase.supplier') . ':') !!}
                {!! Form::select('purchase_list_filter_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_status', __('purchase.purchase_status') . ':') !!}
                {!! Form::select('purchase_list_filter_status', $orderStatuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_payment_status', __('purchase.payment_status') . ':') !!}
                {!! Form::select('purchase_list_filter_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('purchase_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']) !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.all_purchases')])
        @include('purchase.partials.purchase_table')
    @endcomponent

    <div class="modal fade product_modal"       tabindex="-1" role="dialog"></div>
    <div class="modal fade payment_modal"        tabindex="-1" role="dialog"></div>
    <div class="modal fade edit_payment_modal"   tabindex="-1" role="dialog"></div>
    @include('purchase.partials.update_purchase_status_modal')

</section>

<section id="receipt_section" class="print_section"></section>

</div>{{-- .page-modern --}}
@stop

@section('javascript')
@php $custom_labels = json_decode(session('business.custom_labels'), true); @endphp
<script>
    var customFieldVisibility = {
        custom_field_1: @json(!empty($custom_labels['purchase']['custom_field_1'])),
        custom_field_2: @json(!empty($custom_labels['purchase']['custom_field_2'])),
        custom_field_3: @json(!empty($custom_labels['purchase']['custom_field_3'])),
        custom_field_4: @json(!empty($custom_labels['purchase']['custom_field_4']))
    };
</script>
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
    $('#purchase_list_filter_date_range').daterangepicker(dateRangeSettings, function(start, end) {
        $('#purchase_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
        purchase_table.ajax.reload();
    });
    $('#purchase_list_filter_date_range').on('cancel.daterangepicker', function() {
        $('#purchase_list_filter_date_range').val('');
        purchase_table.ajax.reload();
    });

    $(document).on('click', '.update_status', function(e) {
        e.preventDefault();
        $('#update_purchase_status_form').find('#status').val($(this).data('status'));
        $('#update_purchase_status_form').find('#purchase_id').val($(this).data('purchase_id'));
        $('#update_purchase_status_modal').modal('show');
    });

    $(document).on('submit', '#update_purchase_status_form', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            method: 'POST', url: $(this).attr('action'), dataType: 'json', data: form.serialize(),
            beforeSend: function(xhr) { __disable_submit_button(form.find('button[type="submit"]')); },
            success: function(result) {
                if (result.success) {
                    $('#update_purchase_status_modal').modal('hide');
                    toastr.success(result.msg);
                    purchase_table.ajax.reload();
                    form.find('button[type="submit"]').attr('disabled', false);
                } else { toastr.error(result.msg); }
            }
        });
    });

    $(document).on('click', '.pay_purchase_mpesa', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("mpesa.payment-modal") }}',
            data: { transaction_id: $(this).data('id'), transaction_type: 'purchase' },
            dataType: 'html',
            success: function(result) { $('#mpesa_payment_modal').html(result).modal('show'); }
        });
    });
</script>
@endsection
