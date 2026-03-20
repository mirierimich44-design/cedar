@extends('layouts.app')
@section('title', __('lang_v1.orders'))

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.orders')
        <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang('lang_v1.manage_orders')</small>
    </h1>
</section>

<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('orders_location_filter', __('purchase.business_location') . ':') !!}
                {!! Form::select('orders_location_filter', $locations, null, [
                    'class' => 'form-control select2',
                    'style' => 'width:100%',
                    'placeholder' => __('lang_v1.all')
                ]) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('orders_status_filter', __('sale.status') . ':') !!}
                {!! Form::select('orders_status_filter', $statuses, null, [
                    'class' => 'form-control select2',
                    'style' => 'width:100%',
                    'placeholder' => __('lang_v1.all')
                ]) !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.orders')])
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="orders_table">
                <thead>
                    <tr>
                        <th>@lang('lang_v1.ref_no')</th>
                        <th>@lang('contact.customer')</th>
                        <th>@lang('purchase.business_location')</th>
                        <th>@lang('lang_v1.items')</th>
                        <th>@lang('sale.status')</th>
                        <th>@lang('lang_v1.created_by')</th>
                        <th>@lang('lang_v1.created_at')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>

<!-- Order Details Modal -->
<div class="modal fade" id="order_details_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.order_details')</h4>
            </div>
            <div class="modal-body" id="order_details_content">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="update_status_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.update_status')</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="update_order_id">
                <div class="form-group">
                    <label>@lang('sale.status'):</label>
                    <select id="new_order_status" class="form-control">
                        @foreach($statuses as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                <button type="button" class="btn btn-primary" id="save_order_status">@lang('messages.save')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var orders_table = $('#orders_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ action([\App\Http\Controllers\OrderController::class, "index"]) }}',
            data: function(d) {
                d.location_id = $('#orders_location_filter').val();
                d.status = $('#orders_status_filter').val();
            }
        },
        columns: [
            { data: 'ref_no', name: 'pos_orders.ref_no' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'location_name_display', name: 'location_name_display' },
            { data: 'items_count', name: 'items_count', searchable: false, orderable: false },
            { data: 'status', name: 'pos_orders.status' },
            { data: 'created_by_name', name: 'created_by_name' },
            { data: 'created_at', name: 'pos_orders.created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Filter
    $('#orders_location_filter, #orders_status_filter').change(function() {
        orders_table.ajax.reload();
    });

    // View order
    $(document).on('click', '.view-order', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '{{ action([\App\Http\Controllers\OrderController::class, "show"], ["id" => "__ID__"]) }}'.replace('__ID__', id),
            success: function(response) {
                $('#order_details_content').html(response);
                $('#order_details_modal').modal('show');
            }
        });
    });

    // Edit status modal
    $(document).on('click', '.edit-order-status', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        $('#update_order_id').val(id);
        $('#new_order_status').val(status);
        $('#update_status_modal').modal('show');
    });

    // Save status
    $('#save_order_status').click(function() {
        var id = $('#update_order_id').val();
        var status = $('#new_order_status').val();
        $.ajax({
            url: '{{ action([\App\Http\Controllers\OrderController::class, "updateStatus"], ["id" => "__ID__"]) }}'.replace('__ID__', id),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#update_status_modal').modal('hide');
                    orders_table.ajax.reload();
                } else {
                    toastr.error(response.msg);
                }
            }
        });
    });

    // Delete order
    $(document).on('click', '.delete-order', function() {
        var id = $(this).data('id');
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '{{ action([\App\Http\Controllers\OrderController::class, "destroy"], ["id" => "__ID__"]) }}'.replace('__ID__', id),
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            orders_table.ajax.reload();
                        } else {
                            toastr.error(response.msg);
                        }
                    }
                });
            }
        });
    });
});
</script>
@endsection
