{{-- Shared modern page banner styling.
     Uses CSS variables already set in layouts/partials/css.blade.php (:root).
     No PHP computation — fully static and cacheable.
--}}
<style>
/* ── Hide legacy content-header ── */
.page-modern > .content-header,
.page-modern > section.content-header { display:none !important; }

/* ── Page Banner ── */
.pg-banner {
    background: linear-gradient(135deg, var(--theme-dark, #312e81), var(--theme-main, #4f46e5));
    padding: 22px 24px 18px;
    border-radius: 0 0 18px 18px;
    margin: -15px -15px 20px;
    position: relative;
    overflow: hidden;
}
.pg-banner::before {
    content:''; position:absolute; top:-30px; right:-30px;
    width:120px; height:120px; border-radius:50%;
    background:rgba(255,255,255,0.06); pointer-events:none;
}
.pg-banner::after {
    content:''; position:absolute; bottom:-50px; right:80px;
    width:160px; height:160px; border-radius:50%;
    background:rgba(255,255,255,0.04); pointer-events:none;
}
.pg-banner-inner {
    display:flex; flex-wrap:wrap; align-items:center;
    justify-content:space-between; gap:14px; position:relative; z-index:1;
}
.pg-banner-title { display:flex; align-items:center; gap:14px; }
.pg-banner-icon {
    width:46px; height:46px; background:rgba(255,255,255,0.15);
    border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.pg-banner-icon i { color:#fff; font-size:20px; }
.pg-banner h1 { color:#fff; font-size:20px; font-weight:700; margin:0; line-height:1.2; }
.pg-banner .pg-subtitle { color:rgba(255,255,255,0.72); font-size:12px; margin:3px 0 0; }
.pg-banner-actions {
    display:flex; flex-wrap:wrap; align-items:center; gap:8px;
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
.pg-glass-btn:hover { background:rgba(255,255,255,0.20); color:#fff; }

/* ── Box / Widget overrides ── */
.page-modern .box {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.page-modern .box.box-primary { border-top-color: var(--theme-main, #4f46e5); }
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

/* ── Responsive ── */
@media (max-width: 640px) {
    .pg-banner { padding: 16px 14px 14px; margin: -10px -10px 16px; }
    .pg-banner h1 { font-size: 16px; }
    .pg-banner-icon { width: 36px; height: 36px; }
    .pg-banner-icon i { font-size: 16px; }
}

@media print {
    .pg-banner { background: var(--theme-main, #4f46e5) !important;
        -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
