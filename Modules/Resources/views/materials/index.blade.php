@extends('layouts.app')
@section('content')
<div class="container">
    <h3>{{ __('made_to_order::messages.materials') }}</h3>
    <a href="{{ route('made_to_order.materials.create') }}" class="btn btn-sm btn-primary mb-2">{{ __('made_to_order::messages.add_material') }}</a>
    <table class="table">
        <thead><tr><th>#</th><th>{{ __('made_to_order::messages.name') }}</th><th>{{ __('made_to_order::messages.base_price') }}</th></tr></thead>
        <tbody>
        @foreach($materials as $m)
            <tr><td>{{ $m->id }}</td><td>{{ $m->name }}</td><td>{{ number_format($m->base_price,4) }} / {{ $m->unit }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
