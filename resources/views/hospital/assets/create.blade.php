@extends('layouts.app')
@section('title', 'Register Asset')

@section('content')
<section class="content-header">
    <h1>Register New Hospital Asset</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\HospitalAssetController::class, 'store']), 'method' => 'post']) !!}
        <div class="box-body">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('name', 'Asset Name:*') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => 'e.g. Philips Ventilator X3']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('asset_code', 'Asset Tag / Code:*') !!}
                        {!! Form::text('asset_code', null, ['class' => 'form-control', 'required', 'placeholder' => 'HOSP-ASSET-001']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('category', 'Category:') !!}
                        {!! Form::select('category', ['Medical Equipment' => 'Medical Equipment', 'Facility Furniture' => 'Facility Furniture', 'IT Equipment' => 'IT Equipment'], null, ['class' => 'form-control', 'placeholder' => 'Select Category']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('model', 'Model Name:') !!}
                        {!! Form::text('model', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('serial_number', 'Serial Number:') !!}
                        {!! Form::text('serial_number', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('purchase_price', 'Purchase Price:') !!}
                        {!! Form::number('purchase_price', 0, ['class' => 'form-control', 'step' => '0.01']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('purchase_date', 'Purchase Date:') !!}
                        {!! Form::date('purchase_date', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('warranty_expiry', 'Warranty Expiry:') !!}
                        {!! Form::date('warranty_expiry', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('next_service_date', 'Next Maintenance Due:') !!}
                        {!! Form::date('next_service_date', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-right">
            <button type="submit" class="btn btn-primary">Save Asset</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
