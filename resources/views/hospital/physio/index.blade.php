@extends('layouts.app')
@section('title', 'Physiotherapy')

@section('content')
<section class="content-header">
    <h1>Physiotherapy Management</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Treatment Plans</h3>
            <div class="box-tools">
                <a href="{{ action([\App\Http\Controllers\Hospital\PhysiotherapyController::class, 'createPlan']) }}" class="btn btn-block btn-primary">
                    <i class="fa fa-plus"></i> New Plan
                </a>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Diagnosis</th>
                        <th>Sessions</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $plan)
                    <tr>
                        <td>{{ $plan->patient->name }}</td>
                        <td>{{ $plan->diagnosis }}</td>
                        <td>{{ $plan->sessions->count() }} / {{ $plan->total_sessions_planned }}</td>
                        <td>{{ ucfirst($plan->status) }}</td>
                        <td>
                            <a href="{{ action([\App\Http\Controllers\Hospital\PhysiotherapyController::class, 'addSession'], [$plan->id]) }}" class="btn btn-xs btn-success">
                                <i class="fa fa-plus"></i> Add Session
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
