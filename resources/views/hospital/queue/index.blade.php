@extends('layouts.app')
@section('title', 'Hospital Queue Management')

@section('content')
<section class="content-header">
    <h1>Patient Queue Dashboard</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Live Hospital Queue</h3>
            <div class="box-tools">
                <a href="{{ action([\App\Http\Controllers\Hospital\HospitalQueueController::class, 'liveDisplay']) }}" target="_blank" class="btn btn-info btn-sm">
                    <i class="fa fa-television"></i> Open Waiting Room Display
                </a>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Patient</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Wait Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($queues as $q)
                        <tr>
                            <td><span class="label label-primary" style="font-size: 14px;">{{ $q->token_number }}</span></td>
                            <td>{{ $q->patient->name }}</td>
                            <td>
                                <span class="label bg-navy">{{ ucfirst($q->current_location) }}</span>
                            </td>
                            <td>
                                <span class="label {{ $q->status == 'serving' ? 'bg-green' : 'bg-orange' }}">
                                    {{ ucfirst($q->status) }}
                                </span>
                            </td>
                            <td>{{ $q->created_at->diffForHumans(null, true) }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                        Actions <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($q->status == 'waiting')
                                            <li><a href="{{ action([\App\Http\Controllers\Hospital\HospitalQueueController::class, 'updateStatus'], ['queue_id' => $q->id, 'status' => 'serving']) }}">Start Serving</a></li>
                                        @endif
                                        
                                        <li class="divider"></li>
                                        <li class="dropdown-header">Consultation:</li>
                                        <li><a href="{{ action([\App\Http\Controllers\Hospital\HospitalController::class, 'consultation'], [$q->appointment_id ?? 0]) }}">General Consultation</a></li>
                                        <li><a href="{{ action([\App\Http\Controllers\Hospital\DentalController::class, 'index'], [$q->patient_id]) }}">Dental Charting</a></li>
                                        
                                        <li class="divider"></li>
                                        <li class="dropdown-header">Move to:</li>
                                        <li><a href="{{ action([\App\Http\Controllers\Hospital\HospitalQueueController::class, 'movePatient'], ['queue_id' => $q->id, 'next_location' => 'consultation']) }}">Consultation</a></li>
                                        <li><a href="{{ action([\App\Http\Controllers\Hospital\HospitalQueueController::class, 'movePatient'], ['queue_id' => $q->id, 'next_location' => 'laboratory']) }}">Laboratory</a></li>
                                        <li><a href="{{ action([\App\Http\Controllers\Hospital\HospitalQueueController::class, 'movePatient'], ['queue_id' => $q->id, 'next_location' => 'pharmacy']) }}">Pharmacy</a></li>
                                        <li><a href="{{ action([\App\Http\Controllers\Hospital\HospitalQueueController::class, 'movePatient'], ['queue_id' => $q->id, 'next_location' => 'billing']) }}">Billing</a></li>
                                        <li class="divider"></li>
                                        <li><a href="{{ action([\App\Http\Controllers\Hospital\HospitalQueueController::class, 'updateStatus'], ['queue_id' => $q->id, 'status' => 'completed']) }}">Mark Completed</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
