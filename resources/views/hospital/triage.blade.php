@extends('layouts.app')
@section('title', 'Triage')

@section('content')
<section class="content-header">
    <h1>Patient Triage - Token: {{ $queue->token_number }}</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\HospitalController::class, 'storeTriage']), 'method' => 'post']) !!}
        {!! Form::hidden('queue_id', $queue->id) !!}
        
        <div class="box-body">
            <div class="row">
                <div class="col-sm-4">
                    <h4>Patient: {{ $queue->patient->name }}</h4>
                </div>
            </div>
            <hr>
            <h3>Record Vitals</h3>
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
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('notes', 'Triage Notes:') !!}
                        {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Any immediate concerns...']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-right">
            <button type="submit" class="btn btn-success btn-lg">Complete Triage & Move to Doctor</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
