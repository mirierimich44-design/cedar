@extends('layouts.app')
@section('title', 'Create Pregnancy Profile')

@section('content')
<section class="content-header">
    <h1>New Pregnancy Profile</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\MaternityController::class, 'storeProfile']), 'method' => 'post']) !!}
        <div class="box-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('patient_id', 'Patient:*') !!}
                        {!! Form::select('patient_id', $patients, null, ['class' => 'form-control select2', 'required', 'placeholder' => 'Select Patient']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('lmp_date', 'Last Menstrual Period (LMP):*') !!}
                        {!! Form::date('lmp_date', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('edd_date', 'Expected Delivery Date (EDD):') !!}
                        {!! Form::date('edd_date', null, ['class' => 'form-control']) !!}
                        <small class="text-muted">Will be calculated automatically if left blank (LMP + 9 months)</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('gravida', 'Gravida (Total Pregnancies):') !!}
                        {!! Form::number('gravida', 1, ['class' => 'form-control', 'min' => 1]) !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('parity', 'Parity (Total Births):') !!}
                        {!! Form::number('parity', 0, ['class' => 'form-control', 'min' => 0]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('medical_history', 'Relevant Medical History / Risk Factors:') !!}
                        {!! Form::textarea('medical_history', null, ['class' => 'form-control', 'rows' => 3]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-right">
            <button type="submit" class="btn btn-primary">Create Profile</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
