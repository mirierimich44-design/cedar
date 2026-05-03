@inject('request', 'Illuminate\Http\Request')
@php
    $themeMap = [
        'primary' => ['grad' => 'linear-gradient(90deg,#3730a3,#1e1b4b)', 'main' => '#4f46e5', 'dark' => '#3730a3'],
        'green'   => ['grad' => 'linear-gradient(90deg,#065f46,#022c22)', 'main' => '#059669', 'dark' => '#065f46'],
        'purple'  => ['grad' => 'linear-gradient(90deg,#5b21b6,#2e1065)', 'main' => '#7c3aed', 'dark' => '#5b21b6'],
        'red'     => ['grad' => 'linear-gradient(90deg,#991b1b,#450a0a)', 'main' => '#dc2626', 'dark' => '#991b1b'],
        'yellow'  => ['grad' => 'linear-gradient(90deg,#92400e,#451a03)', 'main' => '#d97706', 'dark' => '#92400e'],
        'orange'  => ['grad' => 'linear-gradient(90deg,#9a3412,#431407)', 'main' => '#ea580c', 'dark' => '#9a3412'],
        'sky'     => ['grad' => 'linear-gradient(90deg,#075985,#082f49)', 'main' => '#0284c7', 'dark' => '#075985'],
    ];
    $t = $themeMap[session('business.theme_color','primary')] ?? $themeMap['primary'];
@endphp

<style>
/* Scoped to #app-header so specificity beats everything */
#app-header {
    background: {{ $t['grad'] }};
    height: 64px;
    display: flex;
    align-items: center;
    padding: 0 16px;
    gap: 6px;
    flex-shrink: 0;
    position: relative;
    z-index: 100;
}

/* THE button style — all: unset nukes Bootstrap completely */
#app-header .hb {
    all: unset;
    box-sizing: border-box;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 36px;
    padding: 0 13px;
    background: {{ $t['main'] }};
    color: #fff;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    text-decoration: none;
    flex-shrink: 0;
    transition: opacity .15s;
}
#app-header .hb:hover   { opacity: .82; color: #fff; }
#app-header .hb svg    { width: 15px; height: 15px; color: #fff; flex-shrink: 0; }

/* Sidebar toggle: ghost, no fill */
#app-header .hb-ghost {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    width: 36px;
    padding: 0;
}
#app-header .hb-ghost:hover { background: rgba(255,255,255,0.22); opacity: 1; }

/* POS: white — the one standout */
#app-header .hb-primary {
    background: #fff;
    color: {{ $t['dark'] }};
    font-weight: 800;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
#app-header .hb-primary:hover { opacity: .92; }
#app-header .hb-primary svg  { color: {{ $t['dark'] }}; }

/* Divider */
#app-header .hb-sep {
    width: 1px; height: 22px;
    background: rgba(255,255,255,0.2);
    flex-shrink: 0;
}

/* Bell dot */
#app-header .hb-bell-wrap { position: relative; display: inline-flex; }
#app-header .hb-bell-dot  {
    position: absolute; top: 3px; right: 3px;
    width: 8px; height: 8px;
    background: #ef4444; border-radius: 50%;
    border: 2px solid {{ $t['dark'] }};
    pointer-events: none;
    animation: hbPulse 2s infinite;
}
@keyframes hbPulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,.6); }
    50%      { box-shadow: 0 0 0 4px rgba(239,68,68,0); }
}
@keyframes hbSpin { to { transform: rotate(360deg); } }

/* User dropdown */
#hb-user-menu {
    position: absolute; right: 0; top: calc(100% + 6px);
    width: 210px; background: #fff;
    border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border: 1px solid #e5e7eb; z-index: 9999;
    overflow: hidden; display: none;
}

/* One toggle shows at a time */
.hb-mobile { display: inline-flex; }
.hb-desktop { display: none; }
@media (min-width: 1024px) {
    .hb-mobile  { display: none; }
    .hb-desktop { display: inline-flex; }
}

/* Responsive: hide labels */
@media (max-width: 1200px) { #app-header .hb-label { display: none; } }
@media (max-width: 960px)  { #app-header .hb-hide-md { display: none; } }
@media (max-width: 700px)  { #app-header .hb-hide-sm { display: none; } }
</style>

<header id="app-header" class="no-print">

    {{-- Sidebar toggle: mobile opens drawer, desktop collapses --}}
    <button class="hb hb-ghost hb-mobile small-view-button" type="button" aria-label="Menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l16 0"/><path d="M4 12l16 0"/><path d="M4 18l16 0"/></svg>
    </button>
    <button class="hb hb-ghost hb-desktop side-bar-collapse" type="button" aria-label="Collapse sidebar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M15 4v16"/><path d="M10 10l-2 2l2 2"/></svg>
    </button>

    @if(Module::has('Superadmin'))
        @includeIf('superadmin::layouts.partials.active_subscription')
    @endif
    @if(!empty(session('previous_user_id')))
        <a href="{{ route('sign-in-as-user', session('previous_user_id')) }}"
           class="hb" style="background:#ef4444;">
            <i class="fas fa-undo"></i> Back
        </a>
    @endif

    <div style="flex:1;"></div>

    {{-- Today's Profit --}}
    @can('profit_loss_report.view')
    <button type="button" id="view_todays_profit" class="hb hb-hide-sm"
            style="background:rgba(16,185,129,0.3);border:1px solid rgba(16,185,129,0.5);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 19l4 -4l4 4l4 -6l4 2"/></svg>
        <span class="hb-label" style="opacity:.75;font-size:12px;">Profit</span>
        <span id="hdr_profit_val" style="font-weight:700;">
            <svg style="width:12px;height:12px;animation:hbSpin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 1 0 9 9"/></svg>
        </span>
    </button>
    <div class="hb-sep hb-hide-sm"></div>
    @endcan

    {{-- Calendar --}}
    <a href="{{ route('calendar') }}" class="hb hb-hide-md">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 11h16"/></svg>
        <span class="hb-label">@lang('lang_v1.calendar')</span>
    </a>

    {{-- Expense --}}
    @can('expense.access')
    <a href="{{ action([\App\Http\Controllers\ExpenseController::class, 'create']) }}" class="hb hb-hide-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"/><path d="M14 3v4h-6"/><path d="M3 14h4v-6"/><path d="M12 11v4"/><path d="M10 13h4"/></svg>
        <span class="hb-label">Expense</span>
    </a>
    @endcan

    {{-- Purchase --}}
    @can('purchase.create')
    <a href="{{ action([\App\Http\Controllers\PurchaseController::class, 'create']) }}" class="hb hb-hide-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
        <span class="hb-label">Purchase</span>
    </a>
    @endcan

    {{-- POS — white primary --}}
    @if(in_array('pos_sale', $enabled_modules))
        @can('sell.create')
        <a href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}" class="hb hb-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/></svg>
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

    <div class="hb-sep"></div>

    {{-- Bell --}}
    @php
        $__unread = auth()->user()->unreadNotifications->count();
    @endphp
    <div class="hb-bell-wrap">
        <button type="button" class="hb" id="smart-notif-btn"
                onclick="toggleSmartNotif()"
                style="width:36px;padding:0;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/><path d="M9 17v1a3 3 0 0 0 6 0v-1"/></svg>
        </button>
        @if($__unread > 0)
            <span class="hb-bell-dot" id="notif-badge"></span>
        @else
            <span class="hb-bell-dot" id="notif-badge" style="display:none;"></span>
        @endif
    </div>

    {{-- Notification panel (existing partial logic kept) --}}
    <div id="smart-notif-panel" style="display:none;position:absolute;right:16px;top:68px;width:380px;background:#fff;border-radius:12px;box-shadow:0 20px 40px rgba(0,0,0,0.15);border:1px solid #e2e8f0;z-index:9998;overflow:hidden;">
        <div style="padding:14px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:15px;font-weight:700;color:#1e293b;">Notifications</span>
            <a href="#" onclick="markAllRead()" style="font-size:12px;color:#3b82f6;text-decoration:none;">Mark all read</a>
        </div>
        <div id="smart-alerts-section">
            <div style="padding:6px 16px 2px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Alerts</div>
            <div id="smart-alerts-list"><div style="padding:12px 16px;color:#94a3b8;font-size:13px;">Loading…</div></div>
        </div>
        <div>
            <div style="padding:6px 16px 2px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Recent</div>
            <ul style="margin:0;padding:0;list-style:none;max-height:240px;overflow-y:auto;" id="notifications_list"></ul>
        </div>
    </div>

    {{-- User --}}
    <div style="position:relative;display:inline-flex;">
        <button type="button" class="hb" id="hb-user-btn"
                onclick="document.getElementById('hb-user-menu').style.display=document.getElementById('hb-user-menu').style.display==='block'?'none':'block'">
            <span style="width:22px;height:22px;border-radius:5px;background:rgba(255,255,255,0.25);display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;">
                {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
            </span>
            <span class="hb-label">{{ Auth::user()->first_name }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;opacity:.6;" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6"/></svg>
        </button>

        <div id="hb-user-menu">
            <div style="padding:14px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:9px;background:{{ $t['grad'] }};display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;">
                    {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#111827;">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                    <div style="font-size:11px;color:#6b7280;">Signed in</div>
                </div>
            </div>
            <a href="{{ action([\App\Http\Controllers\UserController::class, 'getProfile']) }}"
               style="display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:13px;color:#374151;text-decoration:none;"
               onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                <svg style="width:15px;height:15px;color:#6b7280;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"/></svg>
                @lang('lang_v1.profile')
            </a>
            <div style="border-top:1px solid #f1f5f9;">
                <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                   style="display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:13px;color:#dc2626;text-decoration:none;"
                   onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background=''">
                    <svg style="width:15px;height:15px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" xmlns="http://www.w3.org/2000/svg"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12l-3 -3"/><path d="M18 15l3 -3"/></svg>
                    @lang('lang_v1.sign_out')
                </a>
                <form id="logout-form" action="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}" method="POST" style="display:none;">@csrf</form>
            </div>
        </div>
    </div>

</header>

<script>
// Close user menu when clicking outside
document.addEventListener('click', function(e) {
    var menu = document.getElementById('hb-user-menu');
    var btn  = document.getElementById('hb-user-btn');
    if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
        menu.style.display = 'none';
    }
    var panel = document.getElementById('smart-notif-panel');
    var notifBtn = document.getElementById('smart-notif-btn');
    if (panel && notifBtn && !notifBtn.contains(e.target) && !panel.contains(e.target)) {
        panel.style.display = 'none';
        smartNotifOpen = false;
    }
});

// Notification helpers (expected by header-notifications logic)
var smartNotifOpen = false;
var smartAlertsLoaded = false;
function toggleSmartNotif() {
    smartNotifOpen = !smartNotifOpen;
    document.getElementById('smart-notif-panel').style.display = smartNotifOpen ? 'block' : 'none';
    if (smartNotifOpen && !smartAlertsLoaded) {
        smartAlertsLoaded = true;
        loadSmartAlerts();
        if (!$('#notifications_list').data('loaded')) loadNotifications();
    }
}
function loadSmartAlerts() {
    $.get('{{ route("smart.alerts") }}', function(data) {
        var html = '';
        if (data.alerts && data.alerts.length) {
            data.alerts.forEach(function(a) {
                html += '<a href="'+(a.url||'#')+'" style="display:flex;align-items:center;gap:10px;padding:10px 16px;text-decoration:none;color:#1e293b;border-bottom:1px solid #f8fafc;" onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'\'">'
                    + '<div style="font-size:13px;font-weight:500;">'+a.title+'</div>'
                    + '<div style="font-size:12px;color:#94a3b8;margin-left:auto;">'+a.message+'</div></a>';
            });
        } else { html = '<div style="padding:12px 16px;color:#94a3b8;font-size:13px;">No alerts ✓</div>'; }
        document.getElementById('smart-alerts-list').innerHTML = html;
        if (data.total_alert_count > 0) {
            var b = document.getElementById('notif-badge');
            if (b) b.style.display = 'block';
        }
    });
}
function loadNotifications() {
    $('#notifications_list').data('loaded', true);
    $.get('{{ action([\App\Http\Controllers\NotificationController::class, "getNotifications"]) }}', {page:1}, function(d) {
        $('#notifications_list').html(d);
    });
}
function markAllRead() {
    $.post('{{ action([\App\Http\Controllers\NotificationController::class, "markAllRead"]) }}', {_token:'{{ csrf_token() }}'}, function() {
        var b = document.getElementById('notif-badge');
        if (b) b.style.display = 'none';
        loadNotifications();
    });
}

// Today's profit
@can('profit_loss_report.view')
document.addEventListener('DOMContentLoaded', function() {
    var el = document.getElementById('hdr_profit_val');
    if (!el) return;
    $.ajax({
        url: '/reports/profit-loss',
        data: { start_date: '{{ now()->toDateString() }}', end_date: '{{ now()->toDateString() }}', type: 'product' },
        success: function(html) {
            var m = html && html.match(/id="gross_profit"[^>]*>([^<]+)</);
            el.textContent = (m && m[1]) ? m[1].trim() : 'View';
        },
        error: function() { el.textContent = 'View'; }
    });
});
@endcan
</script>
