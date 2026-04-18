@extends('layouts.auth2')
@section('title', 'Build Your Plan · ' . config('app.name', 'Apex POS'))
@inject('request', 'Illuminate\Http\Request')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
html, body { background:#0a1628 !important; margin:0; padding:0; }
.right-col, .container-fluid, .row.eq-height-row { padding:0 !important; margin:0 !important; }

.wz {
    position:fixed; inset:0; z-index:9990; overflow-y:auto;
    background:
        radial-gradient(ellipse at top, rgba(13,148,136,0.15), transparent 55%),
        linear-gradient(rgba(5,15,35,0.96), rgba(5,20,45,0.98));
    background-color:#0a1628;
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
    color:white;
}

/* ── TOP BAR ───────────────────────────────────────── */
.wz-top { position:sticky; top:0; z-index:100; display:flex; align-items:center; justify-content:space-between; padding:14px 32px; background:rgba(5,15,35,0.85); backdrop-filter:blur(14px); border-bottom:1px solid rgba(255,255,255,0.08); }
.wz-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
.wz-brand img { width:32px; height:32px; border-radius:8px; background:white; padding:3px; object-fit:contain; }
.wz-brand span { color:white; font-weight:800; font-size:1rem; }
.wz-top-right { display:flex; gap:18px; align-items:center; font-size:.85rem; }
.wz-top-right a { color:rgba(255,255,255,0.65); text-decoration:none; }
.wz-top-right a:hover { color:white; }
.wz-help { color:#5eead4 !important; font-weight:600; }

/* ── PROGRESS BAR ──────────────────────────────────── */
.wz-progress { background:rgba(0,0,0,0.25); padding:20px 32px 0; border-bottom:1px solid rgba(255,255,255,0.05); }
.wz-pb-bar { position:relative; max-width:720px; margin:0 auto 10px; height:6px; background:rgba(255,255,255,0.08); border-radius:50px; }
.wz-pb-fill { position:absolute; left:0; top:0; bottom:0; background:linear-gradient(90deg,#5eead4,#0d9488); border-radius:50px; width:25%; transition:width .35s cubic-bezier(.4,0,.2,1); }
.wz-pb-steps { display:flex; justify-content:space-between; max-width:720px; margin:0 auto; padding:14px 0 18px; }
.wz-pb-step { display:flex; flex-direction:column; align-items:center; gap:6px; flex:1; font-size:.78rem; color:rgba(255,255,255,0.5); font-weight:600; text-align:center; }
.wz-pb-dot { width:30px; height:30px; border-radius:50%; background:rgba(255,255,255,0.08); border:2px solid rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.82rem; transition:.3s; }
.wz-pb-step.done .wz-pb-dot { background:#0d9488; border-color:#0d9488; color:white; }
.wz-pb-step.done { color:#5eead4; }
.wz-pb-step.active .wz-pb-dot { background:#0d9488; border-color:#5eead4; color:white; box-shadow:0 0 0 4px rgba(94,234,212,0.2); }
.wz-pb-step.active { color:white; }

/* ── CONTENT FRAME ─────────────────────────────────── */
.wz-frame { max-width:1180px; margin:0 auto; padding:36px 24px 120px; }
.panel { display:none; animation:slideIn .35s ease; }
.panel.active { display:block; }
@keyframes slideIn { from{opacity:0; transform:translateY(8px);} to{opacity:1; transform:none;} }

.panel-head { text-align:center; margin-bottom:32px; }
.panel-eyebrow { color:#5eead4; font-size:.78rem; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-bottom:10px; }
.panel-head h2 { font-size:clamp(1.6rem, 3.2vw, 2.3rem); font-weight:900; color:white; margin:0 0 10px; letter-spacing:-.02em; }
.panel-head p { color:rgba(255,255,255,0.65); font-size:1rem; max-width:560px; margin:0 auto; line-height:1.6; }

/* ── STEP 1 — GOALS ────────────────────────────────── */
.goals-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; max-width:880px; margin:0 auto; }
@media(max-width:780px){ .goals-grid{ grid-template-columns:repeat(2,1fr);} }
@media(max-width:480px){ .goals-grid{ grid-template-columns:1fr;} }
.goal { background:rgba(15,23,42,0.7); border:2px solid rgba(255,255,255,0.1); border-radius:18px; padding:26px 22px; cursor:pointer; transition:.2s; text-align:center; position:relative; min-height:150px; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.goal:hover { border-color:rgba(13,148,136,0.5); transform:translateY(-3px); background:rgba(15,23,42,0.95); }
.goal.selected { border-color:#0d9488; background:rgba(13,148,136,0.15); box-shadow:0 0 0 1px #0d9488; }
.goal.selected::after { content:'✓'; position:absolute; top:12px; right:12px; width:24px; height:24px; background:#0d9488; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:.85rem; }
.goal-icon { font-size:2.4rem; margin-bottom:12px; }
.goal h4 { color:white; font-size:1.02rem; font-weight:800; margin:0 0 6px; }
.goal p { color:rgba(255,255,255,0.6); font-size:.82rem; line-height:1.5; margin:0; }
.goal-hint { text-align:center; margin-top:24px; color:rgba(255,255,255,0.5); font-size:.85rem; }
.goal-hint b { color:#5eead4; }

/* ── STEP 2 — FEATURES ─────────────────────────────── */
.feat-layout { display:grid; grid-template-columns:1fr 340px; gap:28px; align-items:start; }
@media(max-width:900px){ .feat-layout{ grid-template-columns:1fr;} }

.reco-banner { background:linear-gradient(90deg, rgba(13,148,136,0.2), rgba(13,148,136,0.05)); border:1px solid rgba(13,148,136,0.35); border-radius:14px; padding:14px 18px; margin-bottom:20px; color:#5eead4; font-size:.88rem; display:flex; align-items:center; gap:10px; }
.reco-banner i { font-size:1.1rem; }
.reco-banner a { color:#5eead4; text-decoration:underline; cursor:pointer; margin-left:auto; font-weight:600; font-size:.82rem; }

.cat-accordion { background:rgba(15,23,42,0.6); border:1px solid rgba(255,255,255,0.08); border-radius:16px; margin-bottom:14px; overflow:hidden; transition:border-color .2s; }
.cat-accordion.has-selected { border-color:rgba(13,148,136,0.4); }
.cat-head { display:flex; align-items:center; gap:14px; padding:16px 20px; cursor:pointer; user-select:none; transition:background .2s; }
.cat-head:hover { background:rgba(255,255,255,0.04); }
.cat-icon { width:38px; height:38px; border-radius:10px; background:rgba(13,148,136,0.18); color:#5eead4; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
.cat-title { flex:1; }
.cat-title b { display:block; color:white; font-size:.98rem; font-weight:700; }
.cat-title small { color:rgba(255,255,255,0.5); font-size:.78rem; }
.cat-badge { background:rgba(13,148,136,0.2); color:#5eead4; font-size:.7rem; font-weight:700; padding:3px 9px; border-radius:50px; text-transform:uppercase; letter-spacing:.5px; margin-right:8px; }
.cat-count { color:rgba(255,255,255,0.5); font-size:.82rem; font-weight:600; margin-right:10px; }
.cat-chevron { color:rgba(255,255,255,0.4); transition:transform .2s; }
.cat-accordion.open .cat-chevron { transform:rotate(180deg); }
.cat-body { display:none; border-top:1px solid rgba(255,255,255,0.06); }
.cat-accordion.open .cat-body { display:block; }

.frow { display:flex; align-items:center; padding:14px 20px; gap:14px; border-bottom:1px solid rgba(255,255,255,0.04); cursor:pointer; transition:background .15s; }
.frow:last-child { border-bottom:none; }
.frow:hover { background:rgba(255,255,255,0.03); }
.frow.reco { background:linear-gradient(90deg, rgba(13,148,136,0.08), transparent 40%); }
.fcheck { width:24px; height:24px; border-radius:7px; border:2px solid rgba(255,255,255,0.25); display:flex; align-items:center; justify-content:center; background:transparent; transition:.15s; flex-shrink:0; }
.fcheck.checked { background:#0d9488; border-color:#0d9488; }
.fcheck.required { background:#0d9488; border-color:#0d9488; opacity:.85; cursor:not-allowed; }
.fcheck svg { display:none; stroke:white; }
.fcheck.checked svg, .fcheck.required svg { display:block; }
.finfo { flex:1; min-width:0; }
.fname { color:white; font-weight:600; font-size:.95rem; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.fname .reco-tag { background:rgba(16,185,129,0.2); color:#34d399; font-size:.65rem; padding:2px 7px; border-radius:50px; font-weight:700; letter-spacing:.3px; text-transform:uppercase; }
.fname .inc-tag { color:#5eead4; font-size:.75rem; font-weight:500; }
.fdesc { color:rgba(255,255,255,0.5); font-size:.8rem; margin-top:3px; line-height:1.45; }
.fprice { color:white; font-weight:700; font-size:.92rem; min-width:95px; text-align:right; flex-shrink:0; }
.fprice.free { color:#34d399; }

/* ── SIDEBAR / SUMMARY ─────────────────────────────── */
.sum-card { background:rgba(15,23,42,0.85); border:1px solid rgba(255,255,255,0.12); border-radius:20px; padding:24px; position:sticky; top:170px; backdrop-filter:blur(10px); }
@media(max-width:900px){ .sum-card{ position:static; margin-top:20px;} }
.sum-card h3 { color:white; font-weight:800; font-size:1.1rem; margin:0 0 18px; display:flex; align-items:center; gap:8px; }
.sum-card h3 i { color:#5eead4; }
.sum-empty { text-align:center; padding:24px 10px; color:rgba(255,255,255,0.4); font-size:.85rem; font-style:italic; line-height:1.6; }
.sum-empty strong { display:block; color:rgba(255,255,255,0.7); font-style:normal; font-weight:700; margin-bottom:6px; font-size:.95rem; }
.sum-list { max-height:260px; overflow-y:auto; margin:0 -4px 14px; padding:0 4px; }
.sum-list::-webkit-scrollbar { width:4px; }
.sum-list::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.15); border-radius:2px; }
.sum-item { display:flex; justify-content:space-between; gap:12px; padding:7px 0; font-size:.85rem; color:rgba(255,255,255,0.85); border-bottom:1px solid rgba(255,255,255,0.05); }
.sum-item:last-child { border:none; }
.sum-item b { color:white; font-weight:700; white-space:nowrap; }
.sum-divider { border:none; border-top:1px dashed rgba(255,255,255,0.12); margin:14px 0; }
.sum-total { display:flex; justify-content:space-between; align-items:baseline; }
.sum-total-label { color:rgba(255,255,255,0.7); font-size:.9rem; font-weight:600; }
.sum-total-amount { font-size:1.8rem; font-weight:900; color:#5eead4; line-height:1; }
.sum-cycle-note { text-align:right; color:rgba(255,255,255,0.5); font-size:.78rem; margin-top:3px; }
.sum-savings { background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.3); color:#34d399; padding:10px 12px; border-radius:10px; font-size:.8rem; margin-top:14px; display:none; font-weight:600; }
.sum-savings.show { display:block; }
.sum-trust { margin-top:18px; display:flex; flex-direction:column; gap:7px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.08); }
.sum-trust div { font-size:.78rem; color:rgba(255,255,255,0.6); display:flex; align-items:center; gap:8px; }
.sum-trust i { color:#34d399; width:14px; }

/* ── STEP 3 — BILLING/HOSTING ──────────────────────── */
.billing-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; max-width:920px; margin:0 auto 32px; }
@media(max-width:780px){ .billing-grid{ grid-template-columns:repeat(2,1fr);} }
.bcard { background:rgba(15,23,42,0.7); border:2px solid rgba(255,255,255,0.1); border-radius:16px; padding:22px 18px; cursor:pointer; transition:.2s; text-align:center; position:relative; min-height:170px; display:flex; flex-direction:column; justify-content:center; }
.bcard:hover { border-color:rgba(13,148,136,0.4); transform:translateY(-2px); }
.bcard.selected { border-color:#0d9488; background:rgba(13,148,136,0.15); box-shadow:0 0 0 1px #0d9488; }
.bcard.recommended::before { content:'MOST POPULAR'; position:absolute; top:-11px; left:50%; transform:translateX(-50%); background:#0d9488; color:white; font-size:.65rem; font-weight:800; letter-spacing:.8px; padding:4px 10px; border-radius:50px; white-space:nowrap; }
.bcard .cycle-label { color:white; font-weight:800; font-size:1.1rem; margin-bottom:8px; }
.bcard .cycle-save { color:#34d399; font-size:.78rem; font-weight:700; margin-bottom:10px; min-height:18px; }
.bcard .cycle-desc { color:rgba(255,255,255,0.55); font-size:.78rem; line-height:1.5; }

.hosting-block { max-width:920px; margin:0 auto; }
.hosting-head { color:white; font-weight:800; font-size:1.05rem; margin-bottom:16px; text-align:center; }
.hosting-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media(max-width:600px){ .hosting-grid{ grid-template-columns:1fr;} }
.hcard { background:rgba(15,23,42,0.7); border:2px solid rgba(255,255,255,0.1); border-radius:16px; padding:22px; cursor:pointer; transition:.2s; display:flex; gap:14px; align-items:flex-start; }
.hcard:hover { border-color:rgba(13,148,136,0.4); }
.hcard.selected { border-color:#0d9488; background:rgba(13,148,136,0.12); box-shadow:0 0 0 1px #0d9488; }
.hcard-icon { font-size:2rem; }
.hcard h4 { color:white; margin:0 0 6px; font-size:1rem; font-weight:800; }
.hcard p { color:rgba(255,255,255,0.6); font-size:.82rem; line-height:1.5; margin:0 0 8px; }
.hcard ul { margin:0; padding-left:18px; font-size:.78rem; color:rgba(255,255,255,0.55); }
.hcard ul li { margin-bottom:3px; }

/* ── STEP 4 — REVIEW ───────────────────────────────── */
.review-grid { display:grid; grid-template-columns:1fr 360px; gap:28px; align-items:start; max-width:1100px; margin:0 auto; }
@media(max-width:900px){ .review-grid{ grid-template-columns:1fr;} }
.review-block { background:rgba(15,23,42,0.65); border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:24px; margin-bottom:16px; }
.review-block h3 { color:white; font-weight:800; font-size:1rem; margin:0 0 14px; display:flex; justify-content:space-between; align-items:center; }
.review-block h3 a { color:#5eead4; font-size:.8rem; font-weight:600; text-decoration:none; cursor:pointer; }
.review-item { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.05); font-size:.88rem; color:rgba(255,255,255,0.85); }
.review-item:last-child { border:none; }
.review-item b { color:white; }
.review-kv { display:flex; gap:18px; flex-wrap:wrap; }
.review-kv > div { flex:1; min-width:140px; }
.review-kv small { display:block; color:rgba(255,255,255,0.5); font-size:.72rem; text-transform:uppercase; letter-spacing:.8px; margin-bottom:4px; font-weight:600; }
.review-kv b { color:white; font-size:.95rem; }

.faq-wrap summary { padding:14px 0; color:white; cursor:pointer; list-style:none; display:flex; justify-content:space-between; font-size:.88rem; font-weight:600; border-bottom:1px solid rgba(255,255,255,0.06); }
.faq-wrap summary::-webkit-details-marker { display:none; }
.faq-wrap summary::after { content:'+'; color:#5eead4; font-size:1.2rem; font-weight:300; }
.faq-wrap details[open] summary::after { content:'−'; }
.faq-wrap details p { color:rgba(255,255,255,0.65); font-size:.85rem; line-height:1.65; padding:12px 0 4px; margin:0; }

/* ── NAV BUTTONS ───────────────────────────────────── */
.wz-nav { position:fixed; bottom:0; left:0; right:0; background:rgba(5,15,35,0.95); backdrop-filter:blur(14px); border-top:1px solid rgba(255,255,255,0.08); padding:14px 32px; display:flex; justify-content:space-between; align-items:center; gap:12px; z-index:50; }
.wz-nav-inner { max-width:1180px; margin:0 auto; width:100%; display:flex; justify-content:space-between; align-items:center; gap:12px; }
.wz-nav-hint { color:rgba(255,255,255,0.55); font-size:.82rem; }
.wz-nav-hint b { color:#5eead4; }
.btn-nav { padding:14px 28px; border-radius:50px; border:none; font-weight:800; font-size:.92rem; cursor:pointer; transition:.2s; min-height:48px; display:inline-flex; align-items:center; gap:8px; }
.btn-next { background:#0d9488; color:white; box-shadow:0 6px 18px rgba(13,148,136,0.4); }
.btn-next:hover:not(:disabled) { background:#0f766e; transform:translateY(-1px); }
.btn-next:disabled { opacity:.35; cursor:not-allowed; }
.btn-back { background:transparent; color:rgba(255,255,255,0.7); border:1.5px solid rgba(255,255,255,0.2); }
.btn-back:hover { background:rgba(255,255,255,0.06); color:white; }
.btn-nav.hidden { visibility:hidden; }

/* ── FLASH ─────────────────────────────────────────── */
.flash { max-width:900px; margin:20px auto 0; padding:14px 20px; border-radius:12px; font-weight:600; }
.flash.success { background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.4); color:#34d399; }
.flash.error { background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.4); color:#fca5a5; }
</style>

<div class="wz">

    {{-- TOP BAR --}}
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

    {{-- PROGRESS BAR --}}
    <div class="wz-progress">
        <div class="wz-pb-bar"><div class="wz-pb-fill" id="pb-fill"></div></div>
        <div class="wz-pb-steps">
            <div class="wz-pb-step active" data-step="1"><div class="wz-pb-dot">1</div><span>Your goals</span></div>
            <div class="wz-pb-step" data-step="2"><div class="wz-pb-dot">2</div><span>Pick features</span></div>
            <div class="wz-pb-step" data-step="3"><div class="wz-pb-dot">3</div><span>Billing</span></div>
            <div class="wz-pb-step" data-step="4"><div class="wz-pb-dot">4</div><span>Review</span></div>
        </div>
    </div>

    @if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash error">{{ session('error') }}</div>@endif

    <div class="wz-frame">

    {{-- ═════ STEP 1: GOALS ═════ --}}
    <div class="panel active" data-panel="1">
        <div class="panel-head">
            <div class="panel-eyebrow">Step 1 of 4 · 30 seconds</div>
            <h2>What do you want your system to do?</h2>
            <p>Pick up to 3 goals. We'll recommend the right features — you can still add or remove anything later.</p>
        </div>

        <div class="goals-grid">
            <div class="goal" data-goal="sales" data-cats="core,sales">
                <div class="goal-icon">💰</div>
                <h4>Sell &amp; get paid</h4>
                <p>POS, invoices, M-Pesa, card payments.</p>
            </div>
            <div class="goal" data-goal="stock" data-cats="inventory">
                <div class="goal-icon">📦</div>
                <h4>Track stock</h4>
                <p>Inventory, suppliers, transfers, barcodes.</p>
            </div>
            <div class="goal" data-goal="pharmacy" data-cats="pharmacy">
                <div class="goal-icon">💊</div>
                <h4>Run a pharmacy</h4>
                <p>DDA, prescriptions, patient records.</p>
            </div>
            <div class="goal" data-goal="restaurant" data-cats="restaurant">
                <div class="goal-icon">🍽️</div>
                <h4>Run a restaurant</h4>
                <p>Tables, KOT, split bills, menus.</p>
            </div>
            <div class="goal" data-goal="comms" data-cats="communication">
                <div class="goal-icon">📨</div>
                <h4>Reach customers</h4>
                <p>SMS, WhatsApp receipts, email invoices.</p>
            </div>
            <div class="goal" data-goal="reports" data-cats="reporting">
                <div class="goal-icon">📈</div>
                <h4>See clear reports</h4>
                <p>Dashboards, P&amp;L, sales analytics.</p>
            </div>
        </div>
        <p class="goal-hint">Selected <b id="goal-count">0</b> of 3 · <span id="goal-skip" style="cursor:pointer;text-decoration:underline;">Skip and browse all features →</span></p>
    </div>

    {{-- ═════ STEP 2: FEATURES ═════ --}}
    <div class="panel" data-panel="2">
        <div class="panel-head">
            <div class="panel-eyebrow">Step 2 of 4</div>
            <h2>Pick the features you need.</h2>
            <p>Add any module. Your total updates in real time on the right.</p>
        </div>

        <div class="feat-layout">
            <div>
                <div class="reco-banner" id="reco-banner" style="display:none;">
                    <i class="fas fa-magic"></i>
                    <span><b>Based on your goals</b>, we've highlighted recommended features.</span>
                    <a id="apply-reco">Auto-select all →</a>
                </div>

                @foreach($categories as $catKey => $catMeta)
                    @if($featuresByCategory->has($catKey))
                    @php
                        $isCore = $catKey === 'core';
                        $items = $featuresByCategory[$catKey];
                    @endphp
                    <div class="cat-accordion {{ $isCore ? 'open' : '' }}" data-cat="{{ $catKey }}">
                        <div class="cat-head" onclick="toggleCat(this)">
                            <div class="cat-icon"><i class="fas {{ $catMeta['icon'] }}"></i></div>
                            <div class="cat-title">
                                <b>{{ $catMeta['label'] }}</b>
                                <small>{{ $items->count() }} module{{ $items->count() === 1 ? '' : 's' }}</small>
                            </div>
                            <span class="cat-badge" style="display:none;" data-reco-badge>Recommended</span>
                            <span class="cat-count" data-cat-count="{{ $catKey }}">0 selected</span>
                            <i class="fas fa-chevron-down cat-chevron"></i>
                        </div>
                        <div class="cat-body">
                            @foreach($items as $feature)
                            <div class="frow" data-feature-id="{{ $feature->id }}" data-cat-key="{{ $catKey }}"
                                 onclick="{{ $feature->is_required ? '' : 'toggleFeature(' . $feature->id . ')' }}">
                                <div class="fcheck {{ $feature->is_required ? 'checked required' : '' }}" id="check-{{ $feature->id }}">
                                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                                        <path d="M2 6.5L5 9.5L11 3.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="finfo">
                                    <div class="fname">
                                        <span>{{ $feature->name }}</span>
                                        @if($feature->is_required)<span class="inc-tag">· Included</span>@endif
                                        <span class="reco-tag" style="display:none;" data-reco-tag>Recommended</span>
                                    </div>
                                    @if($feature->description)<div class="fdesc">{{ $feature->description }}</div>@endif
                                </div>
                                <div class="fprice {{ $feature->price_monthly == 0 ? 'free' : '' }}"
                                     id="price-{{ $feature->id }}"
                                     data-monthly="{{ $feature->price_monthly }}"
                                     data-quarterly="{{ $feature->price_quarterly }}"
                                     data-yearly="{{ $feature->price_yearly }}"
                                     data-once="{{ $feature->price_once }}">
                                    {{ $feature->price_monthly == 0 ? 'Free' : 'KES ' . number_format($feature->price_monthly, 0) }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            {{-- SIDEBAR --}}
            <aside>
                <div class="sum-card">
                    <h3><i class="fas fa-shopping-basket"></i> Your Plan</h3>
                    <div id="sum-list" class="sum-list">
                        <div class="sum-empty" id="sum-empty">
                            <strong>Let's build your plan.</strong>
                            Tick any feature on the left and see your price appear here.
                        </div>
                    </div>
                    <hr class="sum-divider" id="sum-divider" style="display:none;">
                    <div class="sum-total">
                        <span class="sum-total-label">Total</span>
                        <div style="text-align:right;">
                            <div class="sum-total-amount" id="sum-total">KES 0</div>
                            <div class="sum-cycle-note" id="sum-cycle-note">per month</div>
                        </div>
                    </div>
                    <div class="sum-savings" id="sum-savings"></div>
                    <div class="sum-trust">
                        <div><i class="fas fa-check"></i> No commitment. Change anytime.</div>
                        <div><i class="fas fa-check"></i> Demo before you pay.</div>
                        <div><i class="fas fa-check"></i> Local Kenyan support.</div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    {{-- ═════ STEP 3: BILLING + HOSTING ═════ --}}
    <div class="panel" data-panel="3">
        <div class="panel-head">
            <div class="panel-eyebrow">Step 3 of 4</div>
            <h2>How would you like to pay?</h2>
            <p>Longer cycles = bigger savings. Pick Cloud for zero-maintenance, or On-Premise to own it.</p>
        </div>

        <div class="billing-grid">
            <div class="bcard" data-cycle="monthly" onclick="selectCycle('monthly')">
                <div class="cycle-label">Monthly</div>
                <div class="cycle-save">&nbsp;</div>
                <div class="cycle-desc">Pay month to month.</div>
            </div>
            <div class="bcard" data-cycle="quarterly" onclick="selectCycle('quarterly')">
                <div class="cycle-label">Quarterly</div>
                <div class="cycle-save">Save 10%</div>
                <div class="cycle-desc">Billed every 3 months.</div>
            </div>
            <div class="bcard recommended" data-cycle="yearly" onclick="selectCycle('yearly')">
                <div class="cycle-label">Yearly</div>
                <div class="cycle-save">Save 20%</div>
                <div class="cycle-desc">Best value. Billed yearly.</div>
            </div>
            <div class="bcard" data-cycle="once" onclick="selectCycle('once')">
                <div class="cycle-label">One-Off</div>
                <div class="cycle-save">Self-hosted only</div>
                <div class="cycle-desc">Buy once, own forever.</div>
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
                        <ul>
                            <li>No server to buy or maintain</li>
                            <li>Daily encrypted backups</li>
                            <li>24/7 uptime monitoring</li>
                        </ul>
                    </div>
                </div>
                <div class="hcard" data-hosting="self_hosted" onclick="selectHosting('self_hosted')">
                    <div class="hcard-icon">🖥️</div>
                    <div>
                        <h4>On-Premise</h4>
                        <p>We install on your own server or computer. You own the data completely.</p>
                        <ul>
                            <li>Works offline</li>
                            <li>One-off payment option</li>
                            <li>Full data ownership</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═════ STEP 4: REVIEW ═════ --}}
    <div class="panel" data-panel="4">
        <div class="panel-head">
            <div class="panel-eyebrow">Final step</div>
            <h2>Looks good. Ready to go?</h2>
            <p>Here's exactly what you'll get and what you'll pay. Change anything by clicking Edit.</p>
        </div>

        <div class="review-grid">
            <div>
                <div class="review-block">
                    <h3>Plan details <a onclick="goStep(3)">Edit</a></h3>
                    <div class="review-kv">
                        <div><small>Billing cycle</small><b id="rv-cycle">—</b></div>
                        <div><small>Hosting</small><b id="rv-hosting">—</b></div>
                        <div><small>Activation</small><b>Within 24 hours</b></div>
                    </div>
                </div>

                <div class="review-block">
                    <h3>Features included <a onclick="goStep(2)">Edit</a></h3>
                    <div id="rv-features"></div>
                </div>

                <div class="review-block faq-wrap">
                    <h3>Common questions</h3>
                    <details><summary>What happens after I click Continue?</summary><p>You'll go to checkout where you give your business details. We then confirm payment via M-Pesa, card or bank transfer. Your account is activated within 24 hours with a live demo on your own data before you're billed.</p></details>
                    <details><summary>Can I change features later?</summary><p>Yes — log into your customer portal and toggle any feature. Your next invoice will reflect the new total automatically.</p></details>
                    <details><summary>Is my data safe?</summary><p>Cloud is on encrypted Kenyan-region servers with daily backups. On-premise keeps data entirely on your own machine. Either way, nobody outside your team can see it.</p></details>
                    <details><summary>Do you help with setup and training?</summary><p>Every plan includes onboarding. We import your existing products, customers and opening stock, set up receipt printers, and train your team in person or online.</p></details>
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
                    <button class="btn-nav btn-next" id="final-cta" style="width:100%; margin-top:18px; justify-content:center;" onclick="goToCheckout()">
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
            <button class="btn-nav btn-back hidden" id="btn-back" onclick="prevStep()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <div class="wz-nav-hint" id="nav-hint">Pick up to 3 goals to continue</div>
            <button class="btn-nav btn-next" id="btn-next" onclick="nextStep()">
                Continue <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

</div>

<script>
/* ─────────────────────────────────────────────────────
   STATE
───────────────────────────────────────────────────── */
let currentStep      = 1;
let selectedGoals    = new Set();
let recommendedCats  = new Set();
let recommendedFeats = new Set();
let selectedFeatures = {};
let currentCycle     = 'yearly';
let currentHosting   = 'cloud';

// Preload required features
@foreach($featuresByCategory->flatten() as $feature)
@if($feature->is_required)
selectedFeatures[{{ $feature->id }}] = {
    name: "{{ addslashes($feature->name) }}",
    cat: "{{ $feature->category }}",
    monthly: {{ $feature->price_monthly }},
    quarterly: {{ $feature->price_quarterly }},
    yearly: {{ $feature->price_yearly }},
    once: {{ $feature->price_once }},
    required: true
};
@endif
@endforeach

/* ─────────────────────────────────────────────────────
   STEP NAVIGATION
───────────────────────────────────────────────────── */
function goStep(n) {
    if (n < 1 || n > 4) return;
    currentStep = n;
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.querySelector(`[data-panel="${n}"]`).classList.add('active');
    document.querySelectorAll('.wz-pb-step').forEach(s => {
        const step = parseInt(s.dataset.step);
        s.classList.remove('active','done');
        if (step === n) s.classList.add('active');
        else if (step < n) s.classList.add('done');
    });
    document.getElementById('pb-fill').style.width = (n*25) + '%';
    document.getElementById('btn-back').classList.toggle('hidden', n === 1);
    const btnNext = document.getElementById('btn-next');
    btnNext.style.display = n === 4 ? 'none' : 'inline-flex';
    if (n === 4) buildReview();
    window.scrollTo({ top:0, behavior:'smooth' });
    updateNavState();
}
function nextStep(){ if (canAdvance()) goStep(currentStep+1); }
function prevStep(){ goStep(currentStep-1); }

function canAdvance() {
    if (currentStep === 1) return true;
    if (currentStep === 2) return Object.keys(selectedFeatures).length > 0;
    if (currentStep === 3) return !!currentCycle && !!currentHosting;
    return true;
}

function updateNavState() {
    const next = document.getElementById('btn-next');
    const hint = document.getElementById('nav-hint');
    const featCount = Object.keys(selectedFeatures).length;
    next.disabled = !canAdvance();
    if (currentStep === 1) hint.innerHTML = selectedGoals.size ? `<b>${selectedGoals.size}</b> goal${selectedGoals.size===1?'':'s'} picked · pick more or continue` : 'Pick up to 3 goals, or skip to all features';
    else if (currentStep === 2) hint.innerHTML = featCount ? `<b>${featCount}</b> feature${featCount===1?'':'s'} selected` : 'Pick at least one feature to continue';
    else if (currentStep === 3) hint.innerHTML = `${currentCycle.charAt(0).toUpperCase()+currentCycle.slice(1)} billing · ${currentHosting === 'cloud' ? 'Cloud' : 'On-Premise'}`;
    else hint.innerHTML = 'Review your plan on the right';
}

/* ─────────────────────────────────────────────────────
   STEP 1 — GOALS
───────────────────────────────────────────────────── */
document.querySelectorAll('.goal').forEach(g => {
    g.addEventListener('click', () => {
        const key = g.dataset.goal;
        if (selectedGoals.has(key)) {
            selectedGoals.delete(key);
            g.classList.remove('selected');
        } else {
            if (selectedGoals.size >= 3) return;
            selectedGoals.add(key);
            g.classList.add('selected');
        }
        document.getElementById('goal-count').textContent = selectedGoals.size;
        recomputeRecommendations();
        updateNavState();
    });
});
document.getElementById('goal-skip').addEventListener('click', () => { goStep(2); });

function recomputeRecommendations() {
    recommendedCats = new Set();
    recommendedFeats = new Set();
    document.querySelectorAll('.goal.selected').forEach(g => {
        (g.dataset.cats || '').split(',').forEach(c => c && recommendedCats.add(c));
    });
    recommendedCats.forEach(cat => {
        document.querySelectorAll(`.frow[data-cat-key="${cat}"]`).forEach(row => {
            recommendedFeats.add(parseInt(row.dataset.featureId));
        });
    });
    applyRecommendationUI();
}
function applyRecommendationUI() {
    document.querySelectorAll('[data-reco-tag]').forEach(t => t.style.display = 'none');
    document.querySelectorAll('[data-reco-badge]').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.frow').forEach(r => r.classList.remove('reco'));

    if (recommendedCats.size === 0) {
        document.getElementById('reco-banner').style.display = 'none';
        return;
    }
    document.getElementById('reco-banner').style.display = 'flex';

    recommendedCats.forEach(cat => {
        const panel = document.querySelector(`.cat-accordion[data-cat="${cat}"]`);
        if (panel) {
            panel.classList.add('open');
            const badge = panel.querySelector('[data-reco-badge]');
            if (badge) badge.style.display = 'inline-block';
        }
    });
    recommendedFeats.forEach(id => {
        const row = document.querySelector(`.frow[data-feature-id="${id}"]`);
        if (row) {
            row.classList.add('reco');
            const tag = row.querySelector('[data-reco-tag]');
            if (tag) tag.style.display = 'inline-block';
        }
    });
}
document.getElementById('apply-reco').addEventListener('click', () => {
    recommendedFeats.forEach(id => {
        if (!selectedFeatures[id]) toggleFeature(id, true);
    });
});

/* ─────────────────────────────────────────────────────
   STEP 2 — FEATURES
───────────────────────────────────────────────────── */
function toggleCat(headEl) {
    headEl.parentElement.classList.toggle('open');
}
function toggleFeature(id, forceOn) {
    const row = document.querySelector(`[data-feature-id="${id}"]`);
    if (!row) return;
    const check = document.getElementById(`check-${id}`);
    const priceEl = document.getElementById(`price-${id}`);
    const name = row.querySelector('.fname span').textContent.trim();
    const cat = row.dataset.catKey;

    if (selectedFeatures[id] && !forceOn) {
        if (selectedFeatures[id].required) return;
        delete selectedFeatures[id];
        check.classList.remove('checked');
    } else {
        selectedFeatures[id] = {
            name, cat,
            monthly:   parseFloat(priceEl.dataset.monthly),
            quarterly: parseFloat(priceEl.dataset.quarterly),
            yearly:    parseFloat(priceEl.dataset.yearly),
            once:      parseFloat(priceEl.dataset.once),
            required: false
        };
        check.classList.add('checked');
    }
    updateCatCounters();
    updateSummary();
    updateNavState();
}
function updateCatCounters() {
    document.querySelectorAll('.cat-accordion').forEach(acc => {
        const cat = acc.dataset.cat;
        let count = 0;
        acc.querySelectorAll('.frow').forEach(r => {
            if (selectedFeatures[r.dataset.featureId]) count++;
        });
        const el = acc.querySelector(`[data-cat-count="${cat}"]`);
        if (el) el.textContent = count === 0 ? '0 selected' : `${count} selected`;
        acc.classList.toggle('has-selected', count > 0);
    });
}

/* ─────────────────────────────────────────────────────
   STEP 3 — CYCLE + HOSTING
───────────────────────────────────────────────────── */
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
        el.textContent = price === 0 ? 'Free' : 'KES ' + price.toLocaleString();
        el.classList.toggle('free', price === 0);
    });
    updateSummary();
    updateNavState();
}
function selectHosting(type) {
    if (type === 'cloud' && currentCycle === 'once') selectCycle('monthly');
    currentHosting = type;
    document.querySelectorAll('.hcard').forEach(h => h.classList.remove('selected'));
    document.querySelector(`.hcard[data-hosting="${type}"]`).classList.add('selected');
    updateNavState();
}

/* ─────────────────────────────────────────────────────
   SUMMARY / PRICE
───────────────────────────────────────────────────── */
const CYCLE_NOTE = { monthly:'per month', quarterly:'per quarter', yearly:'per year', once:'one-off payment' };

function computeTotal() {
    let total = 0;
    Object.values(selectedFeatures).forEach(f => { total += (f[currentCycle] ?? f.monthly) || 0; });
    return total;
}
function computeMonthlyBaseline() {
    let t = 0;
    Object.values(selectedFeatures).forEach(f => { t += f.monthly || 0; });
    return t;
}
function updateSummary() {
    const list = document.getElementById('sum-list');
    const empty = document.getElementById('sum-empty');
    const div = document.getElementById('sum-divider');
    const total = computeTotal();
    const ids = Object.keys(selectedFeatures);

    list.querySelectorAll('.sum-item').forEach(e => e.remove());

    if (ids.length === 0) {
        empty.style.display = 'block';
        div.style.display = 'none';
    } else {
        empty.style.display = 'none';
        div.style.display = 'block';
        ids.forEach(id => {
            const f = selectedFeatures[id];
            const p = (f[currentCycle] ?? f.monthly) || 0;
            const row = document.createElement('div');
            row.className = 'sum-item';
            row.innerHTML = `<span>${f.name}</span><b>${p === 0 ? 'Free' : 'KES ' + p.toLocaleString()}</b>`;
            list.appendChild(row);
        });
    }
    document.getElementById('sum-total').textContent = 'KES ' + total.toLocaleString();
    document.getElementById('sum-cycle-note').textContent = CYCLE_NOTE[currentCycle] || '';

    const sav = document.getElementById('sum-savings');
    if (currentCycle === 'quarterly' || currentCycle === 'yearly') {
        const monthly = computeMonthlyBaseline();
        const months = currentCycle === 'quarterly' ? 3 : 12;
        const saved = (monthly * months) - total;
        if (saved > 0) {
            sav.textContent = `💰 You save KES ${saved.toLocaleString()} vs paying monthly`;
            sav.classList.add('show');
        } else sav.classList.remove('show');
    } else sav.classList.remove('show');
}

/* ─────────────────────────────────────────────────────
   STEP 4 — REVIEW
───────────────────────────────────────────────────── */
function buildReview() {
    document.getElementById('rv-cycle').textContent =
        currentCycle.charAt(0).toUpperCase() + currentCycle.slice(1) +
        (currentCycle === 'quarterly' ? ' (Save 10%)' : currentCycle === 'yearly' ? ' (Save 20%)' : '');
    document.getElementById('rv-hosting').textContent = currentHosting === 'cloud' ? '☁️ Cloud (Managed)' : '🖥️ On-Premise';

    const list = document.getElementById('rv-list');
    list.innerHTML = '';
    Object.values(selectedFeatures).forEach(f => {
        const p = (f[currentCycle] ?? f.monthly) || 0;
        const row = document.createElement('div');
        row.className = 'sum-item';
        row.innerHTML = `<span>${f.name}</span><b>${p === 0 ? 'Free' : 'KES ' + p.toLocaleString()}</b>`;
        list.appendChild(row);
    });

    const fc = document.getElementById('rv-features');
    fc.innerHTML = '';
    Object.values(selectedFeatures).forEach(f => {
        const p = (f[currentCycle] ?? f.monthly) || 0;
        const row = document.createElement('div');
        row.className = 'review-item';
        row.innerHTML = `<span>✓ ${f.name}</span><b>${p === 0 ? 'Free' : 'KES ' + p.toLocaleString()}</b>`;
        fc.appendChild(row);
    });

    const total = computeTotal();
    document.getElementById('rv-total').textContent = 'KES ' + total.toLocaleString();
    document.getElementById('rv-cycle-note').textContent = CYCLE_NOTE[currentCycle] || '';

    const sav = document.getElementById('rv-savings');
    if (currentCycle === 'quarterly' || currentCycle === 'yearly') {
        const monthly = computeMonthlyBaseline();
        const months = currentCycle === 'quarterly' ? 3 : 12;
        const saved = (monthly * months) - total;
        if (saved > 0) {
            sav.textContent = `💰 You save KES ${saved.toLocaleString()} vs monthly`;
            sav.style.display = 'block';
        } else sav.style.display = 'none';
    } else sav.style.display = 'none';
}

function goToCheckout() {
    const ids = Object.keys(selectedFeatures).join(',');
    window.location = `{{ route('saas.checkout') }}?feature_ids=${ids}&cycle=${currentCycle}&hosting=${currentHosting}`;
}

/* ── INIT ── */
selectCycle('yearly');
updateCatCounters();
updateSummary();
updateNavState();
</script>
@endsection
