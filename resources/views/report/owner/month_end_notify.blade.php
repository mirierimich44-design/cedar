@extends('layouts.app')
@section('title', 'Send month-end')
@section('css')
@include('layouts.partials.page_modern_css')
<style>
.ne-box{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px;max-width:560px}
.ne-pre{background:#0f172a;color:#e2e8f0;padding:12px;border-radius:8px;font-size:12px;white-space:pre-wrap}
</style>
@endsection
@section('content')
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-paper-plane"></i></div>
                <div>
                    <h1>Send month-end to phone</h1>
                    <p class="pg-subtitle">SMS and/or WhatsApp (Africa's Talking) · {{ session('business.name') }}</p>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        @if(session('status.msg'))
            <div class="alert alert-{{ session('status.success') ? 'success' : 'danger' }}">{{ session('status.msg') }}</div>
        @endif
        <div class="ne-box">
            <form method="get" style="margin-bottom:12px;display:flex;gap:8px;align-items:flex-end">
                <div>
                    <label style="font-size:12px;font-weight:700">Month</label>
                    <input type="month" name="month" value="{{ $month }}" class="form-control">
                </div>
                <button class="btn btn-default" type="submit">Preview</button>
            </form>
            <p style="font-size:12px;font-weight:700;margin-bottom:6px">Message preview</p>
            <div class="ne-pre">{{ $summary }}</div>
            <form method="post" action="{{ route('reports.month_end_notify.send') }}" style="margin-top:14px">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <div class="form-group">
                    <label>Owner phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $defaultPhone) }}" placeholder="07XXXXXXXX or 2547XXXXXXXX" required>
                </div>
                <div class="form-group">
                    <label>Channel</label>
                    <select name="channel" class="form-control">
                        <option value="sms">SMS</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> Send</button>
                <a href="{{ route('reports.month_end_pack', ['month' => $month]) }}" class="btn btn-default">Open full pack</a>
            </form>
            <p style="font-size:12px;color:#64748b;margin-top:12px">Requires SMS gateway / Africa's Talking WhatsApp in <code>.env</code> / services config.</p>
        </div>
    </section>
</div>
@endsection
