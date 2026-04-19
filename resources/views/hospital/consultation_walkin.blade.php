@extends('layouts.app')
@section('title', 'Consultation — ' . $patient->name)

@section('content')
<section class="content-header">
    <h1>Consultation <small>{{ $queue->token_number }} — {{ $patient->name }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="{{ route('hospital.flow') }}">Patient Flow</a></li>
        <li><a href="{{ route('hospital.queue.index') }}">Queue</a></li>
        <li class="active">Consultation</li>
    </ol>
</section>

<section class="content">

    {{-- Allergy Alert --}}
    @if(!empty($patient->patientDetails->allergies))
    <div class="callout callout-danger">
        <h4><i class="icon fa fa-warning"></i> ALLERGY ALERT!</h4>
        <p>Patient is allergic to: <strong>{{ $patient->patientDetails->allergies }}</strong></p>
    </div>
    @endif

    <div class="row">

        {{-- Triage Vitals Summary --}}
        @if(!empty($triage_vitals))
        <div class="col-md-12">
            <div class="box box-info collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-heartbeat"></i> Triage Vitals (recorded by nurse)</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row text-center">
                        @if(!empty($triage_vitals['bp']))
                        <div class="col-xs-2">
                            <div class="info-box-content" style="background:#f7f7f7;padding:10px;border-radius:4px;">
                                <span class="info-box-text text-muted">BP</span>
                                <span class="info-box-number" style="font-size:18px;">{{ $triage_vitals['bp'] }}</span>
                                <small>mmHg</small>
                            </div>
                        </div>
                        @endif
                        @if(!empty($triage_vitals['temp']))
                        <div class="col-xs-2">
                            <div class="info-box-content" style="background:#f7f7f7;padding:10px;border-radius:4px;">
                                <span class="info-box-text text-muted">Temp</span>
                                <span class="info-box-number" style="font-size:18px;">{{ $triage_vitals['temp'] }}</span>
                                <small>°C</small>
                            </div>
                        </div>
                        @endif
                        @if(!empty($triage_vitals['weight']))
                        <div class="col-xs-2">
                            <div class="info-box-content" style="background:#f7f7f7;padding:10px;border-radius:4px;">
                                <span class="info-box-text text-muted">Weight</span>
                                <span class="info-box-number" style="font-size:18px;">{{ $triage_vitals['weight'] }}</span>
                                <small>kg</small>
                            </div>
                        </div>
                        @endif
                        @if(!empty($triage_vitals['spo2']))
                        <div class="col-xs-2">
                            <div class="info-box-content" style="background:#f7f7f7;padding:10px;border-radius:4px;">
                                <span class="info-box-text text-muted">SpO2</span>
                                <span class="info-box-number" style="font-size:18px;">{{ $triage_vitals['spo2'] }}</span>
                                <small>%</small>
                            </div>
                        </div>
                        @endif
                        @if(!empty($triage_vitals['pulse']))
                        <div class="col-xs-2">
                            <div class="info-box-content" style="background:#f7f7f7;padding:10px;border-radius:4px;">
                                <span class="info-box-text text-muted">Pulse</span>
                                <span class="info-box-number" style="font-size:18px;">{{ $triage_vitals['pulse'] }}</span>
                                <small>bpm</small>
                            </div>
                        </div>
                        @endif
                    </div>
                    @if($queue->triage_notes)
                    <p class="text-muted" style="margin-top:10px;"><strong>Nurse Notes:</strong> {{ $queue->triage_notes }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-stethoscope"></i> Doctor's Consultation</h3>
                </div>

                {!! Form::open(['url' => route('hospital.storeConsultation'), 'method' => 'post']) !!}
                {!! Form::hidden('queue_id', $queue->id) !!}
                {!! Form::hidden('patient_id', $patient->id) !!}
                {!! Form::hidden('appointment_id', '') !!}

                <div class="box-body">

                    <h4 class="text-primary"><i class="fa fa-notes-medical"></i> Medical Assessment</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('symptoms', 'Symptoms / Chief Complaint *') !!}
                                {!! Form::textarea('symptoms', null, ['class' => 'form-control', 'required' => 'required', 'rows' => 4, 'placeholder' => 'Patient\'s presenting symptoms...']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('diagnosis', 'Diagnosis *') !!}
                                {!! Form::textarea('diagnosis', null, ['class' => 'form-control', 'required' => 'required', 'rows' => 4, 'placeholder' => 'Clinical diagnosis / impression...']) !!}
                            </div>
                        </div>
                    </div>

                    <h4 class="text-primary"><i class="fa fa-flask"></i> Lab Tests</h4>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Order Lab Tests <small class="text-muted">(selecting tests will send patient to Lab after consultation)</small></label>
                                {!! Form::select('lab_tests[]', $lab_tests, null, ['class' => 'form-control select2', 'multiple' => 'multiple', 'style' => 'width:100%', 'placeholder' => 'Search and select tests...']) !!}
                            </div>
                        </div>
                    </div>

                    <h4 class="text-primary"><i class="fa fa-medkit"></i> Prescriptions</h4>
                    <table class="table table-bordered" id="prescription_table">
                        <thead>
                            <tr class="bg-gray">
                                <th>Drug / Medicine</th>
                                <th style="width:80px;">Qty</th>
                                <th>Dosage</th>
                                <th>Frequency</th>
                                <th>Duration</th>
                                <th style="width:40px;">
                                    <button type="button" class="btn btn-success btn-xs" id="add_drug_row">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="drug_rows">
                            <tr>
                                <td><select name="drugs[0][variation_id]" class="form-control drug_search" style="width:100%;"></select></td>
                                <td><input type="number" name="drugs[0][quantity]" class="form-control input-sm" value="1" min="1"></td>
                                <td><input type="text" name="drugs[0][dosage]" class="form-control input-sm" placeholder="500mg"></td>
                                <td><input type="text" name="drugs[0][frequency]" class="form-control input-sm" placeholder="1x3"></td>
                                <td><input type="text" name="drugs[0][duration]" class="form-control input-sm" placeholder="5 days"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                {!! Form::label('advice', 'Medical Advice / Follow-up Instructions') !!}
                                {!! Form::textarea('advice', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Diet, rest, follow-up date, referral...']) !!}
                            </div>
                        </div>
                    </div>

                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="{{ route('hospital.queue.index') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Queue
                            </a>
                        </div>
                        <div class="col-sm-6 text-right">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-check-circle"></i> Complete Consultation
                            </button>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>

@section('javascript')
<script>
$(document).ready(function() {
    var row_index = 1;

    function init_drug_search(element) {
        element.select2({
            ajax: {
                url: '{{ route('hospital.searchDrugs') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) { return { q: params.term }; },
                processResults: function(data) { return { results: data }; }
            },
            placeholder: 'Search for medicine...',
            minimumInputLength: 2,
            allowClear: true,
            dropdownParent: $('#prescription_table')
        });
    }

    init_drug_search($('.drug_search'));

    $('#add_drug_row').click(function() {
        var html = '<tr>' +
            '<td><select name="drugs['+row_index+'][variation_id]" class="form-control drug_search" style="width:100%;"></select></td>' +
            '<td><input type="number" name="drugs['+row_index+'][quantity]" class="form-control input-sm" value="1" min="1"></td>' +
            '<td><input type="text" name="drugs['+row_index+'][dosage]" class="form-control input-sm" placeholder="500mg"></td>' +
            '<td><input type="text" name="drugs['+row_index+'][frequency]" class="form-control input-sm" placeholder="1x3"></td>' +
            '<td><input type="text" name="drugs['+row_index+'][duration]" class="form-control input-sm" placeholder="5 days"></td>' +
            '<td><button type="button" class="btn btn-danger btn-xs remove_row"><i class="fa fa-times"></i></button></td>' +
            '</tr>';
        $('#drug_rows').append(html);
        init_drug_search($('#drug_rows tr:last .drug_search'));
        row_index++;
    });

    $(document).on('click', '.remove_row', function() {
        $(this).closest('tr').remove();
    });
});
</script>
@endsection
@endsection
