<div class="row">
    <div class="col-md-6">
        <p><strong>@lang('lang_v1.ref_no'):</strong> {{ $order->ref_no }}</p>
        <p><strong>@lang('contact.customer'):</strong> {{ $order->contact ? $order->contact->name : '-' }}</p>
        <p><strong>@lang('purchase.business_location'):</strong> {{ $order->location ? $order->location->name : '-' }}</p>
    </div>
    <div class="col-md-6">
        <p><strong>@lang('sale.status'):</strong> 
            @php
                $statusClass = [
                    'pending' => 'bg-yellow',
                    'processing' => 'bg-blue',
                    'completed' => 'bg-green',
                    'cancelled' => 'bg-red'
                ][$order->status] ?? 'bg-gray';
            @endphp
            <span class="label {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
        </p>
        <p><strong>@lang('lang_v1.created_by'):</strong> {{ $order->createdBy ? $order->createdBy->first_name : '-' }}</p>
        <p><strong>@lang('lang_v1.created_at'):</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>
</div>

@if($order->notes)
<div class="row">
    <div class="col-md-12">
        <p><strong>@lang('sale.notes'):</strong> {{ $order->notes }}</p>
    </div>
</div>
@endif

<hr>

<h4>@lang('lang_v1.items')</h4>
<table class="table table-bordered table-condensed">
    <thead>
        <tr>
            <th>#</th>
            <th>@lang('sale.product')</th>
            <th>SKU</th>
            <th>@lang('sale.qty')</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->orderLines as $index => $line)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                {{ $line->product ? $line->product->name : '-' }}
                @if($line->variation && $line->variation->name !== 'DUMMY')
                    <small>({{ $line->variation->name }})</small>
                @endif
            </td>
            <td>{{ $line->variation ? $line->variation->sub_sku : ($line->product ? $line->product->sku : '-') }}</td>
            <td>{{ $line->quantity }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
