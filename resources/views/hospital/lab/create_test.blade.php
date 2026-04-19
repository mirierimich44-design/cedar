@extends('layouts.app')
@section('title', 'Add Lab Test')

@section('content')
<section class="content-header">
    <h1>Add New Lab Test</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\LabController::class, 'storeTest']), 'method' => 'post']) !!}
        <div class="box-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('name', 'Test Name:*') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => 'e.g. Full Blood Count']) !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('short_name', 'Short Name:') !!}
                        {!! Form::text('short_name', null, ['class' => 'form-control', 'placeholder' => 'FBC']) !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('price', 'Test Fee:*') !!}
                        {!! Form::number('price', 0, ['class' => 'form-control', 'required', 'step' => '0.01']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('result_unit', 'Result Unit:') !!}
                        {!! Form::text('result_unit', null, ['class' => 'form-control', 'placeholder' => 'mg/dL']) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('normal_range', 'Normal Range:') !!}
                        {!! Form::text('normal_range', null, ['class' => 'form-control', 'placeholder' => 'e.g. 13.5 - 17.5']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-right">
            <button type="submit" class="btn btn-primary">Save Test</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
