@extends('layouts.app')
@section('title', 'Supplier payables')
@section('css')
@include('layouts.partials.page_modern_css')
<style>
.sp-kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:14px}
@media(min-width:900px){.sp-kpi{grid-template-columns:repeat(5,1fr)}}
.sp-card{border-radius:12px;padding:12px 14px;color:#fff}.sp-card .v{font-size:18px;font-weight:800}.sp-card .l{font-size:10px;font-weight:700;opacity:.9;text-transform:uppercase;margin-top:4px}
.sp-1{background:linear-gradient(135deg,#7c3aed,#6d28d9)}.sp-2{background:linear-gradient(135deg,#059669,#047857)}
.sp-3{background:linear-gradient(135deg,#d97706,#b45309)}.sp-4{background:linear-gradient(135deg,#ea580c,#c2410c)}
.sp-5{background:linear-gradient(135deg,#dc2626,#b91c1c)}
.sp-sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden}
.sp-sec h3{margin:0;padding:12px 16px;font-size:14px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.sp-sec .body{padding:12px 16px;overflow-x:auto}
table.sp{width:100%;font-size:12px;border-collapse:collapse}table.sp th,table.sp td{padding:7px 8px;border-bottom:1px solid #f1f5f9;text-align:left}
table.sp th{font-size:10px;text-transform:uppercase;color:#64748b}
</style>
@endsection
@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-file-invoice"></i></div>
                <div>
                    <h1>Supplier payables</h1>
                    <p class="pg-subtitle">Who we still owe · ageing 30 / 60 / 90 · {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important">All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        @include('report.advanced._filters', ['allow_all_locations' => true])
        @includeIf('report.partials.export_toolbar', ['table' => '#sp_export_table', 'title' => 'Supplier payables'])

        <div class="sp-kpi">
            <div class="sp-card sp-1"><div class="v">@format_currency((float)$totals['total_due'])</div><div class="l">Total due</div></div>
            <div class="sp-card sp-2"><div class="v">@format_currency((float)$totals['age_0_30'])</div><div class="l">0–30 days</div></div>
            <div class="sp-card sp-3"><div class="v">@format_currency((float)$totals['age_31_60'])</div><div class="l">31–60</div></div>
            <div class="sp-card sp-4"><div class="v">@format_currency((float)$totals['age_61_90'])</div><div class="l">61–90</div></div>
            <div class="sp-card sp-5"><div class="v">@format_currency((float)$totals['age_90_plus'])</div><div class="l">90+</div></div>
        </div>

        <div class="sp-sec">
            <h3>{{ $totals['count'] }} open purchase bills</h3>
            <div class="body">
                <table class="sp" id="sp_export_table">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Ref</th>
                            <th>Date</th>
                            <th>Days</th>
                            <th>Bill</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r->supplier_name ?: '—' }} <small class="text-muted">{{ $r->mobile }}</small></td>
                            <td>{{ $r->ref_no }}</td>
                            <td>{{ $r->transaction_date }}</td>
                            <td>{{ $r->days_overdue }}</td>
                            <td>@format_currency((float)$r->final_total)</td>
                            <td>@format_currency((float)$r->total_paid)</td>
                            <td><strong>@format_currency((float)$r->total_due)</strong></td>
                            <td>{{ $r->payment_status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted">No supplier balances due</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <p class="text-muted" style="font-size:12px">Ageing is from purchase date to today. Filter dates above are not applied to open bills (open AP is current). Location filter applies.</p>
    </section>
</div>
@endsection
