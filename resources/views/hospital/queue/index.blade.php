@extends('layouts.app')
@section('title', 'Hospital Queue' . ($active_location ? ' — ' . ucfirst($active_location) : ''))

@section('content')
<section class="content-header">
    <h1>
        @if($active_location)
            <i class="fa {{ ['triage'=>'fa-thermometer-half','consultation'=>'fa-stethoscope','laboratory'=>'fa-flask','pharmacy'=>'fa-medkit','billing'=>'fa-money'][$active_location] ?? 'fa-list' }}"></i>
            {{ ucfirst($active_location) }} Queue
        @else
            Outpatient Queue
        @endif
        <small>Live patient list</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="{{ route('hospital.flow') }}">Patient Flow</a></li>
        <li class="active">{{ $active_location ? ucfirst($active_location) : 'All Queue' }}</li>
    </ol>
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
                <form method="POST" action="{{ route('hospital.queue.addToQueue') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Patient <span class="text-red">*</span></label>
                            <select name="patient_id" class="form-control select2" required style="width:100%;">
                                <option value="">-- Search patient --</option>
                                @foreach(\App\Contact::where('business_id', request()->session()->get('user.business_id'))->where('type','customer')->orderBy('name')->get() as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->name }}{{ $patient->mobile ? ' ('.$patient->mobile.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Appointment ID <small class="text-muted">(optional – leave blank for walk-in)</small></label>
                            <input type="number" name="appointment_id" class="form-control" placeholder="Leave blank if walk-in">
                        </div>
                        <div class="alert alert-info" style="margin:0;">
                            <i class="fa fa-info-circle"></i> New patient? <a href="{{ route('hospital.patients.create') }}" target="_blank">Register them first</a>
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

    {{-- Stage Filter Tabs --}}
    <div class="box box-default" style="margin-bottom:0;">
        <div class="box-body" style="padding:10px 15px;">
            <ul class="nav nav-pills" style="flex-wrap:wrap; display:flex; gap:4px;">
                <li class="{{ !$active_location ? 'active' : '' }}">
                    <a href="{{ route('hospital.queue.index') }}">
                        All &nbsp;<span class="badge" style="background:#777;">{{ $stage_counts->sum() }}</span>
                    </a>
                </li>
                @foreach([
                    'triage'       => ['bg-yellow', 'fa-thermometer-half'],
                    'consultation' => ['bg-blue',   'fa-stethoscope'],
                    'laboratory'   => ['bg-purple',  'fa-flask'],
                    'pharmacy'     => ['bg-green',   'fa-medkit'],
                    'billing'      => ['bg-red',     'fa-money'],
                ] as $stage => [$color, $icon])
                <li class="{{ $active_location == $stage ? 'active' : '' }}">
                    <a href="{{ route('hospital.queue.index', ['location' => $stage]) }}">
                        <i class="fa {{ $icon }}"></i> {{ ucfirst($stage) }}
                        @if($stage_counts->get($stage, 0) > 0)
                            <span class="badge" style="background:#e74c3c;">{{ $stage_counts->get($stage) }}</span>
                        @endif
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="box box-primary" style="border-top:0; border-radius:0 0 4px 4px;">
        <div class="box-header">
            <h3 class="box-title">
                {{ $active_location ? ucfirst($active_location).' Queue' : 'All Active Patients' }}
                <small>({{ $queues->count() }})</small>
            </h3>
            <div class="box-tools pull-right">
                <a href="{{ route('hospital.flow') }}" class="btn btn-default btn-sm">
                    <i class="fa fa-th-large"></i> Flow Board
                </a>
                &nbsp;
                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#addToQueueModal">
                    <i class="fa fa-plus"></i> Add Patient
                </button>
                &nbsp;
                <a href="{{ route('hospital.queue.liveDisplay') }}" target="_blank" class="btn btn-info btn-sm">
                    <i class="fa fa-television"></i> TV Display
                </a>
            </div>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Patient</th>
                        @if(!$active_location)<th>Stage</th>@endif
                        <th>Status</th>
                        <th>Wait</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($queues->isEmpty())
                        <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">
                            <i class="fa fa-inbox fa-3x"></i><br><br>
                            @if($active_location)
                                No patients currently in <strong>{{ ucfirst($active_location) }}</strong>.
                            @else
                                Queue is empty. Click <strong>Add Patient</strong> above to get started.
                            @endif
                        </td></tr>
                    @endif

                    @foreach($queues as $q)
                        <tr>
                            <td><span class="label label-primary" style="font-size:14px;">{{ $q->token_number }}</span></td>
                            <td>
                                <strong>{{ $q->patient->name }}</strong>
                                @if($q->patient->mobile)
                                    <br><small class="text-muted"><i class="fa fa-phone"></i> {{ $q->patient->mobile }}</small>
                                @endif
                            </td>
                            @if(!$active_location)
                            <td>
                                @php $loc_colors = ['triage'=>'bg-yellow','consultation'=>'bg-blue','laboratory'=>'bg-purple','pharmacy'=>'bg-green','billing'=>'bg-red']; @endphp
                                <span class="label {{ $loc_colors[$q->current_location] ?? 'bg-navy' }}">{{ ucfirst($q->current_location) }}</span>
                            </td>
                            @endif
                            <td>
                                <span class="label {{ $q->status == 'serving' ? 'bg-green' : 'bg-orange' }}">
                                    {{ ucfirst($q->status) }}
                                </span>
                            </td>
                            <td>{{ $q->created_at->diffForHumans(null, true) }}</td>
                            <td>
                                {{-- Primary action for current stage --}}
                                @if($q->current_location == 'triage')
                                    <a href="{{ route('hospital.triage', $q->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa fa-thermometer-half"></i> Start Triage
                                    </a>
                                @elseif($q->current_location == 'consultation')
                                    <a href="{{ route('hospital.consultWalkIn', $q->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-stethoscope"></i> Consult
                                    </a>
                                @elseif($q->current_location == 'laboratory')
                                    <a href="{{ route('hospital.lab.index') }}" class="btn btn-purple btn-sm">
                                        <i class="fa fa-flask"></i> Lab
                                    </a>
                                @elseif($q->current_location == 'pharmacy')
                                    <a href="{{ route('hospital.pharmacy.dispense', $q->patient_id) }}" class="btn btn-success btn-sm">
                                        <i class="fa fa-medkit"></i> Dispense
                                    </a>
                                @elseif($q->current_location == 'billing')
                                    <a href="{{ route('hospital.billing.patientBill', $q->patient_id) }}" class="btn btn-danger btn-sm">
                                        <i class="fa fa-money"></i> Bill
                                    </a>
                                @endif

                                {{-- More options dropdown --}}
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        @if($q->status == 'waiting')
                                            <li><a href="{{ route('hospital.queue.updateStatus', ['queue_id' => $q->id, 'status' => 'serving']) }}"><i class="fa fa-play text-green"></i> Start Serving</a></li>
                                            <li class="divider"></li>
                                        @endif
                                        <li class="dropdown-header">Move to:</li>
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
                                               onclick="return confirm('Mark {{ addslashes($q->patient->name) }} as completed?')">
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
