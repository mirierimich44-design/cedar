@extends('layouts.app')
@section('title', 'Owner Control')

@section('css')
<style>
.oc-wrap{padding:0 8px 28px}
.oc-hero{background:linear-gradient(135deg,#0f172a,#1e293b 50%,#0f766e);color:#fff;border-radius:0 0 16px 16px;padding:22px 20px;margin:-15px -15px 18px}
.oc-hero h1{margin:0;font-size:22px;font-weight:800}
.oc-hero p{margin:6px 0 0;opacity:.88;font-size:13px}
.oc-kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:16px}
@media(min-width:900px){.oc-kpi{grid-template-columns:repeat(5,1fr)}}
.oc-k{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:12px 14px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
.oc-k .l{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;color:#64748b}
.oc-k .v{font-size:20px;font-weight:800;margin-top:4px;color:#0f172a}
.oc-k .s{font-size:11px;color:#94a3b8;margin-top:2px}
.oc-grid{display:grid;grid-template-columns:1fr;gap:10px}
@media(min-width:700px){.oc-grid{grid-template-columns:1fr 1fr}}
@media(min-width:1000px){.oc-grid{grid-template-columns:1fr 1fr 1fr 1fr}}
.oc-tile{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px;text-decoration:none!important;color:inherit;display:block;border-top:3px solid #4f46e5;transition:.15s}
.oc-tile:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,0,0,.08)}
.oc-tile .ico{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#fff;margin-bottom:8px}
.oc-tile h3{margin:0 0 4px;font-size:14px;font-weight:800;color:#0f172a}
.oc-tile p{margin:0;font-size:12px;color:#64748b;line-height:1.35}
.oc-sec{font-size:12px;font-weight:800;text-transform:uppercase;color:#64748b;margin:8px 0 10px;letter-spacing:.04em}
</style>
@endsection

@section('content')
@php
    $d = $data ?? [];
    $tiles = $tiles ?? [];
@endphp
<div class="page-modern oc-wrap">
    <div class="oc-hero">
        <h1><i class="fa fa-briefcase"></i> Owner Control</h1>
        <p>{{ session('business.name') }} · Your command centre for cash, stock, credit &amp; month-end · {{ $d['today'] ?? '' }}</p>
    </div>

    <div class="oc-kpi">
        <div class="oc-k">
            <div class="l">Sales today</div>
            <div class="v">@format_currency((float)($d['sales_today'] ?? 0))</div>
            <div class="s">{{ (int)($d['invoices_today'] ?? 0) }} invoices</div>
        </div>
        <div class="oc-k">
            <div class="l">Credit due</div>
            <div class="v">@format_currency((float)($d['credit_due'] ?? 0))</div>
            <div class="s">Open customer balances</div>
        </div>
        <div class="oc-k">
            <div class="l">Low / zero stock</div>
            <div class="v">{{ (int)($d['low_stock'] ?? 0) }}</div>
            <div class="s">Needs reorder</div>
        </div>
        <div class="oc-k">
            <div class="l">Open tills</div>
            <div class="v">{{ (int)($d['open_tills'] ?? 0) }}</div>
            <div class="s">Registers still open</div>
        </div>
        <div class="oc-k">
            <div class="l">Expiring (90 days)</div>
            <div class="v">{{ (int)($d['expiring_90d'] ?? 0) }}</div>
            <div class="s">Batches with stock</div>
        </div>
    </div>

    <div class="oc-sec">Shortcuts</div>
    <div class="oc-grid">
        @foreach($tiles as $t)
            <a href="{{ $t['url'] }}" class="oc-tile" style="border-top-color:{{ $t['color'] }}">
                <span class="ico" style="background:{{ $t['color'] }}"><i class="fa {{ $t['icon'] }}"></i></span>
                <h3>{{ $t['title'] }}</h3>
                <p>{{ $t['help'] }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
