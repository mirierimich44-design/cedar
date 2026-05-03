@inject('request', 'Illuminate\Http\Request')
@php
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
<style>
/* ─── Header bar ──────────────────────────────────────────── */
.app-header {
    background: linear-gradient(90deg, {{ $__hdrColors['from'] }}, {{ $__hdrColors['to'] }});
    border-bottom: 1px solid rgba(255,255,255,0.08);
    height: 64px;
    display: flex;
    align-items: center;
    padding: 0 20px;
    gap: 8px;
    flex-shrink: 0;
    position: relative;
    z-index: 100;
}

/* ─── ONE rule for every clickable element in the header ──── */
/* This overrides Bootstrap, modules, everything */
.app-header .hdr-btn,
.app-header .hdr-btn:hover,
.app-header .hdr-btn:focus,
.app-header .hdr-btn:active {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 7px !important;
    height: 38px !important;
    padding: 0 14px !important;
    background: var(--theme-main) !important;
    color: #fff !important;
    border: none !important;
    border-radius: 8px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    white-space: nowrap !important;
    cursor: pointer !important;
    text-decoration: none !important;
    box-shadow: none !important;
    outline: none !important;
    transition: opacity .15s !important;
    line-height: 1 !important;
}
.app-header .hdr-btn:hover { opacity: .88 !important; }
.app-header .hdr-btn svg   { width: 16px !important; height: 16px !important; color: #fff !important; flex-shrink: 0 !important; }

/* ─── Sidebar toggles — subtle, not competing ─────────────── */
.app-header .hdr-toggle {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 38px !important; height: 38px !important;
    background: rgba(255,255,255,0.12) !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
    border-radius: 8px !important;
    color: #fff !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
    transition: background .15s !important;
}
.app-header .hdr-toggle:hover { background: rgba(255,255,255,0.22) !important; }
.app-header .hdr-toggle svg   { width: 18px !important; height: 18px !important; }

/* ─── POS: white pill — the one exception ─────────────────── */
.app-header .hdr-btn-pos,
.app-header .hdr-btn-pos:hover,
.app-header .hdr-btn-pos:focus {
    background: #fff !important;
    color: {{ $__hdrColors['from'] }} !important;
    font-weight: 800 !important;
    font-size: 14px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
}
.app-header .hdr-btn-pos:hover { opacity: 1 !important; background: rgba(255,255,255,0.93) !important; }
.app-header .hdr-btn-pos svg   { color: {{ $__hdrColors['from'] }} !important; }

/* ─── Divider ─────────────────────────────────────────────── */
.app-header .hdr-divider {
    width: 1px; height: 24px;
    background: rgba(255,255,255,0.18);
    flex-shrink: 0; margin: 0 4px;
}

/* ─── Bell dot ────────────────────────────────────────────── */
.hdr-bell-wrap { position: relative; display: inline-flex; }
.hdr-bell-dot  {
    position: absolute; top: 4px; right: 4px;
    width: 8px; height: 8px;
    background: #ef4444; border-radius: 50%;
    border: 2px solid {{ $__hdrColors['to'] }};
    pointer-events: none;
    animation: bellPulse 2s infinite;
}
@keyframes bellPulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,.6); }
    50%      { box-shadow: 0 0 0 4px rgba(239,68,68,0); }
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ─── Sidebar toggle visibility ───────────────────────────── */
.hdr-hamburger { display: inline-flex !important; }
.hdr-collapse  { display: none    !important; }
@media (min-width: 1024px) {
    .hdr-hamburger { display: none   !important; }
    .hdr-collapse  { display: inline-flex !important; }
}

/* ─── Responsive: hide labels then buttons ─────────────────── */
@media (max-width: 1280px) {
    .app-header .hdr-btn { padding: 0 10px !important; }
    .app-header .hdr-label { display: none !important; }
}
@media (max-width: 900px)  { .app-header .hdr-hide-md { display: none !important; } }
@media (max-width: 768px)  { .app-header .hdr-hide-sm { display: none !important; } }

/* ─── Profit chip responsive label ───────────────────────── */
@media (max-width: 1100px) {
    .hdr-profit-label { display: none !important; }
}

/* ─── User dropdown menu ───────────────────────────────────── */
.hdr-user-dropdown {
    position: absolute; right: 0; top: calc(100% + 8px);
    width: 220px; background: #fff;
    border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border: 1px solid #e5e7eb; z-index: 9999; overflow: hidden;
}
</style>

<header class="app-header no-print" id="app-header">

    {{-- Sidebar toggles --}}
    <button type="button" class="hdr-toggle hdr-hamburger small-view-button" aria-label="Open menu">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l16 0"/><path d="M4 12l16 0"/><path d="M4 18l16 0"/></svg>
    </button>
    <button type="button" class="hdr-toggle hdr-collapse side-bar-collapse" aria-label="Collapse sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M15 4v16"/><path d="M10 10l-2 2l2 2"/></svg>
    </button>

    @if(Module::has('Superadmin'))
        @includeIf('superadmin::layouts.partials.active_subscription')
    @endif
    @if(!empty(session('previous_user_id')) && !empty(session('previous_username')))
        <a href="{{ route('sign-in-as-user', session('previous_user_id')) }}"
           class="hdr-btn" style="background:#ef4444 !important;">
            <i class="fas fa-undo"></i>
            @lang('lang_v1.back_to_username', ['username' => session('previous_username')])
        </a>
    @endif

    <div style="flex:1;"></div>

    {{-- Today's Profit --}}
    @can('profit_loss_report.view')
    <button type="button" id="view_todays_profit" class="hdr-btn"
            style="background:rgba(16,185,129,0.25) !important; border:1px solid rgba(16,185,129,0.5) !important;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 19l4 -4l4 4l4 -6l4 2"/></svg>
        <span class="hdr-profit-label" style="opacity:.75;font-weight:500;">Profit</span>
        <span id="hdr_profit_val" style="font-weight:700;">
            <svg style="width:12px;height:12px;animation:spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 1 0 9 9"/></svg>
        </span>
    </button>
    <div class="hdr-divider"></div>
    @endcan

    {{-- Calendar --}}
    <a href="{{ route('calendar') }}" class="hdr-btn hdr-hide-md">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 11h16"/></svg>
        <span class="hdr-label">@lang('lang_v1.calendar')</span>
    </a>

    {{-- Expense --}}
    @can('expense.access')
    <a href="{{ action([\App\Http\Controllers\ExpenseController::class, 'create']) }}" class="hdr-btn hdr-hide-sm">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"/><path d="M14 3v4h-6"/><path d="M3 14h4v-6"/><path d="M12 11v4"/><path d="M10 13h4"/></svg>
        <span class="hdr-label">Expense</span>
    </a>
    @endcan

    {{-- Purchase --}}
    @can('purchase.create')
    <a href="{{ action([\App\Http\Controllers\PurchaseController::class, 'create']) }}" class="hdr-btn hdr-hide-sm">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
        <span class="hdr-label">Purchase</span>
    </a>
    @endcan

    {{-- POS — white primary --}}
    @if(in_array('pos_sale', $enabled_modules))
        @can('sell.create')
        <a href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}" class="hdr-btn hdr-btn-pos">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/></svg>
            @lang('sale.pos_sale')
        </a>
        @endcan
    @endif

    {{-- Modules --}}
    @if(Module::has('Essentials'))
        @includeIf('essentials::layouts.partials.header_part')
    @endif
    @if(Module::has('Repair'))
        @includeIf('repair::layouts.partials.header')
    @endif

    <div class="hdr-divider"></div>

    {{-- Bell --}}
    @include('layouts.partials.header-notifications')

    {{-- User --}}
    <details class="tw-dw-dropdown" style="position:relative;display:inline-block;">
        <summary class="hdr-btn" style="list-style:none;gap:8px;">
            <span style="width:24px;height:24px;border-radius:6px;background:rgba(255,255,255,0.25);display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0;">
                {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
            </span>
            <span class="hdr-label">{{ Auth::user()->first_name }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;opacity:.7;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6"/></svg>
        </summary>
        <ul class="hdr-user-dropdown" style="list-style:none;margin:0;padding:0;">
            <div style="padding:14px 16px 12px;border-bottom:1px solid #f1f5f9;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:9px;background:linear-gradient(135deg,{{ $__hdrColors['from'] }},{{ $__hdrColors['to'] }});display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff;flex-shrink:0;">
                        {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#111827;">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                        <div style="font-size:11px;color:#6b7280;">@lang('lang_v1.signed_in_as')</div>
                    </div>
                </div>
            </div>
            <li>
                <a href="{{ action([\App\Http\Controllers\UserController::class, 'getProfile']) }}"
                   style="display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:13px;color:#374151;text-decoration:none;"
                   onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                    <svg style="width:16px;height:16px;color:#6b7280;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"/></svg>
                    @lang('lang_v1.profile')
                </a>
            </li>
            <li style="border-top:1px solid #f1f5f9;">
                <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}"
                   onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                   style="display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:13px;color:#dc2626;text-decoration:none;"
                   onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background=''">
                    <svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12l-3 -3"/><path d="M18 15l3 -3"/></svg>
                    @lang('lang_v1.sign_out')
                </a>
                <form id="logout-form" action="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}" method="POST" style="display:none;">@csrf</form>
            </li>
        </ul>
    </details>

</header>

{{-- Today's Profit fetch --}}
@can('profit_loss_report.view')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var val = document.getElementById('hdr_profit_val');
    if (!val) return;
    $.ajax({
        url: '/reports/profit-loss',
        data: { start_date: '{{ now()->toDateString() }}', end_date: '{{ now()->toDateString() }}', type: 'product' },
        success: function(html) {
            var m = html ? html.match(/id="gross_profit"[^>]*>([^<]+)</) : null;
            val.textContent = (m && m[1]) ? m[1].trim() : 'View';
        },
        error: function() { val.textContent = 'View'; }
    });
});
</script>
@endcan
