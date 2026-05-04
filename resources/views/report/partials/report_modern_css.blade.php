{{-- Shared modern report styling - include in @section('css') of any report --}}
@php
    $themeMap = [
        'green'=>['dark'=>'#065f46','main'=>'#059669','light'=>'#10b981'],
        'green-light'=>['dark'=>'#065f46','main'=>'#059669','light'=>'#34d399'],
        'blue'=>['dark'=>'#1e3a5f','main'=>'#2563eb','light'=>'#60a5fa'],
        'blue-light'=>['dark'=>'#1e40af','main'=>'#3b82f6','light'=>'#93c5fd'],
        'red'=>['dark'=>'#991b1b','main'=>'#dc2626','light'=>'#f87171'],
        'purple'=>['dark'=>'#581c87','main'=>'#7c3aed','light'=>'#a78bfa'],
        'primary'=>['dark'=>'#312e81','main'=>'#4f46e5','light'=>'#818cf8'],
        'yellow'=>['dark'=>'#92400e','main'=>'#d97706','light'=>'#fbbf24'],
        'orange'=>['dark'=>'#9a3412','main'=>'#ea580c','light'=>'#fb923c'],
        'sky'=>['dark'=>'#075985','main'=>'#0284c7','light'=>'#38bdf8'],
    ];
    $rpt = $themeMap[session('business.theme_color','primary')] ?? $themeMap['primary'];
@endphp
<style>
    /* ── Hide old content-header ── */
    .report-page-modern .content-header,
    .report-page-modern > section.content-header { display:none !important; }

    /* ── Page Header Banner ── */
    .rpt-banner {
        background:linear-gradient(135deg,{{ $rpt['dark'] }},{{ $rpt['main'] }});
        padding:28px 28px 22px;border-radius:0 0 18px 18px;margin:-15px -15px 24px;
    }
    .rpt-banner-inner {
        display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;
    }
    .rpt-banner-title {
        display:flex;align-items:center;gap:14px;
    }
    .rpt-banner-icon {
        width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;
        display:flex;align-items:center;justify-content:center;
    }
    .rpt-banner-icon i { color:white;font-size:20px; }
    .rpt-banner h1 { color:#fff;font-size:22px;font-weight:700;margin:0; }
    .rpt-banner .rpt-subtitle { color:rgba(255,255,255,0.7);font-size:13px;margin:2px 0 0; }
    .rpt-banner-actions {
        display:flex;flex-wrap:wrap;align-items:center;gap:10px;
    }
    .rpt-glass-btn {
        background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);
        color:#fff;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;
        cursor:pointer;display:flex;align-items:center;gap:6px;white-space:nowrap;
        transition:background .15s;
    }
    .rpt-glass-btn:hover { background:rgba(255,255,255,0.25); }

    /* ── Section Card ── */
    .rpt-card {
        background:#fff;border-radius:14px;box-shadow:0 1px 6px rgba(0,0,0,0.06);
        overflow:hidden;margin-bottom:20px;
    }
    .rpt-card-header {
        padding:14px 16px;display:flex;align-items:center;gap:10px;color:#fff;
        background:linear-gradient(135deg,#475569,#334155);
    }
    .rpt-card-header.rch-green  { background:linear-gradient(135deg,#059669,#047857); }
    .rpt-card-header.rch-blue   { background:linear-gradient(135deg,#2563eb,#1d4ed8); }
    .rpt-card-header.rch-red    { background:linear-gradient(135deg,#dc2626,#b91c1c); }
    .rpt-card-header.rch-purple { background:linear-gradient(135deg,#7c3aed,#6d28d9); }
    .rpt-card-header.rch-amber  { background:linear-gradient(135deg,#d97706,#b45309); }
    .rpt-card-header.rch-theme  { background:linear-gradient(135deg,{{ $rpt['dark'] }},{{ $rpt['main'] }}); }
    .rpt-card-header-icon {
        width:30px;height:30px;background:rgba(255,255,255,0.15);border-radius:8px;
        display:flex;align-items:center;justify-content:center;flex-shrink:0;
    }
    .rpt-card-header-icon i { color:white;font-size:14px; }
    .rpt-card-header h3 { color:white;font-weight:700;font-size:15px;margin:0; }
    .rpt-card-body { padding:16px; }

    /* ── KPI Cards ── */
    .rpt-kpi {
        border-radius:14px;overflow:hidden;position:relative;padding:20px;
        box-shadow:0 4px 12px rgba(0,0,0,0.1);transition:all .2s;
    }
    .rpt-kpi:hover { transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,0.18); }
    .rpt-kpi-circle {
        position:absolute;top:-20px;right:-20px;width:80px;height:80px;
        background:rgba(255,255,255,0.08);border-radius:50%;
    }
    .rpt-kpi-label { font-size:12px;font-weight:500;color:rgba(255,255,255,0.8);margin:0; }
    .rpt-kpi-value {
        margin:6px 0 0;font-size:22px;font-weight:700;color:#fff;
        font-family:ui-monospace,monospace;
    }

    /* ── Tables ── */
    .report-page-modern .table thead th {
        background:#f8fafc;color:#475569;font-size:12px;font-weight:600;
        text-transform:uppercase;letter-spacing:0.04em;padding:10px 14px;
        border-bottom:2px solid #e2e8f0;white-space:nowrap;
    }
    .report-page-modern .table tbody td {
        padding:10px 14px;font-size:13px;color:#334155;
        border-bottom:1px solid #f1f5f9;vertical-align:middle;
    }
    .report-page-modern .table.table-striped > tbody > tr:nth-of-type(odd) > td { background:#fff; }
    .report-page-modern .table.table-striped > tbody > tr:nth-of-type(even) > td { background:#f8fafc; }
    .report-page-modern .table tbody tr:hover > td { background:#f0f9ff !important; }
    .report-page-modern .table.table-bordered,
    .report-page-modern .table.table-bordered th,
    .report-page-modern .table.table-bordered td { border-color:#f1f5f9; }
    .report-page-modern .footer-total td {
        background:#f8fafc !important;font-weight:700;color:#1e293b;
    }

    /* ── Tabs ── */
    .rpt-tabs { list-style:none;border:none !important;margin:0;padding:0 12px;display:flex;flex-wrap:nowrap;gap:0; }
    .rpt-tabs > li > a {
        border:none !important;border-radius:0 !important;
        padding:12px 16px;font-size:13px;font-weight:500;color:#64748b;
        white-space:nowrap;display:flex;align-items:center;gap:6px;
        border-bottom:3px solid transparent !important;transition:all .15s;
    }
    .rpt-tabs > li > a:hover { color:#1e293b;background:#f8fafc; }
    .rpt-tabs > li.active > a,
    .rpt-tabs > li.active > a:hover,
    .rpt-tabs > li.active > a:focus {
        color:{{ $rpt['main'] }} !important;background:#fff !important;
        border-bottom:3px solid {{ $rpt['main'] }} !important;
    }

    /* ── Filters Card ── */
    .rpt-filters .form-group label { font-size:12px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.03em; }
    .rpt-filters .form-control { border-radius:8px;border-color:#e2e8f0;font-size:13px; }

    /* ── Widget overrides ── */
    .report-page-modern .box { border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,0.04); }
    .report-page-modern .small-box { border-radius:12px; }
    .report-page-modern .nav-tabs-custom { border-radius:14px;overflow:hidden;border:1px solid #e2e8f0; }
    .report-page-modern .nav-tabs-custom > .nav-tabs { border:none; }

    @media print {
        .rpt-banner { background:{{ $rpt['main'] }} !important;-webkit-print-color-adjust:exact;print-color-adjust:exact; }
        .no-print { display:none !important; }
    }
</style>
