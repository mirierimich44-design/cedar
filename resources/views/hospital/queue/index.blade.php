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
                <a href="{{ route('hospital.flow') }}" class="btn btn-default btn-sm">
                    <i class="fa fa-sitemap"></i> Patient Flow Board
                </a>
                &nbsp;
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
                            <td>
                                <strong>{{ $q->patient->name }}</strong>
                                <br><small class="text-muted">{{ $q->patient->mobile ?? '' }}</small>
                            </td>
                            <td>
                                @php
                                    $loc_colors = ['triage'=>'bg-yellow','consultation'=>'bg-blue','laboratory'=>'bg-purple','pharmacy'=>'bg-green','billing'=>'bg-red'];
                                    $loc_color = $loc_colors[$q->current_location] ?? 'bg-navy';
                                @endphp
                                <span class="label {{ $loc_color }}">{{ ucfirst($q->current_location) }}</span>
                            </td>
                            <td>
                                <span class="label {{ $q->status == 'serving' ? 'bg-green' : 'bg-orange' }}">
                                    {{ ucfirst($q->status) }}
                                </span>
                            </td>
                            <td>{{ $q->created_at->diffForHumans(null, true) }}</td>
                            <td>
                                {{-- Smart next-step button based on location --}}
                                @if($q->current_location == 'triage')
                                    <a href="{{ route('hospital.triage', $q->id) }}" class="btn btn-warning btn-xs">
                                        <i class="fa fa-thermometer-half"></i> Triage
                                    </a>
                                @elseif($q->current_location == 'consultation')
                                    <a href="{{ route('hospital.consultWalkIn', $q->id) }}" class="btn btn-primary btn-xs">
                                        <i class="fa fa-stethoscope"></i> Consult
                                    </a>
                                @elseif($q->current_location == 'pharmacy')
                                    <a href="{{ route('hospital.pharmacy.dispense', $q->patient_id) }}" class="btn btn-success btn-xs">
                                        <i class="fa fa-medkit"></i> Dispense
                                    </a>
                                @elseif($q->current_location == 'billing')
                                    <a href="{{ route('hospital.billing.patientBill', $q->patient_id) }}" class="btn btn-danger btn-xs">
                                        <i class="fa fa-money"></i> Bill
                                    </a>
                                @endif
                                &nbsp;
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        @if($q->status == 'waiting')
                                            <li><a href="{{ route('hospital.queue.updateStatus', ['queue_id' => $q->id, 'status' => 'serving']) }}"><i class="fa fa-play text-green"></i> Start Serving</a></li>
                                        @endif
                                        <li class="dropdown-header">Move to Stage:</li>
                                        @foreach(['triage','consultation','laboratory','pharmacy','billing'] as $dest)
                                            @if($dest != $q->current_location)
                                            <li><a href="{{ route('hospital.queue.movePatient', ['queue_id' => $q->id, 'next_location' => $dest]) }}">→ {{ ucfirst($dest) }}</a></li>
                                            @endif
                                        @endforeach
                                        <li class="divider"></li>
                                        <li><a href="{{ route('hospital.patient.timeline', $q->patient_id) }}"><i class="fa fa-history"></i> Patient History</a></li>
                                        <li><a href="{{ route('hospital.dental.index', $q->patient_id) }}"><i class="fa fa-tooth"></i> Dental Charting</a></li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="{{ route('hospital.queue.updateStatus', ['queue_id' => $q->id, 'status' => 'completed']) }}"
                                               onclick="return confirm('Mark {{ $q->patient->name }} as completed?')">
                                               <i class="fa fa-check text-green"></i> Mark Completed
                                           </a>
                                        </li>
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
