@extends('layouts.app')
@section('title', 'Create Bundle')
@section('content')
<section class="content-header"><h1>Create Bundle</h1></section>
<section class="content">
@component('components.widget', ['class' => 'box-primary'])
{!! Form::open(['route' => 'saas.admin.bundles.store', 'method' => 'POST']) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">{!! Form::label('name', 'Bundle Name *') !!}{!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}</div>
    </div>
    <div class="col-md-3">
        <div class="form-group">{!! Form::label('slug', 'Slug *') !!}{!! Form::text('slug', null, ['class' => 'form-control', 'required', 'placeholder' => 'e.g. pharmacy-suite']) !!}</div>
    </div>
    <div class="col-md-3">
        <div class="form-group">{!! Form::label('sort_order', 'Sort Order') !!}{!! Form::number('sort_order', 0, ['class' => 'form-control']) !!}</div>
    </div>
    <div class="col-md-12">
        <div class="form-group">{!! Form::label('description', 'Description') !!}{!! Form::text('description', null, ['class' => 'form-control']) !!}</div>
    </div>
    <div class="col-md-3">
        <div class="form-group">{!! Form::label('color', 'Card Color') !!}{!! Form::color('color', '#0d9488', ['class' => 'form-control']) !!}</div>
    </div>
    <div class="col-md-3">
        <div class="form-group tw-pt-6">{!! Form::checkbox('is_active', 1, true) !!} Active</div>
    </div>
    <div class="col-md-3">
        <div class="form-group tw-pt-6">{!! Form::checkbox('is_popular', 1, false) !!} Mark as Popular</div>
    </div>
</div>

<h4>Features in this Bundle</h4>
@foreach($features as $category => $items)
<div class="panel panel-default">
    <div class="panel-heading"><strong>{{ ucfirst($category) }}</strong></div>
    <div class="panel-body">
    @foreach($items as $f)
        <label style="margin-right:18px; font-weight:normal;">
            {!! Form::checkbox('feature_ids[]', $f->id, false) !!}
            {{ $f->name }}
        </label>
    @endforeach
    </div>
</div>
@endforeach

<button type="submit" class="btn btn-primary">Create Bundle</button>
<a href="{{ route('saas.admin.bundles') }}" class="btn btn-default">Cancel</a>
{!! Form::close() !!}
@endcomponent
</section>
@endsection
