@extends('layouts.app')
@section('title', 'Add Cooler Asset')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl tw-font-bold tw-text-black">Add Cooler Asset</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title">Asset Details</h3></div>
        <div class="box-body">
            {!! Form::open(['route' => 'cooler.assets.store', 'method' => 'POST']) !!}
            @include('cooler.assets._form')
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save Asset</button>
                <a href="{{ route('cooler.assets.index') }}" class="btn btn-default">Cancel</a>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>
@endsection
