@extends('layouts.app')
@section('title', 'Daily Monitoring')

@section('content')
<section class="content-header">
    <h1>Daily Monitoring - Patient: {{ $admission->patient->name }}</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\HospitalController::class, 'storeDailyRecord']), 'method' => 'post']) !!}
        {!! Form::hidden('admission_id', $admission->id) !!}
        
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

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('notes', 'Nursing Notes / Observations:') !!}
                        {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 4]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-right">
            <button type="submit" class="btn btn-primary">Save Daily Record</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
