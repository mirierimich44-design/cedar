@extends('layouts.app')
@section('title', __('lang_v1.followups'))


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
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h1>@lang('lang_v1.followups')</h1>
                    <p class="pg-subtitle">@lang('lang_v1.manage_followups') &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>

<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('followups_location_filter', __('purchase.business_location') . ':') !!}
                {!! Form::select('followups_location_filter', $locations, null, [
                    'class' => 'form-control select2',
                    'style' => 'width:100%',
                    'placeholder' => __('lang_v1.all')
                ]) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('followups_status_filter', __('sale.status') . ':') !!}
                {!! Form::select('followups_status_filter', $statuses, null, [
                    'class' => 'form-control select2',
                    'style' => 'width:100%',
                    'placeholder' => __('lang_v1.all')
                ]) !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.followups')])
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="followups_table">
                <thead>
                    <tr>
                        <th>@lang('sale.product')</th>
                        <th>@lang('lang_v1.customer_phone')</th>
                        <th>@lang('lang_v1.customer_name')</th>
                        <th>@lang('lang_v1.comment')</th>
                        <th>@lang('purchase.business_location')</th>
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

<!-- Update Status Modal -->
<div class="modal fade" id="update_followup_status_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.update_status')</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="update_followup_id">
                <div class="form-group">
                    <label>@lang('sale.status'):</label>
                    <select id="new_followup_status" class="form-control">
                        @foreach($statuses as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                <button type="button" class="btn btn-primary" id="save_followup_status">@lang('messages.save')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var followups_table = $('#followups_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ action([\App\Http\Controllers\FollowupController::class, "index"]) }}',
            data: function(d) {
                d.location_id = $('#followups_location_filter').val();
                d.status = $('#followups_status_filter').val();
            }
        },
        columns: [
            { data: 'product_name', name: 'product_name' },
            { data: 'customer_phone', name: 'followups.customer_phone' },
            { data: 'customer_name', name: 'followups.customer_name' },
            { data: 'comment', name: 'followups.comment' },
            { data: 'location_name', name: 'location_name' },
            { data: 'status', name: 'followups.status' },
            { data: 'created_by_name', name: 'created_by_name' },
            { data: 'created_at', name: 'followups.created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Filter
    $('#followups_location_filter, #followups_status_filter').change(function() {
        followups_table.ajax.reload();
    });

    // Edit status modal
    $(document).on('click', '.edit-followup-status', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        $('#update_followup_id').val(id);
        $('#new_followup_status').val(status);
        $('#update_followup_status_modal').modal('show');
    });

    // Save status
    $('#save_followup_status').click(function() {
        var id = $('#update_followup_id').val();
        var status = $('#new_followup_status').val();
        $.ajax({
            url: '{{ action([\App\Http\Controllers\FollowupController::class, "updateStatus"], ["id" => "__ID__"]) }}'.replace('__ID__', id),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#update_followup_status_modal').modal('hide');
                    followups_table.ajax.reload();
                } else {
                    toastr.error(response.msg);
                }
            }
        });
    });

    // Delete followup
    $(document).on('click', '.delete-followup', function() {
        var id = $(this).data('id');
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '{{ action([\App\Http\Controllers\FollowupController::class, "destroy"], ["id" => "__ID__"]) }}'.replace('__ID__', id),
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            followups_table.ajax.reload();
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

</div>{{-- .page-modern --}}
@endsection