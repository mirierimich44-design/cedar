@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ __('made_to_order::messages.order_no') }}: {{ $order->order_no }}</h2>
    <p>{{ __('made_to_order::messages.status') }}: <strong>{{ __('made_to_order::messages.' . $order->status) }}</strong></p>
    <p>{{ __('made_to_order::messages.total') }}: {{ number_format($order->total_cost,2) }}</p>

    <h4>{{ __('made_to_order::messages.items') }}</h4>
    <ul>
        @foreach($order->items as $it)
            <li>{{ $it->item_name }} — {{ $it->quantity }} × {{ number_format($it->total_price,2) }} ({{ $it->material->name ?? '-' }})</li>
        @endforeach
    </ul>
</div>
@endsection
