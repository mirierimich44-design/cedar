@extends('layouts.app')
@section('title', 'Liyoh Top 100 Fast Movers')

@section('content')
<div class="report-page-modern">

    {{-- Banner --}}
    <div class="rpt-banner">
        <div class="rpt-banner-inner">
            <div class="rpt-banner-title">
                <span class="rpt-banner-icon"><i class="fas fa-bolt"></i></span>
                <div>
                    <h1>Liyoh Top 100 Fast Movers &amp; Core Stock</h1>
                    <p class="rpt-subtitle">{{ session()->get('business.name') }} &mdash; {{ now()->format('d M Y, H:i') }}</p>
                </div>
            </div>
            <div class="rpt-banner-actions no-print">
                <span style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);padding:5px 12px;border-radius:8px;font-size:12px;color:rgba(255,255,255,0.85);">
                    <i class="fas fa-robot"></i> AI matched: <strong>{{ $ai_matched_count }}/100</strong>
                </span>
                <button class="rpt-glass-btn" id="btn_refresh_mapping" onclick="refreshAiMapping()">
                    <i class="fas fa-sync-alt"></i> Re-sync AI
                </button>
                <button class="rpt-glass-btn" onclick="exportPDF()">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <button class="rpt-glass-btn" onclick="window.print();">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>

    {{-- Summary KPIs --}}
    <div style="padding:0 12px 20px;">
        <div class="row">
            <div class="col-md-3 col-sm-6" style="margin-bottom:14px;">
                <div class="rpt-kpi" style="background:linear-gradient(135deg,#dc2626,#b91c1c)">
                    <div class="rpt-kpi-circle"></div>
                    <p class="rpt-kpi-label"><i class="fas fa-exclamation-circle"></i> Out of Stock</p>
                    <p class="rpt-kpi-value">{{ $out_count }}</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6" style="margin-bottom:14px;">
                <div class="rpt-kpi" style="background:linear-gradient(135deg,#d97706,#b45309)">
                    <div class="rpt-kpi-circle"></div>
                    <p class="rpt-kpi-label"><i class="fas fa-exclamation-triangle"></i> Low Stock — Reorder</p>
                    <p class="rpt-kpi-value">{{ $low_count }}</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6" style="margin-bottom:14px;">
                <div class="rpt-kpi" style="background:linear-gradient(135deg,#059669,#047857)">
                    <div class="rpt-kpi-circle"></div>
                    <p class="rpt-kpi-label"><i class="fas fa-check-circle"></i> Adequate Stock</p>
                    <p class="rpt-kpi-value">{{ $ok_count }}</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6" style="margin-bottom:14px;">
                <div class="rpt-kpi" style="background:linear-gradient(135deg,#4f46e5,#312e81)">
                    <div class="rpt-kpi-circle"></div>
                    <p class="rpt-kpi-label"><i class="fas fa-database"></i> Matched in System</p>
                    <p class="rpt-kpi-value">{{ $found_count }} / 100</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Reorder Alert --}}
    @if(($out_count + $low_count) > 0)
    <div style="padding:0 12px 16px;" class="no-print">
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-bell" style="color:#dc2626;font-size:16px;"></i>
            <span style="color:#991b1b;font-size:13px;font-weight:600;">
                {{ $out_count + $low_count }} item(s) need restocking — {{ $out_count }} out of stock, {{ $low_count }} below reorder level.
            </span>
        </div>
    </div>
    @endif

    {{-- Tier sections --}}
    @php $counter = 1; @endphp
    @foreach($tiers as $tier)
    <div style="padding:0 12px;margin-bottom:20px;">
        <div class="rpt-card">
            <div class="rpt-card-header {{ $tier['color_class'] }}">
                <span class="rpt-card-header-icon"><i class="fas {{ $tier['icon'] }}"></i></span>
                <h3>{{ $tier['label'] }} <span style="font-weight:400;font-size:13px;opacity:.8;">({{ $tier['range'] }})</span></h3>
                @php
                    $tier_reorder = collect($tier['items'])->where('reorder', true)->count();
                @endphp
                @if($tier_reorder > 0)
                <span style="margin-left:auto;background:rgba(255,255,255,0.25);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                    <i class="fas fa-exclamation-triangle"></i> {{ $tier_reorder }} to reorder
                </span>
                @endif
            </div>
            <div class="rpt-card-body" style="padding:12px;">
                <table class="table table-bordered table-sm fm-table" style="margin:0;">
                    <thead>
                        <tr>
                            <th style="width:36px;">#</th>
                            <th>Product</th>
                            <th style="width:110px;text-align:right;">Qty in Stock</th>
                            <th style="width:130px;text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tier['items'] as $item)
                        <tr class="{{ $item['reorder'] ? 'fm-reorder-row' : '' }}">
                            <td style="color:#94a3b8;font-size:12px;text-align:center;">{{ $counter++ }}</td>
                            <td>
                                <span class="fm-name">{{ $item['name'] }}</span>
                                @if($item['reorder'])
                                    <span class="fm-reorder-badge no-print">Reorder</span>
                                @endif
                            </td>
                            <td style="text-align:right;font-weight:600;font-family:monospace;">
                                @if($item['qty'] === null)
                                    <span style="color:#94a3b8;font-size:12px;">—</span>
                                @else
                                    {{ number_format($item['qty'], 0) }}
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($item['status'] === 'out_of_stock')
                                    <span class="fm-badge fm-badge-danger">Out of Stock</span>
                                @elseif($item['status'] === 'low')
                                    <span class="fm-badge fm-badge-warning">Low — Reorder</span>
                                @elseif($item['status'] === 'ok')
                                    <span class="fm-badge fm-badge-success">Adequate</span>
                                @else
                                    <span class="fm-badge fm-badge-muted">Not in System</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Print footer --}}
    <div class="print-only" style="text-align:center;padding:10px;font-size:11px;color:#64748b;border-top:1px solid #e2e8f0;margin-top:10px;">
        Liyoh Healthcare — Top 100 Fast Movers &amp; Core Stock &mdash; Generated {{ now()->format('d M Y H:i') }} &mdash; {{ session()->get('business.name') }}
    </div>

</div>
@endsection

@section('css')
@includeIf('report.partials.report_modern_css')
<style>
    .fm-table thead th {
        background:#f8fafc;color:#475569;font-size:11px;font-weight:700;
        text-transform:uppercase;letter-spacing:0.04em;padding:8px 12px;border-bottom:2px solid #e2e8f0;
    }
    .fm-table td { padding:7px 12px;font-size:13px;vertical-align:middle; }
    .fm-table tr:hover td { background:#f8fafc; }
    .fm-reorder-row td { background:#fffbeb !important; }
    .fm-name { font-weight:500;color:#1e293b; }
    .fm-reorder-badge {
        display:inline-block;margin-left:6px;font-size:10px;font-weight:700;
        background:#fef3c7;color:#92400e;border:1px solid #fcd34d;
        padding:1px 6px;border-radius:10px;vertical-align:middle;
        text-transform:uppercase;letter-spacing:0.04em;
    }
    .fm-badge {
        display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;
    }
    .fm-badge-danger  { background:#fee2e2;color:#991b1b;border:1px solid #fca5a5; }
    .fm-badge-warning { background:#fef3c7;color:#92400e;border:1px solid #fcd34d; }
    .fm-badge-success { background:#d1fae5;color:#065f46;border:1px solid #6ee7b7; }
    .fm-badge-muted   { background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0; }

    @media print {
        .no-print  { display:none !important; }
        .print-only { display:block !important; }
        .rpt-card  { box-shadow:none !important;border:1px solid #e2e8f0;break-inside:avoid; }
        .rpt-banner { border-radius:0 !important;margin:0 0 12px !important;-webkit-print-color-adjust:exact;print-color-adjust:exact; }
        .fm-table tbody tr { break-inside:avoid; }
        .fm-badge, .rpt-kpi { -webkit-print-color-adjust:exact;print-color-adjust:exact; }
        .fm-reorder-row td { background:#fffbeb !important;-webkit-print-color-adjust:exact;print-color-adjust:exact; }
    }
    .print-only { display:none; }
</style>
@endsection

@section('javascript')
<script>
function exportPDF() {
    document.title = 'Liyoh Fast Movers - {{ now()->format("Y-m-d") }}';
    window.print();
}

function refreshAiMapping() {
    var btn = document.getElementById('btn_refresh_mapping');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing…';

    $.ajax({
        method: 'POST',
        url: '/purchases/refresh-fast-movers-mapping',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
            if (res.success) {
                toastr.success('AI matched ' + res.matched + '/' + res.total + ' products. Reloading…');
                setTimeout(function(){ location.reload(); }, 1500);
            } else {
                toastr.error('AI sync failed: ' + res.msg);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Re-sync AI';
            }
        },
        error: function(xhr) {
            toastr.error('AI sync error. Check API key.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync-alt"></i> Re-sync AI';
        }
    });
}
</script>
@endsection
