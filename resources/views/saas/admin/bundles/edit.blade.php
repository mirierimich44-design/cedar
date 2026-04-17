@extends('layouts.app')
@section('title', 'Edit Bundle')
@section('content')
<section class="content-header"><h1>Edit Bundle: {{ $bundle->name }}</h1></section>
<section class="content">
@component('components.widget', ['class' => 'box-primary'])
{!! Form::model($bundle, ['route' => ['saas.admin.bundles.update', $bundle], 'method' => 'PUT']) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">{!! Form::label('name', 'Bundle Name *') !!}{!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}</div>
    </div>
    <div class="col-md-3">
        <div class="form-group">{!! Form::label('slug', 'Slug') !!}{!! Form::text('slug', null, ['class' => 'form-control', 'required']) !!}</div>
    </div>
    <div class="col-md-3">
        <div class="form-group">{!! Form::label('sort_order', 'Sort Order') !!}{!! Form::number('sort_order', null, ['class' => 'form-control']) !!}</div>
    </div>
    <div class="col-md-12">
        <div class="form-group">{!! Form::label('description', 'Description') !!}{!! Form::text('description', null, ['class' => 'form-control']) !!}</div>
    </div>
    <div class="col-md-3">
        <div class="form-group">{!! Form::label('color', 'Card Color') !!}{!! Form::color('color', $bundle->color ?? '#0d9488', ['class' => 'form-control']) !!}</div>
    </div>
    <div class="col-md-3">
        <div class="form-group tw-pt-6">{!! Form::checkbox('is_active', 1, $bundle->is_active) !!} Active</div>
    </div>
    <div class="col-md-3">
        <div class="form-group tw-pt-6">{!! Form::checkbox('is_popular', 1, $bundle->is_popular ?? false) !!} Mark as Popular</div>
    </div>
</div>

<h4>Features in this Bundle</h4>
@foreach($features as $category => $items)
<div class="panel panel-default">
    <div class="panel-heading"><strong>{{ ucfirst($category) }}</strong></div>
    <div class="panel-body">
    @foreach($items as $f)
        <label style="margin-right:18px; font-weight:normal;">
            {!! Form::checkbox('feature_ids[]', $f->id, in_array($f->id, $selected)) !!}
            {{ $f->name }}
        </label>
    @endforeach
    </div>
</div>
@endforeach

<button type="submit" class="btn btn-primary">Update Bundle</button>
<a href="{{ route('saas.admin.bundles') }}" class="btn btn-default">Cancel</a>
{!! Form::close() !!}
@endcomponent
</section>
@endsection
