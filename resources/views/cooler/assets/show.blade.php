@extends('layouts.app')
@section('title', 'Cooler Asset — ' . $asset->asset_number)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">
        {{ $asset->asset_number }} — {{ $asset->asset_type }}
        {!! $asset->status_badge !!}
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Asset Details</h3>
                    @can('cooler.asset.update')
                    <div class="box-tools">
                        <a href="{{ route('cooler.assets.edit', $asset->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit</a>
                    </div>
                    @endcan
                </div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <tr><th>Asset Number</th><td>{{ $asset->asset_number }}</td></tr>
                        <tr><th>Asset Type</th><td>{{ $asset->asset_type }}</td></tr>
                        <tr><th>Serial Number</th><td>{{ $asset->serial_number ?? '—' }}</td></tr>
                        <tr><th>Cooler Tag</th><td>{{ $asset->cooler_tag ?? '—' }}</td></tr>
                        <tr><th>Status</th><td>{!! $asset->status_badge !!}</td></tr>
                        <tr><th>Replacement Value</th><td>KES {{ number_format($asset->replacement_value, 2) }}</td></tr>
                        <tr><th>Deployment Date</th><td>{{ $asset->deployment_date?->format('d M Y') ?? '—' }}</td></tr>
                        <tr><th>Notes</th><td>{{ $asset->notes ?? '—' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border"><h3 class="box-title">Current Dealer</h3></div>
                <div class="box-body">
                    @if($asset->currentDealer)
                        <p><strong>{{ $asset->currentDealer->outlet_name }}</strong> ({{ $asset->currentDealer->name }})</p>
                        <p>{{ $asset->currentDealer->full_address }}</p>
                        <a href="{{ route('cooler.dealers.show', $asset->currentDealer->id) }}" class="btn btn-xs btn-info">View Dealer</a>
                    @else
                        <p class="text-muted">No dealer assigned (cooler is {{ $asset->status }}).</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title">Agreement History</h3></div>
                <div class="box-body">
                    @forelse($asset->agreements as $agr)
                    <div class="tw-flex tw-justify-between tw-items-center tw-py-2 tw-border-b">
                        <div>
                            <strong>{{ $agr->dealer->outlet_name ?? '—' }}</strong>
                            — {{ $agr->agreement_date->format('d M Y') }}
                            {!! $agr->status_badge !!}
                        </div>
                        <a href="{{ route('cooler.agreements.show', $agr->id) }}" class="btn btn-xs btn-info">View</a>
                    </div>
                    @empty
                    <p class="text-muted">No agreements yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
