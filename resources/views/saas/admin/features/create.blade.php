@extends('layouts.app')
@section('title', 'Add Feature')
@section('content')
<section class="content-header"><h1>Add Feature</h1></section>
<section class="content">
@component('components.widget', ['class' => 'box-primary'])
{!! Form::open(['route' => 'saas.admin.features.store', 'method' => 'POST']) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">{!! Form::label('name', 'Feature Name *') !!}{!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}</div>
    </div>
    <div class="col-md-3">
        <div class="form-group">{!! Form::label('key', 'Unique Key *') !!}{!! Form::text('key', null, ['class' => 'form-control', 'required', 'placeholder' => 'e.g. dda_module']) !!}</div>
    </div>
    <div class="col-md-3">
        <div class="form-group">{!! Form::label('category', 'Category *') !!}
        {!! Form::select('category', ['core' => 'Core', 'inventory' => 'Inventory', 'pharmacy' => 'Pharmacy/DDA', 'reporting' => 'Reporting', 'communication' => 'Communication', 'restaurant' => 'Restaurant'], null, ['class' => 'form-control select2', 'required']) !!}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">{!! Form::label('description', 'Description') !!}{!! Form::text('description', null, ['class' => 'form-control']) !!}</div>
    </div>
    <div class="col-md-3"><div class="form-group">{!! Form::label('price_monthly', 'Price / Month (KES)') !!}{!! Form::number('price_monthly', 0, ['class' => 'form-control', 'min' => 0, 'step' => '0.01']) !!}</div></div>
    <div class="col-md-3"><div class="form-group">{!! Form::label('price_quarterly', 'Price / Quarter (KES)') !!}{!! Form::number('price_quarterly', 0, ['class' => 'form-control', 'min' => 0, 'step' => '0.01']) !!}</div></div>
    <div class="col-md-3"><div class="form-group">{!! Form::label('price_yearly', 'Price / Year (KES)') !!}{!! Form::number('price_yearly', 0, ['class' => 'form-control', 'min' => 0, 'step' => '0.01']) !!}</div></div>
    <div class="col-md-3"><div class="form-group">{!! Form::label('price_once', 'One-Off Price (KES)') !!}{!! Form::number('price_once', 0, ['class' => 'form-control', 'min' => 0, 'step' => '0.01']) !!}</div></div>
    <div class="col-md-3"><div class="form-group">{!! Form::label('sort_order', 'Sort Order') !!}{!! Form::number('sort_order', 0, ['class' => 'form-control']) !!}</div></div>
    <div class="col-md-3"><div class="form-group tw-pt-6">{!! Form::checkbox('is_required', 1, false) !!} Required (always included)</div></div>
    <div class="col-md-3"><div class="form-group tw-pt-6">{!! Form::checkbox('is_active', 1, true) !!} Active</div></div>
</div>
<button type="submit" class="btn btn-primary">Save Feature</button>
<a href="{{ route('saas.admin.features') }}" class="btn btn-default">Cancel</a>
{!! Form::close() !!}
@endcomponent
</section>
@endsection
