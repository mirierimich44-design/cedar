@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ __('made_to_order::messages.module_title') }}</h2>
    <a href="{{ route('made_to_order.create') }}" class="btn btn-primary mb-3">{{ __('made_to_order::messages.new_order') }}</a>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('made_to_order::messages.order_no') }}</th>
                <th>{{ __('made_to_order::messages.items') }}</th>
                <th>{{ __('made_to_order::messages.total') }}</th>
                <th>{{ __('made_to_order::messages.status') }}</th>
                <th>{{ __('made_to_order::messages.created_at') }}</th>
                <th>{{ __('made_to_order::messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($orders as $o)
            <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->order_no }}</td>
                <td>{{ $o->items->count() }}</td>
                <td>{{ number_format($o->total_cost,2) }}</td>
                <td>{{ __('made_to_order::messages.' . $o->status) }}</td>
                <td>{{ $o->created_at }}</td>
                <td>
                    <a href="{{ route('made_to_order.show',$o->id) }}" class="btn btn-sm btn-info">{{ __('made_to_order::messages.view') }}</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $orders->links() }}
</div>
@endsection
