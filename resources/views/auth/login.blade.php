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

{{-- ===== LOGIN CARD ===== --}}
<div style="background:#fff;border-radius:1.25rem;box-shadow:0 4px 24px rgba(0,0,0,0.08);border:1px solid #e5e7eb;padding:2rem 2rem;">

    {{-- Header --}}
    <div style="text-align:center;margin-bottom:1.75rem;">
        {{-- Show logo on mobile (left panel is hidden) --}}
        <div class="tw-flex lg:tw-hidden tw-justify-center tw-mb-4">
            <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#4f46e5,#3b82f6);display:flex;align-items:center;justify-content:center;overflow:hidden;">
                <img src="{{ asset('img/logo-small.png') }}" alt="{{ config('app.name') }}" style="width:40px;height:40px;object-fit:contain;">
            </div>
        </div>
        <h1 style="font-size:1.375rem;font-weight:700;color:#111827;margin:0 0 0.25rem;">
            @lang('lang_v1.welcome_back')
        </h1>
        <p style="font-size:0.875rem;color:#6b7280;margin:0;">
            @lang('lang_v1.login_to_your') <strong>{{ config('app.name', 'UltimatePOS') }}</strong>
        </p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" id="login-form">
        {{ csrf_field() }}

        {{-- Username --}}
        <div style="margin-bottom:1.125rem;">
            <label style="display:block;font-size:0.875rem;font-weight:600;color:#374151;margin-bottom:0.375rem;">
                @lang('lang_v1.username')
            </label>
            <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                    <i class="fas fa-user" style="font-size:0.875rem;"></i>
                </span>
                <input
                    type="text"
                    name="username"
                    id="username"
                    value="{{ $username }}"
                    required
                    autofocus
                    data-last-active-input=""
                    placeholder="@lang('lang_v1.username')"
                    style="width:100%;height:44px;border:1.5px solid {{ $errors->has('username') ? '#ef4444' : '#d1d5db' }};border-radius:0.625rem;padding:0 0.875rem 0 2.375rem;font-size:0.9rem;color:#111827;background:#fff;outline:none;transition:border-color .2s;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#4f46e5'"
                    onblur="this.style.borderColor='{{ $errors->has('username') ? '#ef4444' : '#d1d5db' }}'"
                >
            </div>
            @if ($errors->has('username'))
                <p style="color:#ef4444;font-size:0.78rem;margin:0.3rem 0 0;">{{ $errors->first('username') }}</p>
            @endif
        </div>

        {{-- Password --}}
        <div style="margin-bottom:1.25rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.375rem;">
                <label style="font-size:0.875rem;font-weight:600;color:#374151;margin:0;">
                    @lang('lang_v1.password')
                </label>
                @if (config('app.env') != 'demo')
                    <a href="{{ route('password.request') }}"
                       style="font-size:0.8rem;font-weight:500;color:#4f46e5;text-decoration:none;"
                       tabindex="-1">
                        @lang('lang_v1.forgot_your_password')
                    </a>
                @endif
            </div>
            <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                    <i class="fas fa-lock" style="font-size:0.875rem;"></i>
                </span>
                <input
                    type="password"
                    name="password"
                    id="password"
                    value="{{ $password }}"
                    required
                    placeholder="@lang('lang_v1.password')"
                    style="width:100%;height:44px;border:1.5px solid {{ $errors->has('password') ? '#ef4444' : '#d1d5db' }};border-radius:0.625rem;padding:0 2.5rem 0 2.375rem;font-size:0.9rem;color:#111827;background:#fff;outline:none;transition:border-color .2s;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#4f46e5'"
                    onblur="this.style.borderColor='{{ $errors->has('password') ? '#ef4444' : '#d1d5db' }}'"
                >
                <button type="button" id="show_hide_icon"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:0;color:#9ca3af;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                    </svg>
                </button>
            </div>
            @if ($errors->has('password'))
                <p style="color:#ef4444;font-size:0.78rem;margin:0.3rem 0 0;">{{ $errors->first('password') }}</p>
            @endif
        </div>

        {{-- Remember me --}}
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.375rem;">
            <input type="checkbox" name="remember" id="remember"
                   {{ old('remember') ? 'checked' : '' }}
                   style="width:16px;height:16px;accent-color:#4f46e5;cursor:pointer;">
            <label for="remember" style="font-size:0.875rem;font-weight:500;color:#374151;cursor:pointer;margin:0;">
                @lang('lang_v1.remember_me')
            </label>
        </div>

        {{-- reCAPTCHA --}}
        @if(config('constants.enable_recaptcha'))
            <div style="margin-bottom:1.25rem;">
                <div class="g-recaptcha" data-sitekey="{{ config('constants.google_recaptcha_key') }}"></div>
                @if ($errors->has('g-recaptcha-response'))
                    <p style="color:#ef4444;font-size:0.78rem;margin:0.3rem 0 0;">{{ $errors->first('g-recaptcha-response') }}</p>
                @endif
            </div>
        @endif

        {{-- Submit --}}
        <button type="submit"
            style="width:100%;height:46px;border-radius:0.75rem;border:none;background:linear-gradient(135deg,#4f46e5,#3b82f6);color:#fff;font-size:0.95rem;font-weight:700;cursor:pointer;letter-spacing:0.01em;transition:opacity .2s;"
            onmouseover="this.style.opacity='0.9'"
            onmouseout="this.style.opacity='1'">
            @lang('lang_v1.login')
        </button>
    </form>

    {{-- Register link --}}
    @if (!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
        @if (config('constants.allow_registration'))
            <p style="text-align:center;margin:1.25rem 0 0;font-size:0.875rem;color:#6b7280;">
                {{ __('business.not_yet_registered') }}
                <a href="{{ route('business.getRegister') }}@if (!empty(request()->lang)){{'?lang='.request()->lang}}@endif"
                   style="color:#4f46e5;font-weight:700;text-decoration:none;">
                    {{ __('business.register_now') }}
                </a>
            </p>
        @endif
    @endif
</div>

{{-- ===== DEMO SHOPS (only shown in demo mode) ===== --}}
@if (config('app.env') == 'demo')
    <div style="margin-top:1.5rem;background:#fff;border-radius:1.25rem;box-shadow:0 4px 24px rgba(0,0,0,0.08);border:1px solid #e5e7eb;padding:1.5rem;">
        <h4 style="text-align:center;font-size:0.95rem;font-weight:700;color:#374151;margin:0 0 0.25rem;">
            Demo Shops
        </h4>
        <p style="text-align:center;font-size:0.78rem;color:#9ca3af;margin:0 0 1rem;">
            Click any button to log in with a demo business
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;justify-content:center;">
            @foreach([
                ['type'=>'all_in_one',    'label'=>'All In One',         'icon'=>'fa-star',          'color'=>'#6b7280'],
                ['type'=>'pharmacy',      'label'=>'Pharmacy',           'icon'=>'fa-medkit',        'color'=>'#7c3aed'],
                ['type'=>'services',      'label'=>'Services',           'icon'=>'fa-wrench',        'color'=>'#ea580c'],
                ['type'=>'electronics',   'label'=>'Electronics',        'icon'=>'fa-laptop',        'color'=>'#7c3aed'],
                ['type'=>'super_market',  'label'=>'Super Market',       'icon'=>'fa-shopping-cart', 'color'=>'#1d4ed8'],
                ['type'=>'restaurant',    'label'=>'Restaurant',         'icon'=>'fa-utensils',      'color'=>'#dc2626'],
                ['type'=>'superadmin',    'label'=>'SaaS / Superadmin',  'icon'=>'fa-university',    'color'=>'#b91c1c'],
                ['type'=>'essentials',    'label'=>'Essentials & HRM',   'icon'=>'fa-check-circle',  'color'=>'#1d4ed8'],
                ['type'=>'manufacturing', 'label'=>'Manufacturing',      'icon'=>'fa-industry',      'color'=>'#ea580c'],
            ] as $demo)
                <a href="?demo_type={{ $demo['type'] }}"
                   class="demo-login"
                   data-admin="{{ $demo_types[$demo['type']] }}"
                   style="display:inline-flex;align-items:center;gap:0.375rem;padding:0.375rem 0.75rem;border-radius:999px;border:1.5px solid #e5e7eb;font-size:0.78rem;font-weight:600;color:#374151;text-decoration:none;background:#f9fafb;transition:all .15s;"
                   onmouseover="this.style.borderColor='{{ $demo['color'] }}';this.style.color='{{ $demo['color'] }}';this.style.background='#fff';"
                   onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151';this.style.background='#f9fafb';">
                    <i class="fas {{ $demo['icon'] }}" style="font-size:0.75rem;"></i>
                    {{ $demo['label'] }}
                </a>
            @endforeach
        </div>
    </div>
@endif

@stop

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {

    // Language switcher
    $('.change_lang').click(function() {
        window.location = "{{ route('login') }}?lang=" + $(this).attr('value');
    });

    // Demo login buttons
    $('a.demo-login').click(function(e) {
        e.preventDefault();
        $('#username').val($(this).data('admin'));
        $('#password').val("{{ $password }}");
        $('form#login-form').submit();
    });

    // Show / hide password toggle
    $('#show_hide_icon').on('click', function() {
        const pw = $('#password');
        const isPassword = pw.attr('type') === 'password';
        pw.attr('type', isPassword ? 'text' : 'password');
        $(this).html(isPassword
            ? '<svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87"/><path d="M3 3l18 18"/></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>'
        );
    });
});
</script>
@endsection
