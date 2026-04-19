@extends('layouts.app')
@section('title', 'Enter Lab Result')

@section('content')
<section class="content-header">
    <h1>Enter Test Results - Patient: {{ $request->patient->name }}</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\LabController::class, 'storeResult']), 'method' => 'post']) !!}
        {!! Form::hidden('request_id', $request->id) !!}
        <div class="box-body">
            <div class="row">
                <div class="col-sm-12">
                    <h4>Test: {{ $request->test->name }}</h4>
                    <p>Expected Unit: {{ $request->test->result_unit }} | Normal Range: {{ $request->test->normal_range }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('result_notes', 'Detailed Result / Technician Notes:*') !!}
                        {!! Form::textarea('result_notes', null, ['class' => 'form-control', 'required', 'rows' => 10, 'placeholder' => 'Enter test findings here...']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-right">
            <button type="submit" class="btn btn-primary">Submit Results</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
