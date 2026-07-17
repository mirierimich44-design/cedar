@extends('layouts.app')
@section('title', 'Reorder list')

@section('css')
@includeIf('layouts.partials.page_modern_css')
<style>
.rl-wrap{padding:0 4px 24px}
.rl-banner{background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border-radius:0 0 14px 14px;padding:18px 20px;margin:-15px -15px 18px}
.rl-banner h1{margin:0;font-size:20px;font-weight:800}
.rl-banner p{margin:4px 0 0;opacity:.9;font-size:13px}
.rl-actions a{display:inline-block;margin:8px 8px 0 0;padding:8px 12px;border-radius:8px;background:rgba(255,255,255,.2);color:#fff!important;text-decoration:none;font-size:13px;font-weight:600}
.rl-box{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px;overflow-x:auto}
.rl-err{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:10px 12px;border-radius:8px;margin-bottom:12px}
table.rl{width:100%;border-collapse:collapse;font-size:13px}
table.rl th,table.rl td{padding:8px;border-bottom:1px solid #f1f5f9;text-align:left}
table.rl th{font-size:11px;text-transform:uppercase;color:#64748b}
</style>
@endsection

@section('content')
@php
    $rows = $rows ?? collect();
    $business_locations = $business_locations ?? [];
    $canBuy = auth()->user()->can('view_purchase_price');
@endphp
<div class="page-modern rl-wrap">
    <div class="rl-banner">
        <h1><i class="fa fa-truck"></i> Reorder list</h1>
        <p>Low / out of stock — open Shop Orders on POS to place the order · {{ session('business.name') }}</p>
        <div class="rl-actions">
            <a href="{{ url('/pos/create') }}"><i class="fa fa-shopping-cart"></i> Open POS</a>
            <a href="{{ url('/reports') }}">All reports</a>
            @if(\Illuminate\Support\Facades\Route::has('owner.dashboard'))
                <a href="{{ route('owner.dashboard') }}">Owner control</a>
            @endif
        </div>
    </div>

    @if(!empty($error))
        <div class="rl-err"><strong>Query issue:</strong> {{ $error }}</div>
    @endif

    <p style="font-size:13px;color:#64748b;margin-bottom:12px">
        Suggested qty uses alert level when set. On POS open <strong>Shop orders → Low stock</strong>.
    </p>

    <form method="get" action="{{ url('/reports/reorder-list') }}" style="margin-bottom:14px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <label style="font-weight:700;font-size:12px">Location</label>
        <select name="location_id" class="form-control" style="min-width:220px" onchange="this.form.submit()">
            @foreach($business_locations as $id => $name)
                <option value="{{ $id }}" @if((string)$location_id === (string)$id) selected @endif>{{ $name }}</option>
            @endforeach
        </select>
    </form>

    @includeIf('report.partials.export_toolbar', ['table' => '#reorder_table', 'title' => 'Reorder list'])

    <div class="rl-box">
        <table class="rl table table-bordered" id="reorder_table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>In stock</th>
                    <th>Alert level</th>
                    <th>Suggest order qty</th>
                    <th>Buy price</th>
                    <th>Sell price</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $r)
                @php
                    $qty = (float) ($r->qty_available ?? 0);
                    $alert = (float) ($r->alert_quantity ?? 0);
                    $suggest = (float) ($r->suggest_qty ?? 1);
                    $buy = (float) ($r->default_purchase_price ?? 0);
                    $sell = (float) ($r->sell_price_inc_tax ?? 0);
                @endphp
                <tr>
                    <td>{{ $r->name }}</td>
                    <td>{{ $r->sub_sku ?: $r->sku }}</td>
                    <td class="{{ $qty <= 0 ? 'text-danger' : '' }}"><strong>{{ number_format($qty, 2) }}</strong></td>
                    <td>{{ number_format($alert, 2) }}</td>
                    <td><strong>{{ number_format($suggest, 2) }}</strong></td>
                    <td>
                        @if($canBuy)
                            {{ number_format($buy, 2) }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ number_format($sell, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">No low-stock products for this location.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
