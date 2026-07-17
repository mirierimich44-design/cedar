@extends('layouts.app')
@section('title', 'Financial statements')

@section('css')
@include('layouts.partials.page_modern_css')
<style>
.fs-kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:16px}
@media(min-width:900px){.fs-kpi{grid-template-columns:repeat(4,1fr)}}
.fs-card{border-radius:12px;padding:14px 16px;color:#fff;box-shadow:0 2px 8px rgba(0,0,0,.1)}
.fs-card .v{font-size:20px;font-weight:800}.fs-card .l{font-size:11px;font-weight:700;opacity:.9;text-transform:uppercase;margin-top:4px}
.fs-s{background:linear-gradient(135deg,#2563eb,#1d4ed8)}.fs-g{background:linear-gradient(135deg,#0d9488,#0f766e)}
.fs-n{background:linear-gradient(135deg,#059669,#047857)}.fs-e{background:linear-gradient(135deg,#d97706,#b45309)}
.fs-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:14px;overflow:hidden}
.fs-sec h3{margin:0;padding:12px 16px;font-size:14px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.fs-sec .body{padding:12px 16px}
.fs-line{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:13px}
.fs-line:last-child{border-bottom:0}.fs-line.tot{font-weight:800;background:#f8fafc;margin:0 -16px;padding:10px 16px}
.fs-note{font-size:12px;color:#64748b;margin:0 0 12px}
</style>
@endsection

@section('content')
@php
    $pl = $pl ?? [];
    $sales = (float)($pl['total_sell'] ?? 0);
    $gross = (float)($pl['gross_profit'] ?? 0);
    $net = (float)($pl['net_profit'] ?? 0);
    $exp = (float)($pl['total_expense'] ?? 0);
@endphp
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <h1>Financial statements</h1>
                    <p class="pg-subtitle">Trading P&amp;L · simplified balance sheet · cash movement · {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important"><i class="fa fa-th"></i> All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        <p class="fs-note"><i class="fa fa-info-circle"></i> Management statements from POS data — not a full statutory set of books. Use with your accountant for KRA filing.</p>
        @include('report.advanced._filters', ['allow_all_locations' => true])
        @includeIf('report.partials.export_toolbar', ['table' => '#fs_export_table', 'title' => 'Financial statements'])

        <div class="fs-kpi">
            <div class="fs-card fs-s"><div class="v">@format_currency($sales)</div><div class="l">Sales</div></div>
            <div class="fs-card fs-g"><div class="v">@format_currency($gross)</div><div class="l">Gross profit</div></div>
            <div class="fs-card fs-n"><div class="v">@format_currency($net)</div><div class="l">Net profit</div></div>
            <div class="fs-card fs-e"><div class="v">@format_currency($exp)</div><div class="l">Expenses</div></div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="fs-sec">
                    <h3>Income statement ({{ $start }} → {{ $end }})</h3>
                    <div class="body">
                        <div class="fs-line"><span>Sales (exc. tax)</span><span>@format_currency($sales)</span></div>
                        <div class="fs-line"><span>COGS</span><span>@format_currency($cogs)</span></div>
                        <div class="fs-line"><span>Gross profit</span><span>@format_currency($gross)</span></div>
                        <div class="fs-line"><span>Expenses</span><span>@format_currency($exp)</span></div>
                        <div class="fs-line"><span>Stock adjustments</span><span>@format_currency((float)($pl['total_adjustment'] ?? 0))</span></div>
                        <div class="fs-line tot"><span>Net profit</span><span>@format_currency($net)</span></div>
                        <p class="fs-note" style="margin-top:10px">Opening stock @format_currency((float)($pl['opening_stock'] ?? 0)) · Closing @format_currency((float)($pl['closing_stock'] ?? 0)) · Purchases @format_currency((float)($pl['total_purchase'] ?? 0))</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="fs-sec">
                    <h3>Simplified balance sheet (as of {{ $end }})</h3>
                    <div class="body">
                        <div class="fs-line"><span>Closing stock</span><span>@format_currency((float)($bs['closing_stock'] ?? 0))</span></div>
                        <div class="fs-line"><span>Customer receivables</span><span>@format_currency((float)($bs['customer_due'] ?? 0))</span></div>
                        <div class="fs-line"><span>Cash / bank accounts</span><span>@format_currency((float)($bs['cash_total'] ?? 0))</span></div>
                        <div class="fs-line tot"><span>Total assets (approx)</span><span>@format_currency((float)($bs['total_assets'] ?? 0))</span></div>
                        <div class="fs-line"><span>Supplier payables</span><span>@format_currency((float)($bs['supplier_due'] ?? 0))</span></div>
                        <div class="fs-line tot"><span>Equity / net assets (approx)</span><span>@format_currency((float)($bs['equity_approx'] ?? 0))</span></div>
                        @if(!empty($bs['account_balances']) && count($bs['account_balances']))
                        <hr>
                        <strong style="font-size:12px">Account balances</strong>
                        @foreach($bs['account_balances'] as $a)
                            <div class="fs-line"><span>{{ $a->name }}</span><span>@format_currency((float)$a->balance)</span></div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="fs-sec">
            <h3>Cash movement (payments in period)</h3>
            <div class="body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Inflows</strong>
                        @forelse(($cash['in'] ?? []) as $row)
                            <div class="fs-line"><span>{{ ucfirst(str_replace('_',' ', $row->method ?? 'other')) }} ({{ $row->tx_type ?? '' }})</span><span>@format_currency((float)$row->total)</span></div>
                        @empty
                            <p class="text-muted">No inflow rows</p>
                        @endforelse
                        <div class="fs-line tot"><span>Total in</span><span>@format_currency((float)($cash['in_total'] ?? 0))</span></div>
                    </div>
                    <div class="col-md-6">
                        <strong>Outflows</strong>
                        @forelse(($cash['out'] ?? []) as $row)
                            <div class="fs-line"><span>{{ ucfirst(str_replace('_',' ', $row->method ?? 'other')) }} ({{ $row->tx_type ?? '' }})</span><span>@format_currency((float)$row->total)</span></div>
                        @empty
                            <p class="text-muted">No outflow rows</p>
                        @endforelse
                        <div class="fs-line tot"><span>Total out</span><span>@format_currency((float)($cash['out_total'] ?? 0))</span></div>
                    </div>
                </div>
            </div>
        </div>

        <table id="fs_export_table" class="table table-bordered" style="display:none">
            <thead><tr><th>Section</th><th>Metric</th><th>Amount</th></tr></thead>
            <tbody>
                <tr><td>P&L</td><td>Sales</td><td>{{ number_format($sales,2,'.','') }}</td></tr>
                <tr><td>P&L</td><td>COGS</td><td>{{ number_format($cogs,2,'.','') }}</td></tr>
                <tr><td>P&L</td><td>Gross profit</td><td>{{ number_format($gross,2,'.','') }}</td></tr>
                <tr><td>P&L</td><td>Net profit</td><td>{{ number_format($net,2,'.','') }}</td></tr>
                <tr><td>BS</td><td>Closing stock</td><td>{{ number_format((float)($bs['closing_stock']??0),2,'.','') }}</td></tr>
                <tr><td>BS</td><td>Receivables</td><td>{{ number_format((float)($bs['customer_due']??0),2,'.','') }}</td></tr>
                <tr><td>BS</td><td>Payables</td><td>{{ number_format((float)($bs['supplier_due']??0),2,'.','') }}</td></tr>
                <tr><td>BS</td><td>Equity approx</td><td>{{ number_format((float)($bs['equity_approx']??0),2,'.','') }}</td></tr>
            </tbody>
        </table>
    </section>
</div>
@endsection
