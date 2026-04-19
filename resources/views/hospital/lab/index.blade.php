@extends('layouts.app')
@section('title', 'Laboratory Management')

@section('content')
<section class="content-header">
    <h1>Laboratory Dashboard</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Lab Request Queue</h3>
            <div class="box-tools">
                <a href="{{ action([\App\Http\Controllers\Hospital\LabController::class, 'createTest']) }}" class="btn btn-info btn-sm">
                    <i class="fa fa-list"></i> Add Test to Catalog
                </a>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Test Name</th>
                        <th>Ordered By</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lab_requests as $req)
                        <tr>
                            <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $req->patient->name }}</td>
                            <td>{{ $req->test->name }}</td>
                            <td>{{ $req->doctor->first_name }}</td>
                            <td>
                                <span class="label {{ $req->status == 'completed' ? 'bg-green' : 'bg-orange' }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td>
                                @if($req->status != 'completed')
                                    <a href="{{ action([\App\Http\Controllers\Hospital\LabController::class, 'enterResult'], [$req->id]) }}" class="btn btn-primary btn-xs">
                                        <i class="fa fa-edit"></i> Enter Result
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
