@extends('layouts.app')
@section('title', 'Disposal Log')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Drug Disposal Log (PPB Workflow)</h1>
</section>

<section class="content">
    @if(session('status'))
        <div class="alert alert-{{ session('status')['success'] ? 'success' : 'danger' }}">{{ session('status')['msg'] }}</div>
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' => 'Disposal Records'])

        {{-- Status filter tabs --}}
        <ul class="nav nav-tabs" style="margin-bottom:16px">
            <li class="{{ $status == '' ? 'active' : '' }}">
                <a href="{{ route('dda.destruction') }}">All</a>
            </li>
            <li class="{{ $status == 'quarantined' ? 'active' : '' }}">
                <a href="{{ route('dda.destruction', ['status' => 'quarantined']) }}">Quarantined</a>
            </li>
            <li class="{{ $status == 'ppb_applied' ? 'active' : '' }}">
                <a href="{{ route('dda.destruction', ['status' => 'ppb_applied']) }}">PPB Applied</a>
            </li>
            <li class="{{ $status == 'collected' ? 'active' : '' }}">
                <a href="{{ route('dda.destruction', ['status' => 'collected']) }}">Collected</a>
            </li>
            <li class="{{ $status == 'certificate_received' ? 'active' : '' }}">
                <a href="{{ route('dda.destruction', ['status' => 'certificate_received']) }}">Complete</a>
            </li>
        </ul>

        <div class="row" style="margin-bottom:12px">
            <div class="col-md-12">
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#newDisposalModal">
                    <i class="fa fa-plus"></i> New Disposal Request
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Drug / Product</th>
                        <th>Batch</th>
                        <th>Expiry</th>
                        <th>Qty</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>PPB Ref</th>
                        <th>Disposal Company</th>
                        <th>Certificate</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $statusMap = [
                            'quarantined'          => ['label' => 'warning',  'text' => 'Quarantined'],
                            'ppb_applied'          => ['label' => 'info',     'text' => 'PPB Applied'],
                            'collected'            => ['label' => 'primary',  'text' => 'Collected'],
                            'certificate_received' => ['label' => 'success',  'text' => 'Complete'],
                        ];
                        $s = $statusMap[$log->status] ?? ['label' => 'default', 'text' => ucfirst($log->status)];
                    @endphp
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>
                            {{ $log->drug_name ?? optional($log->ddaDrug)->name ?? optional($log->product)->name ?? '—' }}
                        </td>
                        <td>{{ $log->batch_number ?? '—' }}</td>
                        <td>{{ $log->expiry_date ? \Carbon\Carbon::parse($log->expiry_date)->format('M Y') : '—' }}</td>
                        <td>{{ $log->quantity }} {{ $log->unit }}</td>
                        <td><span class="label label-warning">{{ ucfirst($log->reason) }}</span></td>
                        <td><span class="label label-{{ $s['label'] }}">{{ $s['text'] }}</span></td>
                        <td>
                            @if($log->ppb_application_number)
                                {{ $log->ppb_application_number }}
                                @if($log->ppb_application_date)
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($log->ppb_application_date)->format('d M Y') }}</small>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($log->disposal_company)
                                {{ $log->disposal_company }}
                                @if($log->collection_date)
                                    <br><small class="text-muted">Collected: {{ \Carbon\Carbon::parse($log->collection_date)->format('d M Y') }}</small>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($log->ppb_certificate_number)
                                {{ $log->ppb_certificate_number }}
                                @if($log->certificate_image_path)
                                    <br>
                                    <a href="{{ route('dda.destruction.certificate', $log->id) }}" target="_blank" class="btn btn-xs btn-success">
                                        <i class="fa fa-file"></i> View
                                    </a>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($log->status !== 'certificate_received')
                                <button type="button" class="btn btn-xs btn-primary btn-update-status"
                                    data-id="{{ $log->id }}"
                                    data-status="{{ $log->status }}"
                                    data-toggle="modal" data-target="#updateStatusModal">
                                    <i class="fa fa-arrow-right"></i> Update
                                </button>
                            @else
                                <span class="text-success"><i class="fa fa-check"></i> Done</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="text-center text-muted">No disposal records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $logs->links() }}
    @endcomponent
</section>

{{-- New Disposal Request Modal --}}
<div class="modal fade" id="newDisposalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">New Disposal Request</h4>
            </div>
            {!! Form::open(['route' => 'dda.destruction.store', 'method' => 'post']) !!}
            <div class="modal-body">
                <p class="text-muted" style="margin-bottom:16px">
                    Quarantine the drug first. You will apply to PPB (<strong>prims.pharmacyboardkenya.org</strong>) for disposal approval after quarantine.
                </p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Drug Name *</label>
                            <input type="text" name="drug_name" class="form-control" required placeholder="e.g. Morphine Sulphate 10mg">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>DDA Drug (optional)</label>
                            <select name="dda_drug_id" class="form-control">
                                <option value="">-- Select if DDA controlled --</option>
                                @foreach($dda_drugs as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Quantity *</label>
                            <input type="number" name="quantity" step="0.01" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Unit</label>
                            <input type="text" name="unit" class="form-control" placeholder="tabs / ml / vials">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Batch / Lot No.</label>
                            <input type="text" name="batch_number" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Reason *</label>
                            <select name="reason" class="form-control" required>
                                <option value="expired">Expired</option>
                                <option value="damaged">Damaged</option>
                                <option value="contaminated">Contaminated</option>
                                <option value="recalled">Recalled</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Additional information">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger"><i class="fa fa-lock"></i> Quarantine Drug</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

{{-- Update Status Modal --}}
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Disposal Status</h4>
            </div>
            <form id="updateStatusForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>New Status *</label>
                        <select name="status" id="statusSelect" class="form-control" required>
                            <option value="ppb_applied">PPB Applied</option>
                            <option value="collected">Collected by Disposal Company</option>
                            <option value="certificate_received">Certificate Received (Complete)</option>
                        </select>
                    </div>

                    {{-- PPB Applied fields --}}
                    <div id="fields_ppb_applied">
                        <div class="alert alert-info" style="font-size:12px">
                            Apply at <strong>prims.pharmacyboardkenya.org</strong> &mdash; fee KES 2,500. Enter the application reference once submitted.
                        </div>
                        <div class="form-group">
                            <label>PPB Application Number *</label>
                            <input type="text" name="ppb_application_number" class="form-control" placeholder="e.g. PPB/DISP/2024/001">
                        </div>
                        <div class="form-group">
                            <label>Application Date</label>
                            <input type="date" name="ppb_application_date" class="form-control">
                        </div>
                    </div>

                    {{-- Collected fields --}}
                    <div id="fields_collected" style="display:none">
                        <div class="form-group">
                            <label>Disposal Company *</label>
                            <input type="text" name="disposal_company" class="form-control" placeholder="e.g. Royfy Enterprises Ltd">
                        </div>
                        <div class="form-group">
                            <label>Collection Date</label>
                            <input type="date" name="collection_date" class="form-control">
                        </div>
                    </div>

                    {{-- Certificate fields --}}
                    <div id="fields_certificate_received" style="display:none">
                        <div class="alert alert-success" style="font-size:12px">
                            PPB issues the Certificate of Safe Disposal within 30 days. Keep a copy for your records.
                        </div>
                        <div class="form-group">
                            <label>PPB Certificate Number *</label>
                            <input type="text" name="ppb_certificate_number" class="form-control" placeholder="e.g. PPB/CSD/2024/001">
                        </div>
                        <div class="form-group">
                            <label>Certificate Date</label>
                            <input type="date" name="ppb_certificate_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Upload Certificate Image</label>
                            <input type="file" name="certificate_image" class="form-control" accept="image/*,.pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('javascript')
<script>
$(function () {
    var nextStatus = {
        'quarantined': 'ppb_applied',
        'ppb_applied': 'collected',
        'collected':   'certificate_received'
    };

    $('.btn-update-status').on('click', function () {
        var id     = $(this).data('id');
        var status = $(this).data('status');
        var next   = nextStatus[status] || 'ppb_applied';

        // Set form action
        $('#updateStatusForm').attr('action', '/dda/destruction/' + id + '/status');

        // Pre-select next logical status and show relevant fields
        $('#statusSelect').val(next).trigger('change');
    });

    $('#statusSelect').on('change', function () {
        var val = $(this).val();
        $('#fields_ppb_applied, #fields_collected, #fields_certificate_received').hide();
        if (val) $('#fields_' + val).show();
    }).trigger('change');
});
</script>
@endsection
@endsection
