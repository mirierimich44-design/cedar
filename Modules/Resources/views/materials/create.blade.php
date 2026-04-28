@extends('layouts.app')
@section('content')
<div class="container">
    <h3>{{ __('made_to_order::messages.add_material') }}</h3>
    <form method="post" action="{{ route('made_to_order.materials.store') }}">
        @csrf
        <div class="mb-2"><input name="name" class="form-control" placeholder="{{ __('made_to_order::messages.name') }}"/></div>
        <div class="mb-2"><input name="unit" class="form-control" placeholder="{{ __('made_to_order::messages.unit') }}" value="m2"/></div>
        <div class="mb-2"><input name="base_price" class="form-control" placeholder="{{ __('made_to_order::messages.base_price') }}"/></div>
        <div class="mb-2"><textarea name="description" class="form-control" placeholder="{{ __('made_to_order::messages.description') }}"></textarea></div>
        <button class="btn btn-primary">{{ __('made_to_order::messages.save') }}</button>
    </form>
</div>
@endsection
