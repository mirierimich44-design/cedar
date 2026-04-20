@extends('layouts.app')
@section('title', 'Parcel Management')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Parcel Management
        <small class="tw-text-sm tw-text-gray-600">Kenyan Long Distance Courier</small>
    </h1>
</section>

<section class="content">
    <!-- Stats Cards -->
    <div class="row tw-mb-4">
        <div class="col-sm-6 col-md-2">
            <div class="info-box bg-aqua"><span class="info-box-icon"><i class="fa fa-box"></i></span>
                <div class="info-box-content"><span class="info-box-text">Booked</span><span class="info-box-number">{{ $counts['booked'] ?? 0 }}</span></div>
            </div>
        </div>
        <div class="col-sm-6 col-md-2">
            <div class="info-box bg-orange"><span class="info-box-icon"><i class="fa fa-truck"></i></span>
                <div class="info-box-content"><span class="info-box-text">In Transit</span><span class="info-box-number">{{ $counts['in_transit'] ?? 0 }}</span></div>
            </div>
        </div>
        <div class="col-sm-6 col-md-2">
            <div class="info-box bg-yellow"><span class="info-box-icon"><i class="fa fa-building"></i></span>
                <div class="info-box-content"><span class="info-box-text">At Depot</span><span class="info-box-number">{{ $counts['at_depot'] ?? 0 }}</span></div>
            </div>
        </div>
        <div class="col-sm-6 col-md-2">
            <div class="info-box bg-light-blue"><span class="info-box-icon"><i class="fa fa-motorcycle"></i></span>
                <div class="info-box-content"><span class="info-box-text">Out for Delivery</span><span class="info-box-number">{{ $counts['out_for_delivery'] ?? 0 }}</span></div>
            </div>
        </div>
        <div class="col-sm-6 col-md-2">
            <div class="info-box bg-green"><span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content"><span class="info-box-text">Delivered</span><span class="info-box-number">{{ $counts['delivered'] ?? 0 }}</span></div>
            </div>
        </div>
        <div class="col-sm-6 col-md-2">
            <div class="info-box bg-red"><span class="info-box-icon"><i class="fa fa-undo"></i></span>
                <div class="info-box-content"><span class="info-box-text">Returned</span><span class="info-box-number">{{ $counts['returned'] ?? 0 }}</span></div>
            </div>
        </div>
    </div>

    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('parcel_filter_status', 'Status:') !!}
                {!! Form::select('parcel_filter_status', $status_list, null, ['class' => 'form-control select2', 'placeholder' => 'All']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('parcel_filter_from', 'From:') !!}
                {!! Form::text('parcel_filter_from', null, ['class' => 'form-control', 'placeholder' => 'Origin town']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('parcel_filter_to', 'To:') !!}
                {!! Form::text('parcel_filter_to', null, ['class' => 'form-control', 'placeholder' => 'Destination town']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('parcel_filter_date_range', 'Date Range:') !!}
                {!! Form::text('parcel_filter_date_range', null, ['class' => 'form-control', 'id' => 'parcel_filter_date_range', 'placeholder' => 'Select date range', 'readonly']) !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => 'All Parcels'])
        @slot('tool')
            <div class="box-tools tw-flex tw-gap-2">
                <a href="{{ route('parcels.track') }}" class="btn btn-sm btn-default">
                    <i class="fa fa-search"></i> Track Parcel
                </a>
                <a href="{{ route('parcel-manifest') }}" class="btn btn-sm btn-default">
                    <i class="fa fa-list-alt"></i> Manifest
                </a>
                <a href="{{ route('parcel-routes.index') }}" class="btn btn-sm btn-default">
                    <i class="fa fa-route"></i> Routes
                </a>
                <a href="{{ route('parcels.create') }}"
                    class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full">
                    <i class="fa fa-plus"></i> Book Parcel
                </a>
            </div>
        @endslot

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="parcels_table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Waybill No.</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Weight</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Parcel Status</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_parcel_id">
                <div class="form-group">
                    <label>New Status</label>
                    {!! Form::select('modal_status', \App\Parcel::statusList(), null, ['class' => 'form-control select2', 'id' => 'modal_status']) !!}
                </div>
                <div class="form-group">
                    <label>Current Location</label>
                    {!! Form::text('modal_location', null, ['class' => 'form-control', 'id' => 'modal_location', 'placeholder' => 'e.g. Nairobi Depot']) !!}
                </div>
                <div class="form-group" id="delivered_to_group" style="display:none">
                    <label>Delivered To (Name)</label>
                    {!! Form::text('modal_delivered_to', null, ['class' => 'form-control', 'id' => 'modal_delivered_to']) !!}
                </div>
                <div class="form-group">
                    <label>Note</label>
                    {!! Form::text('modal_note', null, ['class' => 'form-control', 'id' => 'modal_note', 'placeholder' => 'Optional note']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirm_status_update">Update Status</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(function() {
    // Date range picker
    $('#parcel_filter_date_range').daterangepicker({
        locale: { format: 'DD/MM/YYYY' }
    });

    var table = $('#parcels_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("parcels.index") }}',
            data: function(d) {
                d.status     = $('#parcel_filter_status').val();
                d.from_town  = $('#parcel_filter_from').val();
                d.to_town    = $('#parcel_filter_to').val();
                d.date_range = $('#parcel_filter_date_range').val();
            }
        },
        columns: [
            { data: 'action', orderable: false, searchable: false, width: '100px' },
            { data: 'waybill_number' },
            { data: 'sender_name' },
            { data: 'receiver_name' },
            { data: 'from_town' },
            { data: 'to_town' },
            { data: 'weight_kg', render: d => d + ' kg' },
            { data: 'total_amount' },
            { data: 'payment_status', orderable: false },
            { data: 'status_badge', orderable: false },
            { data: 'created_at', render: d => d ? d.substring(0,10) : '' },
        ]
    });

    $('#parcel_filter_status').on('change', function() { table.ajax.reload(); });
    $('#parcel_filter_from, #parcel_filter_to').on('keyup', function() { table.ajax.reload(); });
    $('#parcel_filter_date_range').on('apply.daterangepicker', function() { table.ajax.reload(); });

    // Update status
    $(document).on('click', '.update-status-btn', function() {
        $('#modal_parcel_id').val($(this).data('id'));
        $('#modal_status').val($(this).data('status')).trigger('change');
        $('#updateStatusModal').modal('show');
    });

    $('#modal_status').on('change', function() {
        $('#delivered_to_group').toggle($(this).val() === 'delivered');
    });

    $('#confirm_status_update').on('click', function() {
        var id = $('#modal_parcel_id').val();
        $.post('/parcels/' + id + '/status', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            status: $('#modal_status').val(),
            location: $('#modal_location').val(),
            note: $('#modal_note').val(),
            delivered_to: $('#modal_delivered_to').val(),
        }, function(res) {
            if (res.success) {
                toastr.success(res.msg);
                $('#updateStatusModal').modal('hide');
                table.ajax.reload();
            } else {
                toastr.error(res.msg);
            }
        });
    });
});
</script>
@endsection
