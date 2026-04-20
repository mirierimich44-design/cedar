@extends('layouts.app')
@section('title', 'Hospital Billing')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Hospital Billing
        <small class="tw-text-sm tw-text-gray-600">Manage patient bills</small>
    </h1>
</section>

<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('billing_filter_location_id', 'Location:') !!}
                {!! Form::select('billing_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => 'All Locations']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('billing_filter_visit_type', 'Visit Type:') !!}
                {!! Form::select('billing_filter_visit_type', $visit_types, null, ['class' => 'form-control select2', 'placeholder' => 'All Types']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('billing_filter_payment_status', 'Payment:') !!}
                {!! Form::select('billing_filter_payment_status', $payment_statuses, null, ['class' => 'form-control select2', 'placeholder' => 'All']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('billing_filter_start_date', 'From Date:') !!}
                {!! Form::text('billing_filter_start_date', null, ['class' => 'form-control start-date-picker', 'placeholder' => 'Start Date', 'readonly']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('billing_filter_end_date', 'To Date:') !!}
                {!! Form::text('billing_filter_end_date', null, ['class' => 'form-control end-date-picker', 'placeholder' => 'End Date', 'readonly']) !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => 'All Patient Bills'])
        @slot('tool')
            <div class="box-tools">
                <a href="{{ route('hospital-billing.create') }}"
                    class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right">
                    <i class="fa fa-plus"></i> New Bill
                </a>
            </div>
        @endslot

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="billing_table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Bill No.</th>
                        <th>Patient Name</th>
                        <th>Phone</th>
                        <th>Doctor</th>
                        <th>Visit Type</th>
                        <th>Visit Date</th>
                        <th>Total (KES)</th>
                        <th>Balance (KES)</th>
                        <th>Payment</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
@endsection

@section('javascript')
<script>
$(function() {
    var table = $('#billing_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("hospital-billing.index") }}',
            data: function(d) {
                d.location_id      = $('#billing_filter_location_id').val();
                d.visit_type       = $('#billing_filter_visit_type').val();
                d.payment_status   = $('#billing_filter_payment_status').val();
                d.start_date       = $('#billing_filter_start_date').val();
                d.end_date         = $('#billing_filter_end_date').val();
            }
        },
        columns: [
            { data: 'action', orderable: false, searchable: false },
            { data: 'bill_number' },
            { data: 'patient_name' },
            { data: 'patient_phone' },
            { data: 'doctor_name' },
            { data: 'visit_type' },
            { data: 'visit_date' },
            { data: 'total_amount', className: 'text-right' },
            { data: 'balance', className: 'text-right' },
            { data: 'payment_status_badge', orderable: false },
        ]
    });

    $('#billing_filter_location_id, #billing_filter_visit_type, #billing_filter_payment_status').on('change', function() {
        table.ajax.reload();
    });
    $('#billing_filter_start_date, #billing_filter_end_date').on('change', function() {
        if ($('#billing_filter_start_date').val() && $('#billing_filter_end_date').val()) {
            table.ajax.reload();
        }
    });
});
</script>
@endsection
