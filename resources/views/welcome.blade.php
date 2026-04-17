@extends('layouts.auth2')
@section('title', config('app.name', 'Reenson Pharmacy'))
@inject('request', 'Illuminate\Http\Request')

@section('content')
<style>
    /* Force dark background regardless of auth2 gradient */
    html, body {
        background: #0a1628 !important;
        margin: 0; padding: 0;
    }
    .right-col { padding: 0 !important; margin: 0 !important; }
    .container-fluid, .row.eq-height-row { padding: 0 !important; margin: 0 !important; }

    .landing-wrap {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 9990;
        overflow-y: auto;
        background:
            linear-gradient(rgba(5, 15, 35, 0.72), rgba(5, 20, 45, 0.85)),
            url('{{ asset("img/home-bg.jpg") }}') center center / cover no-repeat;
        background-color: #0a1628;
    }

    /* ── TOP NAV ── */
    .lp-nav {
        position: sticky;
        top: 0;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 48px;
        background: rgba(0,0,0,0.3);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .lp-nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .lp-nav-brand img { width: 34px; height: 34px; border-radius: 8px; background: white; padding: 3px; object-fit: contain; }
    .lp-nav-brand span { color: white; font-size: 1.05rem; font-weight: 800; }
    .lp-signin-btn {
        background: #0d9488;
        color: white !important;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 9px 24px;
        border-radius: 50px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .lp-signin-btn:hover { background: #0f766e; text-decoration: none; color: white !important; }

    /* ── HERO ── */
    .lp-hero {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 80px 24px 20px;
    }
    .lp-logo-ring {
        width: 88px; height: 88px;
        background: rgba(255,255,255,0.12);
        border: 2px solid rgba(255,255,255,0.25);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 28px;
        backdrop-filter: blur(4px);
    }
    .lp-logo-ring img { width: 56px; height: 56px; object-fit: contain; }
    .lp-title {
        font-size: clamp(2rem, 5vw, 3.6rem);
        font-weight: 900;
        color: #ffffff;
        margin: 0 0 12px;
        line-height: 1.15;
        text-shadow: 0 2px 16px rgba(0,0,0,0.6);
    }
    .lp-subtitle {
        font-size: clamp(0.95rem, 2vw, 1.15rem);
        color: rgba(255,255,255,0.85);
        max-width: 520px;
        margin: 0 auto 20px;
        line-height: 1.65;
        text-shadow: 0 1px 6px rgba(0,0,0,0.5);
    }
    .lp-divider { width: 52px; height: 4px; background: #0d9488; border-radius: 2px; margin: 0 auto 36px; }
    .lp-cta {
        display: inline-block;
        background: #0d9488;
        color: #ffffff !important;
        font-size: 1rem;
        font-weight: 800;
        padding: 15px 42px;
        border-radius: 50px;
        text-decoration: none;
        box-shadow: 0 8px 24px rgba(13,148,136,0.45);
        transition: all 0.22s;
        letter-spacing: 0.3px;
        margin-bottom: 72px;
    }
    .lp-cta:hover {
        background: #0f766e;
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(13,148,136,0.55);
        text-decoration: none;
        color: #ffffff !important;
    }

    /* ── FEATURES ── */
    .lp-features-label {
        color: rgba(255,255,255,0.5);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 24px;
    }
    .lp-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        max-width: 920px;
        width: 100%;
        margin: 0 auto;
        padding: 0 16px;
    }
    @media (max-width: 640px)  { .lp-grid { grid-template-columns: 1fr; } }
    @media (min-width: 641px) and (max-width: 900px) { .lp-grid { grid-template-columns: repeat(2, 1fr); } }

    .lp-card {
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.14);
        border-radius: 18px;
        padding: 28px 22px 24px;
        text-align: center;
        transition: all 0.25s;
        backdrop-filter: blur(6px);
    }
    .lp-card:hover {
        background: rgba(255,255,255,0.13);
        border-color: rgba(13,148,136,0.5);
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.3);
    }
    .lp-card-icon {
        width: 54px; height: 54px;
        background: rgba(13,148,136,0.2);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem;
        margin: 0 auto 16px;
    }
    .lp-card h3 {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 700;
        margin: 0 0 8px;
        text-shadow: 0 1px 4px rgba(0,0,0,0.3);
    }
    .lp-card p {
        color: rgba(255,255,255,0.75);
        font-size: 0.855rem;
        line-height: 1.55;
        margin: 0;
    }
    .lp-footer {
        text-align: center;
        color: rgba(255,255,255,0.3);
        font-size: 0.76rem;
        padding: 48px 0 32px;
    }
    @media (max-width: 600px) {
        .lp-nav { padding: 12px 20px; }
        .lp-hero { padding: 60px 16px 16px; }
    }
</style>

<div class="landing-wrap">

    {{-- Navbar --}}
    <nav class="lp-nav">
        <a href="{{ url('/') }}" class="lp-nav-brand">
            <img src="{{ asset('img/logo-small.png') }}" alt="logo">
            <span>{{ config('app.name', 'Reenson Pharmacy') }}</span>
        </a>
        <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}" class="lp-signin-btn">
            Sign In
        </a>
    </nav>

    {{-- Hero --}}
    <div class="lp-hero">
        <div class="lp-logo-ring">
            <img src="{{ asset('img/logo-small.png') }}" alt="{{ config('app.name') }}">
        </div>

        <h1 class="lp-title">{{ config('app.name', 'Reenson Pharmacy') }}</h1>
        <p class="lp-subtitle">Integrated Pharmacy &amp; DDA Drug Management System</p>
        <div class="lp-divider"></div>

        <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}" class="lp-cta">
            Sign In to Dashboard &rarr;
        </a>

        <p class="lp-features-label">Everything you need</p>

        <div class="lp-grid">
            <div class="lp-card">
                <div class="lp-card-icon">💊</div>
                <h3>DDA Drug Control</h3>
                <p>Full DDA compliance — prescriptions, dispense logs, stock, and destruction records.</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">📋</div>
                <h3>Prescription Management</h3>
                <p>Create, track, and dispense prescriptions with a complete patient audit trail.</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">📦</div>
                <h3>Inventory & Stock</h3>
                <p>Real-time stock tracking with expiry alerts, low-stock warnings, and batch management.</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">🛒</div>
                <h3>Point of Sale</h3>
                <p>Fast POS system optimised for pharmacy counter sales and walk-in customers.</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">📊</div>
                <h3>Reports & Analytics</h3>
                <p>Sales, stock, DDA, and financial reports to keep your pharmacy profitable and compliant.</p>
            </div>
            <div class="lp-card">
                <div class="lp-card-icon">💬</div>
                <h3>SMS Notifications</h3>
                <p>Automated SMS alerts for prescription reminders, order updates, and promotions.</p>
            </div>
        </div>

        <div class="lp-footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Reenson Pharmacy') }}. All rights reserved.
        </div>
    </div>

</div>
@endsection
