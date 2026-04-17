@inject('request', 'Illuminate\Http\Request')

@if (
    $request->segment(1) == 'pos' &&
        ($request->segment(2) == 'create' || $request->segment(3) == 'edit' || $request->segment(2) == 'payment'))
    @php
        $pos_layout = true;
    @endphp
@else
    @php
        $pos_layout = false;
    @endphp
@endif

@php
    $whitelist = ['127.0.0.1', '::1'];
@endphp

<!DOCTYPE html>
<html class="tw-bg-white tw-scroll-smooth" lang="{{ app()->getLocale() }}"
    dir="{{ in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr' }}">
<head>
    <!-- Tell the browser to be responsive to screen width -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title') - {{ Session::get('business.name') }}</title>

    @include('layouts.partials.css')
    
    <!-- Material Design Assets -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @include('layouts.partials.extracss')

    @yield('css')

</head>
<body
    class="tw-font-sans tw-antialiased tw-text-gray-900 tw-bg-gray-100 @if ($pos_layout) hold-transition lockscreen pos-material @else hold-transition skin-@if (!empty(session('business.theme_color'))){{ session('business.theme_color') }}@else{{ 'blue-light' }} @endif sidebar-mini backend-material @endif" >
    <div class="tw-flex thetop">
        <script type="text/javascript">
            if (localStorage.getItem("upos_sidebar_collapse") == 'true') {
                var body = document.getElementsByTagName("body")[0];
                body.className += " sidebar-collapse";
            }
        </script>
        @if (!$pos_layout && $request->segment(1) != 'customer-display')
            @include('layouts.partials.sidebar')
        @endif

        @if (in_array($_SERVER['REMOTE_ADDR'], $whitelist))
            <input type="hidden" id="__is_localhost" value="true">
        @endif

        <!-- Add currency related field-->
        @php $__cur = session('currency') ?? []; @endphp
        <input type="hidden" id="__code" value="{{ $__cur['code'] ?? '' }}">
        <input type="hidden" id="__symbol" value="{{ $__cur['symbol'] ?? '' }}">
        <input type="hidden" id="__thousand" value="{{ $__cur['thousand_separator'] ?? ',' }}">
        <input type="hidden" id="__decimal" value="{{ $__cur['decimal_separator'] ?? '.' }}">
        <input type="hidden" id="__symbol_placement" value="{{ session('business.currency_symbol_placement') }}">
        <input type="hidden" id="__precision" value="{{ session('business.currency_precision', 2) }}">
        <input type="hidden" id="__quantity_precision" value="{{ session('business.quantity_precision', 2) }}">
        <!-- End of currency related field-->
        @can('view_export_buttons')
            <input type="hidden" id="view_export_buttons">
        @endcan
        @if (isMobile())
            <input type="hidden" id="__is_mobile">
        @endif
        @if (session('status'))
            <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
                data-msg="{{ session('status.msg') }}">
        @endif
        <main class="tw-flex tw-flex-col tw-flex-1 tw-h-full tw-min-w-0 tw-bg-gray-100">
            @if($request->segment(1) != 'customer-display' && !$pos_layout)
                @include('layouts.partials.header')
            @elseif($request->segment(1) != 'customer-display')
                @include('layouts.partials.header-pos')
            @endif
            <!-- empty div for vuejs -->
            <div id="app">
                @yield('vue')
            </div>
            <div class="tw-flex-1 {{ $pos_layout ? 'tw-overflow-hidden' : 'tw-overflow-y-auto' }}" id="scrollable-container">
                @yield('content')
                @if (!$pos_layout)
                
                    @include('layouts.partials.footer')
                @else
                    @include('layouts.partials.footer_pos')
                @endif
            </div>
            <div class='scrolltop no-print'>
                <div class='scroll icon'><i class="fas fa-angle-up"></i></div>
            </div>

            @if (config('constants.iraqi_selling_price_adjustment'))
                <input type="hidden" id="iraqi_selling_price_adjustment">
            @endif

            <!-- This will be printed -->
            <section class="invoice print_section" id="receipt_section">
            </section>
        </main>

        @include('home.todays_profit_modal')
        <!-- /.content-wrapper -->



        <audio id="success-audio">
            <source src="{{ asset('/audio/success.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/success.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="error-audio">
            <source src="{{ asset('/audio/error.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/error.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="warning-audio">
            <source src="{{ asset('/audio/warning.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/warning.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>

        @if (!empty($__additional_html))
            {!! $__additional_html !!}
        @endif

        @include('layouts.partials.javascripts')
        
        {{-- Module JS --}}
        @include('layouts.module-assets')
        <div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
        <div class="modal fade" id="mpesa_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

        @if (!empty($__additional_views) && is_array($__additional_views))
            @foreach ($__additional_views as $additional_view)
                @includeIf($additional_view)
            @endforeach
        @endif
        <div>
            <div class="overlay tw-hidden" style="display: none; pointer-events: none;"></div>
        </div>
</body>
<style>
    @media print {
        #scrollable-container {
            overflow: visible !important;
            height: auto !important;
        }
        
        /* Hide side menu */
        .side-bar,
        .thetop > aside {
            display: none !important;
        }
    }
</style>
<style>
    .small-view-side-active {
        display: grid !important;
        z-index: 1000;
        position: absolute;
    }
    .overlay {
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.8);
        position: fixed;
        top: 0;
        left: 0;
        display: none;
        z-index: 20;
        pointer-events: none;
    }

    .overlay.active, .overlay.show {
        display: block;
        pointer-events: auto;
    }

    .tw-dw-btn.tw-dw-btn-xs.tw-dw-btn-outline {
        width: max-content;
        margin: 2px;
    }

    #scrollable-container{
        position:relative;
    }
    
</style>

<!-- Mobile Sidebar Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create sidebar overlay if it doesn't exist
    if (!document.querySelector('.sidebar-overlay')) {
        var overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }
    
    // Create mobile menu button if it doesn't exist
    var header = document.querySelector('.thetop > div:first-child, .main-header');
    if (header && !document.querySelector('.mobile-menu-toggle')) {
        var menuBtn = document.createElement('button');
        menuBtn.className = 'mobile-menu-toggle';
        menuBtn.innerHTML = '<i class="fas fa-bars"></i>';
        menuBtn.setAttribute('aria-label', 'Toggle menu');
        header.insertBefore(menuBtn, header.firstChild);
    }
    
    // Toggle sidebar function
    function toggleMobileSidebar() {
        var sidebar = document.querySelector('.side-bar, .main-sidebar, aside.side-bar');
        var overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar) {
            sidebar.classList.toggle('mobile-open');
        }
        if (overlay) {
            overlay.classList.toggle('active');
        }
    }
    
    // Close sidebar function
    function closeMobileSidebar() {
        var sidebar = document.querySelector('.side-bar, .main-sidebar, aside.side-bar');
        var overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar) {
            sidebar.classList.remove('mobile-open');
        }
        if (overlay) {
            overlay.classList.remove('active');
        }
    }
    
    // Event listeners
    document.addEventListener('click', function(e) {
        if (e.target.closest('.mobile-menu-toggle')) {
            e.preventDefault();
            toggleMobileSidebar();
        }
        if (e.target.classList.contains('sidebar-overlay')) {
            closeMobileSidebar();
        }
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMobileSidebar();
        }
    });
    
    // Close sidebar when clicking menu items on mobile
    var menuLinks = document.querySelectorAll('.side-bar a, .sidebar-menu a');
    menuLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                setTimeout(closeMobileSidebar, 150);
            }
        });
    });
});
</script>

</html>
