@extends('layouts.app')
@section('title', 'Update Surgical Record')

@section('content')
<section class="content-header">
    <h1>Surgical Record: {{ $booking->surgery->name }}</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Patient: {{ $booking->patient->name }}</h3>
        </div>
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\TheatreController::class, 'updateRecord'], [$booking->id]), 'method' => 'post']) !!}
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('started_at', 'Surgery Started At:*') !!}
                        {!! Form::dateTimeLocal('started_at', $booking->started_at ?? now(), ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('ended_at', 'Surgery Ended At:*') !!}
                        {!! Form::dateTimeLocal('ended_at', $booking->ended_at ?? now(), ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('post_op_diagnosis', 'Post-Op Diagnosis:*') !!}
                {!! Form::textarea('post_op_diagnosis', null, ['class' => 'form-control', 'required', 'rows' => 2]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('procedure_notes', 'Procedure Notes:*') !!}
                {!! Form::textarea('procedure_notes', null, ['class' => 'form-control', 'required', 'rows' => 8]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('complications', 'Complications (if any):') !!}
                {!! Form::textarea('complications', null, ['class' => 'form-control', 'rows' => 3]) !!}
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-success">Complete Surgery Record</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
