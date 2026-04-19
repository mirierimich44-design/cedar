@extends('layouts.app')
@section('title', 'Hospital Queue Management')

@section('content')
<section class="content-header">
    <h1>Patient Queue Dashboard</h1>
</section>

<section class="content">

    {{-- Add Patient to Queue Modal --}}
    <div class="modal fade" id="addToQueueModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add Patient to Queue</h4>
                </div>
                <form method="POST" action="{{ action([\App\Http\Controllers\Hospital\HospitalQueueController::class, 'addToQueue']) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Patient <span class="text-red">*</span></label>
                            <select name="patient_id" class="form-control select2" required style="width:100%;">
                                <option value="">-- Search patient --</option>
                                @foreach(\App\Contact::where('business_id', request()->session()->get('user.business_id'))->where('type','customer')->orderBy('name')->get() as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->name }} {{ $patient->mobile ? '('.$patient->mobile.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Appointment ID <small class="text-muted">(optional)</small></label>
                            <input type="number" name="appointment_id" class="form-control" placeholder="Leave blank if walk-in">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Add to Queue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Live Hospital Queue</h3>
            <div class="box-tools">
                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#addToQueueModal">
                    <i class="fa fa-plus"></i> Add Patient to Queue
                </button>
                &nbsp;
                <a href="{{ action([\App\Http\Controllers\Hospital\HospitalQueueController::class, 'liveDisplay']) }}" target="_blank" class="btn btn-info btn-sm">
                    <i class="fa fa-television"></i> Waiting Room Display
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
                    @if($queues->isEmpty())
                        <tr><td colspan="6" class="text-center text-muted" style="padding:30px;">
                            <i class="fa fa-users fa-2x"></i><br>No patients in queue. Click <strong>Add Patient to Queue</strong> above to get started.
                        </td></tr>
                    @endif
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
