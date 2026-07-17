@extends('layouts.app')
@section('title', 'Month-end pack')

@section('css')
@include('layouts.partials.page_modern_css')
<style>
.me-kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:14px}
@media(min-width:900px){.me-kpi{grid-template-columns:repeat(4,1fr)}}
.me-card{border-radius:12px;padding:14px 16px;color:#fff;box-shadow:0 2px 8px rgba(0,0,0,.1)}
.me-card .v{font-size:20px;font-weight:800;line-height:1.1}.me-card .l{font-size:11px;font-weight:700;opacity:.9;text-transform:uppercase;margin-top:4px}
.me-s{background:linear-gradient(135deg,#2563eb,#1d4ed8)}.me-g{background:linear-gradient(135deg,#0d9488,#0f766e)}
.me-n{background:linear-gradient(135deg,#059669,#047857)}.me-c{background:linear-gradient(135deg,#dc2626,#b91c1c)}
.me-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:14px;overflow:hidden;page-break-inside:avoid}
.me-sec h3{margin:0;padding:11px 14px;font-size:13px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.me-sec .body{padding:12px 14px}
.me-line{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f1f5f9;font-size:13px}
.me-line:last-child{border-bottom:0}.me-line.tot{font-weight:800}
table.me{width:100%;font-size:12px;border-collapse:collapse}table.me th,table.me td{padding:6px 8px;border-bottom:1px solid #f1f5f9;text-align:left}
table.me th{font-size:10px;text-transform:uppercase;color:#64748b}
.me-meta{font-size:12px;color:#64748b;margin-bottom:12px}
.me-grid2{display:grid;grid-template-columns:1fr;gap:14px}
@media(min-width:900px){.me-grid2{grid-template-columns:1fr 1fr}}
@media print{
  .no-print{display:none!important}
  .me-card,.me-sec h3{-webkit-print-color-adjust:exact;print-color-adjust:exact}
  .page-modern .pg-banner{margin:0 0 10px!important;padding:12px!important;border-radius:0!important}
  .me-sec{break-inside:avoid}
}
</style>
@endsection

@section('content')
@php
    $sales = (float)($pl['total_sell'] ?? 0);
    $gross = (float)($pl['gross_profit'] ?? 0);
    $net = (float)($pl['net_profit'] ?? 0);
    $exp = (float)($pl['total_expense'] ?? 0);
    $payTotal = (float)$paymentsByMethod->sum('total');
@endphp
<div class="page-modern" id="month_end_pack_root">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-file-alt"></i></div>
                <div>
                    <h1>Month-end pack</h1>
                    <p class="pg-subtitle">{{ $businessName }} · {{ $month }} ({{ $start }} → {{ $end }})</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important"><i class="fa fa-th"></i> All reports</a>
            </div>
        </div>
    </div>

    <section class="content">
        <form method="get" class="no-print" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:12px;align-items:flex-end">
            <div>
                <label style="font-size:12px;font-weight:700;display:block">Month</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control" style="min-width:160px">
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;display:block">Location</label>
                <select name="location_id" class="form-control select2" style="min-width:200px">
                    <option value="">All locations</option>
                    @foreach($business_locations as $id => $name)
                        @if((string)$id !== '')
                        <option value="{{ $id }}" @if((string)$location_id === (string)$id) selected @endif>{{ $name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Load</button>
            <button type="button" class="btn btn-danger" onclick="window.print()"><i class="fa fa-file-pdf-o"></i> Print / PDF</button>
        </form>
        @includeIf('report.partials.export_toolbar', ['table' => '#me_export_table', 'title' => 'Month end '.$month])

        <p class="me-meta">Generated {{ $generatedAt }} · One pack for owner close: P&amp;L · stock value · credit · top products · payments</p>

        <div class="me-kpi">
            <div class="me-card me-s"><div class="v">@format_currency($sales)</div><div class="l">Sales</div></div>
            <div class="me-card me-g"><div class="v">@format_currency($gross)</div><div class="l">Gross profit</div></div>
            <div class="me-card me-n"><div class="v">@format_currency($net)</div><div class="l">Net profit</div></div>
            <div class="me-card me-c"><div class="v">@format_currency((float)$credit['total_due'])</div><div class="l">Credit outstanding</div></div>
        </div>

        <div class="me-grid2">
            <div class="me-sec">
                <h3>1. Profit &amp; loss ({{ $month }})</h3>
                <div class="body">
                    <div class="me-line"><span>Sales (exc. tax)</span><span>@format_currency($sales)</span></div>
                    <div class="me-line"><span>COGS</span><span>@format_currency($cogs)</span></div>
                    <div class="me-line"><span>Gross profit</span><span>@format_currency($gross)</span></div>
                    <div class="me-line"><span>Expenses</span><span>@format_currency($exp)</span></div>
                    <div class="me-line"><span>Purchases</span><span>@format_currency((float)($pl['total_purchase'] ?? 0))</span></div>
                    <div class="me-line tot"><span>Net profit</span><span>@format_currency($net)</span></div>
                    <div class="me-line"><span>Opening stock</span><span>@format_currency((float)($pl['opening_stock'] ?? 0))</span></div>
                    <div class="me-line"><span>Closing stock</span><span>@format_currency((float)($pl['closing_stock'] ?? 0))</span></div>
                </div>
            </div>
            <div class="me-sec">
                <h3>2. Stock value (current system qty)</h3>
                <div class="body">
                    <div class="me-line"><span>SKUs with stock</span><span>{{ $stock['skus'] }}</span></div>
                    <div class="me-line"><span>Total units</span><span>{{ number_format($stock['qty'], 2) }}</span></div>
                    <div class="me-line tot"><span>Value at cost</span><span>@format_currency((float)$stock['value_cost'])</span></div>
                    <div class="me-line"><span>Value at sell price</span><span>@format_currency((float)$stock['value_sell'])</span></div>
                    <p class="me-meta" style="margin:8px 0 0">Qty × default purchase / sell price. For full schedule use Inventory valuation.</p>
                </div>
            </div>
        </div>

        <div class="me-grid2">
            <div class="me-sec">
                <h3>3. Customer credit (open invoices)</h3>
                <div class="body">
                    <div class="me-line tot"><span>Total outstanding</span><span>@format_currency((float)$credit['total_due'])</span></div>
                    <div class="me-line"><span>Open invoices</span><span>{{ $credit['invoices'] }}</span></div>
                    <div class="me-line"><span>0–30 days</span><span>@format_currency((float)$credit['age_0_30'])</span></div>
                    <div class="me-line"><span>31–60 days</span><span>@format_currency((float)$credit['age_31_60'])</span></div>
                    <div class="me-line"><span>61–90 days</span><span>@format_currency((float)$credit['age_61_90'])</span></div>
                    <div class="me-line"><span>90+ days</span><span>@format_currency((float)$credit['age_90_plus'])</span></div>
                    <table class="me" style="margin-top:10px">
                        <thead><tr><th>Customer</th><th>Invoice</th><th>Days</th><th>Due</th></tr></thead>
                        <tbody>
                        @forelse($credit['top'] as $r)
                            <tr>
                                <td>{{ $r->customer_name ?: '—' }}</td>
                                <td>{{ $r->invoice_no }}</td>
                                <td>{{ $r->days_overdue }}</td>
                                <td>@format_currency((float)$r->total_due)</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">No credit due</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="me-sec">
                <h3>4. Payments received (month)</h3>
                <div class="body">
                    @forelse($paymentsByMethod as $p)
                        <div class="me-line">
                            <span>{{ ucfirst(str_replace('_',' ', $p->method ?? 'other')) }} ({{ $p->cnt }})</span>
                            <span>@format_currency((float)$p->total)</span>
                        </div>
                    @empty
                        <p class="text-muted">No payments</p>
                    @endforelse
                    <div class="me-line tot"><span>Total payments</span><span>@format_currency($payTotal)</span></div>
                </div>
            </div>
        </div>

        <div class="me-sec">
            <h3>5. Top products sold ({{ $month }})</h3>
            <div class="body table-responsive">
                <table class="me" id="me_export_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Qty</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($topProducts as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->product_name }}</td>
                            <td>{{ $p->sku }}</td>
                            <td>{{ number_format((float)$p->qty, 2) }}</td>
                            <td>@format_currency((float)$p->revenue)</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">No product sales</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <p class="me-meta no-print">Tip: use <strong>Print / PDF</strong> (browser print → Save as PDF) for a single owner pack. For line-by-line recon use Bank / M-Pesa recon.</p>
    </section>
</div>
@endsection
