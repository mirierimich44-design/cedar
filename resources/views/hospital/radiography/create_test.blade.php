@extends('layouts.app')
@section('title', 'Add Radiography Test')

@section('content')
<section class="content-header">
    <h1>Add Radiography Test to Catalog</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\RadiographyController::class, 'storeTest']), 'method' => 'post']) !!}
        <div class="box-body">
            <div class="form-group">
                {!! Form::label('name', 'Test Name:*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => 'e.g. Chest X-Ray']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('short_name', 'Short Name:') !!}
                {!! Form::text('short_name', null, ['class' => 'form-control', 'placeholder' => 'e.g. CXR']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('type', 'Imaging Type:*') !!}
                {!! Form::select('type', ['x-ray' => 'X-Ray', 'ultrasound' => 'Ultrasound', 'ct-scan' => 'CT Scan', 'mri' => 'MRI', 'other' => 'Other'], 'x-ray', ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('price', 'Price:*') !!}
                {!! Form::number('price', null, ['class' => 'form-control', 'required', 'step' => '0.01']) !!}
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Save Test</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
