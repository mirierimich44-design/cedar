@extends('layouts.app')
@section('title', 'Parcel Reports')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Parcel Reports</h1>
</section>

<section class="content">
    {!! Form::open(['route' => 'parcel-reports', 'method' => 'GET', 'class' => 'form-inline tw-mb-4']) !!}
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title">Filter</h3></div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Start Date</label>
                        {!! Form::date('start_date', request('start_date', now()->startOfMonth()->format('Y-m-d')), ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>End Date</label>
                        {!! Form::date('end_date', request('end_date', today()->format('Y-m-d')), ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group"><label>&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Generate</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-sm-3">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Parcels</span>
                    <span class="info-box-number">{{ number_format($summary['total']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Revenue</span>
                    <span class="info-box-number">KES {{ number_format($summary['revenue'], 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="info-box bg-blue">
                <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Amount Collected</span>
                    <span class="info-box-number">KES {{ number_format($summary['paid'], 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Outstanding</span>
                    <span class="info-box-number">KES {{ number_format($summary['revenue'] - $summary['paid'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- By Route -->
        <div class="col-md-7">
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Revenue by Route</h3></div>
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <thead><tr><th>Route</th><th class="text-right">Parcels</th><th class="text-right">Revenue (KES)</th></tr></thead>
                        <tbody>
                            @foreach($summary['by_route'] as $row)
                            <tr>
                                <td>{{ $row->from_town }} → {{ $row->to_town }}</td>
                                <td class="text-right">{{ $row->count }}</td>
                                <td class="text-right">{{ number_format($row->revenue, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- By Status -->
        <div class="col-md-5">
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">By Status</h3></div>
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <thead><tr><th>Status</th><th class="text-right">Count</th></tr></thead>
                        <tbody>
                            @foreach(\App\Parcel::statusList() as $key => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-right">{{ $summary['by_status'][$key] ?? 0 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
