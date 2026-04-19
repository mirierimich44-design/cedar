@extends('layouts.app')
@section('title', 'Mortuary Management')

@section('content')
<section class="content-header">
    <h1>Mortuary Management</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Records</h3>
            <div class="box-tools">
                <a href="{{ action([\App\Http\Controllers\Hospital\MortuaryController::class, 'create']) }}" class="btn btn-block btn-primary">
                    <i class="fa fa-plus"></i> Body Intake
                </a>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date Admitted</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Released At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($record->admitted_at)->format('d M Y, H:i') }}</td>
                        <td>{{ $record->body_name ?? ($record->patient->name ?? 'Unknown') }}</td>
                        <td>{{ $record->storage_location }}</td>
                        <td>{{ ucfirst($record->status) }}</td>
                        <td>{{ $record->released_at ? \Carbon\Carbon::parse($record->released_at)->format('d M Y, H:i') : '-' }}</td>
                        <td>
                            @if($record->status == 'admitted')
                            <a href="{{ action([\App\Http\Controllers\Hospital\MortuaryController::class, 'release'], [$record->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Release body?')">
                                <i class="fa fa-share"></i> Release
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
