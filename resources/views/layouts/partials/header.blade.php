@inject('request', 'Illuminate\Http\Request')
@php
    /* Use same accent map as the sidebar so header & sidebar always match */
    $__hdrAccentMap = [
        'primary' => ['from' => '#3730a3', 'to' => '#1e1b4b'],
        'purple'  => ['from' => '#5b21b6', 'to' => '#2e1065'],
        'green'   => ['from' => '#065f46', 'to' => '#022c22'],
        'red'     => ['from' => '#991b1b', 'to' => '#450a0a'],
        'yellow'  => ['from' => '#92400e', 'to' => '#451a03'],
        'orange'  => ['from' => '#9a3412', 'to' => '#431407'],
        'sky'     => ['from' => '#075985', 'to' => '#082f49'],
    ];
    $__hdrTheme  = session('business.theme_color', 'primary');
    $__hdrColors = $__hdrAccentMap[$__hdrTheme] ?? $__hdrAccentMap['primary'];
@endphp
<!-- Main Header -->
<style>
/* ── CSS variables resolved from PHP theme ──────────────── */
:root {
    --hdr-bg-from: {{ $__hdrColors['from'] }};
    --hdr-bg-to:   {{ $__hdrColors['to'] }};
}

/* ── Base header ─────────────────────────────────────────── */
.app-header {
    /* Same color stop as sidebar, horizontal so it reads as a single band */
    background: linear-gradient(90deg, var(--hdr-bg-from) 0%, var(--hdr-bg-to) 100%);
    border-bottom: 1px solid rgba(255,255,255,0.08);
    height: 64px;
    display: flex;
    align-items: center;
    padding: 0 20px;
    gap: 8px;
    position: relative;
    z-index: 100;
    flex-shrink: 0;
}

/* ── Sidebar toggle buttons ──────────────────────────────── */
.hdr-sidebar-btn {
    width: 38px; height: 38px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 9px; color: #fff; cursor: pointer;
    transition: background .15s;
    flex-shrink: 0;
}
.hdr-sidebar-btn:hover { background: rgba(255,255,255,0.2); }
.hdr-sidebar-btn svg  { width: 18px; height: 18px; }

/* ── Show ONLY ONE toggle at a time ──────────────────────── */
/* Mobile (<1024px): show hamburger, hide collapse toggle */
.hdr-btn-hamburger { display: inline-flex; }
.hdr-btn-collapse  { display: none; }
/* Desktop (≥1024px): show collapse toggle, hide hamburger */
@media (min-width: 1024px) {
    .hdr-btn-hamburger { display: none !important; }
    .hdr-btn-collapse  { display: inline-flex !important; }
}

/* ── Spacer ──────────────────────────────────────────────── */
.hdr-spacer { flex: 1; }

/* ── Button groups ───────────────────────────────────────── */
.hdr-group {
    display: flex; align-items: center; gap: 4px;
}
.hdr-divider {
    width: 1px; height: 22px;
    background: rgba(255,255,255,0.18);
    margin: 0 4px; flex-shrink: 0;
}

/* ── Ghost button (Calendar, Calculator, Expense, Purchase…) */
.hdr-btn {
    display: inline-flex; align-items: center; gap: 7px;
    height: 38px; padding: 0 14px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 9px; color: #fff !important;
    font-size: 13px; font-weight: 600;
    cursor: pointer; text-decoration: none !important;
    transition: background .15s, border-color .15s;
    white-space: nowrap;
}
.hdr-btn:hover { background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.32); }
.hdr-btn svg   { width: 16px; height: 16px; color: rgba(255,255,255,.9); flex-shrink: 0; }

/* ── Icon-only variant ───────────────────────────────────── */
.hdr-btn-icon {
    width: 38px; padding: 0;
    justify-content: center;
}

/* ── Date chip — subtle glass ────────────────────────────── */
.hdr-date-chip {
    display: inline-flex; align-items: center; gap: 7px;
    height: 38px; padding: 0 14px;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 9px; color: rgba(255,255,255,.65) !important;
    font-size: 13px; font-weight: 600; font-family: monospace;
    cursor: default; white-space: nowrap;
}
.hdr-date-chip svg { width: 14px; height: 14px; opacity: .6; }

/* ── Today's Profit chip — live stat ─────────────────────── */
.hdr-profit-chip {
    display: inline-flex; align-items: center; gap: 7px;
    height: 38px; padding: 0 14px;
    background: rgba(16,185,129,0.18);
    border: 1px solid rgba(16,185,129,0.35);
    border-radius: 9px; color: #6ee7b7 !important;
    font-size: 13px; font-weight: 700;
    cursor: pointer; white-space: nowrap;
    transition: background .15s;
}
.hdr-profit-chip:hover { background: rgba(16,185,129,0.28); }
.hdr-profit-chip svg   { width: 16px; height: 16px; color: #6ee7b7; flex-shrink: 0; }
.hdr-profit-chip .profit-label  { color: rgba(255,255,255,.55); font-weight: 500; font-size: 12px; }
.hdr-profit-chip .profit-amount { color: #fff; font-weight: 700; font-variant-numeric: tabular-nums; }

/* ── POS primary — white pill, most prominent ────────────── */
.hdr-btn-pos {
    background: #fff;
    border: 1px solid #fff;
    color: var(--hdr-bg-from) !important;
    font-weight: 800;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.25);
    letter-spacing: .01em;
}
.hdr-btn-pos:hover {
    background: rgba(255,255,255,0.92);
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
.hdr-btn-pos svg { color: var(--hdr-bg-from) !important; }

/* ── Divider ─────────────────────────────────────────────── */
.hdr-divider {
    width: 1px; height: 26px;
    background: rgba(255,255,255,0.16);
    margin: 0 6px; flex-shrink: 0;
}

/* ── Notification bell ───────────────────────────────────── */
.hdr-bell-wrap { position: relative; display: inline-flex; }
.hdr-bell-dot  {
    position: absolute; top: 4px; right: 4px;
    width: 9px; height: 9px;
    background: #ef4444; border-radius: 50%;
    border: 2px solid var(--hdr-bg-to);
    pointer-events: none;
}
.hdr-bell-dot.pulse { animation: bellPulse 2s infinite; }
@keyframes bellPulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,.6); }
    50%      { box-shadow: 0 0 0 5px rgba(239,68,68,0); }
}
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ── User chip ───────────────────────────────────────────── */
.hdr-user-btn {
    display: inline-flex; align-items: center; gap: 8px;
    height: 38px; padding: 0 12px 0 7px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 9px; color: #fff !important;
    font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background .15s;
}
.hdr-user-btn:hover { background: rgba(255,255,255,0.2); }
.hdr-user-avatar {
    width: 26px; height: 26px; border-radius: 7px;
    background: rgba(255,255,255,0.22);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 800; color: #fff; flex-shrink: 0;
}

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 1280px) {
    .hdr-btn .hdr-label         { display: none; }
    .hdr-btn                    { padding: 0 10px; }
    .hdr-profit-chip .profit-label { display: none; }
}
@media (max-width: 1024px) {
    .hdr-group.hdr-tools        { display: none; }
    .hdr-divider.hdr-tools      { display: none; }
}
@media (max-width: 768px) {
    .hdr-group.hdr-actions      { display: none; }
    .hdr-divider.hdr-actions    { display: none; }
    .hdr-date-chip              { display: none !important; }
}
</style>

<header class="app-header no-print" id="app-header">

    {{-- ── Left: Sidebar toggles (only ONE shows at a time via CSS) ── --}}
    {{-- Mobile hamburger: visible below 1024px --}}
    <button type="button" class="hdr-sidebar-btn hdr-btn-hamburger small-view-button" aria-label="Open menu">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l16 0"/><path d="M4 12l16 0"/><path d="M4 18l16 0"/></svg>
    </button>
    {{-- Desktop collapse: visible at 1024px+ --}}
    <button type="button" class="hdr-sidebar-btn hdr-btn-collapse side-bar-collapse" aria-label="Collapse sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M15 4v16"/><path d="M10 10l-2 2l2 2"/></svg>
    </button>

    {{-- SaaS active subscription --}}
    @if(Module::has('Superadmin'))
        @includeIf('superadmin::layouts.partials.active_subscription')
    @endif
    @if(!empty(session('previous_user_id')) && !empty(session('previous_username')))
        <a href="{{ route('sign-in-as-user', session('previous_user_id')) }}" class="btn btn-flat btn-danger btn-sm" style="margin:0 4px;">
            <i class="fas fa-undo"></i> @lang('lang_v1.back_to_username', ['username' => session('previous_username')])
        </a>
    @endif

    <div class="hdr-spacer"></div>

    {{-- ── Right: action groups ────────────────────────── --}}

    {{-- GROUP 1 — Date + Profit (info) --}}
    <div class="hdr-group">

        {{-- Date chip --}}
        <span class="hdr-date-chip tw-hidden sm:tw-inline-flex">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 7v5l3 3"/></svg>
            {{ @format_date('now') }}
        </span>

        {{-- Today's Profit live chip --}}
        @can('profit_loss_report.view')
        <button type="button" id="view_todays_profit" class="hdr-profit-chip">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 19l4 -4l4 4l4 -6l4 2"/></svg>
            <span class="profit-label">Today's Profit</span>
            <span class="profit-amount" id="hdr_profit_val">
                <svg style="width:13px;height:13px;animation:spin 1s linear infinite;vertical-align:middle;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 1 0 9 9"/></svg>
            </span>
        </button>
        @endcan

    </div>

    <div class="hdr-divider"></div>

    {{-- GROUP 2 — Tools (Calendar, Calculator) --}}
    <div class="hdr-group hdr-tools">

        <a href="{{ route('calendar') }}" class="hdr-btn" title="@lang('lang_v1.calendar')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 11h16"/><path d="M7 14h.013"/><path d="M10.01 14h.005"/><path d="M13.01 14h.005"/></svg>
            <span class="hdr-label">@lang('lang_v1.calendar')</span>
        </a>

        <button id="btnCalculator" type="button" class="hdr-btn" title="@lang('lang_v1.calculator')"
            data-content='@include('layouts.partials.calculator')' data-trigger="click" data-html="true" data-placement="bottom">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 3m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M8 7m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z"/><path d="M8 14l0 .01"/><path d="M12 14l0 .01"/><path d="M16 14l0 .01"/><path d="M8 17l0 .01"/><path d="M12 17l0 .01"/><path d="M16 17l0 .01"/></svg>
            <span class="hdr-label">@lang('lang_v1.calculator')</span>
        </button>

    </div>

    <div class="hdr-divider hdr-tools"></div>

    {{-- GROUP 3 — Quick Actions (Expense, Purchase) --}}
    <div class="hdr-group hdr-actions">

        @can('expense.access')
        <a href="{{ action([\App\Http\Controllers\ExpenseController::class, 'create']) }}" class="hdr-btn" title="Add Expense">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"/><path d="M14 3v4h-6"/><path d="M3 14h4v-6"/><path d="M12 11v4"/><path d="M10 13h4"/></svg>
            <span class="hdr-label">Expense</span>
        </a>
        @endcan

        @can('purchase.create')
        <a href="{{ action([\App\Http\Controllers\PurchaseController::class, 'create']) }}" class="hdr-btn" title="New Purchase">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
            <span class="hdr-label">Purchase</span>
        </a>
        @endcan

    </div>

    <div class="hdr-divider hdr-actions"></div>

    {{-- GROUP 4 — Primary: POS --}}
    @if(in_array('pos_sale', $enabled_modules))
        @can('sell.create')
        <div class="hdr-group">
            <a href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}" class="hdr-btn hdr-btn-pos" title="@lang('sale.pos_sale')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/></svg>
                @lang('sale.pos_sale')
            </a>
        </div>
        <div class="hdr-divider"></div>
        @endcan
    @endif

    {{-- MODULE: Essentials header items --}}
    @if(Module::has('Essentials'))
        @includeIf('essentials::layouts.partials.header_part')
    @endif

    {{-- MODULE: Repair --}}
    @if(Module::has('Repair'))
        @includeIf('repair::layouts.partials.header')
    @endif

    {{-- GROUP 5 — Utilities: Search + Bell + User --}}
    <div class="hdr-group">

        {{-- Search --}}
        <button type="button" id="global-search-trigger" class="hdr-btn" title="Search (Ctrl+K)" style="min-width:110px;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/><path d="M21 21l-6 -6"/></svg>
            <span class="hdr-label">Search</span>
            <kbd style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);border-radius:4px;padding:1px 5px;font-size:10px;font-family:monospace;line-height:1.4;">⌘K</kbd>
        </button>

        {{-- Notifications bell --}}
        @include('layouts.partials.header-notifications')

        {{-- User dropdown --}}
        <details class="tw-dw-dropdown tw-relative tw-inline-block">
            <summary class="hdr-user-btn" style="list-style:none;">
                <div class="hdr-user-avatar">
                    {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
                </div>
                <span class="tw-hidden md:tw-inline hdr-label">{{ Auth::user()->first_name }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;opacity:.7;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6"/></svg>
            </summary>
            <ul class="tw-p-2 tw-w-52 tw-absolute tw-right-0 tw-z-50 tw-mt-2 tw-origin-top-right tw-bg-white tw-rounded-xl tw-shadow-xl tw-ring-1 tw-ring-gray-200 focus:tw-outline-none" style="top:100%;">
                <div style="padding:12px 14px 10px;border-bottom:1px solid #f1f5f9;margin-bottom:4px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:9px;background:linear-gradient(135deg,var(--hdr-bg-from),var(--hdr-bg-to));display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
                        </div>
                        <div style="min-width:0;">
                            <div style="font-size:13px;font-weight:700;color:#111827;truncate">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                            <div style="font-size:11px;color:#6b7280;">@lang('lang_v1.signed_in_as')</div>
                        </div>
                    </div>
                </div>
                <li>
                    <a href="{{ action([\App\Http\Controllers\UserController::class, 'getProfile']) }}"
                       class="tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-gray-600 tw-rounded-lg hover:tw-text-gray-900 hover:tw-bg-gray-100">
                        <svg class="tw-w-4 tw-h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"/></svg>
                        @lang('lang_v1.profile')
                    </a>
                </li>
                <li>
                    <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-text-red-600 tw-rounded-lg hover:tw-bg-red-50">
                        <svg class="tw-w-4 tw-h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12l-3 -3"/><path d="M18 15l3 -3"/></svg>
                        @lang('lang_v1.sign_out')
                    </a>
                    <form id="logout-form" action="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}" method="POST" style="display:none;">@csrf</form>
                </li>
            </ul>
        </details>

    </div>

</header>

{{-- ── Global Search Overlay ───────────────────────────── --}}
<div id="global-search-overlay" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,0.7);backdrop-filter:blur(4px);" onclick="if(event.target===this)closeGlobalSearch()">
    <div style="max-width:640px;margin:80px auto 0;background:#fff;border-radius:14px;box-shadow:0 25px 50px rgba(0,0,0,0.25);overflow:hidden;">
        <div style="display:flex;align-items:center;padding:0 16px;border-bottom:1px solid #e2e8f0;">
            <svg style="width:20px;height:20px;color:#94a3b8;flex-shrink:0;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/><path d="M21 21l-6 -6"/></svg>
            <input type="text" id="global-search-input" placeholder="Search transactions, contacts, products, parcels…"
                style="flex:1;border:none;outline:none;padding:16px 12px;font-size:16px;color:#1e293b;background:transparent;">
            <kbd onclick="closeGlobalSearch()" style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:4px;padding:2px 8px;font-size:12px;cursor:pointer;color:#64748b;">Esc</kbd>
        </div>
        <div id="global-search-results" style="max-height:400px;overflow-y:auto;padding:8px;">
            <div class="search-empty-state" style="text-align:center;padding:40px 20px;color:#94a3b8;">
                <svg style="width:40px;height:40px;margin:0 auto 12px;display:block;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/><path d="M21 21l-6 -6"/></svg>
                <p style="font-size:14px;margin:0;">Type to search across your system</p>
                <p style="font-size:12px;margin:4px 0 0;color:#cbd5e1;">Transactions · Contacts · Products · Parcels</p>
            </div>
        </div>
        <div style="padding:8px 16px;border-top:1px solid #f1f5f9;display:flex;align-items:center;gap:16px;font-size:11px;color:#94a3b8;">
            <span><kbd style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:3px;padding:1px 5px;">↵</kbd> Open</span>
            <span><kbd style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:3px;padding:1px 5px;">↑↓</kbd> Navigate</span>
            <span><kbd style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:3px;padding:1px 5px;">Esc</kbd> Close</span>
        </div>
    </div>
</div>

{{-- ── Today's Profit fetch on load ───────────────────── --}}
@can('profit_loss_report.view')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var chip = document.getElementById('view_todays_profit');
    var val  = document.getElementById('hdr_profit_val');
    if (!chip || !val) return;

    // Fetch today's profit in the background
    $.ajax({
        url: '/reports/profit-loss',
        data: {
            start_date: '{{ now()->toDateString() }}',
            end_date:   '{{ now()->toDateString() }}',
            type:       'product'
        },
        success: function(html) {
            // parse gross_profit from returned HTML
            var match = html ? html.match(/id="gross_profit"[^>]*>([^<]+)</) : null;
            if (match && match[1]) {
                val.textContent = match[1].trim();
            } else {
                // fallback: just show "View"
                val.textContent = 'View';
            }
        },
        error: function() { val.textContent = 'View'; }
    });
});
</script>
@endcan
