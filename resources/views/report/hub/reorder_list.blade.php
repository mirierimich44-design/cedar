@extends('layouts.app')
@section('title', 'Reorder list')

@section('css')
@include('layouts.partials.page_modern_css')
@endsection

@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-truck-loading"></i></div>
                <div>
                    <h1>Reorder list</h1>
                    <p class="pg-subtitle">Low / out of stock at this location — open Shop Orders on POS to place the order &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions">
                <a href="{{ url('/pos/create') }}" class="pg-add-btn"><i class="fa fa-shopping-cart"></i> Open POS (shop orders)</a>
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important">All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        <p class="apex-page-help"><i class="fa fa-info-circle"></i>
            Suggested qty uses alert level when set. On POS open <strong>Shop orders → Low stock</strong> to load suggestions into an order.
        </p>
        <form method="get" class="form-inline" style="margin-bottom:14px">
            <label>Location &nbsp;</label>
            <select name="location_id" class="form-control select2" style="min-width:220px" onchange="this.form.submit()">
                @foreach($business_locations as $id => $name)
                    <option value="{{ $id }}" @if((string)$location_id === (string)$id) selected @endif>{{ $name }}</option>
                @endforeach
            </select>
        </form>

        @includeIf('report.partials.export_toolbar', ['table' => '#reorder_table', 'title' => 'Reorder list'])

        <div class="box box-solid">
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped" id="reorder_table">
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
                        <tr>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->sub_sku ?: $r->sku }}</td>
                            <td class="{{ $r->qty_available <= 0 ? 'text-danger' : '' }}"><strong>{{ @num_format($r->qty_available) }}</strong></td>
                            <td>{{ @num_format($r->alert_quantity) }}</td>
                            <td><strong>{{ @num_format($r->suggest_qty) }}</strong></td>
                            <td>
                                @if(auth()->user()->can('view_purchase_price'))
                                    @format_currency($r->default_purchase_price)
                                @else
                                    —
                                @endif
                            </td>
                            <td>@format_currency($r->sell_price_inc_tax)</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No low-stock products for this location.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
