@extends('layouts.app')
@section('title', 'Reports')

@section('css')
@include('layouts.partials.page_modern_css')
<style>
.rh-grid{display:grid;grid-template-columns:1fr;gap:16px;padding:0 4px 24px}
@media(min-width:900px){.rh-grid{grid-template-columns:1fr 1fr}}
.rh-group{background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05)}
.rh-group-h{padding:12px 16px;color:#fff;font-weight:800;font-size:14px;display:flex;align-items:center;gap:10px}
.rh-group-h i{opacity:.9}
.rh-items{padding:8px}
.rh-item{display:flex;gap:12px;align-items:flex-start;padding:12px;border-radius:10px;text-decoration:none!important;color:inherit;border:1px solid transparent;transition:.15s}
.rh-item:hover{background:#f8fafc;border-color:#e2e8f0;transform:translateY(-1px)}
.rh-ico{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0}
.rh-item h4{margin:0 0 3px;font-size:14px;font-weight:700;color:#0f172a}
.rh-item p{margin:0;font-size:12px;color:#64748b;line-height:1.4}
.rh-intro{padding:0 4px 16px;color:#64748b;font-size:14px;max-width:720px}
</style>
@endsection

@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-chart-pie"></i></div>
                <div>
                    <h1>Reports</h1>
                    <p class="pg-subtitle">Pick a report by what you need — plain language for the pharmacy floor &middot; {{ session('business.name') }}</p>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <p class="rh-intro">
            <i class="fa fa-info-circle" style="color:var(--theme-main,#4f46e5)"></i>
            Start with <strong>Close the day</strong> at end of shift. Use <strong>Stock</strong> for expiry and reorder. Classic report links still work from the menu.
        </p>

        <div class="rh-grid">
            @foreach($groups as $group)
            <div class="rh-group">
                <div class="rh-group-h" style="background:linear-gradient(135deg,{{ $group['color'] }},{{ $group['color'] }}cc)">
                    <i class="fa fa-folder-open"></i> {{ $group['title'] }}
                </div>
                <div class="rh-items">
                    @foreach($group['items'] as $item)
                    <a href="{{ $item['url'] }}" class="rh-item">
                        <span class="rh-ico" style="background:{{ $group['color'] }}"><i class="fa {{ $item['icon'] }}"></i></span>
                        <div>
                            <h4>{{ $item['title'] }}</h4>
                            <p>{{ $item['help'] }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        @if(empty($groups))
        <div class="alert alert-warning">You do not have permission to view reports. Ask an admin.</div>
        @endif
    </section>
</div>
@endsection
