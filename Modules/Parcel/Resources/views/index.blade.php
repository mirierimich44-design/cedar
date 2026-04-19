@extends('layouts.app')
@section('title', 'Parcel Management')

@section('content')
<section class="content-header">
    <h1>Parcel Management <small>Courier & Logistics</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Parcels</li>
    </ol>
</section>

<section class="content">

    {{-- KPI Cards --}}
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="info-box"><span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content"><span class="info-box-text">Total Parcels</span>
                    <span class="info-box-number">{{ number_format($stats->total ?? 0) }}</span></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box"><span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content"><span class="info-box-text">Delivered</span>
                    <span class="info-box-number">{{ number_format($stats->delivered ?? 0) }}</span></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box"><span class="info-box-icon bg-yellow"><i class="fa fa-truck"></i></span>
                <div class="info-box-content"><span class="info-box-text">In Transit</span>
                    <span class="info-box-number">{{ number_format($stats->in_transit ?? 0) }}</span></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box"><span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                <div class="info-box-content"><span class="info-box-text">Revenue (Paid)</span>
                    <span class="info-box-number">KES {{ number_format($stats->revenue ?? 0, 2) }}</span></div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="box box-default">
        <div class="box-body">
            <form method="GET" class="form-inline" style="display:flex;flex-wrap:wrap;gap:6px;align-items:flex-end;">
                <input type="date" name="date_from" class="form-control input-sm" value="{{ $date_from }}">
                <input type="date" name="date_to" class="form-control input-sm" value="{{ $date_to }}">
                <select name="status" class="form-control input-sm">
                    <option value="">All Statuses</option>
                    @foreach(['booked','in_transit','out_for_delivery','delivered','failed','returned'] as $s)
                        <option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
                <select name="station_id" class="form-control input-sm">
                    <option value="">All Stations</option>
                    @foreach($stations as $st)
                        <option value="{{ $st->id }}" {{ $station == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" class="form-control input-sm" placeholder="Waybill / name / phone" value="{{ $search }}">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Filter</button>
                <a href="{{ route('parcel.index') }}" class="btn btn-default btn-sm">Reset</a>
                <a href="{{ route('parcel.booking.create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> New Booking</a>
                <a href="{{ route('parcel.stations.index') }}" class="btn btn-default btn-sm"><i class="fa fa-map-marker"></i> Stations</a>
            </form>
        </div>
    </div>

    {{-- Parcels Table --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Parcels <small>{{ $parcels->total() }} records</small></h3>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Waybill</th>
                        <th>Sender</th>
                        <th>Recipient</th>
                        <th>Route</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Charge</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parcels as $parcel)
                    <tr>
                        <td><strong>{{ $parcel->waybill_number }}</strong></td>
                        <td>{{ $parcel->sender_name }}<br><small class="text-muted">{{ $parcel->sender_phone }}</small></td>
                        <td>{{ $parcel->recipient_name }}<br><small class="text-muted">{{ $parcel->recipient_phone }}</small></td>
                        <td>
                            <small>{{ $parcel->originStation?->name ?? '?' }}</small>
                            <i class="fa fa-long-arrow-down text-muted"></i>
                            <small>{{ $parcel->destinationStation?->name ?? '?' }}</small>
                        </td>
                        <td>
                            @php $colors = ['booked'=>'primary','in_transit'=>'warning','out_for_delivery'=>'info','delivered'=>'success','failed'=>'danger','returned'=>'default']; @endphp
                            <span class="label label-{{ $colors[$parcel->status] ?? 'default' }}">{{ ucfirst(str_replace('_',' ',$parcel->status)) }}</span>
                        </td>
                        <td>
                            <span class="label {{ $parcel->payment_status === 'paid' ? 'label-success' : 'label-warning' }}">
                                {{ ucfirst($parcel->payment_status ?? 'pending') }}
                            </span>
                        </td>
                        <td>KES {{ number_format($parcel->charge_amount, 2) }}</td>
                        <td><small>{{ $parcel->created_at->format('d M Y') }}</small></td>
                        <td>
                            <div class="btn-group btn-group-xs">
                                <a href="{{ route('parcel.show', $parcel->id) }}" class="btn btn-default" title="View Detail"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('parcel.track.show', $parcel->waybill_number) }}" target="_blank" class="btn btn-info" title="Track"><i class="fa fa-map-marker"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted" style="padding:30px;">
                        <i class="fa fa-cubes fa-2x"></i><br><br>No parcels found.
                        <br><a href="{{ route('parcel.booking.create') }}" class="btn btn-success btn-sm" style="margin-top:10px;"><i class="fa fa-plus"></i> Create First Booking</a>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="box-footer clearfix">
            {{ $parcels->links() }}
        </div>
    </div>

</section>
@endsection
