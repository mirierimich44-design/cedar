@extends('layouts.app')
@section('title', 'Log Maintenance')

@section('content')
<section class="content-header">
    <h1>Log Maintenance - {{ $asset->name }}</h1>
</section>

<section class="content">
    <div class="box box-primary">
        {!! Form::open(['url' => action([\App\Http\Controllers\Hospital\HospitalAssetController::class, 'storeMaintenance']), 'method' => 'post']) !!}
        {!! Form::hidden('asset_id', $asset->id) !!}
        <div class="box-body">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('service_date', 'Service Date:*') !!}
                        {!! Form::date('service_date', \Carbon::now()->toDateString(), ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('service_type', 'Service Type:*') !!}
                        {!! Form::select('service_type', ['Preventive' => 'Preventive', 'Repair' => 'Repair', 'Calibration' => 'Calibration'], null, ['class' => 'form-control', 'required', 'placeholder' => 'Select Type']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('status', 'Current Asset Status:*') !!}
                        {!! Form::select('status', ['active' => 'Active/Working', 'under_maintenance' => 'Under Maintenance', 'broken' => 'Broken/Out of Order'], $asset->status, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('cost', 'Maintenance Cost:') !!}
                        {!! Form::number('cost', 0, ['class' => 'form-control', 'step' => '0.01']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('performed_by', 'Performed By (Vendor/Staff):') !!}
                        {!! Form::text('performed_by', null, ['class' => 'form-control', 'placeholder' => 'e.g. Biomedical Tech']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('next_service_date', 'Next Service Due Date:*') !!}
                        {!! Form::date('next_service_date', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('details', 'Service Details / Findings:') !!}
                        {!! Form::textarea('details', null, ['class' => 'form-control', 'rows' => 4]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer text-right">
            <button type="submit" class="btn btn-success">Update Maintenance Record</button>
        </div>
        {!! Form::close() !!}
    </div>
</section>
@endsection
