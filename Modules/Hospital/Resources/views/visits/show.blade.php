@extends('layouts.app')
@section('title', 'Visit — ' . $visit->visit_no)

@section('content')
<section class="content-header">
    <h1><i class="fa fa-clipboard"></i> Visit: {{ $visit->visit_no }}
        <small>{{ $visit->patient->full_name ?? '' }}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('hospital.visits.index') }}">Today's Visits</a></li>
        @if($visit->patient)
        <li><a href="{{ route('hospital.patients.show', $visit->patient->id) }}">{{ $visit->patient->full_name }}</a></li>
        @endif
        <li class="active">{{ $visit->visit_no }}</li>
    </ol>
</section>

<section class="content">

    {{-- Patient Journey Stepper --}}
    @php
        $pipeline = ['triage','consultation','lab','pharmacy','discharged'];
        $currentStatus = $visit->status;
        $terminalStates = ['admitted','deceased'];
        $stageLabels = [
            'triage'       => 'Triage',
            'consultation' => 'Consultation',
            'lab'          => 'Lab',
            'pharmacy'     => 'Pharmacy',
            'discharged'   => 'Discharged',
        ];
        $stageIcons = [
            'triage'       => 'fa-stethoscope',
            'consultation' => 'fa-user-md',
            'lab'          => 'fa-flask',
            'pharmacy'     => 'fa-pills',
            'discharged'   => 'fa-check-circle',
        ];
        $currentIndex = array_search($currentStatus, $pipeline);
        if ($currentIndex === false) $currentIndex = -1;
    @endphp

    {{-- Terminal state banner --}}
    @if($visit->status === 'admitted')
    <div class="alert alert-dark" style="background:#343a40;color:#fff;border-radius:6px;margin-bottom:15px;">
        <i class="fa fa-bed fa-fw"></i> <strong>Patient Admitted</strong>
        @if($visit->ward) — Ward: <strong>{{ $visit->ward }}</strong>@endif
        @if($visit->bed_number) | Bed: <strong>{{ $visit->bed_number }}</strong>@endif
        @if($visit->admission_date) | Since: {{ \Carbon\Carbon::parse($visit->admission_date)->format('d M Y H:i') }}@endif
    </div>
    @elseif($visit->status === 'deceased')
    <div class="alert alert-danger" style="border-radius:6px;margin-bottom:15px;">
        <i class="fa fa-times-circle fa-fw"></i> <strong>Patient Deceased</strong>
    </div>
    @else
    {{-- Stepper --}}
    <div class="box box-default" style="margin-bottom:15px;">
        <div class="box-body" style="padding:20px 10px 10px;">
            <div style="display:flex;align-items:center;justify-content:space-between;position:relative;padding:0 20px;">
                {{-- Connector line --}}
                <div style="position:absolute;top:20px;left:40px;right:40px;height:3px;background:#e9ecef;z-index:0;"></div>
                @if($currentIndex > 0)
                <div style="position:absolute;top:20px;left:40px;height:3px;background:#28a745;z-index:1;width:{{ $currentIndex * 25 }}%;"></div>
                @endif

                @foreach($pipeline as $i => $stage)
                @php
                    $done    = $i < $currentIndex;
                    $active  = $i === $currentIndex;
                    $pending = $i > $currentIndex;
                    $color   = $done ? '#28a745' : ($active ? '#007bff' : '#dee2e6');
                    $textColor = ($done || $active) ? '#212529' : '#adb5bd';
                @endphp
                <div style="display:flex;flex-direction:column;align-items:center;z-index:2;flex:1;">
                    <div style="width:40px;height:40px;border-radius:50%;background:{{ $color }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;border:3px solid {{ $active ? '#007bff' : ($done ? '#28a745' : '#dee2e6') }};">
                        @if($done)
                            <i class="fa fa-check"></i>
                        @else
                            <i class="fa {{ $stageIcons[$stage] }}"></i>
                        @endif
                    </div>
                    <div style="margin-top:6px;font-size:12px;font-weight:{{ $active ? '700' : '400' }};color:{{ $textColor }};text-align:center;">
                        {{ $stageLabels[$stage] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="row">

        {{-- Left: Patient + Vitals --}}
        <div class="col-md-4">

            {{-- Patient Info --}}
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-user"></i> Patient</h3>
                    @if($visit->patient)
                    <div class="box-tools">
                        <a href="{{ route('hospital.patients.show', $visit->patient->id) }}" class="btn btn-xs btn-default">
                            <i class="fa fa-external-link"></i> Profile
                        </a>
                    </div>
                    @endif
                </div>
                <div class="box-body" style="padding:0;">
                    @if($visit->patient)
                    <table class="table table-condensed" style="margin:0;">
                        <tr><th style="width:45%;">Patient No.</th><td><strong>{{ $visit->patient->patient_no }}</strong></td></tr>
                        <tr><th>Name</th><td>{{ $visit->patient->full_name }}</td></tr>
                        <tr><th>Gender</th><td>{{ $visit->patient->gender }}</td></tr>
                        <tr><th>Blood Group</th><td>
                            @if($visit->patient->blood_group)
                                <span class="label label-danger">{{ $visit->patient->blood_group }}</span>
                            @else -
                            @endif
                        </td></tr>
                        <tr><th>Allergies</th><td><span class="text-danger">{{ $visit->patient->allergies ?: 'None' }}</span></td></tr>
                    </table>
                    @endif
                </div>
            </div>

            {{-- Vital Signs --}}
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-heartbeat"></i> Vital Signs</h3>
                    <div class="box-tools">
                        <button class="btn btn-xs btn-default" data-toggle="modal" data-target="#triageModal">
                            <i class="fa fa-edit"></i> Update
                        </button>
                    </div>
                </div>
                <div class="box-body" style="padding:0;">
                    <table class="table table-condensed" style="margin:0;">
                        <tr>
                            <th style="width:55%;">Triage Category</th>
                            <td>
                                @php
                                $tc = ['green'=>'success','yellow'=>'warning','orange'=>'warning','red'=>'danger','black'=>'default'];
                                $cls = $tc[$visit->triage_category] ?? 'default';
                                @endphp
                                <span class="label label-{{ $cls }}">{{ ucfirst($visit->triage_category ?? 'N/A') }}</span>
                            </td>
                        </tr>
                        <tr><th>Blood Pressure</th><td>{{ $visit->bp_systolic ? $visit->bp_systolic.'/'.$visit->bp_diastolic.' mmHg' : '-' }}</td></tr>
                        <tr><th>Temperature</th><td>{{ $visit->temperature ? $visit->temperature.' °C' : '-' }}</td></tr>
                        <tr><th>Pulse Rate</th><td>{{ $visit->pulse_rate ? $visit->pulse_rate.' bpm' : '-' }}</td></tr>
                        <tr><th>Respiratory Rate</th><td>{{ $visit->respiratory_rate ? $visit->respiratory_rate.' /min' : '-' }}</td></tr>
                        <tr><th>O₂ Saturation</th><td>{{ $visit->oxygen_saturation ? $visit->oxygen_saturation.' %' : '-' }}</td></tr>
                        <tr><th>Weight</th><td>{{ $visit->weight_kg ? $visit->weight_kg.' kg' : '-' }}</td></tr>
                        <tr><th>Height</th><td>{{ $visit->height_cm ? $visit->height_cm.' cm' : '-' }}</td></tr>
                    </table>
                    @if($visit->triage_notes)
                    <div style="padding:8px 12px;background:#fffbe6;border-top:1px solid #f0f0f0;">
                        <small class="text-muted">Notes:</small><br>
                        <small>{{ $visit->triage_notes }}</small>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Right: Consultation + Lab + Actions --}}
        <div class="col-md-8">

            {{-- Visit Info + Complaint --}}
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-info-circle"></i> Visit Details</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Visit No:</strong><br>{{ $visit->visit_no }}<br><br>
                            <strong>Type:</strong><br>{{ ucfirst($visit->visit_type) }}<br><br>
                            <strong>Doctor:</strong><br>{{ $visit->assigned_doctor ?: '-' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Date &amp; Time:</strong><br>{{ \Carbon\Carbon::parse($visit->visited_at)->format('d M Y, H:i') }}<br><br>
                            <strong>Status:</strong><br>
                            @php
                            $sc = ['triage'=>'info','consultation'=>'primary','lab'=>'warning','pharmacy'=>'secondary','discharged'=>'success','admitted'=>'dark','deceased'=>'danger'];
                            $scc = $sc[$visit->status] ?? 'secondary';
                            @endphp
                            <span class="label label-{{ $scc }}">{{ ucfirst($visit->status) }}</span>
                        </div>
                        <div class="col-md-4">
                            @if($visit->discharge_date)
                            <strong>Discharged:</strong><br>{{ \Carbon\Carbon::parse($visit->discharge_date)->format('d M Y, H:i') }}
                            @endif
                        </div>
                    </div>
                    @if($visit->chief_complaint)
                    <hr style="margin:10px 0;">
                    <strong>Chief Complaint:</strong>
                    <p style="margin-top:4px;">{{ $visit->chief_complaint }}</p>
                    @endif
                </div>
            </div>

            {{-- Lab Orders --}}
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-flask"></i> Lab Orders</h3>
                    <div class="box-tools">
                        <button class="btn btn-xs btn-info" data-toggle="modal" data-target="#labOrderModal">
                            <i class="fa fa-plus"></i> Order Test
                        </button>
                    </div>
                </div>
                <div class="box-body no-padding">
                    @if($visit->labOrders->count())
                    <table class="table table-condensed table-striped" style="margin:0;">
                        <thead>
                            <tr>
                                <th>Test</th>
                                <th>Ordered By</th>
                                <th>Status</th>
                                <th>Result</th>
                                <th>Ref Range</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visit->labOrders as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->test_name }}</strong>
                                    @if($order->test_code) <small class="text-muted">({{ $order->test_code }})</small>@endif
                                </td>
                                <td>{{ $order->ordered_by }}</td>
                                <td>
                                    @php $lmap=['pending'=>'warning','sample_collected'=>'info','processing'=>'primary','resulted'=>'success','cancelled'=>'danger']; @endphp
                                    <span class="label label-{{ $lmap[$order->status] ?? 'default' }}">
                                        {{ ucwords(str_replace('_',' ',$order->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->result_value)
                                        {{ $order->result_value }} {{ $order->result_unit }}
                                        @if($order->result_notes)<br><small class="text-muted">{{ $order->result_notes }}</small>@endif
                                    @else
                                        <span class="text-muted">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $order->reference_range ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center text-muted" style="padding:20px;">No lab orders</div>
                    @endif
                </div>
            </div>

            {{-- Advance Status --}}
            @if(!in_array($visit->status, ['discharged','deceased','admitted']))
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-arrow-circle-right"></i> Advance Patient</h3>
                </div>
                <div class="box-body">
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        @php
                        $nextStages = [];
                        if ($visit->status === 'triage')       $nextStages = ['consultation'];
                        if ($visit->status === 'consultation') $nextStages = ['lab','pharmacy','discharged','admitted'];
                        if ($visit->status === 'lab')          $nextStages = ['pharmacy','consultation','discharged'];
                        if ($visit->status === 'pharmacy')     $nextStages = ['discharged'];
                        $stageBtn = ['consultation'=>'btn-primary','lab'=>'btn-warning','pharmacy'=>'btn-default','discharged'=>'btn-success','admitted'=>'btn-dark'];
                        @endphp
                        @foreach($nextStages as $ns)
                        <button class="btn {{ $stageBtn[$ns] ?? 'btn-default' }} btn-advance-status" data-status="{{ $ns }}">
                            <i class="fa {{ $stageIcons[$ns] ?? 'fa-arrow-right' }}"></i>
                            {{ $stageLabels[$ns] ?? ucfirst($ns) }}
                        </button>
                        @endforeach
                        <button class="btn btn-danger btn-advance-status" data-status="deceased">
                            <i class="fa fa-times-circle"></i> Mark Deceased
                        </button>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</section>

{{-- Update Triage Modal --}}
<div class="modal fade" id="triageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-heartbeat"></i> Update Vital Signs</h4>
            </div>
            <div class="modal-body">
                <form id="triage_form">
                    @csrf
                    <div class="form-group">
                        <label>Triage Category</label>
                        <select name="triage_category" class="form-control">
                            <option value="">-- No change --</option>
                            <option value="green" {{ $visit->triage_category==='green'?'selected':'' }}>Green (Minor)</option>
                            <option value="yellow" {{ $visit->triage_category==='yellow'?'selected':'' }}>Yellow (Moderate)</option>
                            <option value="orange" {{ $visit->triage_category==='orange'?'selected':'' }}>Orange (Urgent)</option>
                            <option value="red" {{ $visit->triage_category==='red'?'selected':'' }}>Red (Critical)</option>
                            <option value="black" {{ $visit->triage_category==='black'?'selected':'' }}>Black (Deceased)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>BP Systolic</label>
                                <input type="number" name="bp_systolic" class="form-control" value="{{ $visit->bp_systolic }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>BP Diastolic</label>
                                <input type="number" name="bp_diastolic" class="form-control" value="{{ $visit->bp_diastolic }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Temperature (°C)</label>
                                <input type="number" name="temperature" step="0.1" class="form-control" value="{{ $visit->temperature }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pulse Rate (bpm)</label>
                                <input type="number" name="pulse_rate" class="form-control" value="{{ $visit->pulse_rate }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Respiratory Rate (/min)</label>
                                <input type="number" name="respiratory_rate" class="form-control" value="{{ $visit->respiratory_rate }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>O₂ Saturation (%)</label>
                                <input type="number" name="oxygen_saturation" class="form-control" value="{{ $visit->oxygen_saturation }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Weight (kg)</label>
                                <input type="number" name="weight_kg" step="0.1" class="form-control" value="{{ $visit->weight_kg }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Height (cm)</label>
                                <input type="number" name="height_cm" step="0.1" class="form-control" value="{{ $visit->height_cm }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Triage Notes</label>
                        <textarea name="triage_notes" class="form-control" rows="2">{{ $visit->triage_notes }}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="btn-save-triage">
                    <i class="fa fa-save"></i> Save Vitals
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Lab Order Modal --}}
<div class="modal fade" id="labOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-flask"></i> Order Lab Test</h4>
            </div>
            <div class="modal-body">
                <form id="lab_order_form">
                    @csrf
                    <input type="hidden" name="visit_id" value="{{ $visit->id }}">
                    <div class="form-group">
                        <label>Test Name <span class="text-danger">*</span></label>
                        <input type="text" name="test_name" class="form-control" required placeholder="e.g. Full Blood Count">
                    </div>
                    <div class="form-group">
                        <label>Test Code</label>
                        <input type="text" name="test_code" class="form-control" placeholder="e.g. FBC">
                    </div>
                    <div class="form-group">
                        <label>Ordered By <span class="text-danger">*</span></label>
                        <input type="text" name="ordered_by" class="form-control" required value="{{ $visit->assigned_doctor }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" id="btn-save-lab-order">
                    <i class="fa fa-flask"></i> Order Test
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Admit Modal --}}
<div class="modal fade" id="admitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-bed"></i> Admit Patient</h4>
            </div>
            <div class="modal-body">
                <form id="admit_form">
                    @csrf
                    <input type="hidden" name="status" value="admitted">
                    <div class="form-group">
                        <label>Ward</label>
                        <input type="text" name="ward" class="form-control" placeholder="e.g. General Ward A">
                    </div>
                    <div class="form-group">
                        <label>Bed Number</label>
                        <input type="text" name="bed_number" class="form-control" placeholder="e.g. B-12">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-dark" id="btn-confirm-admit">
                    <i class="fa fa-bed"></i> Confirm Admission
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(function () {
    var visitId = {{ $visit->id }};

    // Advance status buttons
    $(document).on('click', '.btn-advance-status', function () {
        var status = $(this).data('status');
        if (status === 'admitted') {
            $('#admitModal').modal('show');
            return;
        }
        if (!confirm('Move patient to: ' + status + '?')) return;
        $.ajax({
            url: '/hospital/visits/' + visitId + '/status',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', status: status },
            success: function (r) {
                if (r.success) { toastr.success(r.msg); setTimeout(function(){ location.reload(); }, 800); }
                else toastr.error(r.msg);
            },
            error: function () { toastr.error('An error occurred.'); }
        });
    });

    // Admit confirm
    $('#btn-confirm-admit').on('click', function () {
        $.ajax({
            url: '/hospital/visits/' + visitId + '/status',
            method: 'POST',
            data: $('#admit_form').serialize(),
            success: function (r) {
                if (r.success) {
                    $('#admitModal').modal('hide');
                    toastr.success(r.msg);
                    setTimeout(function(){ location.reload(); }, 800);
                } else toastr.error(r.msg);
            },
            error: function () { toastr.error('An error occurred.'); }
        });
    });

    // Save triage
    $('#btn-save-triage').on('click', function () {
        $.ajax({
            url: '/hospital/visits/' + visitId + '/triage',
            method: 'POST',
            data: $('#triage_form').serialize(),
            success: function (r) {
                if (r.success) {
                    $('#triageModal').modal('hide');
                    toastr.success(r.msg);
                    setTimeout(function(){ location.reload(); }, 800);
                } else toastr.error(r.msg);
            },
            error: function () { toastr.error('An error occurred.'); }
        });
    });

    // Save lab order
    $('#btn-save-lab-order').on('click', function () {
        $.ajax({
            url: '{{ route('hospital.lab.store') }}', // POST /hospital/lab-orders
            method: 'POST',
            data: $('#lab_order_form').serialize(),
            success: function (r) {
                if (r.success) {
                    $('#labOrderModal').modal('hide');
                    toastr.success(r.msg);
                    setTimeout(function(){ location.reload(); }, 800);
                } else toastr.error(r.msg);
            },
            error: function (xhr) {
                var errors = xhr.responseJSON;
                if (errors && errors.errors) {
                    $.each(errors.errors, function(k, v){ toastr.error(v[0]); });
                } else toastr.error('An error occurred.');
            }
        });
    });
});
</script>
@endsection
