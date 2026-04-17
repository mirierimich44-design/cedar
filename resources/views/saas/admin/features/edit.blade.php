@extends('layouts.app')
@section('title', 'Edit Feature')
@section('content')
<section class="content-header"><h1>Edit Feature: {{ $feature->name }}</h1></section>
<section class="content">
@component('components.widget', ['class' => 'box-primary'])
{!! Form::model($feature, ['route' => ['saas.admin.features.update', $feature], 'method' => 'PUT']) !!}
<div class="row">
    <div class="col-md-6"><div class="form-group">{!! Form::label('name', 'Feature Name *') !!}{!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}</div></div>
    <div class="col-md-3"><div class="form-group">{!! Form::label('key', 'Key') !!}{!! Form::text('key', null, ['class' => 'form-control', 'disabled']) !!}</div></div>
    <div class="col-md-3">
        <div class="form-group">{!! Form::label('category', 'Category') !!}
        {!! Form::select('category', ['core' => 'Core', 'inventory' => 'Inventory', 'pharmacy' => 'Pharmacy/DDA', 'reporting' => 'Reporting', 'communication' => 'Communication', 'restaurant' => 'Restaurant'], null, ['class' => 'form-control select2']) !!}
        </div>
    </div>
    <div class="col-md-12"><div class="form-group">{!! Form::label('description', 'Description') !!}{!! Form::text('description', null, ['class' => 'form-control']) !!}</div></div>
    <div class="col-md-3"><div class="form-group">{!! Form::label('price_monthly', 'Monthly (KES)') !!}{!! Form::number('price_monthly', null, ['class' => 'form-control', 'min' => 0, 'step' => '0.01']) !!}</div></div>
    <div class="col-md-3"><div class="form-group">{!! Form::label('price_quarterly', 'Quarterly (KES)') !!}{!! Form::number('price_quarterly', null, ['class' => 'form-control', 'min' => 0, 'step' => '0.01']) !!}</div></div>
    <div class="col-md-3"><div class="form-group">{!! Form::label('price_yearly', 'Yearly (KES)') !!}{!! Form::number('price_yearly', null, ['class' => 'form-control', 'min' => 0, 'step' => '0.01']) !!}</div></div>
    <div class="col-md-3"><div class="form-group">{!! Form::label('price_once', 'One-Off (KES)') !!}{!! Form::number('price_once', null, ['class' => 'form-control', 'min' => 0, 'step' => '0.01']) !!}</div></div>
    <div class="col-md-3"><div class="form-group">{!! Form::label('sort_order', 'Sort Order') !!}{!! Form::number('sort_order', null, ['class' => 'form-control']) !!}</div></div>
    <div class="col-md-3"><div class="form-group tw-pt-6">{!! Form::checkbox('is_active', 1, $feature->is_active) !!} Active</div></div>
</div>
<button type="submit" class="btn btn-primary">Update Feature</button>
<a href="{{ route('saas.admin.features') }}" class="btn btn-default">Cancel</a>
{!! Form::close() !!}
@endcomponent
</section>
@endsection
