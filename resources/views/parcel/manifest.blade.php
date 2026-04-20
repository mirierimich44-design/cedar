@extends('layouts.app')
@section('title', 'Parcel Manifest')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Parcel Manifest</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-filter"></i> Filter Manifest</h3></div>
        <div class="box-body">
            {!! Form::open(['route' => 'parcel-manifest', 'method' => 'GET', 'class' => 'form-inline']) !!}
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Date</label>
                        {!! Form::date('date', request('date', today()->format('Y-m-d')), ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Town</label>
                        {!! Form::select('from_town', array_combine($kenyan_towns, $kenyan_towns), request('from_town'), ['class' => 'form-control select2', 'placeholder' => 'All Origins', 'style' => 'width:100%']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Town</label>
                        {!! Form::select('to_town', array_combine($kenyan_towns, $kenyan_towns), request('to_town'), ['class' => 'form-control select2', 'placeholder' => 'All Destinations', 'style' => 'width:100%']) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Status</label>
                        {!! Form::select('status', $status_list, request('status'), ['class' => 'form-control select2', 'placeholder' => 'All', 'style' => 'width:100%']) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Generate</button>
                        @if($parcels->count())
                        <button type="button" class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
                        @endif
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    @if($parcels->count())
    <div class="box box-default" id="manifest_print_area">
        <div class="box-header with-border">
            <h3 class="box-title">
                Manifest — {{ request('from_town') ?: 'All Origins' }} → {{ request('to_town') ?: 'All Destinations' }}
                &nbsp;|&nbsp; Date: {{ request('date', today()->format('Y-m-d')) }}
                &nbsp;|&nbsp; <strong>{{ $parcels->count() }} parcels</strong>
                &nbsp;|&nbsp; Total: KES {{ number_format($parcels->sum('total_amount'), 2) }}
                &nbsp;|&nbsp; Total Weight: {{ $parcels->sum('weight_kg') }} kg
            </h3>
        </div>
        <div class="box-body no-padding">
            <table class="table table-bordered table-striped table-condensed">
                <thead class="bg-light-blue">
                    <tr>
                        <th>#</th>
                        <th>Waybill</th>
                        <th>Sender</th>
                        <th>Sender Phone</th>
                        <th>Receiver</th>
                        <th>Receiver Phone</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Type</th>
                        <th>Weight</th>
                        <th>Pcs</th>
                        <th>Amount</th>
                        <th>Pay By</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parcels as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $p->waybill_number }}</strong></td>
                        <td>{{ $p->sender_name }}</td>
                        <td>{{ $p->sender_phone }}</td>
                        <td>{{ $p->receiver_name }}</td>
                        <td>{{ $p->receiver_phone }}</td>
                        <td>{{ $p->from_town }}</td>
                        <td>{{ $p->to_town }}</td>
                        <td>{{ ucfirst($p->parcel_type) }}</td>
                        <td>{{ $p->weight_kg }} kg</td>
                        <td>{{ $p->pieces }}</td>
                        <td>{{ number_format($p->total_amount, 2) }}</td>
                        <td>{{ ucfirst($p->payment_by) }}</td>
                        <td>{!! $p->status_badge !!}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-light-blue">
                        <td colspan="9"><strong>TOTALS</strong></td>
                        <td><strong>{{ $parcels->sum('weight_kg') }} kg</strong></td>
                        <td><strong>{{ $parcels->sum('pieces') }}</strong></td>
                        <td><strong>{{ number_format($parcels->sum('total_amount'), 2) }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="box-footer">
            <div class="row">
                <div class="col-md-4">
                    <p>Prepared by: _________________________</p>
                    <p>Signature: _________________________</p>
                    <p>Date: _________________________</p>
                </div>
                <div class="col-md-4">
                    <p>Driver / Transporter: _________________________</p>
                    <p>Vehicle Reg: _________________________</p>
                    <p>Signature: _________________________</p>
                </div>
                <div class="col-md-4">
                    <p>Receiving Depot Staff: _________________________</p>
                    <p>Signature: _________________________</p>
                    <p>Date: _________________________</p>
                </div>
            </div>
        </div>
    </div>
    @elseif(request()->has('date'))
    <div class="alert alert-info">No parcels found for the selected criteria.</div>
    @endif
</section>

@section('css')
<style>
@media print {
    .content-header, .box-header .box-tools, form, .sidebar-wrapper, nav { display: none !important; }
    body { margin: 5mm; }
}
</style>
@endsection
@endsection
