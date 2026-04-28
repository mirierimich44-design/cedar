@extends('layouts.app')
@section('title', __('purchase.purchases'))

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
    table.dataTable tfoot td { background:#f9fafb; font-size:12px; font-weight:700; padding:10px 12px !important; border-top:2px solid #e5e7eb !important; }
</style>
@endsection

@section('content')

<section class="content-header no-print">
    <div class="page-toolbar">
        <h1>@lang('purchase.purchases')</h1>
        @can('purchase.create')
            <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-lg"
               href="{{ action([\App\Http\Controllers\PurchaseController::class, 'create']) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 5l0 14"/><path d="M5 12l14 0"/>
                </svg>
                @lang('messages.add')
            </a>
        @endcan
    </div>
</section>

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
