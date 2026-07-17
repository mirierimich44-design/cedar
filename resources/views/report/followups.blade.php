@extends('layouts.app')
@section('title', __('lang_v1.followup_report'))

@section('content')

<div class="report-page-modern">
    <div class="rpt-banner">
        <div class="rpt-banner-inner">
            <div class="rpt-banner-title">
                <span class="rpt-banner-icon"><i class="fas fa-phone-alt"></i></span>
                <div>
                    <h1>{{ __('lang_v1.followup_report') }}</h1>
                    <p class="rpt-subtitle">{{ session()->get('business.name') }}</p>
                </div>
            </div>
        </div>
    </div>



<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('followup_location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('followup_location_id', $locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('followup_status',  __('sale.status') . ':') !!}
                        {!! Form::select('followup_status', ['' => __('lang_v1.all')] + $statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('followup_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('followup_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="followup_report_table">
                        <thead>
                            <tr>
                                <th>@lang('lang_v1.date')</th>
                                <th>@lang('purchase.business_location')</th>
                                <th>@lang('contact.customer')</th>
                                <th>@lang('lang_v1.customer_phone')</th>
                                <th>@lang('sale.product')</th>
                                <th>Follow-up Comment</th>
                                <th>@lang('sale.status')</th>
                                <th>@lang('lang_v1.initiated_by')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
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
<!-- /.content -->

</div>



@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready( function(){
            $('#followup_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#followup_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    followup_report_table.ajax.reload();
                }
            );
            $('#followup_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#followup_date_range').val('');
                followup_report_table.ajax.reload();
            });

            followup_report_table = $('#followup_report_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{action([\App\Http\Controllers\ReportController::class, "getFollowupReport"])}}',
                    data: function(d) {
                        d.location_id = $('#followup_location_id').val();
                        d.status = $('#followup_status').val();
                        var start = '';
                        var end = '';
                        if ($('#followup_date_range').val()) {
                            start = $('input#followup_date_range')
                                .data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            end = $('input#followup_date_range')
                                .data('daterangepicker')
                                .endDate.format('YYYY-MM-DD');
                        }
                        d.start_date = start;
                        d.end_date = end;
                    }
                },
                columns: [
                    { data: 'created_at', name: 'created_at' },
                    { data: 'location_name', name: 'location_name', searchable: false },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'customer_phone', name: 'customer_phone' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'comment', name: 'comment' },
                    { data: 'status', name: 'status' },
                    { data: 'created_by_name', name: 'created_by_name', searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $(document).on('change', '#followup_location_id, #followup_status', function() {
                followup_report_table.ajax.reload();
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
                            followup_report_table.ajax.reload();
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
                                    followup_report_table.ajax.reload();
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



@section('css')
@includeIf('report.partials.report_modern_css')
@endsection
@endsection