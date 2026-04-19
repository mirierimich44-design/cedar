@extends('layouts.app')
@section('title', 'Patient Flow — Live View')

@section('content')
<section class="content-header">
    <h1>Patient Flow <small>Live hospital journey tracker</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Patient Flow</li>
    </ol>
</section>

<section class="content">

    {{-- Stats bar --}}
    <div class="row">
        <div class="col-xs-6 col-sm-3">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active Patients</span>
                    <span class="info-box-number">{{ $active->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completed Today</span>
                    <span class="info-box-number">{{ $completed_today }}</span>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Waiting</span>
                    <span class="info-box-number">{{ $active->where('status','waiting')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3">
            <div class="info-box">
                <span class="info-box-icon bg-purple"><i class="fa fa-user-md"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Being Served</span>
                    <span class="info-box-number">{{ $active->where('status','serving')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div style="margin-bottom:15px;">
        <a href="{{ route('hospital.queue.index') }}" class="btn btn-default btn-sm">
            <i class="fa fa-list"></i> Full Queue
        </a>
        &nbsp;
        <a href="{{ route('hospital.patients.index') }}" class="btn btn-default btn-sm">
            <i class="fa fa-user-plus"></i> Register New Patient
        </a>
        &nbsp;
        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#addToQueueModal">
            <i class="fa fa-plus"></i> Add to Queue
        </button>
        &nbsp;
        <a href="{{ route('hospital.queue.liveDisplay') }}" target="_blank" class="btn btn-default btn-sm">
            <i class="fa fa-television"></i> Waiting Room TV
        </a>
        &nbsp;
        <button class="btn btn-default btn-sm" onclick="location.reload()">
            <i class="fa fa-refresh"></i> Refresh
        </button>
    </div>

    {{-- Kanban Board --}}
    @php
    $stage_config = [
        'triage'       => ['label' => 'Triage',       'color' => 'bg-yellow',  'icon' => 'fa-thermometer-half', 'action_label' => 'Start Triage',    'action_route' => 'hospital.triage'],
        'consultation' => ['label' => 'Consultation',  'color' => 'bg-blue',    'icon' => 'fa-stethoscope',      'action_label' => 'Consult',          'action_route' => 'hospital.consultWalkIn'],
        'laboratory'   => ['label' => 'Laboratory',    'color' => 'bg-purple',  'icon' => 'fa-flask',            'action_label' => 'Enter Results',    'action_route' => null],
        'pharmacy'     => ['label' => 'Pharmacy',      'color' => 'bg-green',   'icon' => 'fa-medkit',           'action_label' => 'Dispense',         'action_route' => null],
        'billing'      => ['label' => 'Billing',       'color' => 'bg-red',     'icon' => 'fa-money',            'action_label' => 'Bill Patient',     'action_route' => null],
    ];
    @endphp

    <div class="row" id="flow-kanban" style="overflow-x:auto; white-space:nowrap; display:flex; gap:10px; padding-bottom:10px;">
        @foreach($stages as $stage)
        @php
            $cfg = $stage_config[$stage];
            $patients = $by_stage[$stage];
        @endphp
        <div style="display:inline-block; vertical-align:top; min-width:210px; max-width:220px; white-space:normal; flex:1;">
            {{-- Column header --}}
            <div class="box {{ str_replace('bg-','box-',str_replace('bg-','box-',$cfg['color'])) }}">
                <div class="box-header {{ $cfg['color'] }}" style="border-radius:4px 4px 0 0;">
                    <h3 class="box-title" style="color:#fff;">
                        <i class="fa {{ $cfg['icon'] }}"></i>
                        {{ $cfg['label'] }}
                        <span class="badge" style="background:rgba(255,255,255,0.3);">{{ $patients->count() }}</span>
                    </h3>
                </div>
                <div class="box-body" style="padding:8px; min-height:80px;">
                    @forelse($patients as $q)
                    <div class="callout {{ $q->status == 'serving' ? 'callout-success' : 'callout-default' }}"
                         style="padding:8px 10px; margin-bottom:8px; border-radius:4px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span class="label label-primary">{{ $q->token_number }}</span>
                            <small class="text-muted">{{ $q->created_at->diffForHumans(null, true) }}</small>
                        </div>
                        <strong style="display:block; margin-top:5px; font-size:13px;">{{ $q->patient->name }}</strong>

                        {{-- Action Button --}}
                        @if($cfg['action_route'])
                            @if($stage == 'triage')
                            <a href="{{ route($cfg['action_route'], $q->id) }}"
                               class="btn btn-warning btn-xs btn-block" style="margin-top:6px;">
                                <i class="fa fa-arrow-right"></i> {{ $cfg['action_label'] }}
                            </a>
                            @elseif($stage == 'consultation')
                            <a href="{{ route($cfg['action_route'], $q->id) }}"
                               class="btn btn-primary btn-xs btn-block" style="margin-top:6px;">
                                <i class="fa fa-stethoscope"></i> {{ $cfg['action_label'] }}
                            </a>
                            @endif
                        @else
                            {{-- Move actions for lab/pharmacy/billing --}}
                            @if($stage == 'laboratory')
                            <div class="btn-group btn-block" style="margin-top:6px;">
                                <a href="{{ route('hospital.lab.index') }}"
                                   class="btn btn-purple btn-xs btn-block">
                                    <i class="fa fa-flask"></i> Go to Lab
                                </a>
                            </div>
                            @elseif($stage == 'pharmacy')
                            <a href="{{ route('hospital.pharmacy.dispense', $q->patient_id) }}"
                               class="btn btn-success btn-xs btn-block" style="margin-top:6px;">
                                <i class="fa fa-medkit"></i> Dispense
                            </a>
                            @elseif($stage == 'billing')
                            <a href="{{ route('hospital.billing.patientBill', $q->patient_id) }}"
                               class="btn btn-danger btn-xs btn-block" style="margin-top:6px;">
                                <i class="fa fa-money"></i> Bill
                            </a>
                            @endif
                        @endif

                        {{-- Move / Complete dropdown --}}
                        <div class="btn-group btn-block" style="margin-top:4px;">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle btn-block" data-toggle="dropdown">
                                Move / Complete <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" style="min-width:auto;">
                                @foreach(['triage','consultation','laboratory','pharmacy','billing'] as $dest)
                                    @if($dest != $stage)
                                    <li>
                                        <a href="{{ route('hospital.queue.movePatient', ['queue_id' => $q->id, 'next_location' => $dest]) }}">
                                            → {{ ucfirst($dest) }}
                                        </a>
                                    </li>
                                    @endif
                                @endforeach
                                <li class="divider"></li>
                                <li>
                                    <a href="{{ route('hospital.queue.updateStatus', ['queue_id' => $q->id, 'status' => 'completed']) }}"
                                       onclick="return confirm('Mark {{ $q->patient->name }} as completed?')">
                                        <i class="fa fa-check text-green"></i> Completed / Discharged
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center" style="font-size:12px; padding:15px 0;">
                        <i class="fa fa-inbox fa-2x"></i><br>No patients
                    </p>
                    @endforelse
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Patient Flow Journey Guide --}}
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-info-circle"></i> Patient Journey</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row text-center">
                <div class="col-sm-2">
                    <div style="background:#f7f7f7; padding:15px; border-radius:6px;">
                        <i class="fa fa-user-plus fa-2x text-green"></i>
                        <p class="small" style="margin-top:5px;"><strong>1. Register Patient</strong></p>
                        <a href="{{ route('hospital.patients.create') }}" class="btn btn-success btn-xs">Register</a>
                    </div>
                </div>
                <div class="col-sm-1 text-center" style="padding-top:25px; font-size:20px; color:#aaa;">→</div>
                <div class="col-sm-2">
                    <div style="background:#f7f7f7; padding:15px; border-radius:6px;">
                        <i class="fa fa-list-ol fa-2x text-blue"></i>
                        <p class="small" style="margin-top:5px;"><strong>2. Add to Queue</strong></p>
                        <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#addToQueueModal">Add</button>
                    </div>
                </div>
                <div class="col-sm-1 text-center" style="padding-top:25px; font-size:20px; color:#aaa;">→</div>
                <div class="col-sm-2">
                    <div style="background:#fffde7; padding:15px; border-radius:6px;">
                        <i class="fa fa-thermometer-half fa-2x text-orange"></i>
                        <p class="small" style="margin-top:5px;"><strong>3. Triage</strong> (Vitals)</p>
                    </div>
                </div>
                <div class="col-sm-1 text-center" style="padding-top:25px; font-size:20px; color:#aaa;">→</div>
                <div class="col-sm-2">
                    <div style="background:#e3f2fd; padding:15px; border-radius:6px;">
                        <i class="fa fa-stethoscope fa-2x text-blue"></i>
                        <p class="small" style="margin-top:5px;"><strong>4. Doctor</strong> (Consultation)</p>
                    </div>
                </div>
            </div>
            <div class="row text-center" style="margin-top:10px;">
                <div class="col-sm-2 col-sm-offset-3">
                    <div style="background:#f3e5f5; padding:15px; border-radius:6px;">
                        <i class="fa fa-flask fa-2x text-purple"></i>
                        <p class="small" style="margin-top:5px;"><strong>5a. Lab</strong> (if ordered)</p>
                    </div>
                </div>
                <div class="col-sm-1 text-center" style="padding-top:25px; font-size:20px; color:#aaa;">→</div>
                <div class="col-sm-2">
                    <div style="background:#e8f5e9; padding:15px; border-radius:6px;">
                        <i class="fa fa-medkit fa-2x text-green"></i>
                        <p class="small" style="margin-top:5px;"><strong>5b. Pharmacy</strong> (Dispensing)</p>
                    </div>
                </div>
                <div class="col-sm-1 text-center" style="padding-top:25px; font-size:20px; color:#aaa;">→</div>
                <div class="col-sm-2">
                    <div style="background:#ffebee; padding:15px; border-radius:6px;">
                        <i class="fa fa-money fa-2x text-red"></i>
                        <p class="small" style="margin-top:5px;"><strong>6. Billing</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

{{-- Add to Queue Modal (same as queue/index.blade.php) --}}
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
                        <label>Appointment ID <small class="text-muted">(optional, leave blank for walk-in)</small></label>
                        <input type="number" name="appointment_id" class="form-control" placeholder="Leave blank if walk-in">
                    </div>
                    <div class="alert alert-info" style="margin-bottom:0;">
                        <i class="fa fa-info-circle"></i> New patient? <a href="{{ route('hospital.patients.create') }}">Register them first</a>, then add to queue.
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

@section('javascript')
<script>
    // Auto-refresh every 60 seconds
    setTimeout(function() { location.reload(); }, 60000);
</script>
@endsection
@endsection
