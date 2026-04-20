@extends('layouts.app')
@section('title', 'Dispatch Manifest')

@section('content')
<section class="content-header">
    <h1>Dispatch Manifest <small>Daily parcel dispatch list</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="{{ route('parcel.index') }}">Parcels</a></li>
        <li class="active">Manifest</li>
    </ol>
</section>

<section class="content">
    {{-- Filter Bar --}}
    <div class="box box-default">
        <div class="box-body">
            <form method="GET" class="form-inline" style="display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end;">
                <div class="form-group">
                    <label class="sr-only">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                </div>
                <div class="form-group">
                    <label class="sr-only">Route</label>
                    <select name="route_id" class="form-control">
                        <option value="">All Routes</option>
                        @foreach($routes as $r)
                            <option value="{{ $r->id }}" {{ $route_id == $r->id ? 'selected' : '' }}>
                                {{ $r->origin?->name }} → {{ $r->destination?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
                <a href="{{ route('parcel.manifest') }}" class="btn btn-default">Today</a>
                <button type="button" class="btn btn-default" onclick="window.print()">
                    <i class="fa fa-print"></i> Print Manifest
                </button>
            </form>
        </div>
    </div>

    {{-- Summary KPIs --}}
    <div class="row">
        <div class="col-sm-3"><div class="info-box"><span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span>
            <div class="info-box-content"><span class="info-box-text">Total Parcels</span>
            <span class="info-box-number">{{ $summary['total'] }}</span></div></div></div>
        <div class="col-sm-3"><div class="info-box"><span class="info-box-icon bg-yellow"><i class="fa fa-balance-scale"></i></span>
            <div class="info-box-content"><span class="info-box-text">Total Weight</span>
            <span class="info-box-number">{{ number_format($summary['weight'], 1) }} kg</span></div></div></div>
        <div class="col-sm-3"><div class="info-box"><span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
            <div class="info-box-content"><span class="info-box-text">Revenue (Paid)</span>
            <span class="info-box-number">KES {{ number_format($summary['revenue'], 0) }}</span></div></div></div>
        <div class="col-sm-3"><div class="info-box"><span class="info-box-icon bg-red"><i class="fa fa-clock-o"></i></span>
            <div class="info-box-content"><span class="info-box-text">COD / Pending</span>
            <span class="info-box-number">{{ $summary['cod'] }} / {{ $summary['pending_payment'] }}</span></div></div></div>
    </div>

    {{-- Manifest Table --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                Manifest — {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
            </h3>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-striped" id="manifest_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Waybill</th>
                        <th>Sender</th>
                        <th>Recipient</th>
                        <th>Route</th>
                        <th>Weight</th>
                        <th>Charge</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th class="print-hide">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parcels as $i => $parcel)
                    @php $colors = ['booked'=>'primary','in_transit'=>'warning','arrived'=>'info','collected'=>'success','failed'=>'danger']; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $parcel->waybill_number }}</strong></td>
                        <td>{{ $parcel->sender_name }}<br><small class="text-muted">{{ $parcel->sender_phone }}</small></td>
                        <td>{{ $parcel->recipient_name }}<br><small class="text-muted">{{ $parcel->recipient_phone }}</small></td>
                        <td>
                            <small>{{ $parcel->originStation?->town ?? '?' }}</small>
                            <i class="fa fa-arrow-right text-muted"></i>
                            <small>{{ $parcel->destinationStation?->town ?? '?' }}</small>
                        </td>
                        <td>{{ $parcel->weight_kg }} kg</td>
                        <td>KES {{ number_format($parcel->charge_amount, 0) }}</td>
                        <td>
                            <span class="label {{ $parcel->payment_status === 'paid' ? 'label-success' : 'label-warning' }}">
                                {{ strtoupper($parcel->payment_method) }}
                                @if($parcel->payment_status === 'paid') ✓ @endif
                            </span>
                        </td>
                        <td>
                            <span class="label label-{{ $colors[$parcel->status] ?? 'default' }}">
                                {{ ucfirst(str_replace('_', ' ', $parcel->status)) }}
                            </span>
                        </td>
                        <td class="print-hide">
                            <a href="{{ route('parcel.show', $parcel->id) }}" class="btn btn-default btn-xs"><i class="fa fa-eye"></i></a>
                            <a href="{{ route('parcel.receipt', $parcel->id) }}" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-print"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted" style="padding:30px;">
                        No parcels on {{ $date }}.
                    </td></tr>
                    @endforelse
                </tbody>
                @if($parcels->isNotEmpty())
                <tfoot>
                    <tr class="bg-gray">
                        <th colspan="5" class="text-right">TOTALS:</th>
                        <th>{{ number_format($summary['weight'], 1) }} kg</th>
                        <th>KES {{ number_format($parcels->sum('charge_amount'), 0) }}</th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</section>

<style>
@media print {
    .content-header, .box-tools, .print-hide, nav, .sidebar, .main-header { display:none !important; }
    .content-wrapper { margin:0 !important; }
    .box { box-shadow: none !important; border: 1px solid #ddd !important; }
    table { font-size: 11px !important; }
}
</style>
@endsection
