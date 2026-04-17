<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name', 'POS') }}</title>
    @include('layouts.partials.css')
    @include('layouts.partials.extracss_auth')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #fff !important;
        }
        .auth-split-left {
            background: linear-gradient(145deg, #4f46e5 0%, #3b82f6 55%, #0ea5e9 100%);
        }
        .auth-feature-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .auth-stat-divider {
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        .auth-deco-circle-1 {
            position: absolute; top: 60px; right: -40px;
            width: 180px; height: 180px; border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .auth-deco-circle-2 {
            position: absolute; bottom: 120px; right: 40px;
            width: 90px; height: 90px; border-radius: 50%;
            background: rgba(255,255,255,0.07);
        }
        .auth-deco-circle-3 {
            position: absolute; top: 45%; left: -30px;
            width: 120px; height: 120px; border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }
        .auth-right-panel {
            background: #ffffff;
            overflow-y: auto;
        }
        .auth-form-wrapper {
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
        }
        /* Override any old auth background */
        .right-col { padding: 0 !important; background: transparent !important; }
    </style>
</head>

<body class="pace-done">
    @inject('request', 'Illuminate\Http\Request')

    @if (session('status') && session('status.success'))
        <input type="hidden" id="status_span"
            data-status="{{ session('status.success') }}"
            data-msg="{{ session('status.msg') }}">
    @endif

    <div style="display:flex; min-height:100vh;">

        {{-- ===================== LEFT BRAND PANEL ===================== --}}
        <div class="auth-split-left tw-hidden lg:tw-flex tw-flex-col tw-w-1/2 tw-relative tw-overflow-hidden">

            {{-- Decorative circles --}}
            <div class="auth-deco-circle-1"></div>
            <div class="auth-deco-circle-2"></div>
            <div class="auth-deco-circle-3"></div>

            {{-- Logo / app name --}}
            <div class="tw-p-8 tw-z-10">
                <a href="{{ url('/') }}" class="tw-flex tw-items-center tw-gap-3 tw-no-underline">
                    <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        <img src="{{ asset('img/logo-small.png') }}"
                             alt="{{ config('app.name') }}"
                             style="width:36px;height:36px;object-fit:contain;">
                    </div>
                    <span style="color:#fff;font-weight:700;font-size:1.25rem;letter-spacing:-0.01em;">
                        {{ config('app.name', 'UltimatePOS') }}
                    </span>
                </a>
            </div>

            {{-- Central hero content --}}
            <div class="tw-flex-1 tw-flex tw-flex-col tw-justify-center tw-px-12 tw-pb-8 tw-z-10">
                <div style="max-width:420px;">
                    <h1 style="color:#fff;font-size:2.25rem;font-weight:800;line-height:1.2;margin-bottom:1rem;letter-spacing:-0.02em;">
                        Manage your business<br>smarter &amp; faster
                    </h1>
                    <p style="color:rgba(255,255,255,0.8);font-size:1.05rem;margin-bottom:2.5rem;line-height:1.6;">
                        All-in-one POS, inventory, accounting and CRM platform built for growing businesses worldwide.
                    </p>

                    {{-- Feature list --}}
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:1rem;">
                        @php
                            $features = [
                                ['icon' => 'fa-shopping-cart',       'text' => 'Point of Sale & Inventory Management'],
                                ['icon' => 'fa-chart-line',          'text' => 'Real-time Sales Analytics & Reports'],
                                ['icon' => 'fa-users',               'text' => 'Customer & Supplier CRM'],
                                ['icon' => 'fa-file-invoice-dollar', 'text' => 'Invoicing, Accounting & Expenses'],
                                ['icon' => 'fa-store',               'text' => 'Multi-location & Multi-user Support'],
                            ];
                        @endphp
                        @foreach($features as $feature)
                            <li style="display:flex;align-items:center;gap:0.875rem;">
                                <div class="auth-feature-icon">
                                    <i class="fas {{ $feature['icon'] }}" style="color:#fff;font-size:0.875rem;"></i>
                                </div>
                                <span style="color:#fff;font-weight:500;font-size:0.95rem;">{{ $feature['text'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Bottom stats bar --}}
            <div class="tw-px-12 tw-pb-10 tw-z-10">
                <div class="auth-stat-divider tw-pt-6" style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
                    <div>
                        <p style="color:#fff;font-size:1.5rem;font-weight:800;margin:0;">10k+</p>
                        <p style="color:rgba(255,255,255,0.65);font-size:0.8rem;margin:0;">Businesses</p>
                    </div>
                    <div>
                        <p style="color:#fff;font-size:1.5rem;font-weight:800;margin:0;">50+</p>
                        <p style="color:rgba(255,255,255,0.65);font-size:0.8rem;margin:0;">Countries</p>
                    </div>
                    <div>
                        <p style="color:#fff;font-size:1.5rem;font-weight:800;margin:0;">99.9%</p>
                        <p style="color:rgba(255,255,255,0.65);font-size:0.8rem;margin:0;">Uptime</p>
                    </div>
                </div>
            </div>
        </div>
        {{-- ===================== END LEFT PANEL ===================== --}}


        {{-- ===================== RIGHT CONTENT PANEL ===================== --}}
        <div class="auth-right-panel tw-flex tw-flex-col" style="flex:1;">

            {{-- Mobile top bar --}}
            <div class="tw-flex lg:tw-hidden tw-items-center tw-justify-between tw-px-5 tw-py-4"
                 style="background:linear-gradient(90deg,#4f46e5,#3b82f6);">
                <a href="{{ url('/') }}" class="tw-flex tw-items-center tw-gap-2 tw-no-underline">
                    <img src="{{ asset('img/logo-small.png') }}" alt="{{ config('app.name') }}"
                         style="width:30px;height:30px;object-fit:contain;">
                    <span style="color:#fff;font-weight:700;font-size:1rem;">{{ config('app.name', 'UltimatePOS') }}</span>
                </a>
                <div class="tw-flex tw-items-center tw-gap-3">
                    @if (!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                        @if (config('constants.allow_registration'))
                            <a href="{{ route('business.getRegister') }}@if(!empty(request()->lang)){{'?lang='.request()->lang}}@endif"
                               style="color:#fff;font-size:0.85rem;font-weight:600;text-decoration:none;">
                                {{ __('business.register') }}
                            </a>
                        @endif
                    @endif
                    @include('layouts.partials.language_btn')
                </div>
            </div>

            {{-- Desktop top-right nav --}}
            <div class="tw-hidden lg:tw-flex tw-items-center tw-justify-end tw-gap-5 tw-px-8 tw-pt-6">
                @if (!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                    @if (config('constants.allow_registration'))
                        <a href="{{ route('business.getRegister') }}@if(!empty(request()->lang)){{'?lang='.request()->lang}}@endif"
                           style="color:#6b7280;font-size:0.875rem;font-weight:500;text-decoration:none;">
                            {{ __('business.not_yet_registered') }}
                            <span style="color:#4f46e5;font-weight:700;">{{ __('business.register') }}</span>
                        </a>
                        @if (Route::has('pricing') && config('app.env') != 'demo' && $request->segment(1) != 'pricing')
                            <a style="color:#6b7280;font-size:0.875rem;font-weight:500;text-decoration:none;"
                               href="{{ action([\Modules\Superadmin\Http\Controllers\PricingController::class, 'index']) }}">
                                @lang('superadmin::lang.pricing')
                            </a>
                        @endif
                    @endif
                @endif
                @if ($request->segment(1) != 'login')
                    <a style="color:#6b7280;font-size:0.875rem;font-weight:500;text-decoration:none;"
                       href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}@if(!empty(request()->lang)){{'?lang='.request()->lang}}@endif">
                        {{ __('business.sign_in') }}
                    </a>
                @endif
                @include('layouts.partials.language_btn')
            </div>

            {{-- Form area --}}
            <div class="tw-flex-1 tw-flex tw-items-center tw-justify-center tw-px-6 tw-py-10">
                <div class="auth-form-wrapper">
                    @yield('content')
                </div>
            </div>

            {{-- Footer --}}
            <div class="tw-hidden lg:tw-flex tw-items-center tw-justify-center tw-pb-6">
                <p style="color:#9ca3af;font-size:0.8rem;margin:0;">
                    &copy; {{ date('Y') }} {{ config('app.name', 'UltimatePOS') }}. All rights reserved.
                </p>
            </div>
        </div>
        {{-- ===================== END RIGHT PANEL ===================== --}}

    </div>

    @include('layouts.partials.javascripts')
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    @yield('javascript')

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2_register').select2();
        });
    </script>
    <style>
        .wizard > .content { background-color: white !important; }
    </style>
</body>
</html>
