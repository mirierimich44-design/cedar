@extends('layouts.app')
@section('title', 'Discount & margin abuse')

@section('css')
@include('layouts.partials.page_modern_css')
<style>
.da-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:14px;overflow:hidden}
.da-sec h3{margin:0;padding:12px 16px;font-size:14px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.da-sec .body{padding:12px 16px;overflow-x:auto}
table.da{width:100%;font-size:12px;border-collapse:collapse}table.da th,table.da td{padding:7px 8px;border-bottom:1px solid #f1f5f9;text-align:left}
table.da th{font-size:11px;text-transform:uppercase;color:#64748b}
.da-kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:14px}
@media(min-width:700px){.da-kpi{grid-template-columns:repeat(3,1fr)}}
.da-card{border-radius:12px;padding:14px;color:#fff}.da-card .v{font-size:20px;font-weight:800}.da-card .l{font-size:11px;font-weight:700;opacity:.9;text-transform:uppercase;margin-top:4px}
.da-1{background:linear-gradient(135deg,#dc2626,#b91c1c)}.da-2{background:linear-gradient(135deg,#d97706,#b45309)}.da-3{background:linear-gradient(135deg,#7c3aed,#6d28d9)}
</style>
@endsection

@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-percentage"></i></div>
                <div>
                    <h1>Discount &amp; margin abuse</h1>
                    <p class="pg-subtitle">Invoice &amp; line discounts by cashier · find unusual discounts · {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important"><i class="fa fa-th"></i> All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        @include('report.advanced._filters', [
            'allow_all_locations' => true,
            'extra' => '<div><label style="font-size:12px;font-weight:700;display:block">Min discount %</label><input type="number" step="0.1" name="min_discount_pct" value="'.e($min_discount_pct).'" class="form-control" style="width:100px"></div>'
        ])
        @includeIf('report.partials.export_toolbar', ['table' => '#da_export_table', 'title' => 'Discount abuse'])

        <div class="da-kpi">
            <div class="da-card da-1"><div class="v">@format_currency($totalDiscountValue)</div><div class="l">Invoice discount value</div></div>
            <div class="da-card da-2"><div class="v">{{ $invoiceDiscounts->count() }}</div><div class="l">Discounted invoices</div></div>
            <div class="da-card da-3"><div class="v">{{ $lineDiscounts->count() }}</div><div class="l">Line discounts</div></div>
        </div>

        <div class="da-sec">
            <h3>By cashier</h3>
            <div class="body">
                <table class="da">
                    <thead><tr><th>Cashier</th><th>Invoices</th><th>Discount value</th><th>Sales (final)</th></tr></thead>
                    <tbody>
                    @forelse($byCashier as $c)
                        <tr>
                            <td>{{ $c->cashier }}</td>
                            <td>{{ $c->invoices }}</td>
                            <td>@format_currency((float)$c->discount_value)</td>
                            <td>@format_currency((float)$c->sales)</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">No discount activity</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="da-sec">
            <h3>Invoice-level discounts</h3>
            <div class="body">
                <table class="da" id="da_export_table">
                    <thead>
                        <tr>
                            <th>Date</th><th>Invoice</th><th>Cashier</th><th>Type</th><th>%</th><th>Value</th><th>Final total</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($invoiceDiscounts as $r)
                        <tr>
                            <td>{{ $r->transaction_date }}</td>
                            <td>{{ $r->invoice_no }}</td>
                            <td>{{ $r->cashier ?: '—' }}</td>
                            <td>{{ $r->discount_type ?: 'fixed' }}</td>
                            <td>{{ number_format((float)$r->discount_pct, 1) }}%</td>
                            <td>@format_currency((float)$r->discount_value)</td>
                            <td>@format_currency((float)$r->final_total)</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted">No invoices matched</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="da-sec">
            <h3>Line-level discounts</h3>
            <div class="body">
                <table class="da">
                    <thead>
                        <tr>
                            <th>Date</th><th>Invoice</th><th>Product</th><th>Qty</th><th>Before</th><th>After</th><th>Line disc</th><th>Cashier</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($lineDiscounts as $r)
                        <tr>
                            <td>{{ $r->transaction_date }}</td>
                            <td>{{ $r->invoice_no }}</td>
                            <td>{{ $r->product_name }}</td>
                            <td>{{ $r->quantity }}</td>
                            <td>@format_currency((float)$r->unit_price_before_discount)</td>
                            <td>@format_currency((float)$r->unit_price)</td>
                            <td>{{ $r->line_discount_type }} {{ $r->line_discount_amount }}</td>
                            <td>{{ $r->cashier ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted">No line discounts</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
