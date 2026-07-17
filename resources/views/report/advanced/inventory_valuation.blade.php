@extends('layouts.app')
@section('title', 'Inventory valuation')

@section('css')
@include('layouts.partials.page_modern_css')
<style>
.iv-kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:14px}
@media(min-width:800px){.iv-kpi{grid-template-columns:repeat(3,1fr)}}
.iv-card{border-radius:12px;padding:14px;color:#fff}.iv-card .v{font-size:20px;font-weight:800}.iv-card .l{font-size:11px;font-weight:700;opacity:.9;text-transform:uppercase;margin-top:4px}
.iv-1{background:linear-gradient(135deg,#2563eb,#1d4ed8)}.iv-2{background:linear-gradient(135deg,#059669,#047857)}.iv-3{background:linear-gradient(135deg,#d97706,#b45309)}
.iv-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden}
.iv-sec h3{margin:0;padding:12px 16px;font-size:14px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.iv-sec .body{padding:12px 16px;overflow-x:auto}
table.iv{width:100%;font-size:12px;border-collapse:collapse}table.iv th,table.iv td{padding:7px 8px;border-bottom:1px solid #f1f5f9;text-align:right}
table.iv th:first-child,table.iv td:first-child,table.iv th:nth-child(2),table.iv td:nth-child(2){text-align:left}
table.iv th{font-size:11px;text-transform:uppercase;color:#64748b;background:#f8fafc}
.iv-note{font-size:12px;color:#64748b;margin-bottom:12px}
</style>
@endsection

@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-balance-scale"></i></div>
                <div>
                    <h1>Inventory valuation</h1>
                    <p class="pg-subtitle">Formal stock value at cost &amp; sell price · {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important"><i class="fa fa-th"></i> All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        <p class="iv-note"><i class="fa fa-info-circle"></i> Valuation uses current system qty × default purchase / sell price on the variation (standard retail method). Not full FIFO layer accounting.</p>
        <form method="get" class="no-print" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;align-items:flex-end">
            <div>
                <label style="font-size:12px;font-weight:700;display:block">Location</label>
                <select name="location_id" class="form-control select2" style="min-width:200px">
                    @foreach($business_locations as $id => $name)
                        <option value="{{ $id }}" @if((string)$location_id === (string)$id) selected @endif>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;display:block">As of (label)</label>
                <input type="date" name="as_of" value="{{ $as_of }}" class="form-control">
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;display:block">Sort by</label>
                <select name="basis" class="form-control">
                    <option value="purchase" @if($basis==='purchase') selected @endif>Cost value</option>
                    <option value="sell" @if($basis==='sell') selected @endif>Sell value</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Load</button>
            <button type="button" class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
        </form>
        @includeIf('report.partials.export_toolbar', ['table' => '#iv_export_table', 'title' => 'Inventory valuation '.$as_of])

        <div class="iv-kpi">
            <div class="iv-card iv-1"><div class="v">{{ number_format($totals['qty'], 2) }}</div><div class="l">Total units</div></div>
            <div class="iv-card iv-2"><div class="v">@format_currency((float)$totals['value_cost'])</div><div class="l">Value at cost</div></div>
            <div class="iv-card iv-3"><div class="v">@format_currency((float)$totals['value_sell'])</div><div class="l">Value at sell price</div></div>
        </div>

        <div class="iv-sec">
            <h3>Stock valuation schedule — {{ $as_of }}</h3>
            <div class="body">
                <table class="iv" id="iv_export_table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Qty</th>
                            <th>Unit cost</th>
                            <th>Value (cost)</th>
                            <th>Unit sell</th>
                            <th>Value (sell)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->sub_sku ?: $r->sku }}</td>
                            <td>{{ number_format((float)$r->qty_available, 2) }}</td>
                            <td>@format_currency((float)$r->unit_cost)</td>
                            <td>@format_currency((float)$r->value_cost)</td>
                            <td>@format_currency((float)$r->unit_sell)</td>
                            <td>@format_currency((float)$r->value_sell)</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted" style="text-align:left">No stock at this location</td></tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:800;background:#f8fafc">
                            <td colspan="2" style="text-align:left">Total</td>
                            <td>{{ number_format($totals['qty'], 2) }}</td>
                            <td></td>
                            <td>@format_currency((float)$totals['value_cost'])</td>
                            <td></td>
                            <td>@format_currency((float)$totals['value_sell'])</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
