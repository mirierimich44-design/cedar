@extends('layouts.app')
@section('title', 'Multi-period dashboard')

@section('css')
@include('layouts.partials.page_modern_css')
<style>
.mp-kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:14px}
@media(min-width:900px){.mp-kpi{grid-template-columns:repeat(5,1fr)}}
.mp-card{border-radius:12px;padding:14px;color:#fff}.mp-card .v{font-size:18px;font-weight:800}.mp-card .l{font-size:11px;font-weight:700;opacity:.9;text-transform:uppercase;margin-top:4px}
.mp-1{background:linear-gradient(135deg,#2563eb,#1d4ed8)}.mp-2{background:linear-gradient(135deg,#7c3aed,#6d28d9)}
.mp-3{background:linear-gradient(135deg,#d97706,#b45309)}.mp-4{background:linear-gradient(135deg,#0d9488,#0f766e)}
.mp-5{background:linear-gradient(135deg,#059669,#047857)}
.mp-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:14px}
.mp-sec h3{margin:0;padding:12px 16px;font-size:14px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.mp-bars{display:flex;align-items:flex-end;gap:8px;height:160px;padding:16px;border-bottom:1px solid #f1f5f9}
.mp-bar-wrap{flex:1;display:flex;flex-direction:column;align-items:center;height:100%;justify-content:flex-end}
.mp-bar{width:100%;max-width:48px;border-radius:6px 6px 0 0;background:linear-gradient(180deg,#6366f1,#4f46e5);min-height:4px}
.mp-bar-lab{font-size:10px;color:#64748b;margin-top:6px;text-align:center}
table.mp{width:100%;font-size:13px;border-collapse:collapse}table.mp th,table.mp td{padding:8px;border-bottom:1px solid #f1f5f9;text-align:right}
table.mp th:first-child,table.mp td:first-child{text-align:left}
table.mp th{font-size:11px;text-transform:uppercase;color:#64748b;background:#f8fafc}
.neg{color:#b91c1c}
</style>
@endsection

@section('content')
@php
    $salesVals = array_column($periods, 'sales');
    $maxSales = max(1, $salesVals ? max($salesVals) : 0);
@endphp
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-chart-bar"></i></div>
                <div>
                    <h1>Multi-period dashboard</h1>
                    <p class="pg-subtitle">Sales, profit &amp; expenses over recent months · {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important"><i class="fa fa-th"></i> All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        @include('report.advanced._filters', ['allow_all_locations' => true, 'showMonths' => true])
        @includeIf('report.partials.export_toolbar', ['table' => '#mp_export_table', 'title' => 'Multi period'])

        <div class="mp-kpi">
            <div class="mp-card mp-1"><div class="v">@format_currency((float)$totals['sales'])</div><div class="l">Sales ({{ $months }} mo)</div></div>
            <div class="mp-card mp-2"><div class="v">@format_currency((float)$totals['purchases'])</div><div class="l">Purchases</div></div>
            <div class="mp-card mp-3"><div class="v">@format_currency((float)$totals['expenses'])</div><div class="l">Expenses</div></div>
            <div class="mp-card mp-4"><div class="v">@format_currency((float)$totals['gross'])</div><div class="l">Gross profit</div></div>
            <div class="mp-card mp-5"><div class="v">@format_currency((float)$totals['net'])</div><div class="l">Net profit</div></div>
        </div>

        <div class="mp-sec">
            <h3>Sales trend</h3>
            <div class="mp-bars">
                @foreach($periods as $p)
                    @php $h = max(4, round(($p['sales'] / $maxSales) * 130)); @endphp
                    <div class="mp-bar-wrap">
                        <div class="mp-bar" style="height:{{ $h }}px" title="{{ number_format($p['sales'],0) }}"></div>
                        <div class="mp-bar-lab">{{ $p['label'] }}</div>
                    </div>
                @endforeach
            </div>
            <div style="padding:12px;overflow-x:auto">
                <table class="mp" id="mp_export_table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Invoices</th>
                            <th>Sales</th>
                            <th>Avg ticket</th>
                            <th>Purchases</th>
                            <th>Expenses</th>
                            <th>Gross</th>
                            <th>Net</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($periods as $p)
                        <tr>
                            <td>{{ $p['label'] }}</td>
                            <td>{{ $p['invoices'] }}</td>
                            <td>@format_currency((float)$p['sales'])</td>
                            <td>@format_currency((float)$p['avg_ticket'])</td>
                            <td>@format_currency((float)$p['purchases'])</td>
                            <td>@format_currency((float)$p['expenses'])</td>
                            <td class="{{ $p['gross'] < 0 ? 'neg' : '' }}">@format_currency((float)$p['gross'])</td>
                            <td class="{{ $p['net'] < 0 ? 'neg' : '' }}">@format_currency((float)$p['net'])</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:800;background:#f8fafc">
                            <td>Total</td>
                            <td>{{ $totals['invoices'] }}</td>
                            <td>@format_currency((float)$totals['sales'])</td>
                            <td>—</td>
                            <td>@format_currency((float)$totals['purchases'])</td>
                            <td>@format_currency((float)$totals['expenses'])</td>
                            <td>@format_currency((float)$totals['gross'])</td>
                            <td>@format_currency((float)$totals['net'])</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
