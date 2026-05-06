{{-- Shared modern page styling — include in @section('css') @parent ... @endsection --}}
@php
    $themeMap = [
        'green'       => ['dark'=>'#065f46','main'=>'#059669','light'=>'#10b981'],
        'green-light' => ['dark'=>'#065f46','main'=>'#059669','light'=>'#34d399'],
        'blue'        => ['dark'=>'#1e3a5f','main'=>'#2563eb','light'=>'#60a5fa'],
        'blue-light'  => ['dark'=>'#1e40af','main'=>'#3b82f6','light'=>'#93c5fd'],
        'red'         => ['dark'=>'#991b1b','main'=>'#dc2626','light'=>'#f87171'],
        'purple'      => ['dark'=>'#581c87','main'=>'#7c3aed','light'=>'#a78bfa'],
        'primary'     => ['dark'=>'#312e81','main'=>'#4f46e5','light'=>'#818cf8'],
        'yellow'      => ['dark'=>'#92400e','main'=>'#d97706','light'=>'#fbbf24'],
        'orange'      => ['dark'=>'#9a3412','main'=>'#ea580c','light'=>'#fb923c'],
        'sky'         => ['dark'=>'#075985','main'=>'#0284c7','light'=>'#38bdf8'],
    ];
    $pg = $themeMap[session('business.theme_color','primary')] ?? $themeMap['primary'];
@endphp
<style>
/* ── Hide legacy content-header ── */
.page-modern > .content-header,
.page-modern > section.content-header { display:none !important; }

/* ── Page Banner ── */
.pg-banner {
    background: linear-gradient(135deg, {{ $pg['dark'] }}, {{ $pg['main'] }});
    padding: 22px 24px 18px;
    border-radius: 0 0 18px 18px;
    margin: -15px -15px 20px;
    position: relative;
    overflow: hidden;
}
.pg-banner::before {
    content:'';position:absolute;top:-30px;right:-30px;
    width:120px;height:120px;border-radius:50%;
    background:rgba(255,255,255,0.06);pointer-events:none;
}
.pg-banner::after {
    content:'';position:absolute;bottom:-50px;right:80px;
    width:160px;height:160px;border-radius:50%;
    background:rgba(255,255,255,0.04);pointer-events:none;
}
.pg-banner-inner {
    display:flex;flex-wrap:wrap;align-items:center;
    justify-content:space-between;gap:14px;position:relative;z-index:1;
}
.pg-banner-title { display:flex;align-items:center;gap:14px; }
.pg-banner-icon {
    width:46px;height:46px;background:rgba(255,255,255,0.15);
    border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.pg-banner-icon i { color:#fff;font-size:20px; }
.pg-banner h1 { color:#fff;font-size:20px;font-weight:700;margin:0;line-height:1.2; }
.pg-banner .pg-subtitle { color:rgba(255,255,255,0.72);font-size:12px;margin:3px 0 0; }
.pg-banner-actions {
    display:flex;flex-wrap:wrap;align-items:center;gap:8px;
}

/* Add / action buttons in banner */
.pg-add-btn {
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.30);
    color: #fff;
    padding: 7px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    transition: background .15s, transform .1s;
    text-decoration: none;
}
.pg-add-btn:hover, .pg-add-btn:focus {
    background: rgba(255,255,255,0.28);
    color: #fff;
    text-decoration: none;
    transform: translateY(-1px);
}
.pg-glass-btn {
    background: rgba(255,255,255,0.10);
    border: 1px solid rgba(255,255,255,0.20);
    color: rgba(255,255,255,0.85);
    padding: 7px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    transition: background .15s;
    text-decoration: none;
}
.pg-glass-btn:hover { background:rgba(255,255,255,0.20);color:#fff; }

/* ── Page wrapper ── */
.page-modern { min-height: 100%; }

/* ── Box / Widget overrides ── */
.page-modern .box {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.page-modern .box.box-primary { border-top-color: {{ $pg['main'] }}; }
.page-modern .box-header {
    border-radius: 12px 12px 0 0;
    padding: 12px 16px;
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
}
.page-modern .box-header .box-title {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}
.page-modern .box-body { padding: 0; }
.page-modern .box .table-responsive { border-radius: 0 0 12px 12px; overflow: hidden; }

/* ── Filters box ── */
.page-modern .filters-box,
.page-modern .box.filters-box {
    border-radius: 12px !important;
    margin-bottom: 16px;
}

/* ── DataTable / Tables ── */
.page-modern table.dataTable thead th,
.page-modern .table thead th {
    background: #f8fafc !important;
    color: #475569 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    border-bottom: 2px solid #e2e8f0 !important;
    padding: 10px 14px !important;
    white-space: nowrap;
}
.page-modern table.dataTable tbody tr:hover td,
.page-modern .table tbody tr:hover td { background: #f0f9ff !important; }
.page-modern table.dataTable tbody td,
.page-modern .table tbody td {
    font-size: 13px;
    color: #374151;
    padding: 10px 14px !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #f1f5f9 !important;
}
.page-modern table.dataTable tfoot td,
.page-modern .table tfoot td {
    background: #f8fafc;
    font-size: 12px;
    font-weight: 700;
    padding: 10px 14px !important;
    border-top: 2px solid #e2e8f0 !important;
    color: #1e293b;
}
.page-modern table.dataTable.table-bordered,
.page-modern table.dataTable.table-bordered th,
.page-modern table.dataTable.table-bordered td,
.page-modern .table.table-bordered,
.page-modern .table.table-bordered th,
.page-modern .table.table-bordered td { border-color: #e2e8f0 !important; }

/* ── DataTables pagination ── */
.page-modern .dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 6px !important;
    font-size: 12px !important;
}
.page-modern .dataTables_wrapper .dataTables_info { font-size: 12px; color: #9ca3af; }
.page-modern .dataTables_wrapper .dataTables_length select { border-radius: 6px; }

/* ── Filter form labels ── */
.page-modern .box.filters-box .form-group label,
.page-modern .box-body .form-group label {
    font-size: 11px;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

/* ── Responsive ── */
@media (max-width: 640px) {
    .pg-banner { padding: 16px 14px 14px; margin: -10px -10px 16px; }
    .pg-banner h1 { font-size: 16px; }
    .pg-banner-icon { width: 36px; height: 36px; }
    .pg-banner-icon i { font-size: 16px; }
}

@media print {
    .pg-banner { background: {{ $pg['main'] }} !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
