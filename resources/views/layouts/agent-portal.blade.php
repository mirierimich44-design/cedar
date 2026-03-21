@inject('request', 'Illuminate\Http\Request')
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ Session::get('business.name') }}</title>

    @include('layouts.partials.css')

    @yield('css')

    <style>
        body.agent-portal-body {
            background: #f1f5f9;
            padding: 0;
            margin: 0;
        }
        /* Agent header */
        .agent-header {
            background: linear-gradient(to right, var(--theme-dark), var(--theme-main));
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .agent-header-left { display: flex; flex-direction: column; }
        .agent-header-biz { color: #fff; font-size: 15px; font-weight: 700; line-height: 1.2; }
        .agent-header-user { color: rgba(255,255,255,0.75); font-size: 12px; margin-top: 2px; }
        .agent-header-logout {
            color: rgba(255,255,255,0.85);
            font-size: 12px;
            font-weight: 600;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 6px 12px;
            border-radius: 8px;
            text-decoration: none;
            white-space: nowrap;
        }
        .agent-header-logout:hover { background: rgba(255,255,255,0.25); color: #fff; text-decoration: none; }

        /* Main content area */
        #agent-content {
            padding: 16px;
            padding-bottom: 80px; /* space for bottom nav */
            min-height: calc(100vh - 56px);
        }

        /* Bottom navigation */
        .agent-bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            z-index: 1049;
            background: linear-gradient(to right, var(--theme-dark), var(--theme-main));
            display: flex;
            box-shadow: 0 -2px 16px rgba(0,0,0,0.18);
            padding: 6px 0 max(6px, env(safe-area-inset-bottom, 6px));
        }
        .agent-bottom-nav a {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            color: rgba(255,255,255,0.60);
            text-decoration: none !important;
            font-size: 10px;
            font-weight: 600;
            padding: 6px 4px 4px;
            letter-spacing: 0.02em;
            -webkit-tap-highlight-color: transparent;
            transition: color 0.12s;
        }
        .agent-bottom-nav a.nav-active,
        .agent-bottom-nav a:hover { color: #ffffff; }
        .agent-bottom-nav a i {
            font-size: 20px;
            display: block;
        }
        /* Content sections (section.content / section.content-header) */
        .agent-portal-body section.content-header { padding: 0 0 12px 0; }
        .agent-portal-body section.content { padding: 0; }
    </style>
</head>

<body class="agent-portal-body">

    {{-- Currency / session hidden fields (needed by JS helpers) --}}
    <input type="hidden" id="__code"             value="{{ session('currency')['code'] }}">
    <input type="hidden" id="__symbol"           value="{{ session('currency')['symbol'] }}">
    <input type="hidden" id="__thousand"         value="{{ session('currency')['thousand_separator'] }}">
    <input type="hidden" id="__decimal"          value="{{ session('currency')['decimal_separator'] }}">
    <input type="hidden" id="__symbol_placement" value="{{ session('business.currency_symbol_placement') }}">
    <input type="hidden" id="__precision"        value="{{ session('business.currency_precision', 2) }}">
    <input type="hidden" id="__quantity_precision" value="{{ session('business.quantity_precision', 2) }}">
    @if(session('status'))
        <input type="hidden" id="status_span"
               data-status="{{ session('status.success') }}"
               data-msg="{{ session('status.msg') }}">
    @endif

    {{-- ── Top Header ──────────────────────────────────────────────── --}}
    <header class="agent-header">
        <div class="agent-header-left">
            <span class="agent-header-biz">{{ Session::get('business.name') }}</span>
            <span class="agent-header-user">
                <i class="fa fa-user-circle" style="font-size:11px;"></i>
                {{ auth()->user()->first_name ?? auth()->user()->name }}
                &nbsp;·&nbsp; Agent
            </span>
        </div>
        <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}"
           onclick="event.preventDefault(); document.getElementById('agent-logout-form').submit();"
           class="agent-header-logout">
            <i class="fa fa-sign-out"></i> Logout
        </a>
        <form id="agent-logout-form"
              action="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}"
              method="POST" style="display:none;">
            @csrf
        </form>
    </header>

    {{-- ── Page content ─────────────────────────────────────────────── --}}
    <div id="agent-content">
        @yield('content')
    </div>

    {{-- ── Bottom Navigation ────────────────────────────────────────── --}}
    <nav class="agent-bottom-nav" aria-label="Agent Navigation">
        <a href="{{ route('cooler.agent.dashboard') }}"
           class="{{ request()->routeIs('cooler.agent.dashboard') ? 'nav-active' : '' }}">
            <i class="fa fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('cooler.agent.customers') }}"
           class="{{ request()->routeIs('cooler.agent.customers*') || request()->routeIs('cooler.agent.customer*') ? 'nav-active' : '' }}">
            <i class="fa fa-users"></i>
            <span>Customers</span>
        </a>
        <a href="{{ route('cooler.agent.retrievals') }}"
           class="{{ request()->routeIs('cooler.agent.retrievals*') ? 'nav-active' : '' }}">
            <i class="fa fa-truck"></i>
            <span>Retrievals</span>
        </a>
        <a href="{{ route('cooler.agent.orders.create') }}"
           class="{{ request()->routeIs('cooler.agent.orders*') ? 'nav-active' : '' }}">
            <i class="fa fa-shopping-cart"></i>
            <span>New Order</span>
        </a>
    </nav>

    @include('layouts.partials.javascripts')

    @yield('javascript')

    {{-- Flash message — runs after jQuery + toastr are loaded --}}
    <script>
        $(function () {
            var statusEl = document.getElementById('status_span');
            if (statusEl) {
                var ok  = statusEl.dataset.status;
                var msg = statusEl.dataset.msg;
                if (msg) {
                    ok === '1' ? toastr.success(msg) : toastr.error(msg);
                }
            }
        });
    </script>

    <div class="modal fade view_modal" tabindex="-1" role="dialog"></div>

</body>
</html>
