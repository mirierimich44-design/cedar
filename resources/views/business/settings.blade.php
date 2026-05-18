@extends('layouts.app')
@section('title', __('business.business_settings'))

@section('content')
<div class="stg-page">

    {{-- ── Banner ── --}}
    <div class="stg-banner">
        <div class="stg-banner-inner">
            <div class="stg-banner-title">
                <span class="stg-banner-icon"><i class="fas fa-cog"></i></span>
                <div>
                    <h1>@lang('business.business_settings')</h1>
                    <p class="stg-banner-sub">{{ session()->get('business.name') }}</p>
                </div>
            </div>
            <div class="stg-banner-actions">
                <div class="stg-search-wrap">
                    @include('layouts.partials.search_settings')
                </div>
                <button form="bussiness_edit_form" type="submit" class="stg-save-btn">
                    <i class="fas fa-save"></i> <span class="stg-save-text">@lang('business.update_settings')</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Main layout ── --}}
    {!! Form::open(['url' => action([\App\Http\Controllers\BusinessController::class, 'postBusinessSettings']), 'method' => 'post', 'id' => 'bussiness_edit_form', 'files' => true]) !!}

    <div class="stg-layout pos-tab-container">

        {{-- ── Sidebar ── --}}
        <div class="stg-sidebar pos-tab-menu">
            <div class="list-group">

                {{-- Group: General --}}
                <div class="stg-nav-group">@lang('business.general')</div>
                <a href="#" class="list-group-item stg-nav-item active">
                    <span class="stg-nav-icon"><i class="fas fa-building"></i></span>
                    <span class="stg-nav-label">@lang('business.business')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-percent"></i></span>
                    <span class="stg-nav-label">@lang('business.tax')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-box"></i></span>
                    <span class="stg-nav-label">@lang('business.product')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-address-book"></i></span>
                    <span class="stg-nav-label">@lang('contact.contact')</span>
                </a>

                {{-- Group: Sales --}}
                <div class="stg-nav-group">@lang('report.sales')</div>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-shopping-cart"></i></span>
                    <span class="stg-nav-label">@lang('business.sale')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-cash-register"></i></span>
                    <span class="stg-nav-label">@lang('sale.pos_sale')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-desktop"></i></span>
                    <span class="stg-nav-label">@lang('lang_v1.display_screen')</span>
                </a>

                {{-- Group: Purchasing --}}
                <div class="stg-nav-group">@lang('lang_v1.purchasing')</div>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-truck"></i></span>
                    <span class="stg-nav-label">@lang('purchase.purchases')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-credit-card"></i></span>
                    <span class="stg-nav-label">@lang('lang_v1.payment')</span>
                </a>

                {{-- Group: Analytics --}}
                <div class="stg-nav-group">@lang('business.analytics')</div>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                    <span class="stg-nav-label">@lang('business.dashboard')</span>
                </a>

                {{-- Group: Communications --}}
                <div class="stg-nav-group">@lang('lang_v1.communications')</div>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-cog"></i></span>
                    <span class="stg-nav-label">@lang('business.system')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-tag"></i></span>
                    <span class="stg-nav-label">@lang('lang_v1.prefixes')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-envelope"></i></span>
                    <span class="stg-nav-label">@lang('lang_v1.email_settings')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-comment-dots"></i></span>
                    <span class="stg-nav-label">@lang('lang_v1.sms_settings')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fab fa-whatsapp"></i></span>
                    <span class="stg-nav-label">@lang('lang_v1.whatsapp_settings')</span>
                </a>

                {{-- Group: Advanced --}}
                <div class="stg-nav-group">@lang('lang_v1.advanced')</div>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-gift"></i></span>
                    <span class="stg-nav-label">@lang('lang_v1.reward_point_settings')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-puzzle-piece"></i></span>
                    <span class="stg-nav-label">@lang('lang_v1.modules')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-font"></i></span>
                    <span class="stg-nav-label">@lang('lang_v1.custom_labels')</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-file-invoice"></i></span>
                    <span class="stg-nav-label">eTIMS</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-sign-in-alt"></i></span>
                    <span class="stg-nav-label">Login Screen</span>
                </a>
                <a href="#" class="list-group-item stg-nav-item">
                    <span class="stg-nav-icon"><i class="fas fa-robot"></i></span>
                    <span class="stg-nav-label">AI Integrations</span>
                </a>

            </div>
        </div>{{-- /.stg-sidebar --}}

        {{-- ── Content panels ── --}}
        <div class="stg-content pos-tab">
            @include('business.partials.settings_business')
            @include('business.partials.settings_tax')
            @include('business.partials.settings_product')
            @include('business.partials.settings_contact')
            @include('business.partials.settings_sales')
            @include('business.partials.settings_pos')
            @include('business.partials.settings_display_pos')
            @include('business.partials.settings_purchase')
            @include('business.partials.settings_payment')
            @include('business.partials.settings_dashboard')
            @include('business.partials.settings_system')
            @include('business.partials.settings_prefixes')
            @include('business.partials.settings_email')
            @include('business.partials.settings_sms')
            @include('business.partials.settings_whatsapp')
            @include('business.partials.settings_reward_point')
            @include('business.partials.settings_modules')
            @include('business.partials.settings_custom_labels')
            @include('business.partials.settings_etims')
            @include('business.partials.settings_login_screen')
            @include('business.partials.settings_integrations')
        </div>{{-- /.stg-content --}}

    </div>{{-- /.stg-layout --}}

    {{-- ── Sticky save bar ── --}}
    <div class="stg-sticky-bar">
        <span class="stg-sticky-hint"><i class="fas fa-info-circle"></i> @lang('lang_v1.unsaved_changes')</span>
        <button type="submit" class="stg-save-btn">
            <i class="fas fa-save"></i> @lang('business.update_settings')
        </button>
    </div>

    {!! Form::close() !!}

</div>{{-- /.stg-page --}}
@stop
@section('css')
@php
$stgMain  = 'var(--theme-main, #059669)';
$stgDark  = 'var(--theme-dark, #065f46)';
$stgLight = 'var(--theme-light, #34d399)';
$stgSubtle= 'var(--theme-subtle, #ecfdf5)';
$stgBorder= 'var(--theme-border, #a7f3d0)';
@endphp
<style>
/* ═══════════════════════════════════════════
   Settings page — modern redesign
   Structural classes kept: pos-tab-container,
   pos-tab-menu, pos-tab, pos-tab-content,
   list-group > a  (JS hooks untouched)
═══════════════════════════════════════════ */

/* Reset the widget wrapper that gets added */
.stg-page > .box.pos-tab-container,
.stg-layout.pos-tab-container {
    border: none !important;
    box-shadow: none !important;
    margin: 0 !important;
    padding: 0 !important;
    background: transparent !important;
}

/* ── Page wrapper ── */
.stg-page {
    background: #f1f5f9;
    min-height: 100vh;
    padding-bottom: 80px;
}

/* ── Banner ── */
.stg-banner {
    background: linear-gradient(135deg, {{ $stgDark }} 0%, {{ $stgMain }} 100%);
    border-radius: 0 0 18px 18px;
    margin: -15px -15px 0;
    position: relative;
    overflow: hidden;
}
.stg-banner::before {
    content:''; position:absolute; top:-30px; right:-30px;
    width:120px; height:120px; border-radius:50%;
    background:rgba(255,255,255,0.06); pointer-events:none;
}
.stg-banner-inner {
    max-width: 100%;
    padding: 22px 28px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    position: relative; z-index: 1;
}
.stg-banner-title {
    display: flex;
    align-items: center;
    gap: 14px;
}
.stg-banner-icon {
    width: 42px; height: 42px;
    background: rgba(255,255,255,0.18);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stg-banner-icon i { color: #fff; font-size: 18px; }
.stg-banner-title h1 {
    color: #fff;
    font-size: 20px;
    font-weight: 700;
    margin: 0;
}
.stg-banner-sub {
    color: rgba(255,255,255,0.75);
    font-size: 12px;
    margin: 2px 0 0;
}
.stg-banner-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.stg-search-wrap {
    min-width: 200px;
    max-width: 260px;
}
/* Strip Bootstrap row/col/offset from the included search partial */
.stg-search-wrap .row { margin: 0 !important; }
.stg-search-wrap .col-md-8,
.stg-search-wrap .col-xs-12 {
    width: 100% !important;
    padding: 0 !important;
    float: none !important;
    margin: 0 !important;
}
.stg-search-wrap .col-md-offset-2 { margin-left: 0 !important; }
.stg-search-wrap .input-group { width: 100% !important; }
.stg-search-wrap .input-group-addon {
    background: rgba(255,255,255,0.15) !important;
    border: 1.5px solid rgba(255,255,255,0.35) !important;
    border-right: none !important;
    border-radius: 8px 0 0 8px !important;
    color: rgba(255,255,255,0.8) !important;
}
.stg-search-wrap .select2-container {
    width: 100% !important;
}
/* Override select2 inside banner */
.stg-search-wrap .select2-container .select2-selection--single {
    background: rgba(255,255,255,0.15) !important;
    border: 1.5px solid rgba(255,255,255,0.35) !important;
    border-radius: 8px !important;
    height: 36px !important;
}
.stg-search-wrap .select2-container .select2-selection--single .select2-selection__rendered {
    color: #fff !important;
    line-height: 34px !important;
    font-size: 13px !important;
}
.stg-search-wrap .select2-container .select2-selection--single .select2-selection__placeholder {
    color: rgba(255,255,255,0.7) !important;
}
.stg-search-wrap .select2-container .select2-selection--single .select2-selection__arrow b {
    border-color: rgba(255,255,255,0.7) transparent transparent !important;
}
.stg-save-text { white-space: nowrap; }
@@media (max-width: 600px) {
    .stg-banner-inner { flex-direction: column; align-items: flex-start; }
    .stg-banner-actions { width: 100%; }
    .stg-search-wrap { flex: 1; max-width: none; }
    .stg-save-text { display: none; }
}

/* ── Save button (used in banner + sticky bar) ── */
.stg-save-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 22px;
    background: #fff;
    color: {{ $stgDark }};
    font-size: 13px;
    font-weight: 700;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    white-space: nowrap;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
.stg-save-btn:hover {
    background: {{ $stgSubtle }};
    color: {{ $stgDark }};
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(0,0,0,0.15);
}

/* ── Layout: sidebar + content ── */
.stg-layout {
    display: flex !important;
    align-items: flex-start;
    gap: 0;
    padding: 24px 16px 20px !important;
}

/* ── Sidebar ── */
.stg-sidebar {
    width: 230px !important;
    min-width: 230px !important;
    flex-shrink: 0;
    position: sticky;
    top: 60px;
    max-height: calc(100vh - 80px);
    overflow-y: auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.07);
    padding: 8px 0 12px !important;
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 transparent;
}
.stg-sidebar::-webkit-scrollbar { width: 4px; }
.stg-sidebar::-webkit-scrollbar-track { background: transparent; }
.stg-sidebar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

/* Group headers */
.stg-nav-group {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #94a3b8;
    padding: 14px 16px 4px;
}
.stg-nav-group:first-child { padding-top: 6px; }

/* Nav items — override list-group styles completely */
.stg-sidebar .list-group { margin: 0; border-radius: 0; }
.stg-sidebar .list-group-item.stg-nav-item {
    display: flex !important;
    align-items: center;
    gap: 10px;
    padding: 9px 14px !important;
    margin: 1px 8px !important;
    border: none !important;
    border-radius: 8px !important;
    background: transparent !important;
    color: #475569 !important;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all .15s;
    text-decoration: none;
    width: calc(100% - 16px) !important;
}
.stg-sidebar .list-group-item.stg-nav-item:hover {
    background: {{ $stgSubtle }} !important;
    color: {{ $stgDark }} !important;
}
.stg-sidebar .list-group-item.stg-nav-item.active {
    background: linear-gradient(135deg, {{ $stgMain }}, {{ $stgLight }}) !important;
    color: #fff !important;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}
.stg-nav-icon {
    width: 28px; height: 28px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    border-radius: 6px;
    background: rgba(0,0,0,0.05);
    font-size: 12px;
    transition: background .15s;
}
.stg-nav-item.active .stg-nav-icon {
    background: rgba(255,255,255,0.2);
    color: #fff;
}
.stg-nav-item:hover:not(.active) .stg-nav-icon {
    background: {{ $stgBorder }};
    color: {{ $stgDark }};
}
.stg-nav-label { flex: 1; line-height: 1.2; }

/* ── Content area ── */
.stg-content {
    flex: 1 !important;
    min-width: 0;
    padding: 0 0 0 16px !important;
    width: auto !important;
}

/* ── Individual tab panels ── */
.stg-content .pos-tab-content {
    display: none;
    padding: 0 !important;
    animation: stgFadeIn .2s ease;
}
.stg-content .pos-tab-content.active {
    display: block;
}
@@keyframes stgFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Wrap each panel's content in a card look */
.stg-content .pos-tab-content > .row {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.06);
    padding: 20px 20px 8px !important;
    margin: 0 0 16px !important;
}
.stg-content .pos-tab-content > .row:first-child {
    border-top: 3px solid {{ $stgMain }};
}

/* Form labels inside panels */
.stg-content .pos-tab-content label:not(.sp-label):not(.sp-remember):not(.checkbox label):not(.radio label) {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 5px;
    display: block;
}
.stg-content .pos-tab-content .form-control {
    border-radius: 8px;
    border-color: #e2e8f0;
    font-size: 13px;
}
.stg-content .pos-tab-content .form-control:focus {
    border-color: {{ $stgMain }} !important;
    box-shadow: 0 0 0 3px rgba(5,150,105,0.12) !important;
}
.stg-content .pos-tab-content .input-group-addon {
    border-radius: 8px 0 0 8px;
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #64748b;
}

/* Section headings inside panels */
.stg-content .pos-tab-content h4,
.stg-content .pos-tab-content .panel-heading,
.stg-content .pos-tab-content > .row > .col-sm-12 > h4 {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    margin: 8px 0 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ── Sticky save bar ── */
.stg-sticky-bar {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    z-index: 9999;
    background: #1e293b;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 24px;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
    transform: translateY(100%);
    transition: transform .3s ease;
}
.stg-sticky-bar.visible { transform: translateY(0); }
.stg-sticky-hint {
    color: #94a3b8;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 7px;
}

/* ── Mobile: collapse sidebar ── */
@@media (max-width: 768px) {
    .stg-layout { flex-direction: column; padding: 12px 10px !important; }
    .stg-sidebar { width: 100% !important; min-width: unset !important; position: static; max-height: none; }
    .stg-content { padding: 12px 0 0 !important; }
}

/* Kill the outer box/widget wrapper that components.widget adds */
.stg-layout.box { box-shadow: none !important; border: none !important; background: transparent !important; }
.stg-layout > .box-body { padding: 0 !important; }

/* ═══════════════════════════════════════════════════════════
   CONTENT PANEL ELEMENTS — covers all 20 partials at once
═══════════════════════════════════════════════════════════ */

/* ── Panel: row blocks become cards ── */
.stg-content .pos-tab-content > .row {
    background: #fff !important;
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    padding: 22px 22px 10px !important;
    margin: 0 0 16px !important;
    border: 1px solid #f1f5f9;
    position: relative;
}
.stg-content .pos-tab-content > .row + hr { display: none; }

/* First row in each panel gets theme top-border accent */
.stg-content .pos-tab-content > .row:first-child {
    border-top: 3px solid {{ $stgMain }};
}

/* ── Sub-section headings (h4 inside rows) ── */
.stg-content .pos-tab-content h4 {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    margin: 4px 0 16px !important;
    padding: 10px 14px !important;
    background: #f8fafc;
    border-left: 3px solid {{ $stgMain }};
    border-radius: 0 6px 6px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
}
.stg-content .pos-tab-content h4 small {
    font-size: 11px;
    color: #94a3b8;
    font-weight: 400;
}

/* ── Labels ── */
.stg-content .pos-tab-content label:not(.checkbox label):not(.radio label):not(.sp-label) {
    font-size: 11px !important;
    font-weight: 600 !important;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b !important;
    margin-bottom: 6px !important;
    display: block;
}

/* ── Inputs & selects ── */
.stg-content .pos-tab-content .form-control {
    height: 40px !important;
    border-radius: 8px !important;
    border: 1.5px solid #e2e8f0 !important;
    font-size: 13px !important;
    color: #0f172a !important;
    background: #fff !important;
    padding: 0 12px !important;
    transition: border-color .15s, box-shadow .15s;
    line-height: 40px !important;
}
.stg-content .pos-tab-content .form-control:focus {
    border-color: {{ $stgMain }} !important;
    box-shadow: 0 0 0 3px rgba(5,150,105,0.12) !important;
    outline: none !important;
}
.stg-content .pos-tab-content textarea.form-control {
    height: auto !important;
    line-height: 1.5 !important;
    padding: 10px 12px !important;
}

/* ── Input groups: flatten addon into input ── */
.stg-content .pos-tab-content .input-group {
    display: flex;
}
.stg-content .pos-tab-content .input-group .form-control {
    border-radius: 0 8px 8px 0 !important;
    border-left: none !important;
    flex: 1;
}
.stg-content .pos-tab-content .input-group-addon {
    background: #f8fafc !important;
    border: 1.5px solid #e2e8f0 !important;
    border-right: none !important;
    border-radius: 8px 0 0 8px !important;
    color: #94a3b8 !important;
    padding: 0 10px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 38px;
    font-size: 13px;
}

/* ── Checkboxes & radios — modernize without breaking iCheck ── */
.stg-content .pos-tab-content .checkbox,
.stg-content .pos-tab-content .radio {
    margin: 0 0 10px !important;
    padding: 0 !important;
}
.stg-content .pos-tab-content .checkbox label,
.stg-content .pos-tab-content .radio label {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #334155 !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
    cursor: pointer;
    padding: 8px 12px !important;
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    background: #fafafa;
    transition: all .15s;
    margin: 0 !important;
    min-height: 40px;
}
.stg-content .pos-tab-content .checkbox label:hover,
.stg-content .pos-tab-content .radio label:hover {
    border-color: {{ $stgBorder }};
    background: {{ $stgSubtle }};
    color: {{ $stgDark }};
}
/* iCheck sits inside label — give it consistent size */
.stg-content .pos-tab-content .checkbox label .icheckbox_square-blue,
.stg-content .pos-tab-content .checkbox label .icheckbox_minimal-blue,
.stg-content .pos-tab-content .radio label .iradio_square-blue,
.stg-content .pos-tab-content .radio label .iradio_minimal-blue {
    flex-shrink: 0;
}

/* ── Help text ── */
.stg-content .pos-tab-content .help-block {
    font-size: 11px !important;
    color: #94a3b8 !important;
    margin: 4px 0 0 !important;
    line-height: 1.5 !important;
}

/* ── Form group spacing ── */
.stg-content .pos-tab-content .form-group {
    margin-bottom: 18px !important;
}

/* ── Tables inside panels (keyboard shortcuts etc) ── */
.stg-content .pos-tab-content .table {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e2e8f0 !important;
    margin-bottom: 0 !important;
    font-size: 13px;
}
.stg-content .pos-tab-content .table > thead > tr > th {
    background: #f8fafc !important;
    color: #475569 !important;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid #e2e8f0 !important;
    padding: 10px 14px !important;
}
.stg-content .pos-tab-content .table > tbody > tr > td {
    padding: 10px 14px !important;
    vertical-align: middle !important;
    border-color: #f1f5f9 !important;
    color: #334155;
    font-size: 13px;
}
.stg-content .pos-tab-content .table-striped > tbody > tr:nth-of-type(odd) {
    background: #fafbfc !important;
}
.stg-content .pos-tab-content .table > tbody > tr > td .form-control {
    height: 34px !important;
    line-height: 34px !important;
    font-size: 12px !important;
}

/* ── Raw file input (not inside fileinput plugin) ── */
.stg-content .pos-tab-content input[type="file"]:not(.fileinput-new):not(.fileinput-exists) {
    border: 2px dashed #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 8px 12px !important;
    background: #fafafa !important;
    font-size: 12px !important;
    color: #64748b !important;
    cursor: pointer;
    width: 100% !important;
    height: auto !important;
    transition: border-color .15s;
    display: block;
}
.stg-content .pos-tab-content input[type="file"]:not(.fileinput-new):not(.fileinput-exists):hover {
    border-color: {{ $stgMain }} !important;
    background: {{ $stgSubtle }} !important;
}

/* ── Bootstrap fileinput plugin: reset and restyle cleanly ── */
.stg-content .pos-tab-content .file-input {
    width: 100%;
}
/* The actual hidden file input inside the plugin must not get dashed border */
.stg-content .pos-tab-content .file-input input[type="file"] {
    border: none !important;
    background: none !important;
    padding: 0 !important;
    height: 0 !important;
    width: 0 !important;
    overflow: hidden !important;
    position: absolute !important;
}
/* Caption (filename) text box */
.stg-content .pos-tab-content .file-input .file-caption.form-control {
    height: 40px !important;
    border-radius: 8px 0 0 8px !important;
    border: 1.5px solid #e2e8f0 !important;
    border-right: none !important;
    font-size: 12px !important;
    color: #94a3b8 !important;
    background: #fafafa !important;
    padding: 0 10px !important;
    line-height: 40px !important;
}
/* Browse button */
.stg-content .pos-tab-content .file-input .btn-file {
    background: {{ $stgMain }} !important;
    border-color: {{ $stgMain }} !important;
    color: #fff !important;
    border-radius: 0 8px 8px 0 !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    height: 40px !important;
    padding: 0 14px !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    white-space: nowrap;
}
.stg-content .pos-tab-content .file-input .btn-file:hover {
    background: {{ $stgDark }} !important;
    border-color: {{ $stgDark }} !important;
}
/* Remove button */
.stg-content .pos-tab-content .file-input .btn-default.fileinput-cancel {
    background: #f1f5f9 !important;
    border-color: #e2e8f0 !important;
    color: #475569 !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    height: 40px !important;
}
/* Input group inside fileinput */
.stg-content .pos-tab-content .file-input .input-group {
    width: 100% !important;
    flex-wrap: nowrap !important;
}
.stg-content .pos-tab-content .file-input .input-group-btn {
    width: auto !important;
}
/* Preview thumbnail area */
.stg-content .pos-tab-content .file-input .file-preview {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    background: #fafafa !important;
    margin-bottom: 8px;
    padding: 6px !important;
}

/* ── Panels / box inside content ── */
.stg-content .pos-tab-content .box {
    border-radius: 10px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: none !important;
}
.stg-content .pos-tab-content .box .box-header {
    background: #f8fafc !important;
    border-radius: 10px 10px 0 0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 12px 16px !important;
}
.stg-content .pos-tab-content .box .box-header .box-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
}

/* ── Select2 inside panels ── */
.stg-content .pos-tab-content .select2-container .select2-selection--single {
    height: 40px !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    background: #fff !important;
}
.stg-content .pos-tab-content .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 38px !important;
    font-size: 13px !important;
    color: #0f172a !important;
    padding-left: 12px !important;
}
.stg-content .pos-tab-content .select2-container .select2-selection--single .select2-selection__arrow {
    height: 40px !important;
}
.stg-content .pos-tab-content .input-group .select2-container .select2-selection--single {
    border-radius: 0 8px 8px 0 !important;
    border-left: none !important;
}

/* ── Tooltip icons inline with labels ── */
.stg-content .pos-tab-content .tooltip-icon,
.stg-content .pos-tab-content a[data-toggle="tooltip"],
.stg-content .pos-tab-content i.fa-question-circle,
.stg-content .pos-tab-content i.fa-info-circle {
    color: {{ $stgMain }} !important;
    font-size: 12px;
    margin-left: 4px;
    vertical-align: middle;
}

/* ── Buttons inside panels ── */
.stg-content .pos-tab-content .btn-primary,
.stg-content .pos-tab-content .btn-info {
    background: {{ $stgMain }} !important;
    border-color: {{ $stgMain }} !important;
    border-radius: 8px !important;
    font-size: 12px;
    font-weight: 600;
    padding: 8px 16px !important;
}
.stg-content .pos-tab-content .btn-default {
    border-radius: 8px !important;
    font-size: 12px;
    font-weight: 500;
    padding: 8px 16px !important;
    border-color: #e2e8f0 !important;
    color: #475569 !important;
}
.stg-content .pos-tab-content .btn-danger {
    background: #dc2626 !important;
    border-color: #dc2626 !important;
    border-radius: 8px !important;
    font-size: 12px;
    font-weight: 600;
    padding: 8px 16px !important;
}

/* ── Color swatches (theme color picker) ── */
.stg-content .pos-tab-content .icheck-label {
    padding: 6px 12px !important;
    border-radius: 6px;
    font-size: 12px;
}

/* ── Alert/info boxes inside panels ── */
.stg-content .pos-tab-content .alert {
    border-radius: 8px !important;
    border: none !important;
    font-size: 12px;
    padding: 10px 14px !important;
}
.stg-content .pos-tab-content .alert-info {
    background: #eff6ff !important;
    color: #1e40af !important;
}
.stg-content .pos-tab-content .alert-warning {
    background: #fffbeb !important;
    color: #92400e !important;
}

/* ── Clearfix: remove visual gaps caused by .clearfix divs ── */
.stg-content .pos-tab-content .clearfix { margin: 0 !important; }

/* ── Tinymce editor container ── */
.stg-content .pos-tab-content .tox-tinymce {
    border-radius: 8px !important;
    border-color: #e2e8f0 !important;
}

/* ── Mobile responsiveness ── */
@@media (max-width: 768px) {
    .stg-content .pos-tab-content > .row {
        padding: 14px 14px 6px !important;
    }
}
</style>
@endsection

@section('javascript')
<script type="text/javascript">
    __page_leave_confirmation('#bussiness_edit_form');
    $(document).on('ifToggled', '#use_superadmin_settings', function() {
        if ($('#use_superadmin_settings').is(':checked')) {
            $('#toggle_visibility').addClass('hide');
            $('.test_email_btn').addClass('hide');
        } else {
            $('#toggle_visibility').removeClass('hide');
            $('.test_email_btn').removeClass('hide');
        }
    });

    $(document).ready(function(){

    
        $('#test_email_btn').click( function() {
            var data = {
                mail_driver: $('#mail_driver').val(),
                mail_host: $('#mail_host').val(),
                mail_port: $('#mail_port').val(),
                mail_username: $('#mail_username').val(),
                mail_password: $('#mail_password').val(),
                mail_encryption: $('#mail_encryption').val(),
                mail_from_address: $('#mail_from_address').val(),
                mail_from_name: $('#mail_from_name').val(),
            };
            $.ajax({
                method: 'post',
                data: data,
                url: "{{ action([\App\Http\Controllers\BusinessController::class, 'testEmailConfiguration']) }}",
                dataType: 'json',
                success: function(result) {
                    if (result.success == true) {
                        swal({
                            text: result.msg,
                            icon: 'success'
                        });
                    } else {
                        swal({
                            text: result.msg,
                            icon: 'error'
                        });
                    }
                },
            });
        });

        $('#test_sms_btn').click( function() {
            var test_number = $('#test_number').val();
            if (test_number.trim() == '') {
                toastr.error('{{__("lang_v1.test_number_is_required")}}');
                $('#test_number').focus();

                return false;
            }

            var data = {
                url: $('#sms_settings_url').val(),
                send_to_param_name: $('#send_to_param_name').val(),
                msg_param_name: $('#msg_param_name').val(),
                request_method: $('#request_method').val(),
                param_1: $('#sms_settings_param_key1').val(),
                param_2: $('#sms_settings_param_key2').val(),
                param_3: $('#sms_settings_param_key3').val(),
                param_4: $('#sms_settings_param_key4').val(),
                param_5: $('#sms_settings_param_key5').val(),
                param_6: $('#sms_settings_param_key6').val(),
                param_7: $('#sms_settings_param_key7').val(),
                param_8: $('#sms_settings_param_key8').val(),
                param_9: $('#sms_settings_param_key9').val(),
                param_10: $('#sms_settings_param_key10').val(),

                param_val_1: $('#sms_settings_param_val1').val(),
                param_val_2: $('#sms_settings_param_val2').val(),
                param_val_3: $('#sms_settings_param_val3').val(),
                param_val_4: $('#sms_settings_param_val4').val(),
                param_val_5: $('#sms_settings_param_val5').val(),
                param_val_6: $('#sms_settings_param_val6').val(),
                param_val_7: $('#sms_settings_param_val7').val(),
                param_val_8: $('#sms_settings_param_val8').val(),
                param_val_9: $('#sms_settings_param_val9').val(),
                param_val_10: $('#sms_settings_param_val10').val(),
                test_number: test_number,

                header_1: $('#sms_settings_header_key1').val(),
                header_val_1: $('#sms_settings_header_val1').val(),
                header_2: $('#sms_settings_header_key2').val(),
                header_val_2: $('#sms_settings_header_val2').val(),
                header_3: $('#sms_settings_header_key3').val(),
                header_val_3: $('#sms_settings_header_val3').val(),
                data_parameter_type: $('#data_parameter_type').val(),
            };

            $.ajax({
                method: 'post',
                data: data,
                url: "{{ action([\App\Http\Controllers\BusinessController::class, 'testSmsConfiguration']) }}",
                dataType: 'json',
                success: function(result) {
                    if (result.success == true) {
                        swal({
                            text: result.msg,
                            icon: 'success'
                        });
                    } else {
                        swal({
                            text: result.msg,
                            icon: 'error'
                        });
                    }
                },
            });

        });

        $('select.custom_labels_products').change(function(){
            value = $(this).val();
            textarea = $(this).parents('div.custom_label_product_div').find('div.custom_label_product_dropdown');
            if(value == 'dropdown'){
                textarea.removeClass('hide');
            } else{
                textarea.addClass('hide');
            }
        })

        tinymce.init({
            selector: 'textarea#display_screen_heading',
            height: 250
        });

        $('.carousel_image').fileinput({
            showUpload: true,
            showPreview: true,
            browseLabel: LANG.file_browse_label,
            removeLabel: LANG.remove,
        });

        // ── Fix tab switching: $(this).index() counts .stg-nav-group divs too,
        //    so we override with an <a>-only index calculation ──
        $('div.pos-tab-menu > div.list-group > a.stg-nav-item').off('click').on('click', function(e) {
            e.preventDefault();
            // Index among <a> siblings only — ignores .stg-nav-group divs
            var index = $(this).parent().children('a.stg-nav-item').index(this);
            // Update sidebar active state
            $(this).siblings('a.stg-nav-item').removeClass('active');
            $(this).addClass('active');
            // Show matching content panel
            $('div.stg-content > div.pos-tab-content').removeClass('active');
            $('div.stg-content > div.pos-tab-content').eq(index).addClass('active');
            // Scroll content to top
            $('.stg-content').scrollTop(0);
        });

        // ── Sticky save bar: show when form is dirty ──
        var formDirty = false;
        $('#bussiness_edit_form').on('change input', function() {
            if (!formDirty) {
                formDirty = true;
                $('.stg-sticky-bar').addClass('visible');
            }
        });
        $('#bussiness_edit_form').on('submit', function() {
            $('.stg-sticky-bar').removeClass('visible');
        });

        // ── Keep search-settings sync with new index logic ──
        $('#search_settings').off('change').on('change', function() {
            var label_index = $(this).val();
            if (typeof label_objects === 'undefined' || !label_objects[label_index]) return;
            var label = label_objects[label_index];
            var tab_content = label.closest('.pos-tab-content');
            var index = $('div.stg-content > div.pos-tab-content').index(tab_content);
            $('div.stg-content > div.pos-tab-content').removeClass('active');
            tab_content.addClass('active');
            $('div.pos-tab-menu a.stg-nav-item').removeClass('active');
            $('div.pos-tab-menu a.stg-nav-item').eq(index).addClass('active');
            $([document.documentElement, document.body]).animate({ scrollTop: label.offset().top - 100 }, 500);
            label.css('background-color', 'yellow');
            setTimeout(function(){ label.css('background-color', ''); }, 3000);
        });
    });
</script>
@endsection