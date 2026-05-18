@extends('layouts.app')
@section('title', 'Register Visit')

@section('content')
<section class="content-header">
    <h1><i class="fa fa-plus-circle"></i> Register Visit</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('hospital.visits.index') }}">Today's Visits</a></li>
        <li class="active">Register Visit</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-8">
            @component('components.widget')
                <form id="create_visit_form">
                    @csrf

                    {{-- Patient Selection --}}
                    <div class="form-group">
                        <label>Patient <span class="text-danger">*</span></label>
                        <select name="patient_id" id="patient_id" class="form-control select2" required style="width:100%;">
                            <option value="">-- Search or select patient --</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ optional($patient)->id == $p->id ? 'selected' : '' }}>
                                    {{ $p->patient_no }} — {{ $p->first_name }} {{ $p->last_name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="help-block">
                            Not registered? <a href="{{ route('hospital.patients.create') }}" target="_blank">Register new patient <i class="fa fa-external-link"></i></a>
                        </p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Visit Type <span class="text-danger">*</span></label>
                                <select name="visit_type" class="form-control" required>
                                    <option value="outpatient">Outpatient</option>
                                    <option value="inpatient">Inpatient</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assigned Doctor</label>
                                <input type="text" name="assigned_doctor" class="form-control" placeholder="Dr. Name">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Chief Complaint</label>
                        <textarea name="chief_complaint" class="form-control" rows="2" placeholder="Brief description of presenting complaint…"></textarea>
                    </div>

                    <hr>
                    <h5><i class="fa fa-heartbeat"></i> Triage & Vital Signs</h5>

                    <div class="form-group">
                        <label>Triage Category</label>
                        <div class="btn-group btn-group-triage" style="display:flex;gap:6px;flex-wrap:wrap;">
                            @foreach([
                                'green'  => ['label'=>'Green (Minor)',    'btn'=>'success'],
                                'yellow' => ['label'=>'Yellow (Moderate)','btn'=>'warning'],
                                'orange' => ['label'=>'Orange (Urgent)',  'btn'=>'warning'],
                                'red'    => ['label'=>'Red (Critical)',   'btn'=>'danger'],
                                'black'  => ['label'=>'Black (Deceased)', 'btn'=>'default'],
                            ] as $val => $triage)
                            <label class="btn btn-{{ $triage['btn'] }} btn-triage-opt" style="font-weight:normal;">
                                <input type="radio" name="triage_category" value="{{ $val }}" style="margin-right:4px;">
                                {{ $triage['label'] }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>BP Systolic</label>
                                <div class="input-group">
                                    <input type="number" name="bp_systolic" class="form-control" placeholder="120">
                                    <span class="input-group-addon">mmHg</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>BP Diastolic</label>
                                <div class="input-group">
                                    <input type="number" name="bp_diastolic" class="form-control" placeholder="80">
                                    <span class="input-group-addon">mmHg</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Temperature</label>
                                <div class="input-group">
                                    <input type="number" name="temperature" step="0.1" class="form-control" placeholder="37.0">
                                    <span class="input-group-addon">°C</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pulse Rate</label>
                                <div class="input-group">
                                    <input type="number" name="pulse_rate" class="form-control" placeholder="72">
                                    <span class="input-group-addon">bpm</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Respiratory Rate</label>
                                <div class="input-group">
                                    <input type="number" name="respiratory_rate" class="form-control" placeholder="16">
                                    <span class="input-group-addon">/min</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>O₂ Saturation</label>
                                <div class="input-group">
                                    <input type="number" name="oxygen_saturation" class="form-control" placeholder="98">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Weight</label>
                                <div class="input-group">
                                    <input type="number" name="weight_kg" step="0.1" class="form-control" placeholder="70">
                                    <span class="input-group-addon">kg</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Height</label>
                                <div class="input-group">
                                    <input type="number" name="height_cm" step="0.1" class="form-control" placeholder="170">
                                    <span class="input-group-addon">cm</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Triage Notes</label>
                        <textarea name="triage_notes" class="form-control" rows="2" placeholder="Additional triage observations…"></textarea>
                    </div>

                    <div class="tw-flex tw-gap-2 tw-mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-check"></i> Register Visit
                        </button>
                        <a href="{{ route('hospital.visits.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            @endcomponent
        </div>

        {{-- Patient Info Panel --}}
        <div class="col-md-4">
            <div class="box box-info" id="patient-info-box" style="display:none;">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-user"></i> Patient Info</h3>
                </div>
                <div class="box-body" id="patient-info-body">
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(function () {
    $('#patient_id').select2({ placeholder: 'Search patient...', width: '100%' });

    // Patient data pre-loaded from server (no extra AJAX needed)
    var patientMap = @json($patients->keyBy('id'));

    // Load patient info on selection
    $('#patient_id').on('change', function () {
        var id = $(this).val();
        if (!id) { $('#patient-info-box').hide(); return; }
        var p = patientMap[id];
        if (p) {
            var html = '<table class="table table-condensed" style="margin:0">'
                + '<tr><th>Patient No.</th><td><strong>' + p.patient_no + '</strong></td></tr>'
                + '<tr><th>Name</th><td>' + p.first_name + ' ' + p.last_name + '</td></tr>'
                + '<tr><th>DOB</th><td>' + (p.dob || '-') + '</td></tr>'
                + '<tr><th>Gender</th><td>' + (p.gender || '-') + '</td></tr>'
                + '<tr><th>Blood Group</th><td>' + (p.blood_group || '-') + '</td></tr>'
                + '<tr><th>Allergies</th><td class="text-danger">' + (p.allergies || 'None') + '</td></tr>'
                + '</table>';
            $('#patient-info-body').html(html);
            $('#patient-info-box').show();
        } else {
            $('#patient-info-box').hide();
        }
    });

    // Pre-load if patient was passed
    @if($patient)
    var p = @json($patient);
    var html = '<table class="table table-condensed" style="margin:0">'
        + '<tr><th>Patient No.</th><td><strong>' + p.patient_no + '</strong></td></tr>'
        + '<tr><th>Name</th><td>' + p.first_name + ' ' + p.last_name + '</td></tr>'
        + '<tr><th>DOB</th><td>' + (p.dob || '-') + '</td></tr>'
        + '<tr><th>Gender</th><td>' + (p.gender || '-') + '</td></tr>'
        + '<tr><th>Blood Group</th><td>' + (p.blood_group || '-') + '</td></tr>'
        + '<tr><th>Allergies</th><td class="text-danger">' + (p.allergies || 'None') + '</td></tr>'
        + '</table>';
    $('#patient-info-body').html(html);
    $('#patient-info-box').show();
    @endif

    $('#create_visit_form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route('hospital.visits.store') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    toastr.success(response.msg);
                    setTimeout(function () {
                        window.location.href = '/hospital/visits/' + response.data.id;
                    }, 800);
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON;
                if (errors && errors.errors) {
                    $.each(errors.errors, function (key, val) { toastr.error(val[0]); });
                } else {
                    toastr.error('An error occurred.');
                }
            }
        });
    });
});
</script>
@endsection
