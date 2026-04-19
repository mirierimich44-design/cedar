@extends('layouts.app')
@section('title', 'Enter Imaging Findings')

@section('content')
<section class="content-header">
    <h1>Radiography Findings: {{ $request->test->name }}</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Patient: {{ $request->patient->name }}</h3>
        </div>
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\RadiographyController::class, 'storeResult']), 'method' => 'post']) !!}
        {!! Form::hidden('request_id', $request->id) !!}
        <div class="box-body">
            <div class="form-group">
                {!! Form::label('clinical_history', 'Clinical History:') !!}
                <p class="well well-sm">{{ $request->clinical_history ?? 'No history provided' }}</p>
            </div>
            <div class="form-group">
                {!! Form::label('radiologist_findings', 'Radiologist Findings:*') !!}
                {!! Form::textarea('radiologist_findings', null, ['class' => 'form-control', 'required', 'rows' => 5]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('conclusion', 'Conclusion / Impression:*') !!}
                {!! Form::textarea('conclusion', null, ['class' => 'form-control', 'required', 'rows' => 3]) !!}
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Save Findings & Complete</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
