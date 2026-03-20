@extends('layouts.app')
@section('title', __('lang_v1.mpesa_c2b_payments'))

@section('content')

<!-- Content Header -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        <i class="fas fa-money-bill-wave"></i> @lang('lang_v1.mpesa_c2b_payments')
    </h1>
    <p class="tw-text-gray-500">@lang('lang_v1.mpesa_c2b_description')</p>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
            <div class="box-tools">
                <a href="{{ route('mpesa.settings') }}" class="btn btn-default">
                    <i class="fas fa-cog"></i> @lang('lang_v1.mpesa_settings')
                </a>
                <a href="{{ route('mpesa.transactions') }}" class="btn btn-info">
                    <i class="fas fa-history"></i> @lang('lang_v1.mpesa_transactions')
                </a>
            </div>
        @endslot

        <!-- Filters -->
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('c2b_status_filter', __('lang_v1.status') . ':') !!}
                    {!! Form::select('c2b_status_filter', [
                        'all' => __('lang_v1.all'),
                        'received' => __('lang_v1.mpesa_c2b_received'),
                        'matched' => __('lang_v1.mpesa_c2b_matched'),
                        'used' => __('lang_v1.mpesa_c2b_used'),
                        'unmatched' => __('lang_v1.mpesa_c2b_unmatched'),
                    ], 'all', ['class' => 'form-control select2', 'id' => 'c2b_status_filter']) !!}
                </div>
            </div>
        </div>

        <hr>

        <!-- C2B Payments Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="mpesa_c2b_table">
                <thead>
                    <tr>
                        <th>@lang('lang_v1.date')</th>
                        <th>@lang('lang_v1.mpesa_receipt')</th>
                        <th>@lang('lang_v1.phone')</th>
                        <th>@lang('lang_v1.customer_name')</th>
                        <th>@lang('lang_v1.amount')</th>
                        <th>@lang('lang_v1.bill_ref')</th>
                        <th>@lang('lang_v1.status')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>

<!-- Match Payment Modal -->
<div class="modal fade" id="match_payment_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">@lang('lang_v1.mpesa_use_payment')</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>@lang('lang_v1.mpesa_payment_details'):</strong>
                    <p id="match_payment_info"></p>
                </div>
                <div class="form-group">
                    {!! Form::label('match_transaction_id', __('lang_v1.transaction_id') . ':') !!}
                    {!! Form::text('match_transaction_id', null, [
                        'class' => 'form-control',
                        'id' => 'match_transaction_id',
                        'placeholder' => __('lang_v1.enter_transaction_id')
                    ]) !!}
                </div>
                <input type="hidden" id="match_c2b_payment_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                <button type="button" class="btn btn-primary" id="confirm_match_payment">
                    <i class="fas fa-check"></i> @lang('lang_v1.mpesa_confirm_match')
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    var c2b_table = $('#mpesa_c2b_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("mpesa.c2b-payments") }}',
            data: function(d) {
                d.status = $('#c2b_status_filter').val();
            }
        },
        columns: [
            { data: 'trans_time', name: 'trans_time' },
            { data: 'trans_id', name: 'trans_id' },
            { data: 'msisdn', name: 'msisdn' },
            { data: 'customer_name', name: 'customer_name', orderable: false, searchable: false },
            { data: 'amount', name: 'amount' },
            { data: 'bill_ref_number', name: 'bill_ref_number', defaultContent: '-' },
            { data: 'status_label', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

    $('#c2b_status_filter').change(function() {
        c2b_table.ajax.reload();
    });

    // Open match modal
    $(document).on('click', '.use-payment-btn', function() {
        var id = $(this).data('id');
        var row = c2b_table.row($(this).closest('tr')).data();
        
        $('#match_c2b_payment_id').val(id);
        $('#match_payment_info').html(
            '<strong>Receipt:</strong> ' + row.trans_id + '<br>' +
            '<strong>Amount:</strong> KES ' + row.amount + '<br>' +
            '<strong>Phone:</strong> ' + row.msisdn + '<br>' +
            '<strong>Name:</strong> ' + row.customer_name
        );
        $('#match_transaction_id').val('');
        $('#match_payment_modal').modal('show');
    });

    // Confirm match
    $('#confirm_match_payment').click(function() {
        var btn = $(this);
        var c2b_id = $('#match_c2b_payment_id').val();
        var transaction_id = $('#match_transaction_id').val();

        if (!transaction_id) {
            toastr.error('@lang("lang_v1.please_enter_transaction_id")');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: '{{ route("mpesa.match-payment") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                c2b_payment_id: c2b_id,
                transaction_id: transaction_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#match_payment_modal').modal('hide');
                    c2b_table.ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('@lang("messages.something_went_wrong")');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> @lang("lang_v1.mpesa_confirm_match")');
            }
        });
    });
});
</script>
@endsection
