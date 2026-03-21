@if($errors->any())
<div class="alert alert-danger">
    <ul class="tw-list-disc tw-pl-4">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('asset_type') ? 'has-error' : '' }}">
            {!! Form::label('asset_type', 'Asset Type *') !!}
            {!! Form::text('asset_type', null, ['class' => 'form-control', 'placeholder' => 'e.g. Upright Cooler 300L', 'required']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('asset_number') ? 'has-error' : '' }}">
            {!! Form::label('asset_number', 'Asset Number *') !!}
            {!! Form::text('asset_number', null, ['class' => 'form-control', 'placeholder' => 'e.g. SBC-001', 'required']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('serial_number', 'Serial Number') !!}
            {!! Form::text('serial_number', null, ['class' => 'form-control', 'placeholder' => 'Manufacturer serial number']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('cooler_tag', 'Cooler Tag') !!}
            {!! Form::text('cooler_tag', null, ['class' => 'form-control', 'placeholder' => 'Internal tag/barcode']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
            {!! Form::label('status', 'Status *') !!}
            {!! Form::select('status', $statuses, null, ['class' => 'form-control select2', 'required']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('replacement_value', 'Replacement Value (KES)') !!}
            {!! Form::number('replacement_value', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'placeholder' => '0.00']) !!}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('notes', 'Notes') !!}
            {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
        </div>
    </div>
</div>
