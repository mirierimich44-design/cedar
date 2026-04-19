@extends('layouts.app')
@section('title', 'Parcel Detail — ' . $parcel->waybill_number)

@section('content')
<section class="content-header">
    <h1>Parcel <small>{{ $parcel->waybill_number }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="{{ route('parcel.index') }}">Parcels</a></li>
        <li class="active">{{ $parcel->waybill_number }}</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-7">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Parcel Details</h3>
                    <div class="box-tools">
                        @php $colors = ['booked'=>'primary','in_transit'=>'warning','out_for_delivery'=>'info','delivered'=>'success','failed'=>'danger','returned'=>'default']; @endphp
                        <span class="label label-{{ $colors[$parcel->status] ?? 'default' }}" style="font-size:13px;">{{ ucfirst(str_replace('_',' ',$parcel->status)) }}</span>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr><th style="width:35%">Waybill</th><td><strong>{{ $parcel->waybill_number }}</strong></td></tr>
                        <tr><th>Sender</th><td>{{ $parcel->sender_name }} — {{ $parcel->sender_phone }}</td></tr>
                        <tr><th>Recipient</th><td>{{ $parcel->recipient_name }} — {{ $parcel->recipient_phone }}</td></tr>
                        <tr><th>Origin</th><td>{{ $parcel->originStation?->name ?? 'N/A' }}</td></tr>
                        <tr><th>Destination</th><td>{{ $parcel->destinationStation?->name ?? 'N/A' }}</td></tr>
                        <tr><th>Weight</th><td>{{ $parcel->weight_kg ?? '—' }} kg</td></tr>
                        <tr><th>Description</th><td>{{ $parcel->description ?? '—' }}</td></tr>
                        <tr><th>Charge</th><td>KES {{ number_format($parcel->charge_amount, 2) }}</td></tr>
                        <tr><th>Payment</th><td>
                            <span class="label {{ $parcel->payment_status === 'paid' ? 'label-success' : 'label-warning' }}">{{ ucfirst($parcel->payment_status ?? 'pending') }}</span>
                            &nbsp; {{ $parcel->payment_method ? ucfirst($parcel->payment_method) : '' }}
                        </td></tr>
                        <tr><th>Booked</th><td>{{ $parcel->created_at->format('d M Y H:i') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-history"></i> Status Timeline</h3>
                </div>
                <div class="box-body">
                    <ul class="timeline" style="padding:0;">
                        @forelse($parcel->statusLogs->sortByDesc('created_at') as $log)
                        <li>
                            <i class="fa fa-circle bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock-o"></i> {{ $log->created_at->format('d M H:i') }}</span>
                                <h3 class="timeline-header">{{ ucfirst(str_replace('_',' ',$log->status)) }}</h3>
                                @if($log->notes)<div class="timeline-body">{{ $log->notes }}</div>@endif
                            </div>
                        </li>
                        @empty
                        <li><p class="text-muted">No status updates yet.</p></li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Update Status --}}
            <div class="box box-warning">
                <div class="box-header with-border"><h3 class="box-title">Update Status</h3></div>
                <div class="box-body">
                    <form method="POST" action="{{ route('parcel.dispatch.update_status') }}">
                        @csrf
                        <input type="hidden" name="parcel_id" value="{{ $parcel->id }}">
                        <div class="form-group">
                            <select name="status" class="form-control" required>
                                @foreach(['booked','in_transit','out_for_delivery','delivered','failed','returned'] as $s)
                                    <option value="{{ $s }}" {{ $parcel->status == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="notes" class="form-control" placeholder="Optional notes...">
                        </div>
                        <button type="submit" class="btn btn-warning btn-block"><i class="fa fa-refresh"></i> Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
