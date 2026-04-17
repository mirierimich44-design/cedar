@extends('layouts.app')
@section('title', 'DDA Stock Balance')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">DDA Stock Balance</h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Current DDA Drug Stock'])

        @if($balance->isEmpty())
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                No DDA products found. Make sure products are marked as DDA in the product list.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Drug / Product</th>
                            <th>SKU</th>
                            <th>Location</th>
                            <th>Balance</th>
                            <th>Unit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($balance as $b)
                        @php
                            $qty    = (float) $b->qty_available;
                            $color  = $qty <= 0 ? 'danger' : ($qty <= 10 ? 'warning' : 'success');
                            $label  = $qty <= 0 ? 'Out of Stock' : ($qty <= 10 ? 'Low Stock' : 'In Stock');
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $b->drug_name ?? $b->product_name }}</strong>
                                @if($b->drug_name && $b->drug_name !== $b->product_name)
                                    <br><small class="text-muted">{{ $b->product_name }}</small>
                                @endif
                            </td>
                            <td><code>{{ $b->sku }}</code></td>
                            <td>{{ $b->location_name ?? '—' }}</td>
                            <td>
                                <strong class="text-{{ $color }}">
                                    {{ number_format($qty, 2) }}
                                </strong>
                            </td>
                            <td>{{ $b->unit_name ?? '—' }}</td>
                            <td><span class="label label-{{ $color }}">{{ $label }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="active">
                            <th colspan="3">Total balance across all locations</th>
                            <th><strong>{{ number_format($balance->sum('qty_available'), 2) }}</strong></th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <p class="text-muted" style="font-size:12px; margin-top:8px">
                <i class="fa fa-info-circle"></i>
                Live balances — updated automatically on every purchase and sale.
                Low stock threshold: ≤10 units.
            </p>
        @endif

    @endcomponent
</section>
@endsection
