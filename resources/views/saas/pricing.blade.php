@extends('layouts.auth2')
@section('title', 'Build Your Plan · ' . config('app.name', 'Apex POS'))
@inject('request', 'Illuminate\Http\Request')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
/* ── HARD RESET any wrapping layout bg ─────────────── */
html, body, .right-col, .container-fluid, .row, .row.eq-height-row, .main-wrapper, .content-wrapper, .main-sidebar, .skin-blue {
    background:#f1f5f9 !important;
    margin:0 !important; padding:0 !important;
    border:none !important;
}

.wz {
    position:fixed; inset:0; z-index:9990; overflow-y:auto;
    background:
        radial-gradient(ellipse 800px 500px at 50% -10%, rgba(13,148,136,0.10), transparent 60%),
        linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    color:#0f172a;
    -webkit-font-smoothing:antialiased;
}

/* Colors (CSS vars for clarity) */
.wz {
    --accent:#0d9488;
    --accent-600:#0f766e;
    --accent-soft:#ccfbf1;
    --accent-50:#f0fdfa;
    --ink:#0f172a;
    --ink-2:#334155;
    --muted:#64748b;
    --muted-2:#94a3b8;
    --line:#e2e8f0;
    --line-2:#cbd5e1;
    --surface:#ffffff;
    --surface-2:#f8fafc;
    --success:#10b981;
    --success-soft:#d1fae5;
    --warn:#f59e0b;
    --star:#facc15;
}

/* ── TOP BAR ───────────────────────────────────────── */
.wz-top { position:sticky; top:0; z-index:100; display:flex; align-items:center; justify-content:space-between; padding:14px 32px; background:rgba(255,255,255,0.85); backdrop-filter:blur(14px); border-bottom:1px solid var(--line); }
.wz-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
.wz-brand img { width:32px; height:32px; border-radius:8px; background:white; padding:2px; object-fit:contain; box-shadow:0 1px 3px rgba(0,0,0,.08); }
.wz-brand span { color:var(--ink); font-weight:800; font-size:1rem; letter-spacing:-.01em; }
.wz-top-right { display:flex; gap:18px; align-items:center; font-size:.88rem; }
.wz-top-right a { color:var(--muted); text-decoration:none; font-weight:500; }
.wz-top-right a:hover { color:var(--ink); }
.wz-help { color:var(--accent) !important; font-weight:600; }

/* ── PROGRESS ──────────────────────────────────────── */
.wz-progress { background:#fff; padding:22px 32px 0; border-bottom:1px solid var(--line); }
.wz-pb-bar { position:relative; max-width:880px; margin:0 auto 12px; height:5px; background:var(--line); border-radius:50px; overflow:hidden; }
.wz-pb-fill { position:absolute; left:0; top:0; bottom:0; background:linear-gradient(90deg,#14b8a6,#0d9488); border-radius:50px; width:16.66%; transition:width .4s cubic-bezier(.4,0,.2,1); }
.wz-pb-steps { display:flex; justify-content:space-between; max-width:880px; margin:0 auto; padding:10px 0 18px; }
.wz-pb-step { display:flex; flex-direction:column; align-items:center; gap:6px; flex:1; font-size:.72rem; color:var(--muted-2); font-weight:600; text-align:center; cursor:default; transition:.2s; }
.wz-pb-step.clickable { cursor:pointer; }
.wz-pb-step.clickable:hover { color:var(--ink-2); }
.wz-pb-dot { width:28px; height:28px; border-radius:50%; background:#fff; border:2px solid var(--line-2); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.78rem; color:var(--muted); transition:.3s; }
.wz-pb-step.done .wz-pb-dot { background:var(--accent); border-color:var(--accent); color:#fff; }
.wz-pb-step.done { color:var(--accent); }
.wz-pb-step.active .wz-pb-dot { background:var(--accent); border-color:var(--accent); color:#fff; box-shadow:0 0 0 4px rgba(13,148,136,0.18); }
.wz-pb-step.active { color:var(--ink); }
@media (max-width:640px) { .wz-pb-step span { display:none; } }

/* ── FRAME ─────────────────────────────────────────── */
.wz-frame { max-width:1180px; margin:0 auto; padding:40px 24px 130px; }
.panel { display:none; animation:slideIn .35s ease; }
.panel.active { display:block; }
@keyframes slideIn { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:none;} }

.panel-head { text-align:center; margin-bottom:34px; }
.panel-eyebrow { display:inline-block; color:var(--accent); font-size:.72rem; font-weight:700; letter-spacing:1.6px; text-transform:uppercase; margin-bottom:12px; background:var(--accent-50); padding:5px 12px; border-radius:50px; }
.panel-head h2 { font-size:clamp(1.6rem, 3.2vw, 2.2rem); font-weight:800; color:var(--ink); margin:0 0 10px; letter-spacing:-.02em; line-height:1.2; }
.panel-head p { color:var(--muted); font-size:1rem; max-width:580px; margin:0 auto; line-height:1.6; }

/* ── CHOICE GRID (used by business type + goals) ───── */
.choice-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; max-width:960px; margin:0 auto; }
@media(max-width:780px){ .choice-grid{ grid-template-columns:repeat(2,1fr);} }
@media(max-width:480px){ .choice-grid{ grid-template-columns:1fr;} }
.choice { background:var(--surface); border:2px solid var(--line); border-radius:16px; padding:24px 20px; cursor:pointer; transition:.2s; text-align:center; position:relative; min-height:158px; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.choice:hover { border-color:var(--accent); transform:translateY(-3px); box-shadow:0 10px 30px -12px rgba(13,148,136,.25); }
.choice.selected { border-color:var(--accent); background:var(--accent-50); box-shadow:0 0 0 1px var(--accent); }
.choice.selected::after { content:'✓'; position:absolute; top:12px; right:12px; width:22px; height:22px; background:var(--accent); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:.78rem; }
.choice .ci { font-size:2.2rem; margin-bottom:12px; line-height:1; }
.choice h4 { color:var(--ink); font-size:1rem; font-weight:700; margin:0 0 5px; }
.choice p { color:var(--muted); font-size:.82rem; line-height:1.5; margin:0; }

/* ── STEP HINT ─────────────────────────────────────── */
.hint { text-align:center; margin-top:22px; color:var(--muted); font-size:.85rem; }
.hint a { color:var(--accent); font-weight:600; cursor:pointer; text-decoration:none; }
.hint a:hover { text-decoration:underline; }

/* ── FEATURES LAYOUT ───────────────────────────────── */
.feat-layout { display:grid; grid-template-columns:1fr 340px; gap:28px; align-items:start; max-width:1180px; margin:0 auto; }
@media(max-width:980px){ .feat-layout{ grid-template-columns:1fr;} }

.reco-banner { background:linear-gradient(90deg, var(--accent-50), #fff); border:1px solid var(--accent-soft); border-radius:12px; padding:12px 16px; margin-bottom:18px; color:var(--accent-600); font-size:.88rem; display:flex; align-items:center; gap:10px; }
.reco-banner i { color:var(--accent); }
.reco-banner a { color:var(--accent); text-decoration:underline; cursor:pointer; margin-left:auto; font-weight:600; font-size:.82rem; }
.reco-banner b { color:var(--ink); font-weight:700; }

.cat-box { background:var(--surface); border:1px solid var(--line); border-radius:16px; margin-bottom:14px; overflow:hidden; transition:border-color .2s, box-shadow .2s; }
.cat-box.has-selected { border-color:var(--accent); box-shadow:0 4px 16px -8px rgba(13,148,136,.25); }
.cat-head { display:flex; align-items:center; gap:14px; padding:16px 20px; cursor:pointer; user-select:none; transition:background .15s; }
.cat-head:hover { background:var(--surface-2); }
.cat-icon { width:40px; height:40px; border-radius:10px; background:var(--accent-50); color:var(--accent); display:flex; align-items:center; justify-content:center; font-size:1.05rem; flex-shrink:0; }
.cat-title { flex:1; }
.cat-title b { display:block; color:var(--ink); font-size:.98rem; font-weight:700; }
.cat-title small { color:var(--muted); font-size:.78rem; }
.cat-badge { background:var(--accent); color:#fff; font-size:.65rem; font-weight:800; padding:3px 9px; border-radius:50px; text-transform:uppercase; letter-spacing:.6px; margin-right:8px; display:none; }
.cat-box[data-reco="1"] .cat-badge { display:inline-block; }
.cat-count { color:var(--muted); font-size:.82rem; font-weight:600; margin-right:10px; }
.cat-count.has { color:var(--accent); }
.cat-chevron { color:var(--muted-2); transition:transform .25s; }
.cat-box.open .cat-chevron { transform:rotate(180deg); }
.cat-body { display:none; border-top:1px solid var(--line); }
.cat-box.open .cat-body { display:block; }

.frow { display:flex; align-items:center; padding:14px 20px; gap:14px; border-bottom:1px solid var(--line); cursor:pointer; transition:background .15s; }
.frow:last-child { border-bottom:none; }
.frow:hover { background:var(--surface-2); }
.frow.reco { background:linear-gradient(90deg, var(--accent-50), transparent 40%); }
.fcheck { width:22px; height:22px; border-radius:6px; border:2px solid var(--line-2); display:flex; align-items:center; justify-content:center; background:#fff; transition:.15s; flex-shrink:0; }
.fcheck.checked { background:var(--accent); border-color:var(--accent); }
.fcheck.required { background:var(--accent); border-color:var(--accent); opacity:.85; cursor:not-allowed; }
.fcheck svg { display:none; stroke:#fff; }
.fcheck.checked svg, .fcheck.required svg { display:block; }
.finfo { flex:1; min-width:0; }
.fname { color:var(--ink); font-weight:600; font-size:.93rem; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.fname .reco-tag { background:var(--success-soft); color:#047857; font-size:.62rem; padding:2px 7px; border-radius:50px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; display:none; }
.frow.reco .fname .reco-tag { display:inline-block; }
.fname .inc-tag { color:var(--accent); font-size:.72rem; font-weight:500; }
.fdesc { color:var(--muted); font-size:.8rem; margin-top:3px; line-height:1.5; }
.fprice { color:var(--ink); font-weight:700; font-size:.9rem; min-width:92px; text-align:right; flex-shrink:0; }
.fprice.free { color:var(--success); }
.fprice small { color:var(--muted); font-weight:500; font-size:.72rem; display:block; }

/* ── SIDEBAR SUMMARY ───────────────────────────────── */
.sum-card { background:#fff; border:1px solid var(--line); border-radius:18px; padding:22px; position:sticky; top:170px; box-shadow:0 4px 24px -8px rgba(15,23,42,.08); }
@media(max-width:980px){ .sum-card{ position:static; margin-top:20px;} }
.sum-card h3 { color:var(--ink); font-weight:800; font-size:1.02rem; margin:0 0 16px; display:flex; align-items:center; gap:8px; letter-spacing:-.01em; }
.sum-card h3 i { color:var(--accent); }
.sum-empty { text-align:center; padding:22px 8px; color:var(--muted); font-size:.85rem; line-height:1.6; }
.sum-empty strong { display:block; color:var(--ink); font-weight:700; margin-bottom:4px; font-size:.92rem; }
.sum-list { max-height:260px; overflow-y:auto; margin:0 -4px 12px; padding:0 4px; }
.sum-list::-webkit-scrollbar { width:4px; }
.sum-list::-webkit-scrollbar-thumb { background:var(--line-2); border-radius:2px; }
.sum-item { display:flex; justify-content:space-between; gap:12px; padding:7px 0; font-size:.85rem; color:var(--ink-2); border-bottom:1px solid var(--line); }
.sum-item:last-child { border:none; }
.sum-item b { color:var(--ink); font-weight:700; white-space:nowrap; }
.sum-item-cat { font-size:.68rem; color:var(--muted-2); text-transform:uppercase; letter-spacing:.5px; font-weight:600; padding:10px 0 4px; }
.sum-divider { border:none; border-top:1px dashed var(--line-2); margin:12px 0; }
.sum-total { display:flex; justify-content:space-between; align-items:baseline; }
.sum-total-label { color:var(--ink-2); font-size:.92rem; font-weight:700; }
.sum-total-amount { font-size:1.75rem; font-weight:900; color:var(--accent); line-height:1; letter-spacing:-.02em; }
.sum-cycle-note { text-align:right; color:var(--muted); font-size:.76rem; margin-top:3px; }
.sum-savings { background:var(--success-soft); border:1px solid #6ee7b7; color:#047857; padding:10px 12px; border-radius:10px; font-size:.8rem; margin-top:12px; display:none; font-weight:600; }
.sum-savings.show { display:block; }
.sum-trust { margin-top:16px; display:flex; flex-direction:column; gap:6px; padding-top:14px; border-top:1px solid var(--line); }
.sum-trust div { font-size:.77rem; color:var(--muted); display:flex; align-items:center; gap:8px; }
.sum-trust i { color:var(--success); width:14px; font-size:.8rem; }

/* ── BILLING / HOSTING ─────────────────────────────── */
.billing-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; max-width:940px; margin:0 auto 34px; }
@media(max-width:780px){ .billing-grid{ grid-template-columns:repeat(2,1fr);} }
.bcard { background:#fff; border:2px solid var(--line); border-radius:14px; padding:22px 16px; cursor:pointer; transition:.2s; text-align:center; position:relative; min-height:170px; display:flex; flex-direction:column; justify-content:center; }
.bcard:hover { border-color:var(--accent); transform:translateY(-2px); box-shadow:0 8px 24px -10px rgba(13,148,136,.2); }
.bcard.selected { border-color:var(--accent); background:var(--accent-50); box-shadow:0 0 0 1px var(--accent); }
.bcard.recommended::before { content:'MOST POPULAR'; position:absolute; top:-11px; left:50%; transform:translateX(-50%); background:var(--accent); color:#fff; font-size:.65rem; font-weight:800; letter-spacing:.8px; padding:4px 10px; border-radius:50px; white-space:nowrap; }
.bcard .cycle-label { color:var(--ink); font-weight:800; font-size:1.1rem; margin-bottom:6px; }
.bcard .cycle-save { color:var(--success); font-size:.78rem; font-weight:700; margin-bottom:8px; min-height:18px; }
.bcard .cycle-desc { color:var(--muted); font-size:.78rem; line-height:1.5; }

.hosting-block { max-width:940px; margin:0 auto; }
.hosting-head { color:var(--ink); font-weight:800; font-size:1.02rem; margin-bottom:14px; text-align:center; }
.hosting-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
@media(max-width:600px){ .hosting-grid{ grid-template-columns:1fr;} }
.hcard { background:#fff; border:2px solid var(--line); border-radius:14px; padding:20px; cursor:pointer; transition:.2s; display:flex; gap:14px; align-items:flex-start; }
.hcard:hover { border-color:var(--accent); }
.hcard.selected { border-color:var(--accent); background:var(--accent-50); box-shadow:0 0 0 1px var(--accent); }
.hcard-icon { font-size:1.9rem; line-height:1; }
.hcard h4 { color:var(--ink); margin:0 0 5px; font-size:1rem; font-weight:800; }
.hcard p { color:var(--muted); font-size:.82rem; line-height:1.5; margin:0 0 8px; }
.hcard ul { margin:0; padding-left:18px; font-size:.78rem; color:var(--ink-2); }
.hcard ul li { margin-bottom:3px; }

/* ── REVIEW ────────────────────────────────────────── */
.review-grid { display:grid; grid-template-columns:1fr 360px; gap:24px; align-items:start; max-width:1140px; margin:0 auto; }
@media(max-width:980px){ .review-grid{ grid-template-columns:1fr;} }
.review-block { background:#fff; border:1px solid var(--line); border-radius:16px; padding:22px; margin-bottom:14px; }
.review-block h3 { color:var(--ink); font-weight:800; font-size:1rem; margin:0 0 14px; display:flex; justify-content:space-between; align-items:center; letter-spacing:-.01em; }
.review-block h3 a { color:var(--accent); font-size:.8rem; font-weight:600; text-decoration:none; cursor:pointer; }
.review-block h3 a:hover { text-decoration:underline; }
.review-item { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--line); font-size:.88rem; color:var(--ink-2); }
.review-item:last-child { border:none; }
.review-item b { color:var(--ink); }
.review-kv { display:flex; gap:18px; flex-wrap:wrap; }
.review-kv > div { flex:1; min-width:140px; }
.review-kv small { display:block; color:var(--muted); font-size:.7rem; text-transform:uppercase; letter-spacing:.8px; margin-bottom:4px; font-weight:600; }
.review-kv b { color:var(--ink); font-size:.95rem; }

.faq-wrap summary { padding:14px 0; color:var(--ink); cursor:pointer; list-style:none; display:flex; justify-content:space-between; font-size:.88rem; font-weight:600; border-bottom:1px solid var(--line); }
.faq-wrap summary::-webkit-details-marker { display:none; }
.faq-wrap summary::after { content:'+'; color:var(--accent); font-size:1.2rem; font-weight:300; }
.faq-wrap details[open] summary::after { content:'−'; }
.faq-wrap details p { color:var(--muted); font-size:.85rem; line-height:1.65; padding:12px 0 4px; margin:0; }

/* ── BOTTOM NAV ────────────────────────────────────── */
.wz-nav { position:fixed; bottom:0; left:0; right:0; background:rgba(255,255,255,0.95); backdrop-filter:blur(14px); border-top:1px solid var(--line); padding:14px 32px; display:flex; justify-content:space-between; align-items:center; gap:12px; z-index:50; }
.wz-nav-inner { max-width:1180px; margin:0 auto; width:100%; display:flex; justify-content:space-between; align-items:center; gap:12px; }
.wz-nav-hint { color:var(--muted); font-size:.85rem; }
.wz-nav-hint b { color:var(--ink); }
.wz-nav-hint .dot { color:var(--accent); font-weight:700; }
.btn-nav { padding:13px 26px; border-radius:50px; border:none; font-weight:700; font-size:.92rem; cursor:pointer; transition:.2s; min-height:46px; display:inline-flex; align-items:center; gap:8px; font-family:inherit; }
.btn-next { background:var(--accent); color:#fff; box-shadow:0 6px 18px -4px rgba(13,148,136,.45); }
.btn-next:hover:not(:disabled) { background:var(--accent-600); transform:translateY(-1px); box-shadow:0 10px 24px -6px rgba(13,148,136,.55); }
.btn-next:disabled { opacity:.45; cursor:not-allowed; box-shadow:none; }
.btn-back { background:#fff; color:var(--ink-2); border:1.5px solid var(--line-2); }
.btn-back:hover { background:var(--surface-2); border-color:var(--muted); color:var(--ink); }
.btn-nav.hidden { visibility:hidden; }

/* ── FLASH ─────────────────────────────────────────── */
.flash { max-width:900px; margin:20px auto 0; padding:14px 20px; border-radius:12px; font-weight:600; font-size:.9rem; }
.flash.success { background:var(--success-soft); border:1px solid #6ee7b7; color:#047857; }
.flash.error { background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; }
</style>

<div class="wz">

    {{-- TOP --}}
    <nav class="wz-top">
        <a href="{{ url('/') }}" class="wz-brand">
            <img src="{{ asset('img/logo-small.png') }}" alt="logo">
            <span>{{ config('app.name', 'Apex POS') }}</span>
        </a>
        <div class="wz-top-right">
            <a href="{{ url('/') }}"><i class="fas fa-arrow-left"></i> Home</a>
            <a href="mailto:hello@apexpos.co.ke" class="wz-help"><i class="fas fa-comment-dots"></i> Need help?</a>
        </div>
    </nav>

    {{-- PROGRESS --}}
    <div class="wz-progress">
        <div class="wz-pb-bar"><div class="wz-pb-fill" id="pb-fill"></div></div>
        <div class="wz-pb-steps">
            <div class="wz-pb-step active" data-step="1"><div class="wz-pb-dot">1</div><span>Business</span></div>
            <div class="wz-pb-step" data-step="2"><div class="wz-pb-dot">2</div><span>Core &amp; Stock</span></div>
            <div class="wz-pb-step" data-step="3"><div class="wz-pb-dot">3</div><span>Pharmacy &amp; Reports</span></div>
            <div class="wz-pb-step" data-step="4"><div class="wz-pb-dot">4</div><span>eTIMS &amp; Comms</span></div>
            <div class="wz-pb-step" data-step="5"><div class="wz-pb-dot">5</div><span>Billing</span></div>
            <div class="wz-pb-step" data-step="6"><div class="wz-pb-dot">6</div><span>Review</span></div>
        </div>
    </div>

    @if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash error">{{ session('error') }}</div>@endif

    <div class="wz-frame">

    {{-- ═════ STEP 1 · BUSINESS TYPE ═════ --}}
    <div class="panel active" data-panel="1">
        <div class="panel-head">
            <div class="panel-eyebrow">Step 1 of 6 · 10 seconds</div>
            <h2>What kind of business do you run?</h2>
            <p>Pick the closest match. We'll tailor the modules and recommendations to fit you.</p>
        </div>

        <div class="choice-grid" id="biz-grid">
            <div class="choice" data-biz="retail"      data-cats="core,inventory,reporting,communication">
                <div class="ci">🏪</div><h4>Retail / Shop</h4><p>Supermarket, hardware, electronics, general shop.</p>
            </div>
            <div class="choice" data-biz="pharmacy"    data-cats="core,inventory,pharmacy,reporting,communication">
                <div class="ci">💊</div><h4>Pharmacy</h4><p>Prescriptions, DDA compliance, dispensing.</p>
            </div>
            <div class="choice" data-biz="restaurant"  data-cats="core,inventory,restaurant,reporting">
                <div class="ci">🍽️</div><h4>Restaurant / Cafe</h4><p>Tables, kitchen, menus, bar.</p>
            </div>
            <div class="choice" data-biz="clinic"      data-cats="core,reporting,communication">
                <div class="ci">🏥</div><h4>Clinic / Service</h4><p>Appointments, patient records, invoicing.</p>
            </div>
            <div class="choice" data-biz="distribution" data-cats="core,inventory,reporting">
                <div class="ci">🚚</div><h4>Distribution / Wholesale</h4><p>Multi-branch stock, supplier chain.</p>
            </div>
            <div class="choice" data-biz="other"       data-cats="core,inventory,reporting,communication">
                <div class="ci">🧩</div><h4>Something else</h4><p>Build a custom mix of any modules.</p>
            </div>
        </div>
        <p class="hint">Your pick highlights recommended modules — you can still add or remove anything.</p>
    </div>

    {{-- ═════ STEP 2 · CORE + INVENTORY ═════ --}}
    <div class="panel" data-panel="2">
        <div class="panel-head">
            <div class="panel-eyebrow">Step 2 of 6</div>
            <h2>The essentials.</h2>
            <p>Start with sales &amp; inventory — the foundation every business needs.</p>
        </div>

        <div class="feat-layout">
            <div>
                @include('saas._wizard_reco_banner')
                @include('saas._wizard_category', ['catKey' => 'core'])
                @include('saas._wizard_category', ['catKey' => 'inventory'])
            </div>
            @include('saas._wizard_summary', ['id' => 'sum-2'])
        </div>
    </div>

    {{-- ═════ STEP 3 · PHARMACY + REPORTS ═════ --}}
    <div class="panel" data-panel="3">
        <div class="panel-head">
            <div class="panel-eyebrow">Step 3 of 6</div>
            <h2>Pharmacy &amp; reporting.</h2>
            <p>Regulated dispensing plus insight into your numbers. Skip anything you don't need.</p>
        </div>

        <div class="feat-layout">
            <div>
                @include('saas._wizard_reco_banner')
                @include('saas._wizard_category', ['catKey' => 'pharmacy'])
                @include('saas._wizard_category', ['catKey' => 'reporting'])
            </div>
            @include('saas._wizard_summary', ['id' => 'sum-3'])
        </div>
    </div>

    {{-- ═════ STEP 4 · ETIMS + COMMS + RESTAURANT ═════ --}}
    <div class="panel" data-panel="4">
        <div class="panel-head">
            <div class="panel-eyebrow">Step 4 of 6</div>
            <h2>Compliance, communication &amp; more.</h2>
            <p>KRA eTIMS, SMS, WhatsApp and restaurant tooling — add what applies.</p>
        </div>

        <div class="feat-layout">
            <div>
                @include('saas._wizard_reco_banner')
                @include('saas._wizard_category', ['catKey' => 'communication'])
                @include('saas._wizard_category', ['catKey' => 'restaurant'])
            </div>
            @include('saas._wizard_summary', ['id' => 'sum-4'])
        </div>
    </div>

    {{-- ═════ STEP 5 · BILLING + HOSTING ═════ --}}
    <div class="panel" data-panel="5">
        <div class="panel-head">
            <div class="panel-eyebrow">Step 5 of 6</div>
            <h2>How would you like to pay?</h2>
            <p>Longer cycles = bigger savings. Pick Cloud for zero maintenance, or On-Premise to own it.</p>
        </div>

        <div class="billing-grid">
            <div class="bcard" data-cycle="monthly" onclick="selectCycle('monthly')">
                <div class="cycle-label">Monthly</div><div class="cycle-save">&nbsp;</div><div class="cycle-desc">Pay month to month.</div>
            </div>
            <div class="bcard" data-cycle="quarterly" onclick="selectCycle('quarterly')">
                <div class="cycle-label">Quarterly</div><div class="cycle-save">Save 10%</div><div class="cycle-desc">Billed every 3 months.</div>
            </div>
            <div class="bcard recommended" data-cycle="yearly" onclick="selectCycle('yearly')">
                <div class="cycle-label">Yearly</div><div class="cycle-save">Save 20%</div><div class="cycle-desc">Best value. Billed yearly.</div>
            </div>
            <div class="bcard" data-cycle="once" onclick="selectCycle('once')">
                <div class="cycle-label">One-Off</div><div class="cycle-save">Own it forever</div><div class="cycle-desc">Self-hosted only.</div>
            </div>
        </div>

        <div class="hosting-block">
            <div class="hosting-head">Where do you want it hosted?</div>
            <div class="hosting-grid">
                <div class="hcard selected" data-hosting="cloud" onclick="selectHosting('cloud')">
                    <div class="hcard-icon">☁️</div>
                    <div>
                        <h4>Cloud (Managed)</h4>
                        <p>We host, secure, back up and update. Access from anywhere.</p>
                        <ul><li>No server to buy or maintain</li><li>Daily encrypted backups</li><li>24/7 uptime monitoring</li></ul>
                    </div>
                </div>
                <div class="hcard" data-hosting="self_hosted" onclick="selectHosting('self_hosted')">
                    <div class="hcard-icon">🖥️</div>
                    <div>
                        <h4>On-Premise</h4>
                        <p>We install on your own server or computer. You own the data completely.</p>
                        <ul><li>Works offline</li><li>One-off payment option</li><li>Full data ownership</li></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═════ STEP 6 · REVIEW ═════ --}}
    <div class="panel" data-panel="6">
        <div class="panel-head">
            <div class="panel-eyebrow">Final step</div>
            <h2>Looks good. Ready to go?</h2>
            <p>Here's exactly what you'll get and what you'll pay. Edit anything by clicking the pencil.</p>
        </div>

        <div class="review-grid">
            <div>
                <div class="review-block">
                    <h3>Plan details <a onclick="goStep(5)">✎ Edit</a></h3>
                    <div class="review-kv">
                        <div><small>Business type</small><b id="rv-biz">—</b></div>
                        <div><small>Billing cycle</small><b id="rv-cycle">—</b></div>
                        <div><small>Hosting</small><b id="rv-hosting">—</b></div>
                        <div><small>Activation</small><b>Within 24 hours</b></div>
                    </div>
                </div>

                <div class="review-block">
                    <h3>Features included <a onclick="goStep(2)">✎ Edit</a></h3>
                    <div id="rv-features"></div>
                </div>

                <div class="review-block faq-wrap">
                    <h3>Common questions</h3>
                    <details><summary>What happens after Continue?</summary><p>You'll go to checkout where you give your business details. We confirm payment via M-Pesa, card or bank transfer. Your account is activated within 24 hours with a live demo on your own data before you're billed.</p></details>
                    <details><summary>Can I change features later?</summary><p>Yes — log into your customer portal and toggle any feature. Your next invoice reflects the new total automatically.</p></details>
                    <details><summary>Is my data safe?</summary><p>Cloud data sits on encrypted Kenyan-region servers with daily backups. On-Premise keeps data entirely on your own machine.</p></details>
                    <details><summary>Do you help with setup?</summary><p>Every plan includes onboarding — import of products &amp; stock, printer configuration, and team training in person or online.</p></details>
                </div>
            </div>

            <aside>
                <div class="sum-card">
                    <h3><i class="fas fa-receipt"></i> Your Total</h3>
                    <div id="rv-list" class="sum-list"></div>
                    <hr class="sum-divider">
                    <div class="sum-total">
                        <span class="sum-total-label">Total</span>
                        <div style="text-align:right;">
                            <div class="sum-total-amount" id="rv-total">KES 0</div>
                            <div class="sum-cycle-note" id="rv-cycle-note">per month</div>
                        </div>
                    </div>
                    <div class="sum-savings" id="rv-savings" style="display:none;"></div>
                    <button class="btn-nav btn-next" style="width:100%; margin-top:18px; justify-content:center;" onclick="goToCheckout()">
                        Continue to Checkout <i class="fas fa-arrow-right"></i>
                    </button>
                    <div class="sum-trust">
                        <div><i class="fas fa-shield-alt"></i> Secure — demo before payment</div>
                        <div><i class="fas fa-undo"></i> Cancel anytime, no penalty</div>
                        <div><i class="fas fa-headset"></i> Kenyan support, fast response</div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    </div>{{-- /frame --}}

    {{-- BOTTOM NAV --}}
    <div class="wz-nav">
        <div class="wz-nav-inner">
            <button class="btn-nav btn-back hidden" id="btn-back" onclick="prevStep()"><i class="fas fa-arrow-left"></i> Back</button>
            <div class="wz-nav-hint" id="nav-hint">Pick a business type to continue</div>
            <button class="btn-nav btn-next" id="btn-next" onclick="nextStep()">Continue <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

</div>

<script>
/* ───── STATE ───── */
let currentStep=1, selectedBiz=null, recommendedCats=new Set(), selectedFeatures={}, currentCycle='yearly', currentHosting='cloud';

const BIZ_LABEL = {
    retail:'🏪 Retail / Shop', pharmacy:'💊 Pharmacy', restaurant:'🍽️ Restaurant / Cafe',
    clinic:'🏥 Clinic / Service', distribution:'🚚 Distribution', other:'🧩 Custom'
};

// Preload required features
@foreach($featuresByCategory->flatten() as $feature)
@if($feature->is_required)
selectedFeatures[{{ $feature->id }}] = {
    name:"{{ addslashes($feature->name) }}",
    cat:"{{ $feature->category }}",
    monthly:{{ $feature->price_monthly }},
    quarterly:{{ $feature->price_quarterly }},
    yearly:{{ $feature->price_yearly }},
    once:{{ $feature->price_once }},
    required:true
};
@endif
@endforeach

/* ───── NAV ───── */
function goStep(n) {
    if (n < 1 || n > 6) return;
    // Gate forward movement
    if (n > currentStep && !canAdvanceTo(n)) return;
    currentStep = n;
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.querySelector(`[data-panel="${n}"]`).classList.add('active');
    document.querySelectorAll('.wz-pb-step').forEach(s => {
        const step = parseInt(s.dataset.step);
        s.classList.remove('active','done');
        if (step === n) s.classList.add('active');
        else if (step < n) s.classList.add('done');
        s.classList.toggle('clickable', step < n);
        s.onclick = step < n ? () => goStep(step) : null;
    });
    document.getElementById('pb-fill').style.width = (n*100/6) + '%';
    document.getElementById('btn-back').classList.toggle('hidden', n === 1);
    document.getElementById('btn-next').style.display = n === 6 ? 'none' : 'inline-flex';
    if (n === 6) buildReview();
    // Refresh summaries on active panel
    updateAllSummaries();
    window.scrollTo({ top:0, behavior:'smooth' });
    updateNavState();
}
function canAdvanceTo(n) {
    if (n >= 2 && !selectedBiz) return false;
    if (n >= 6 && Object.keys(selectedFeatures).length === 0) return false;
    return true;
}
function canAdvance() {
    if (currentStep === 1) return !!selectedBiz;
    if (currentStep >= 2 && currentStep <= 4) return Object.keys(selectedFeatures).length > 0;
    if (currentStep === 5) return !!currentCycle && !!currentHosting;
    return true;
}
function nextStep(){ if (canAdvance()) goStep(currentStep+1); }
function prevStep(){ goStep(currentStep-1); }

function updateNavState() {
    const next = document.getElementById('btn-next');
    const hint = document.getElementById('nav-hint');
    const count = Object.keys(selectedFeatures).length;
    next.disabled = !canAdvance();
    if (currentStep === 1) hint.innerHTML = selectedBiz ? `<b>${BIZ_LABEL[selectedBiz]}</b> selected` : 'Pick a business type to continue';
    else if (currentStep >= 2 && currentStep <= 4) hint.innerHTML = count ? `<b>${count}</b> feature${count===1?'':'s'} in your plan <span class="dot">·</span> Total so far: <b>KES ${computeTotal().toLocaleString()}</b>` : 'Pick at least one feature';
    else if (currentStep === 5) hint.innerHTML = `<b>${currentCycle.charAt(0).toUpperCase()+currentCycle.slice(1)}</b> · ${currentHosting === 'cloud' ? '☁️ Cloud' : '🖥️ On-Premise'}`;
    else hint.innerHTML = 'Review your plan, then checkout';
}

/* ───── STEP 1 · BUSINESS TYPE ───── */
document.querySelectorAll('#biz-grid .choice').forEach(c => {
    c.addEventListener('click', () => {
        document.querySelectorAll('#biz-grid .choice').forEach(x => x.classList.remove('selected'));
        c.classList.add('selected');
        selectedBiz = c.dataset.biz;
        recommendedCats = new Set((c.dataset.cats || '').split(',').filter(Boolean));
        applyReco();
        updateNavState();
    });
});

function applyReco() {
    document.querySelectorAll('.cat-box').forEach(b => {
        const cat = b.dataset.cat;
        const isReco = recommendedCats.has(cat);
        b.setAttribute('data-reco', isReco ? '1' : '0');
        if (isReco) b.classList.add('open');
    });
    document.querySelectorAll('.frow').forEach(row => {
        const cat = row.dataset.catKey;
        row.classList.toggle('reco', recommendedCats.has(cat));
    });
    document.querySelectorAll('.reco-banner').forEach(rb => {
        rb.style.display = recommendedCats.size ? 'flex' : 'none';
    });
}
function applyAllRecommended() {
    document.querySelectorAll('.frow.reco').forEach(row => {
        const id = parseInt(row.dataset.featureId);
        if (!selectedFeatures[id]) toggleFeature(id, true);
    });
}

/* ───── STEPS 2–4 · FEATURES ───── */
function toggleCat(headEl) { headEl.parentElement.classList.toggle('open'); }

function toggleFeature(id, forceOn) {
    const row = document.querySelector(`[data-feature-id="${id}"]`);
    if (!row) return;
    const check = document.getElementById(`check-${id}`);
    const priceEl = document.getElementById(`price-${id}`);
    const name = row.querySelector('.fname span.fn').textContent.trim();
    const cat = row.dataset.catKey;

    if (selectedFeatures[id] && !forceOn) {
        if (selectedFeatures[id].required) return;
        delete selectedFeatures[id];
        check.classList.remove('checked');
    } else {
        selectedFeatures[id] = {
            name, cat,
            monthly:parseFloat(priceEl.dataset.monthly),
            quarterly:parseFloat(priceEl.dataset.quarterly),
            yearly:parseFloat(priceEl.dataset.yearly),
            once:parseFloat(priceEl.dataset.once),
            required:false
        };
        check.classList.add('checked');
    }
    updateCatCounters();
    updateAllSummaries();
    updateNavState();
}
function updateCatCounters() {
    document.querySelectorAll('.cat-box').forEach(acc => {
        let count = 0;
        acc.querySelectorAll('.frow').forEach(r => { if (selectedFeatures[r.dataset.featureId]) count++; });
        const el = acc.querySelector('.cat-count');
        if (el) {
            el.textContent = count === 0 ? '0 selected' : `${count} selected`;
            el.classList.toggle('has', count > 0);
        }
        acc.classList.toggle('has-selected', count > 0);
    });
}

/* ───── STEP 5 · BILLING + HOSTING ───── */
function selectCycle(cycle) {
    currentCycle = cycle;
    document.querySelectorAll('.bcard').forEach(b => b.classList.remove('selected'));
    document.querySelector(`.bcard[data-cycle="${cycle}"]`).classList.add('selected');
    if (cycle === 'once') {
        currentHosting = 'self_hosted';
        document.querySelectorAll('.hcard').forEach(h => h.classList.remove('selected'));
        document.querySelector('.hcard[data-hosting="self_hosted"]').classList.add('selected');
    }
    document.querySelectorAll('.fprice').forEach(el => {
        const price = parseFloat(el.dataset[cycle] || el.dataset.monthly);
        el.innerHTML = price === 0 ? 'Free' : 'KES ' + price.toLocaleString() + '<small>' + cycleSuffix(cycle) + '</small>';
        el.classList.toggle('free', price === 0);
    });
    updateAllSummaries();
    updateNavState();
}
function cycleSuffix(c){ return c==='monthly'?'/mo':c==='quarterly'?'/qtr':c==='yearly'?'/yr':' one-off'; }

function selectHosting(type) {
    if (type === 'cloud' && currentCycle === 'once') selectCycle('monthly');
    currentHosting = type;
    document.querySelectorAll('.hcard').forEach(h => h.classList.remove('selected'));
    document.querySelector(`.hcard[data-hosting="${type}"]`).classList.add('selected');
    updateNavState();
}

/* ───── PRICE / SUMMARY ───── */
const CYCLE_NOTE = { monthly:'per month', quarterly:'per quarter', yearly:'per year', once:'one-off payment' };
function computeTotal(){ let t=0; Object.values(selectedFeatures).forEach(f => { t += (f[currentCycle] ?? f.monthly) || 0; }); return t; }
function computeMonthlyBaseline(){ let t=0; Object.values(selectedFeatures).forEach(f => { t += f.monthly || 0; }); return t; }

function updateAllSummaries() {
    ['sum-2','sum-3','sum-4'].forEach(id => renderSummary(id));
}
function renderSummary(domId) {
    const root = document.getElementById(domId);
    if (!root) return;
    const list = root.querySelector('[data-role="list"]');
    const empty = root.querySelector('[data-role="empty"]');
    const divider = root.querySelector('[data-role="divider"]');
    const total = computeTotal();
    const ids = Object.keys(selectedFeatures);

    list.querySelectorAll('.sum-item, .sum-item-cat').forEach(e => e.remove());
    if (ids.length === 0) { empty.style.display='block'; divider.style.display='none'; }
    else {
        empty.style.display='none'; divider.style.display='block';
        // group by cat
        const groups = {};
        ids.forEach(id => { const f = selectedFeatures[id]; (groups[f.cat] = groups[f.cat] || []).push({id, ...f}); });
        Object.entries(groups).forEach(([cat, items]) => {
            const h = document.createElement('div'); h.className='sum-item-cat'; h.textContent = catLabel(cat); list.appendChild(h);
            items.forEach(f => {
                const p = (f[currentCycle] ?? f.monthly) || 0;
                const row = document.createElement('div');
                row.className='sum-item';
                row.innerHTML = `<span>${f.name}</span><b>${p===0?'Free':'KES '+p.toLocaleString()}</b>`;
                list.appendChild(row);
            });
        });
    }
    root.querySelector('[data-role="total"]').textContent = 'KES ' + total.toLocaleString();
    root.querySelector('[data-role="cycle-note"]').textContent = CYCLE_NOTE[currentCycle] || '';
    const sav = root.querySelector('[data-role="savings"]');
    if (currentCycle === 'quarterly' || currentCycle === 'yearly') {
        const monthly = computeMonthlyBaseline(); const months = currentCycle === 'quarterly' ? 3 : 12;
        const saved = (monthly * months) - total;
        if (saved > 0) { sav.textContent = `💰 You save KES ${saved.toLocaleString()} vs monthly`; sav.classList.add('show'); }
        else sav.classList.remove('show');
    } else sav.classList.remove('show');
}
function catLabel(cat) {
    const map = {
        core:'Core', inventory:'Inventory', pharmacy:'Pharmacy / DDA',
        reporting:'Reports & Analytics', communication:'Communication', restaurant:'Restaurant'
    };
    return map[cat] || cat;
}

/* ───── STEP 6 · REVIEW ───── */
function buildReview() {
    document.getElementById('rv-biz').textContent = selectedBiz ? BIZ_LABEL[selectedBiz] : '—';
    document.getElementById('rv-cycle').textContent =
        currentCycle.charAt(0).toUpperCase() + currentCycle.slice(1) +
        (currentCycle === 'quarterly' ? ' (Save 10%)' : currentCycle === 'yearly' ? ' (Save 20%)' : '');
    document.getElementById('rv-hosting').textContent = currentHosting === 'cloud' ? '☁️ Cloud (Managed)' : '🖥️ On-Premise';

    const list = document.getElementById('rv-list');
    const fc = document.getElementById('rv-features');
    list.innerHTML = ''; fc.innerHTML = '';
    const groups = {};
    Object.entries(selectedFeatures).forEach(([id,f]) => { (groups[f.cat]=groups[f.cat]||[]).push(f); });
    Object.entries(groups).forEach(([cat,items]) => {
        // sidebar
        const h = document.createElement('div'); h.className='sum-item-cat'; h.textContent = catLabel(cat); list.appendChild(h);
        // main list header
        const mh = document.createElement('div'); mh.style.cssText='color:#64748b;font-size:.7rem;text-transform:uppercase;letter-spacing:.8px;font-weight:700;padding:12px 0 4px;'; mh.textContent = catLabel(cat); fc.appendChild(mh);
        items.forEach(f => {
            const p = (f[currentCycle] ?? f.monthly) || 0;
            const si = document.createElement('div'); si.className='sum-item'; si.innerHTML=`<span>${f.name}</span><b>${p===0?'Free':'KES '+p.toLocaleString()}</b>`; list.appendChild(si);
            const ri = document.createElement('div'); ri.className='review-item'; ri.innerHTML=`<span>✓ ${f.name}</span><b>${p===0?'Free':'KES '+p.toLocaleString()}</b>`; fc.appendChild(ri);
        });
    });
    const total = computeTotal();
    document.getElementById('rv-total').textContent = 'KES ' + total.toLocaleString();
    document.getElementById('rv-cycle-note').textContent = CYCLE_NOTE[currentCycle] || '';
    const sav = document.getElementById('rv-savings');
    if (currentCycle === 'quarterly' || currentCycle === 'yearly') {
        const monthly = computeMonthlyBaseline(); const months = currentCycle === 'quarterly' ? 3 : 12;
        const saved = (monthly*months) - total;
        if (saved > 0) { sav.textContent = `💰 You save KES ${saved.toLocaleString()} vs monthly`; sav.style.display='block'; }
        else sav.style.display='none';
    } else sav.style.display='none';
}

function goToCheckout() {
    const ids = Object.keys(selectedFeatures).join(',');
    const biz = selectedBiz || '';
    window.location = `{{ route('saas.checkout') }}?feature_ids=${ids}&cycle=${currentCycle}&hosting=${currentHosting}&biz=${biz}`;
}

/* ───── INIT ───── */
selectCycle('yearly');
updateCatCounters();
updateAllSummaries();
updateNavState();
</script>
@endsection
