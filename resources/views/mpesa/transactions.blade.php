@extends('layouts.app')
@section('title', __('lang_v1.mpesa_transactions'))

@section('content')

<!-- Content Header -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        <i class="fas fa-history"></i> @lang('lang_v1.mpesa_transactions')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
            <div class="box-tools">
                <a href="{{ route('mpesa.settings') }}" class="btn btn-default">
                    <i class="fas fa-cog"></i> @lang('lang_v1.mpesa_settings')
                </a>
                <a href="{{ route('mpesa.c2b-payments') }}" class="btn btn-info">
                    <i class="fas fa-money-bill-wave"></i> @lang('lang_v1.mpesa_c2b_payments')
                </a>
            </div>
        @endslot

        <!-- Filters -->
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('status_filter', __('lang_v1.status') . ':') !!}
                    {!! Form::select('status_filter', [
                        'all' => __('lang_v1.all'),
                        'pending' => __('lang_v1.mpesa_status_pending'),
                        'paid' => __('lang_v1.mpesa_status_paid'),
                        'failed' => __('lang_v1.mpesa_status_failed'),
                        'cancelled' => __('lang_v1.mpesa_status_cancelled'),
                        'expired' => __('lang_v1.mpesa_status_expired'),
                    ], 'all', ['class' => 'form-control select2', 'id' => 'status_filter']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('start_date', __('report.start_date') . ':') !!}
                    {!! Form::date('start_date', \Carbon\Carbon::today()->subDays(30), ['class' => 'form-control', 'id' => 'start_date']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('end_date', __('report.end_date') . ':') !!}
                    {!! Form::date('end_date', \Carbon\Carbon::today(), ['class' => 'form-control', 'id' => 'end_date']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" id="apply_filter" class="btn btn-primary btn-block">
                        <i class="fas fa-filter"></i> @lang('report.apply_filters')
                    </button>
                </div>
            </div>
        </div>

        <hr>

        <!-- Transactions Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="mpesa_transactions_table">
                <thead>
                    <tr>
                        <th>@lang('lang_v1.date')</th>
                        <th>@lang('lang_v1.phone')</th>
                        <th>@lang('lang_v1.amount')</th>
                        <th>@lang('lang_v1.reference')</th>
                        <th>@lang('lang_v1.mpesa_receipt')</th>
                        <th>@lang('lang_v1.status')</th>
                        <th>@lang('lang_v1.initiated_by')</th>
                        <th>@lang('lang_v1.description')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    var mpesa_transactions_table = $('#mpesa_transactions_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("mpesa.transactions") }}',
            data: function(d) {
                d.status = $('#status_filter').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'phone', name: 'phone' },
            { data: 'amount', name: 'amount' },
            { data: 'account_reference', name: 'account_reference' },
            { data: 'mpesa_receipt_number', name: 'mpesa_receipt_number', defaultContent: '-' },
            { data: 'status_label', name: 'status', orderable: false, searchable: false },
            { data: 'initiated_by_name', name: 'initiated_by_name', orderable: false, searchable: false },
            { data: 'result_description', name: 'result_description', defaultContent: '-' }
        ],
        order: [[0, 'desc']]
    });

    $('#apply_filter').click(function() {
        mpesa_transactions_table.ajax.reload();
    });

    $('#status_filter').change(function() {
        mpesa_transactions_table.ajax.reload();
    });
});
</script>
@endsection
