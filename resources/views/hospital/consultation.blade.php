@extends('layouts.app')
@section('title', 'Consultation')

@section('content')
<section class="content-header">
    <h1>Consultation - Patient: {{ $appointment->patient->name }}</h1>
</section>

<section class="content">
    @if(!empty($appointment->patient->patientDetails->allergies))
        <div class="callout callout-danger">
            <h4><i class="icon fa fa-warning"></i> ALLERGY ALERT!</h4>
            <p>Patient is allergic to: <strong>{{ $appointment->patient->patientDetails->allergies }}</strong></p>
        </div>
    @endif

    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\HospitalController::class, 'storeConsultation']), 'method' => 'post']) !!}
        {!! Form::hidden('appointment_id', $appointment->id) !!}
        {!! Form::hidden('patient_id', $appointment->patient->id) !!}
        
        <div class="box-body">
            <h3>Vitals</h3>
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('vitals[bp]', 'BP (mmHg):') !!}
                        {!! Form::text('vitals[bp]', null, ['class' => 'form-control', 'placeholder' => '120/80']) !!}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('vitals[weight]', 'Weight (kg):') !!}
                        {!! Form::text('vitals[weight]', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('vitals[temp]', 'Temp (°C):') !!}
                        {!! Form::text('vitals[temp]', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('vitals[spo2]', 'SpO2 (%):') !!}
                        {!! Form::text('vitals[spo2]', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('vitals[pulse]', 'Pulse (bpm):') !!}
                        {!! Form::text('vitals[pulse]', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <h3>Medical Assessment</h3>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('symptoms', 'Symptoms:*') !!}
                        {!! Form::textarea('symptoms', null, ['class' => 'form-control', 'required', 'rows' => 3]) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('diagnosis', 'Diagnosis:*') !!}
                        {!! Form::textarea('diagnosis', null, ['class' => 'form-control', 'required', 'rows' => 3]) !!}
                    </div>
                </div>
            </div>
            
            <h3>Treatment Plan</h3>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('lab_tests', 'Order Lab Tests:') !!}
                        {!! Form::select('lab_tests[]', $lab_tests, null, ['class' => 'form-control select2', 'multiple', 'style' => 'width:100%']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered" id="prescription_table">
                        <thead>
                            <tr class="bg-gray">
                                <th>Drug / Medicine (Search)</th>
                                <th style="width: 100px;">Qty</th>
                                <th>Dosage</th>
                                <th>Freq</th>
                                <th>Duration</th>
                                <th style="width: 50px;"><button type="button" class="btn btn-success btn-xs" id="add_drug_row"><i class="fa fa-plus"></i></button></th>
                            </tr>
                        </thead>
                        <tbody id="drug_rows">
                            <tr>
                                <td>
                                    <select name="drugs[0][variation_id]" class="form-control drug_search" style="width: 100%;"></select>
                                </td>
                                <td><input type="number" name="drugs[0][quantity]" class="form-control input-sm" value="1"></td>
                                <td><input type="text" name="drugs[0][dosage]" class="form-control input-sm" placeholder="500mg"></td>
                                <td><input type="text" name="drugs[0][frequency]" class="form-control input-sm" placeholder="1x3"></td>
                                <td><input type="text" name="drugs[0][duration]" class="form-control input-sm" placeholder="5 days"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('advice', 'Medical Advice / Follow-up:') !!}
                        {!! Form::textarea('advice', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Diet, rest, follow-up date...']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-right">
            <button type="submit" class="btn btn-primary">Complete Consultation</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@section('javascript')
<script>
    $(document).ready(function() {
        var row_index = 1;

        function init_drug_search(element) {
            element.select2({
                ajax: {
                    url: '{{ action([\App\Http\Controllers\Hospital\HospitalController::class, "searchDrugs"]) }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term };
                    },
                    processResults: function(data) {
                        return { results: data };
                    }
                },
                placeholder: 'Search for medicine...',
                minimumInputLength: 2,
                dropdownParent: $('#prescription_table')
            });
        }

        init_drug_search($('.drug_search'));

        $('#add_drug_row').click(function() {
            var html = '<tr>' +
                '<td><select name="drugs['+row_index+'][variation_id]" class="form-control drug_search" style="width: 100%;"></select></td>' +
                '<td><input type="number" name="drugs['+row_index+'][quantity]" class="form-control input-sm" value="1"></td>' +
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
