@extends('layouts.app')
@section('title', 'Patient Admission')

@section('content')
<section class="content-header">
    <h1>Patient Admission</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\HospitalController::class, 'storeAdmission']), 'method' => 'post']) !!}
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
                        {!! Form::label('bed_id', 'Select Bed:*') !!}
                        {!! Form::select('bed_id', $available_beds, null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select Ward & Bed']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('admission_date', 'Admission Date & Time:*') !!}
                        {!! Form::datetimeLocal('admission_date', \Carbon::now()->format('Y-m-d\TH:i'), ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('reason_for_admission', 'Reason for Admission:') !!}
                        {!! Form::textarea('reason_for_admission', null, ['class' => 'form-control', 'rows' => 3]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-right">
            <button type="submit" class="btn btn-primary">Admit Patient</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
