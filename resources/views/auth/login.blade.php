@extends('layouts.auth2')
@section('title', __('lang_v1.login'))
@inject('request', 'Illuminate\Http\Request')

@section('content')
@php
    $username = old('username');
    $password = null;
    if (config('app.env') == 'demo') {
        $username = 'admin';
        $password = '123456';
        $demo_types = [
            'all_in_one'    => 'admin',
            'super_market'  => 'admin',
            'pharmacy'      => 'admin-pharmacy',
            'electronics'   => 'admin-electronics',
            'services'      => 'admin-services',
            'restaurant'    => 'admin-restaurant',
            'superadmin'    => 'superadmin',
            'woocommerce'   => 'woocommerce_user',
            'essentials'    => 'admin-essentials',
            'manufacturing' => 'manufacturer-demo',
        ];
        if (!empty($_GET['demo_type']) && array_key_exists($_GET['demo_type'], $demo_types)) {
            $username = $demo_types[$_GET['demo_type']];
        }
    }
@endphp

<style>
    html, body { background: white !important; margin: 0; padding: 0; }
    .right-col { padding: 0 !important; margin: 0 !important; }
    .container-fluid, .row.eq-height-row { padding: 0 !important; margin: 0 !important; }

    .split-wrap {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 9999;
        display: flex;
    }

    /* ── LEFT PANEL ── */
    .sp-left {
        width: 52%;
        min-width: 52%;
        background: linear-gradient(150deg, #0f766e 0%, #0369a1 55%, #1e3a8a 100%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 60px 56px;
        position: relative;
        overflow: hidden;
    }
    .sp-left::before {
        content: '';
        position: absolute; top: -140px; right: -100px;
        width: 380px; height: 380px; border-radius: 50%;
        background: rgba(255,255,255,0.06);
        pointer-events: none;
    }
    .sp-left::after {
        content: '';
        position: absolute; bottom: -100px; left: -80px;
        width: 300px; height: 300px; border-radius: 50%;
        background: rgba(255,255,255,0.05);
        pointer-events: none;
    }
    .sp-brand {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 52px;
        position: relative; z-index: 1;
        text-decoration: none;
    }
    .sp-brand img {
        width: 48px; height: 48px; border-radius: 12px;
        background: white; padding: 6px; object-fit: contain;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    }
    .sp-brand span { color: white; font-size: 1.3rem; font-weight: 800; }
    .sp-headline {
        color: #ffffff;
        font-size: clamp(1.6rem, 2.5vw, 2.4rem);
        font-weight: 800;
        line-height: 1.25;
        margin: 0 0 14px;
        position: relative; z-index: 1;
        text-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .sp-tagline {
        color: rgba(255,255,255,0.82);
        font-size: 0.97rem;
        line-height: 1.65;
        margin-bottom: 44px;
        position: relative; z-index: 1;
        max-width: 380px;
    }
    .sp-features { list-style: none; padding: 0; margin: 0; position: relative; z-index: 1; }
    .sp-features li {
        display: flex; align-items: center; gap: 14px;
        color: rgba(255,255,255,0.9);
        font-size: 0.94rem;
        font-weight: 500;
        margin-bottom: 18px;
    }
    .sp-icon {
        width: 40px; height: 40px; min-width: 40px;
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }

    /* ── RIGHT PANEL ── */
    .sp-right {
        flex: 1;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 48px 52px;
        overflow-y: auto;
    }
    .sp-form-box { width: 100%; max-width: 420px; }
    .sp-form-box h2 {
        font-size: 1.85rem; font-weight: 800;
        color: #0f172a; margin: 0 0 6px;
    }
    .sp-form-box .sp-sub {
        color: #64748b; font-size: 0.93rem; margin-bottom: 34px;
    }
    .sp-label {
        display: block;
        font-size: 0.84rem; font-weight: 600;
        color: #374151; margin-bottom: 6px;
    }
    .sp-input {
        width: 100%; height: 50px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 0 44px 0 14px;
        font-size: 0.95rem;
        color: #0f172a !important;
        background-color: #ffffff !important;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.18s, box-shadow 0.18s;
        -webkit-text-fill-color: #0f172a !important;
    }
    .sp-input:focus {
        border-color: #0f766e;
        box-shadow: 0 0 0 3px rgba(15,118,110,0.14);
    }
    /* Fix browser autofill gray background */
    .sp-input:-webkit-autofill,
    .sp-input:-webkit-autofill:hover,
    .sp-input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 50px #ffffff inset !important;
        -webkit-text-fill-color: #0f172a !important;
        background-color: #ffffff !important;
        caret-color: #0f172a;
    }
    .sp-field { margin-bottom: 20px; }
    .sp-field-head {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 6px;
    }
    .sp-forgot {
        font-size: 0.83rem; font-weight: 600;
        color: #0f766e; text-decoration: none;
    }
    .sp-forgot:hover { text-decoration: underline; }
    .sp-input-wrap { position: relative; }
    .sp-eye {
        position: absolute; right: 13px; top: 50%;
        transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: #94a3b8; padding: 0; line-height: 1;
        display: flex; align-items: center;
    }
    .sp-eye:hover { color: #475569; }
    .sp-remember {
        display: flex; align-items: center; gap: 8px;
        font-size: 0.875rem; color: #475569;
        cursor: pointer; margin-bottom: 0;
    }
    .sp-remember input[type=checkbox] {
        accent-color: #0f766e; width: 16px; height: 16px;
    }
    .sp-submit {
        width: 100%; height: 52px;
        background: linear-gradient(135deg, #0f766e, #0369a1);
        border: none; border-radius: 12px;
        color: #ffffff; font-size: 1rem; font-weight: 700;
        cursor: pointer; margin-top: 22px;
        transition: all 0.2s; letter-spacing: 0.3px;
    }
    .sp-submit:hover {
        background: linear-gradient(135deg, #0d9488, #0284c7);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(15,118,110,0.4);
    }
    .sp-error { color: #ef4444; font-size: 0.82rem; margin-top: 5px; }
    .sp-register {
        text-align: center; margin-top: 22px;
        font-size: 0.875rem; color: #64748b;
    }
    .sp-register a {
        color: #0f766e; font-weight: 700; text-decoration: none;
    }
    .sp-register a:hover { text-decoration: underline; }

    /* Mobile: hide left panel below 860px */
    @media (max-width: 860px) {
        .sp-left { display: none; }
        .sp-right { padding: 40px 28px; }
    }
</style>

<div class="split-wrap">

    {{-- LEFT PANEL --}}
    <div class="sp-left">
        <a href="{{ url('/') }}" class="sp-brand">
            <img src="{{ asset('img/logo-small.png') }}" alt="{{ config('app.name') }}">
            <span>{{ config('app.name', 'Reenson Pharmacy') }}</span>
        </a>

        <h2 class="sp-headline">Pharmacy Management<br>Made Simple.</h2>
        <p class="sp-tagline">A complete solution for DDA compliance, inventory, prescriptions, and pharmacy sales — all in one platform.</p>

        <ul class="sp-features">
            <li><div class="sp-icon">💊</div><span>DDA Drug Compliance &amp; Audit Trails</span></li>
            <li><div class="sp-icon">📋</div><span>Prescription Tracking &amp; Dispensing</span></li>
            <li><div class="sp-icon">📦</div><span>Real-time Inventory &amp; Expiry Alerts</span></li>
            <li><div class="sp-icon">📊</div><span>Sales Reports &amp; Financial Analytics</span></li>
            <li><div class="sp-icon">💬</div><span>Automated SMS Notifications</span></li>
        </ul>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="sp-right">
        <div class="sp-form-box">

            <h2>Welcome back</h2>
            <p class="sp-sub">Sign in to your {{ config('app.name', 'Reenson Pharmacy') }} account</p>

            <form method="POST" action="{{ route('login') }}" id="login-form">
                {{ csrf_field() }}

                {{-- Username --}}
                <div class="sp-field">
                    <label class="sp-label">@lang('lang_v1.username')</label>
                    <input class="sp-input" type="text" name="username" id="username"
                           value="{{ $username }}" required autofocus
                           placeholder="Enter your username">
                    @if($errors->has('username'))
                        <div class="sp-error">{{ $errors->first('username') }}</div>
                    @endif
                </div>

                {{-- Password --}}
                <div class="sp-field">
                    <div class="sp-field-head">
                        <label class="sp-label" style="margin:0;">@lang('lang_v1.password')</label>
                        @if(config('app.env') != 'demo')
                            <a href="{{ route('password.request') }}" class="sp-forgot">@lang('lang_v1.forgot_your_password')</a>
                        @endif
                    </div>
                    <div class="sp-input-wrap">
                        <input class="sp-input" type="password" name="password" id="password"
                               value="{{ $password }}" required
                               placeholder="Enter your password">
                        <button type="button" id="show_hide_icon" class="sp-eye">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                            </svg>
                        </button>
                    </div>
                    @if($errors->has('password'))
                        <div class="sp-error">{{ $errors->first('password') }}</div>
                    @endif
                </div>

                {{-- Remember --}}
                <label class="sp-remember">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    @lang('lang_v1.remember_me')
                </label>

                @if(config('constants.enable_recaptcha'))
                <div style="margin-top:16px;">
                    <div class="g-recaptcha" data-sitekey="{{ config('constants.google_recaptcha_key') }}"></div>
                    @if($errors->has('g-recaptcha-response'))
                        <div class="sp-error">{{ $errors->first('g-recaptcha-response') }}</div>
                    @endif
                </div>
                @endif

                <button type="submit" class="sp-submit">@lang('lang_v1.login')</button>

                @if(config('constants.allow_registration'))
                <p class="sp-register">
                    {{ __('business.not_yet_registered') }}
                    <a href="{{ route('business.getRegister') }}">{{ __('business.register_now') }}</a>
                </p>
                @endif
            </form>

            @if(config('app.env') == 'demo')
            <div style="margin-top:24px; padding:16px; background:#f1f5f9; border-radius:10px;">
                <p style="font-size:0.78rem; font-weight:700; color:#475569; margin:0 0 10px; text-transform:uppercase; letter-spacing:1px;">Demo Logins</p>
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @foreach($demo_types as $type => $admin)
                    <a href="?demo_type={{ $type }}" class="demo-login" data-admin="{{ $admin }}"
                       style="font-size:0.78rem; background:#0f766e; color:white !important; padding:4px 10px; border-radius:6px; text-decoration:none;">
                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@stop

@section('javascript')
<script>
$(document).ready(function() {
    $('.change_lang').click(function() {
        window.location = "{{ route('login') }}?lang=" + $(this).attr('value');
    });
    $('a.demo-login').click(function(e) {
        e.preventDefault();
        $('#username').val($(this).data('admin'));
        $('#password').val("{{ $password }}");
        $('form#login-form').submit();
    });
    $('#show_hide_icon').on('click', function(e) {
        e.preventDefault();
        var pw = $('#password');
        var isHidden = pw.attr('type') === 'password';
        pw.attr('type', isHidden ? 'text' : 'password');
        $(this).html(isHidden
            ? '<svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/><path d="M16.681 16.673a8.717 8.717 0 0 1-4.681 1.327c-3.6 0-6.6-2-9-6c1.272-2.12 2.712-3.678 4.32-4.674m2.86-1.146a9.055 9.055 0 0 1 1.82-.18c3.6 0 6.6 2 9 6c-.666 1.11-1.379 2.067-2.138 2.87"/><path d="M3 3l18 18"/></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0-4 0"/><path d="M21 12c-2.4 4-5.4 6-9 6c-3.6 0-6.6-2-9-6c2.4-4 5.4-6 9-6c3.6 0 6.6 2 9 6"/></svg>'
        );
    });
});
</script>
@endsection
