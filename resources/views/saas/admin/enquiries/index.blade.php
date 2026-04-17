@extends('layouts.app')
@section('title', 'Enquiries / Pending Orders')
@section('content')
<section class="content-header">
    <h1 class="tw-text-2xl tw-font-bold">Enquiries &amp; Pending Orders
        <small>
            <a href="{{ route('saas.admin.features') }}" class="btn btn-default btn-sm" style="margin-left:12px;">
                <i class="fa fa-arrow-left"></i> Back to Features
            </a>
        </small>
    </h1>
</section>

<section class="content">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

{{-- Filter bar --}}
<div class="box box-default" style="margin-bottom:16px;">
    <div class="box-body" style="padding:12px 16px;">
        <form method="GET" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <label style="margin:0;font-weight:600;font-size:13px;">Filter:</label>
            @foreach([''=>'All','pending'=>'Pending','contacted'=>'Contacted','converted'=>'Converted','cancelled'=>'Cancelled'] as $val=>$label)
            <a href="?status={{ $val }}"
               class="btn btn-sm {{ request('status',$val===''?'':null) == $val ? 'btn-primary' : 'btn-default' }}">
                {{ $label }}
            </a>
            @endforeach
        </form>
    </div>
</div>

@component('components.widget', ['header' => 'Enquiries'])
@if($enquiries->isEmpty())
    <p class="text-center text-muted" style="padding:30px;">No enquiries found.</p>
@else
<table class="table table-bordered table-hover" style="font-size:13px;">
    <thead>
        <tr>
            <th>#</th>
            <th>Business</th>
            <th>Contact</th>
            <th>Features Requested</th>
            <th>Billing</th>
            <th>Total (KES)</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($enquiries as $eq)
    @php
        $featureIds = json_decode($eq->feature_ids ?? '[]', true);
        $featureNames = \App\SaasFeature::whereIn('id', $featureIds)->pluck('name')->implode(', ');
        $statusColors = ['pending'=>'warning','contacted'=>'info','converted'=>'success','cancelled'=>'danger'];
        $statusColor = $statusColors[$eq->status] ?? 'default';
    @endphp
    <tr>
        <td>{{ $eq->id }}</td>
        <td>
            <strong>{{ $eq->business_name }}</strong><br>
            <small class="text-muted">{{ $eq->hosting === 'self_hosted' ? '🖥️ Self-Hosted' : '☁️ Cloud' }}</small>
        </td>
        <td>
            <a href="mailto:{{ $eq->email }}">{{ $eq->email }}</a><br>
            <small>{{ $eq->phone }}</small>
        </td>
        <td style="max-width:220px;">
            <small>{{ $featureNames ?: '—' }}</small>
        </td>
        <td><span class="label label-default" style="text-transform:capitalize;">{{ $eq->cycle }}</span></td>
        <td><strong>{{ number_format($eq->total, 0) }}</strong></td>
        <td><span class="label label-{{ $statusColor }}" style="text-transform:capitalize;">{{ $eq->status }}</span></td>
        <td><small>{{ \Carbon\Carbon::parse($eq->created_at)->format('d M Y H:i') }}</small></td>
        <td>
            <button class="btn btn-xs btn-info" onclick="openModal({{ $eq->id }}, '{{ $eq->status }}', `{{ addslashes($eq->notes ?? '') }}`)">
                <i class="fa fa-edit"></i> Update
            </button>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
<div style="padding:12px;">{{ $enquiries->links() }}</div>
@endif
@endcomponent
</section>

{{-- Update Status Modal --}}
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#0f172a;color:white;">
                <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:1;">&times;</button>
                <h4 class="modal-title">Update Enquiry</h4>
            </div>
            <form method="POST" id="updateForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" id="modal-status">
                            <option value="pending">Pending</option>
                            <option value="contacted">Contacted</option>
                            <option value="converted">Converted</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes (internal)</label>
                        <textarea name="notes" class="form-control" rows="3" id="modal-notes"
                            placeholder="e.g. Called client, demo scheduled for Monday..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(id, status, notes) {
    document.getElementById('updateForm').action = '/saas-admin/enquiries/' + id + '/status';
    document.getElementById('modal-status').value = status;
    document.getElementById('modal-notes').value = notes;
    $('#updateModal').modal('show');
}
</script>
@endsection
