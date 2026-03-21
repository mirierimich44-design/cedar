@extends('layouts.agent-portal')
@section('title', 'My Retrievals')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-2xl tw-font-bold tw-text-black">Cooler Retrievals</h1>
</section>

<section class="content">
    {{-- Initiate Retrieval Form --}}
    <div class="box box-primary tw-mb-4">
        <div class="box-header with-border" style="background:linear-gradient(135deg,var(--theme-dark) 0%,var(--theme-main) 100%);border:none;">
            <h3 class="box-title tw-text-white"><i class="fa fa-truck tw-mr-2"></i>Initiate Cooler Retrieval</h3>
        </div>
        <div class="box-body">
            {!! Form::open(['route' => 'cooler.agent.retrievals.store', 'method' => 'POST', 'id' => 'agent-retrieval-form']) !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('dealer_id', 'Select Customer *') !!}
                        {!! Form::select('dealer_id', $customers, old('dealer_id'), ['class' => 'form-control select2', 'required', 'id' => 'agent-customer-select', 'placeholder' => '— Select customer —']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('cooler_id', 'Cooler Asset *') !!}
                        {!! Form::select('cooler_id', [], old('cooler_id'), ['class' => 'form-control select2', 'required', 'id' => 'agent-cooler-select', 'placeholder' => '— Select customer first —', 'disabled']) !!}
                        {!! Form::hidden('agreement_id', old('agreement_id'), ['id' => 'agent-agreement-id']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('retrieval_date', 'Retrieval Date *') !!}
                        {!! Form::date('retrieval_date', old('retrieval_date', date('Y-m-d')), ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        {!! Form::label('reason', 'Reason for Retrieval *') !!}
                        {!! Form::select('reason', $reasons, old('reason'), ['class' => 'form-control select2', 'required', 'placeholder' => '— Select reason —']) !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('reason_notes', 'Additional Notes') !!}
                        {!! Form::textarea('reason_notes', old('reason_notes'), ['class' => 'form-control', 'rows' => 2]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('authorized_staff_name', 'Your Name (Authorised Staff) *') !!}
                        {!! Form::text('authorized_staff_name', old('authorized_staff_name', $user->full_name ?? ''), ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('authorized_staff_id_no', 'Your ID Number') !!}
                        {!! Form::text('authorized_staff_id_no', old('authorized_staff_id_no'), ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('authorized_staff_tel', 'Your Phone') !!}
                        {!! Form::text('authorized_staff_tel', old('authorized_staff_tel'), ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-warning"><i class="fa fa-truck"></i> Initiate Retrieval</button>
            {!! Form::close() !!}
        </div>
    </div>

    {{-- My retrievals list --}}
    @component('components.widget', ['title' => 'My Retrieval History'])
        <table class="table table-bordered table-striped" id="agent-retrievals-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Cooler</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    @endcomponent

</section>
@endsection

@section('javascript')
<script>
$(function () {
    // Load coolers when customer selected
    $('#agent-customer-select').on('change', function () {
        var customerId = $(this).val();
        var coolerSelect = $('#agent-cooler-select');
        coolerSelect.prop('disabled', true).empty().append('<option value="">Loading...</option>');

        if (!customerId) {
            coolerSelect.empty().append('<option value="">— Select customer first —</option>');
            return;
        }

        $.get('/cooler/dealer/' + customerId + '/coolers', function (data) {
            coolerSelect.empty().append('<option value="">— Select cooler —</option>');
            if (data.coolers && data.coolers.length) {
                $.each(data.coolers, function (i, c) {
                    coolerSelect.append('<option value="' + c.id + '">' + c.asset_number + ' (' + c.asset_type + ')</option>');
                });
                coolerSelect.prop('disabled', false);
            } else {
                coolerSelect.append('<option value="" disabled>No deployed coolers for this customer</option>');
            }
            if (data.agreement_id) {
                $('#agent-agreement-id').val(data.agreement_id);
            }
        });
    });

    // DataTable
    if ($.fn.DataTable.isDataTable('#agent-retrievals-table')) {
        $('#agent-retrievals-table').DataTable().destroy();
    }
    $('#agent-retrievals-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('cooler.agent.retrievals') }}',
        columns: [
            { data: 'retrieval_date' },
            { data: 'outlet_name' },
            { data: 'asset_number' },
            { data: 'reason_label' },
            { data: 'status_badge', orderable: false },
            { data: 'action', orderable: false },
        ],
        order: [[0, 'desc']],
    });
});
</script>
@endsection
