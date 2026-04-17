@extends('layouts.auth2')
@section('title', config('app.name', 'Reenson Pharmacy'))
@inject('request', 'Illuminate\Http\Request')

@section('content')
<style>
    html {
        background: linear-gradient(rgba(0,0,0,0.62), rgba(0,15,30,0.78)),
                    url('{{ asset("img/home-bg.jpg") }}') center center / cover no-repeat fixed !important;
    }
    .landing-top-nav {
        position: fixed;
        top: 0; left: 0; right: 0;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 40px;
        background: rgba(0,0,0,0.25);
        backdrop-filter: blur(6px);
    }
    .landing-top-nav a { color: white; text-decoration: none; font-weight: 600; font-size: 0.95rem; }
    .landing-btn-outline {
        border: 2px solid white;
        border-radius: 50px;
        padding: 8px 24px;
        color: white !important;
        font-weight: 700 !important;
        transition: all 0.2s;
    }
    .landing-btn-outline:hover { background: white; color: #0f766e !important; }
    .landing-btn-primary {
        background: #0f766e;
        border-radius: 50px;
        padding: 14px 40px;
        color: white !important;
        font-weight: 700;
        font-size: 1.05rem;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 6px 20px rgba(0,0,0,0.35);
        transition: all 0.2s;
    }
    .landing-btn-primary:hover { background: #0d9488; transform: translateY(-2px); box-shadow: 0 10px 28px rgba(0,0,0,0.4); color: white !important; text-decoration: none; }
    .feature-card {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 16px;
        padding: 28px 22px;
        text-align: center;
        transition: all 0.25s;
        backdrop-filter: blur(4px);
    }
    .feature-card:hover {
        background: rgba(255,255,255,0.18);
        transform: translateY(-4px);
        border-color: rgba(255,255,255,0.4);
    }
    .feature-card h3 { color: white; font-size: 1.05rem; font-weight: 700; margin: 12px 0 8px; }
    .feature-card p { color: rgba(255,255,255,0.82); font-size: 0.875rem; line-height: 1.5; margin: 0; }
    .feature-icon {
        width: 52px; height: 52px;
        background: rgba(255,255,255,0.15);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto;
        font-size: 1.5rem;
    }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        max-width: 900px;
        width: 100%;
        margin: 0 auto;
    }
    @media (max-width: 768px) {
        .features-grid { grid-template-columns: repeat(1, 1fr); }
        .landing-top-nav { padding: 14px 20px; }
    }
    @media (min-width: 769px) and (max-width: 992px) {
        .features-grid { grid-template-columns: repeat(2, 1fr); }
    }
    .divider-line {
        width: 60px; height: 4px;
        background: #0d9488;
        border-radius: 2px;
        margin: 16px auto 0;
    }
</style>

{{-- Custom Top Nav --}}
<div class="landing-top-nav">
    <div style="display:flex; align-items:center; gap:12px;">
        <img src="{{ asset('img/logo-small.png') }}" style="width:36px; height:36px; border-radius:8px; background:white; padding:3px; object-fit:contain;">
        <span style="color:white; font-size:1.1rem; font-weight:800; letter-spacing:0.5px;">{{ config('app.name', 'Reenson Pharmacy') }}</span>
    </div>
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}" class="landing-btn-outline">Sign In</a>
    </div>
</div>

{{-- Hero --}}
<div style="min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 100px 20px 60px; text-align: center;">

    <div style="margin-bottom: 24px;">
        <img src="{{ asset('img/logo-small.png') }}" style="width:90px; height:90px; border-radius:50%; background:white; padding:10px; object-fit:contain; box-shadow: 0 12px 35px rgba(0,0,0,0.4);">
    </div>

    <h1 style="font-size: clamp(2.2rem, 5vw, 3.8rem); font-weight: 900; color: white; margin: 0 0 14px; text-shadow: 0 3px 12px rgba(0,0,0,0.5); line-height: 1.15;">
        {{ config('app.name', 'Reenson Pharmacy') }}
    </h1>

    <p style="font-size: clamp(1rem, 2vw, 1.25rem); color: rgba(255,255,255,0.88); max-width: 560px; margin: 0 auto 14px; text-shadow: 0 1px 6px rgba(0,0,0,0.4); line-height: 1.6;">
        Integrated Pharmacy &amp; DDA Drug Management System
    </p>

    <div class="divider-line" style="margin-bottom: 36px;"></div>

    <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login']) }}" class="landing-btn-primary" style="margin-bottom: 70px;">
        Sign In to Dashboard &rarr;
    </a>

    {{-- Section Title --}}
    <p style="color: rgba(255,255,255,0.6); font-size: 0.8rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 20px;">
        Everything you need
    </p>

    {{-- Features Grid --}}
    <div class="features-grid">

        <div class="feature-card">
            <div class="feature-icon">💊</div>
            <h3>DDA Drug Control</h3>
            <p>Full compliance tracking for Dangerous Drugs Act — prescriptions, dispense logs, stock, and destruction records.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">📋</div>
            <h3>Prescription Management</h3>
            <p>Create, track, and dispense prescriptions with a full audit trail for every patient transaction.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">📦</div>
            <h3>Inventory & Stock</h3>
            <p>Real-time stock tracking with expiry date alerts, low-stock notifications, and batch management.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">🛒</div>
            <h3>Point of Sale</h3>
            <p>Fast, intuitive POS system optimized for pharmacy counter sales and walk-in customers.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Reports & Analytics</h3>
            <p>Comprehensive sales, stock, DDA, and financial reports to keep your pharmacy compliant and profitable.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">💬</div>
            <h3>SMS Notifications</h3>
            <p>Automated SMS alerts to customers for prescription reminders, order updates, and promotional messages.</p>
        </div>

    </div>

    {{-- Footer --}}
    <p style="color: rgba(255,255,255,0.4); font-size: 0.78rem; margin-top: 60px;">
        &copy; {{ date('Y') }} {{ config('app.name', 'Reenson Pharmacy') }}. All rights reserved.
    </p>
</div>
@endsection
