@extends('layouts.app')
@section('title', 'Radiography / Imaging')

@section('content')
<section class="content-header">
    <h1>Radiography & Imaging</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Imaging Requests</h3>
            <div class="box-tools">
                <a href="{{ action([\App\Http\Controllers\Hospital\RadiographyController::class, 'createTest']) }}" class="btn btn-block btn-primary">
                    <i class="fa fa-plus"></i> Add Test to Catalog
                </a>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Test</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Radiologist</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $request->patient->name }}</td>
                        <td>{{ $request->test->name }}</td>
                        <td>{{ strtoupper($request->test->type) }}</td>
                        <td>
                            <span class="label @if($request->status == 'completed') label-success @elseif($request->status == 'ordered') label-warning @else label-info @endif">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td>{{ $request->radiologist->user_full_name ?? 'Pending' }}</td>
                        <td>
                            @if($request->status != 'completed')
                            <a href="{{ action([\App\Http\Controllers\Hospital\RadiographyController::class, 'enterResult'], [$request->id]) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-edit"></i> Enter Findings
                            </a>
                            @else
                            <button class="btn btn-xs btn-info">View Report</button>
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
