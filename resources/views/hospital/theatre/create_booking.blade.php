@extends('layouts.app')
@section('title', 'Schedule Surgery')

@section('content')
<section class="content-header">
    <h1>Schedule Surgery</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\TheatreController::class, 'storeBooking']), 'method' => 'post']) !!}
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('patient_id', 'Patient:*') !!}
                        {!! Form::select('patient_id', $patients, null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select Patient']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('surgery_id', 'Surgery Type:*') !!}
                        {!! Form::select('surgery_id', $surgeries, null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select Procedure']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('theatre_id', 'Operating Theatre:*') !!}
                        {!! Form::select('theatre_id', $theatres, null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('surgeon_id', 'Lead Surgeon:*') !!}
                        {!! Form::select('surgeon_id', $users, null, ['class' => 'form-control select2', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('anaesthetist_id', 'Anaesthetist:') !!}
                        {!! Form::select('anaesthetist_id', $users, null, ['class' => 'form-control select2', 'placeholder' => 'Optional']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('scheduled_at', 'Scheduled Date & Time:*') !!}
                        {!! Form::dateTimeLocal('scheduled_at', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('pre_op_diagnosis', 'Pre-Op Diagnosis:') !!}
                {!! Form::textarea('pre_op_diagnosis', null, ['class' => 'form-control', 'rows' => 3]) !!}
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Schedule Booking</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
