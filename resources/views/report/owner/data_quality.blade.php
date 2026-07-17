@extends('layouts.app')
@section('title', 'Data quality')
@section('css')
@include('layouts.partials.page_modern_css')
<style>
.dq-grid{display:grid;grid-template-columns:1fr;gap:12px}
@media(min-width:800px){.dq-grid{grid-template-columns:1fr 1fr}}
.dq-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;text-decoration:none!important;color:inherit;display:block}
.dq-card:hover{border-color:#c7d2fe}
.dq-card h4{margin:0 0 6px;font-size:14px;font-weight:800}
.dq-card .c{font-size:24px;font-weight:800}
.dq-card p{margin:6px 0 0;font-size:12px;color:#64748b}
.sev-ok .c{color:#047857}.sev-low .c{color:#0369a1}.sev-medium .c{color:#b45309}.sev-high .c{color:#b91c1c}
</style>
@endsection
@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-heartbeat"></i></div>
                <div>
                    <h1>Data quality</h1>
                    <p class="pg-subtitle">Fix these so reports &amp; stock stays trustworthy · {{ session('business.name') }}</p>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <form method="get" class="no-print" style="display:flex;gap:10px;margin-bottom:14px;align-items:flex-end">
            <div>
                <label style="font-size:12px;font-weight:700;display:block">Location</label>
                <select name="location_id" class="form-control select2" style="min-width:200px">
                    @foreach($business_locations as $id => $name)
                        <option value="{{ $id }}" @if((string)$location_id === (string)$id) selected @endif>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Refresh</button>
        </form>
        <div class="dq-grid">
            @foreach($issues as $i)
            <a href="{{ $i['url'] }}" class="dq-card sev-{{ $i['severity'] }}">
                <h4>{{ $i['title'] }}</h4>
                <div class="c">
                    @if(!empty($i['is_money']))
                        @format_currency((float)$i['count'])
                    @else
                        {{ $i['count'] }}
                    @endif
                </div>
                <p>{{ $i['help'] }}</p>
            </a>
            @endforeach
        </div>
    </section>
</div>
@endsection
