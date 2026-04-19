@extends('layouts.app')
@section('title', 'Book Appointment')

@section('content')
<section class="content-header">
    <h1>Book Appointment</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\HospitalController::class, 'storeAppointment']), 'method' => 'post']) !!}
        <div class="box-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('patient_id', 'Patient:*') !!}
                        {!! Form::select('patient_id', $patients, null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select Patient']) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('doctor_id', 'Doctor:*') !!}
                        {!! Form::select('doctor_id', $doctors, null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select Doctor']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('appointment_date', 'Appointment Date & Time:*') !!}
                        {!! Form::datetimeLocal('appointment_date', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('priority', 'Priority:*') !!}
                        {!! Form::select('priority', ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'], 'medium', ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('notes', 'Notes:') !!}
                        {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-right">
            <button type="submit" class="btn btn-primary">Save Appointment</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
