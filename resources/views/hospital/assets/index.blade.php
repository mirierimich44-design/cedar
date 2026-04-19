@extends('layouts.app')
@section('title', 'Hospital Assets')

@section('content')
<section class="content-header">
    <h1>Medical Equipment & Assets</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Asset Inventory</h3>
            <div class="box-tools">
                <a href="{{ action([\App\Http\Controllers\Hospital\HospitalAssetController::class, 'create']) }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Register New Asset
                </a>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Asset Name</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Next Service</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $asset)
                        <tr>
                            <td><strong>{{ $asset->asset_code }}</strong></td>
                            <td>{{ $asset->name }} <br><small class="text-muted">{{ $asset->model }}</small></td>
                            <td>{{ $asset->category }}</td>
                            <td>
                                <span class="label {{ $asset->status == 'active' ? 'bg-green' : 'bg-red' }}">
                                    {{ ucfirst($asset->status) }}
                                </span>
                            </td>
                            <td>
                                @if($asset->next_service_date)
                                    <span class="{{ \Carbon\Carbon::parse($asset->next_service_date)->isPast() ? 'text-danger' : '' }}">
                                        {{ $asset->next_service_date }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ action([\App\Http\Controllers\Hospital\HospitalAssetController::class, 'addMaintenance'], [$asset->id]) }}" class="btn btn-info btn-xs">
                                    <i class="fa fa-wrench"></i> Log Maintenance
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
