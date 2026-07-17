@extends('layouts.app')
@section('title', 'Owner weekly ritual')
@section('css')
@include('layouts.partials.page_modern_css')
<style>
.rt-grid{display:grid;grid-template-columns:1fr;gap:10px}
@media(min-width:800px){.rt-grid{grid-template-columns:1fr 1fr}}
.rt-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;display:flex;gap:12px;text-decoration:none!important;color:inherit;transition:.15s}
.rt-card:hover{border-color:#c7d2fe;box-shadow:0 4px 12px rgba(0,0,0,.06);transform:translateY(-1px)}
.rt-ico{width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.rt-when{font-size:11px;font-weight:800;text-transform:uppercase;color:#64748b;letter-spacing:.04em}
.rt-card h4{margin:2px 0 4px;font-size:15px;font-weight:800;color:#0f172a}
.rt-card p{margin:0;font-size:12px;color:#64748b}
</style>
@endsection
@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-list-check"></i></div>
                <div>
                    <h1>Owner weekly ritual</h1>
                    <p class="pg-subtitle">Turn reports into a habit — click each step when you do it · {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important">All reports</a>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="rt-grid">
            @foreach($steps as $s)
            <a href="{{ $s['url'] }}" class="rt-card">
                <span class="rt-ico"><i class="fa {{ $s['icon'] }}"></i></span>
                <div>
                    <div class="rt-when">{{ $s['when'] }}</div>
                    <h4>{{ $s['title'] }}</h4>
                    <p>{{ $s['help'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </section>
</div>
@endsection
