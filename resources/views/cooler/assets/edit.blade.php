@extends('layouts.app')
@section('title', 'Edit Cooler Asset')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">Edit Cooler Asset — {{ $asset->asset_number }}</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title">Asset Details</h3></div>
        <div class="box-body">
            {!! Form::model($asset, ['route' => ['cooler.assets.update', $asset->id], 'method' => 'PUT']) !!}
            @include('cooler.assets._form')
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Asset</button>
                <a href="{{ route('cooler.assets.show', $asset->id) }}" class="btn btn-default">Cancel</a>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>
@endsection
