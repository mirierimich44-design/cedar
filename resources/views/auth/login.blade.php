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
            'all_in_one'   => 'admin',
            'super_market' => 'admin',
            'pharmacy'     => 'admin-pharmacy',
            'electronics'  => 'admin-electronics',
            'services'     => 'admin-services',
            'restaurant'   => 'admin-restaurant',
            'superadmin'   => 'superadmin',
            'woocommerce'  => 'woocommerce_user',
            'essentials'   => 'admin-essentials',
            'manufacturing'=> 'manufacturer-demo',
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

    .split-screen {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        display: flex;
        z-index: 9999;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* ─── LEFT PANEL ─── */
    .split-left {
        width: 55%;
        background: linear-gradient(145deg, #0f766e 0%, #0369a1 60%, #1e40af 100%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        padding: 60px 64px;
        position: relative;
        overflow: hidden;
    }
    .split-left::before {
        content: '';
        position: absolute;
        top: -120px; right: -120px;
        width: 400px; height: 400px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .split-left::after {
        content: '';
        position: absolute;
        bottom: -100px; left: -80px;
        width: 320px; height: 320px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
    }
    .split-left-brand {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 48px;
        position: relative; z-index: 1;
    }
    .split-left-brand img {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: white;
        padding: 6px;
        object-fit: contain;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .split-left-brand span {
        color: white;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: 0.3px;
    }
    .split-left h2 {
        color: white;
        font-size: 2.4rem;
        font-weight: 800;
        line-height: 1.2;
        margin: 0 0 14px;
        position: relative; z-index: 1;
    }
    .split-left .tagline {
        color: rgba(255,255,255,0.8);
        font-size: 1.05rem;
        line-height: 1.6;
        margin-bottom: 44px;
        position: relative; z-index: 1;
        max-width: 400px;
    }
    .feature-list {
        list-style: none;
        padding: 0; margin: 0;
        display: flex;
        flex-direction: column;
        gap: 18px;
        position: relative; z-index: 1;
    }
    .feature-list li {
        display: flex;
        align-items: center;
        gap: 14px;
        color: rgba(255,255,255,0.92);
        font-size: 0.95rem;
        font-weight: 500;
    }
    .feature-list li .icon-wrap {
        width: 38px; height: 38px;
        min-width: 38px;
        border-radius: 10px;
        background: rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }

    /* ─── RIGHT PANEL ─── */
    .split-right {
        width: 45%;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 50px 56px;
        overflow-y: auto;
    }
    .login-box {
        width: 100%;
        max-width: 400px;
    }
    .login-box h3 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .login-box .subtitle {
        color: #64748b;
        font-size: 0.95rem;
        margin-bottom: 32px;
    }
    .form-label-text {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }
    .modern-input {
        width: 100%;
        height: 48px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 0 14px;
        font-size: 0.95rem;
        color: #0f172a;
        background: white;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .modern-input:focus {
        border-color: #0f766e;
        box-shadow: 0 0 0 3px rgba(15,118,110,0.12);
    }
    .input-wrapper { position: relative; }
    .eye-btn {
        position: absolute;
        right: 12px; top: 50%;
        transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: #94a3b8; padding: 0;
        display: flex; align-items: center;
    }
    .eye-btn:hover { color: #475569; }
    .login-submit-btn {
        width: 100%;
        height: 50px;
        background: linear-gradient(135deg, #0f766e, #0369a1);
        border: none;
        border-radius: 12px;
        color: white;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        margin-top: 24px;
        transition: all 0.2s;
        letter-spacing: 0.3px;
    }
    .login-submit-btn:hover {
        background: linear-gradient(135deg, #0d9488, #0284c7);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(15,118,110,0.35);
    }
    .remember-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 14px;
    }
    .remember-row label {
        display: flex; align-items: center; gap: 8px;
        font-size: 0.875rem; color: #475569; cursor: pointer;
    }
    .remember-row input[type=checkbox] { accent-color: #0f766e; width: 16px; height: 16px; }
    .forgot-link {
        font-size: 0.875rem;
        color: #0f766e;
        text-decoration: none;
        font-weight: 600;
    }
    .forgot-link:hover { text-decoration: underline; }
    .error-text { color: #ef4444; font-size: 0.82rem; margin-top: 4px; }
    .form-group-mod { margin-bottom: 18px; }

    @media (max-width: 768px) {
        .split-left { display: none; }
        .split-right { width: 100%; padding: 40px 28px; }
    }
</style>

<div class="split-screen">

    {{-- LEFT PANEL --}}
    <div class="split-left">
        <div class="split-left-brand">
            <img src="{{ asset('img/logo-small.png') }}" alt="{{ config('app.name') }}">
            <span>{{ config('app.name', 'Reenson Pharmacy') }}</span>
        </div>

        <h2>Pharmacy Management<br>Made Simple.</h2>
        <p class="tagline">A complete solution for DDA compliance, inventory, prescriptions, and pharmacy sales — all in one place.</p>

        <ul class="feature-list">
            <li>
                <div class="icon-wrap">💊</div>
                <span>DDA Drug Compliance & Audit Trails</span>
            </li>
            <li>
                <div class="icon-wrap">📋</div>
                <span>Prescription Tracking & Dispensing</span>
            </li>
            <li>
                <div class="icon-wrap">📦</div>
                <span>Real-time Inventory & Expiry Alerts</span>
            </li>
            <li>
                <div class="icon-wrap">📊</div>
                <span>Sales Reports & Financial Analytics</span>
            </li>
            <li>
                <div class="icon-wrap">💬</div>
                <span>Automated SMS Notifications</span>
            </li>
        </ul>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="split-right">
        <div class="login-box">

            <h3>Welcome back</h3>
            <p class="subtitle">Sign in to your {{ config('app.name', 'Reenson Pharmacy') }} account</p>

            <form method="POST" action="{{ route('login') }}" id="login-form">
                {{ csrf_field() }}

                {{-- Username --}}
                <div class="form-group-mod">
                    <span class="form-label-text">@lang('lang_v1.username')</span>
                    <input class="modern-input" type="text" name="username" id="username"
                           value="{{ $username }}" required autofocus
                           placeholder="Enter your username">
                    @if ($errors->has('username'))
                        <div class="error-text">{{ $errors->first('username') }}</div>
                    @endif
                </div>

                {{-- Password --}}
                <div class="form-group-mod">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <span class="form-label-text" style="margin:0;">@lang('lang_v1.password')</span>
                        @if(config('app.env') != 'demo')
                            <a href="{{ route('password.request') }}" class="forgot-link">@lang('lang_v1.forgot_your_password')</a>
                        @endif
                    </div>
                    <div class="input-wrapper">
                        <input class="modern-input" type="password" name="password" id="password"
                               value="{{ $password }}" required
                               placeholder="Enter your password" style="padding-right: 44px;">
                        <button type="button" id="show_hide_icon" class="eye-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                            </svg>
                        </button>
                    </div>
                    @if ($errors->has('password'))
                        <div class="error-text">{{ $errors->first('password') }}</div>
                    @endif
                </div>

                {{-- Remember Me --}}
                <div class="remember-row">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        @lang('lang_v1.remember_me')
                    </label>
                </div>

                @if(config('constants.enable_recaptcha'))
                <div style="margin-top: 16px;">
                    <div class="g-recaptcha" data-sitekey="{{ config('constants.google_recaptcha_key') }}"></div>
                    @if ($errors->has('g-recaptcha-response'))
                        <div class="error-text">{{ $errors->first('g-recaptcha-response') }}</div>
                    @endif
                </div>
                @endif

                <button type="submit" class="login-submit-btn">@lang('lang_v1.login')</button>

                @if(config('constants.allow_registration'))
                <p style="text-align:center; margin-top:20px; font-size:0.875rem; color:#64748b;">
                    {{ __('business.not_yet_registered') }}
                    <a href="{{ route('business.getRegister') }}" style="color:#0f766e; font-weight:700; text-decoration:none;">{{ __('business.register_now') }}</a>
                </p>
                @endif
            </form>

            @if(config('app.env') == 'demo')
            <div style="margin-top: 24px; padding: 16px; background: #f1f5f9; border-radius: 10px;">
                <p style="font-size:0.8rem; font-weight:700; color:#475569; margin:0 0 10px; text-transform:uppercase; letter-spacing:1px;">Demo Logins</p>
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @foreach($demo_types as $type => $admin)
                    <a href="?demo_type={{ $type }}" class="demo-login" data-admin="{{ $admin }}"
                       style="font-size:0.78rem; background:#0f766e; color:white; padding:4px 10px; border-radius:6px; text-decoration:none;">
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
        const pw = $('#password');
        const isPassword = pw.attr('type') === 'password';
        pw.attr('type', isPassword ? 'text' : 'password');
        $(this).html(isPassword
            ? '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87"/><path d="M3 3l18 18"/></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>'
        );
    });
});
</script>
@endsection
