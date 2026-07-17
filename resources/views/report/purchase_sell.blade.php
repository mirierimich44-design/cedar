@extends('layouts.app')
@section('title', __( 'report.purchase_sell' ))

@section('content')
@php
    $themeMap = [
        'green'=>['dark'=>'#065f46','main'=>'#059669'],'green-light'=>['dark'=>'#065f46','main'=>'#059669'],
        'blue'=>['dark'=>'#1e3a5f','main'=>'#2563eb'],'blue-light'=>['dark'=>'#1e40af','main'=>'#3b82f6'],
        'red'=>['dark'=>'#991b1b','main'=>'#dc2626'],'purple'=>['dark'=>'#581c87','main'=>'#7c3aed'],
        'primary'=>['dark'=>'#312e81','main'=>'#4f46e5'],'yellow'=>['dark'=>'#92400e','main'=>'#d97706'],
        'orange'=>['dark'=>'#9a3412','main'=>'#ea580c'],'sky'=>['dark'=>'#075985','main'=>'#0284c7'],
    ];
    $t = $themeMap[session('business.theme_color','primary')] ?? $themeMap['primary'];
@endphp

<div class="report-page-modern">
    {{-- Page Header Banner --}}
    <div style="background:linear-gradient(135deg,{{ $t['dark'] }},{{ $t['main'] }});padding:28px 28px 22px;border-radius:0 0 18px 18px;margin:-15px -15px 24px;">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <span style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-exchange-alt" style="color:white;font-size:20px;"></i>
                </span>
                <div>
                    <h1 style="color:#fff;font-size:22px;font-weight:700;margin:0;">@lang('report.purchase_sell')</h1>
                    <p style="color:rgba(255,255,255,0.7);font-size:13px;margin:2px 0 0;">@lang('report.purchase_sell_msg')</p>
                </div>
            </div>
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;" class="no-print">
                <select class="form-control select2" id="purchase_sell_location_filter" style="min-width:180px;border-radius:8px;font-size:13px;height:36px;">
                    @foreach($business_locations as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <button type="button" id="purchase_sell_date_filter" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:6px;white-space:nowrap;">
                    <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }} <i class="fa fa-caret-down"></i>
                </button>
                <button onclick="window.print();" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:6px;">
                    <i class="fas fa-print"></i> @lang('messages.print')
                </button>
            </div>
        </div>
    </div>

    <div class="print_section"><h2>{{session()->get('business.name')}} - @lang('report.purchase_sell')</h2></div>

    {{-- Purchase & Sale Cards --}}
    <div style="padding:0 12px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px;margin-bottom:20px;">
            {{-- Purchases Card --}}
            <div style="background:#fff;border-radius:14px;box-shadow:0 1px 6px rgba(0,0,0,0.06);overflow:hidden;">
                <div style="background:linear-gradient(135deg,#2563eb,#60a5fa);padding:14px 16px;display:flex;align-items:center;gap:10px;">
                    <span style="width:30px;height:30px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-truck" style="color:white;font-size:14px;"></i>
                    </span>
                    <h3 style="color:white;font-weight:700;font-size:15px;margin:0;">@lang('purchase.purchases')</h3>
                </div>
                <div style="padding:16px;">
                    <table class="table table-striped" style="margin:0;">
                        <tr><th>{{ __('report.total_purchase') }}:</th><td><span class="total_purchase"><i class="fas fa-sync fa-spin fa-fw"></i></span></td></tr>
                        <tr><th>{{ __('report.purchase_inc_tax') }}:</th><td><span class="purchase_inc_tax"><i class="fas fa-sync fa-spin fa-fw"></i></span></td></tr>
                        <tr><th>{{ __('lang_v1.total_purchase_return_inc_tax') }}:</th><td><span class="purchase_return_inc_tax"><i class="fas fa-sync fa-spin fa-fw"></i></span></td></tr>
                        <tr><th>{{ __('report.purchase_due') }}: @show_tooltip(__('tooltip.purchase_due'))</th><td><span class="purchase_due"><i class="fas fa-sync fa-spin fa-fw"></i></span></td></tr>
                    </table>
                </div>
            </div>

            {{-- Sales Card --}}
            <div style="background:#fff;border-radius:14px;box-shadow:0 1px 6px rgba(0,0,0,0.06);overflow:hidden;">
                <div style="background:linear-gradient(135deg,#059669,#10b981);padding:14px 16px;display:flex;align-items:center;gap:10px;">
                    <span style="width:30px;height:30px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-shopping-cart" style="color:white;font-size:14px;"></i>
                    </span>
                    <h3 style="color:white;font-weight:700;font-size:15px;margin:0;">@lang('sale.sells')</h3>
                </div>
                <div style="padding:16px;">
                    <table class="table table-striped" style="margin:0;">
                        <tr><th>{{ __('report.total_sell') }}:</th><td><span class="total_sell"><i class="fas fa-sync fa-spin fa-fw"></i></span></td></tr>
                        <tr><th>{{ __('report.sell_inc_tax') }}:</th><td><span class="sell_inc_tax"><i class="fas fa-sync fa-spin fa-fw"></i></span></td></tr>
                        <tr><th>{{ __('lang_v1.total_sell_return_inc_tax') }}:</th><td><span class="total_sell_return"><i class="fas fa-sync fa-spin fa-fw"></i></span></td></tr>
                        <tr><th>{{ __('report.sell_due') }}: @show_tooltip(__('tooltip.sell_due'))</th><td><span class="sell_due"><i class="fas fa-sync fa-spin fa-fw"></i></span></td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Overall Summary Card --}}
        <div style="background:#fff;border-radius:14px;box-shadow:0 1px 6px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:20px;">
            <div style="background:linear-gradient(135deg,#475569,#334155);padding:14px 16px;display:flex;align-items:center;gap:10px;">
                <span style="width:30px;height:30px;background:rgba(255,255,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-balance-scale" style="color:white;font-size:14px;"></i>
                </span>
                <h3 style="color:white;font-weight:700;font-size:15px;margin:0;">
                    {{ __('lang_v1.overall') }}
                    <span style="font-weight:400;font-size:12px;color:rgba(255,255,255,0.7);">
                        (@lang('business.sale') - @lang('lang_v1.sell_return')) - (@lang('lang_v1.purchase') - @lang('lang_v1.purchase_return'))
                    </span>
                    @show_tooltip(__('tooltip.over_all_sell_purchase'))
                </h3>
            </div>
            <div style="padding:20px;display:flex;flex-wrap:wrap;gap:32px;">
                <div>
                    <span style="font-size:13px;color:#64748b;font-weight:500;">{{ __('report.sell_minus_purchase') }}</span>
                    <p style="font-size:24px;font-weight:700;color:#1e293b;margin:4px 0 0;font-family:ui-monospace,monospace;">
                        <span class="sell_minus_purchase"><i class="fas fa-sync fa-spin fa-fw"></i></span>
                    </p>
                </div>
                <div>
                    <span style="font-size:13px;color:#64748b;font-weight:500;">{{ __('report.difference_due') }}</span>
                    <p style="font-size:24px;font-weight:700;color:#1e293b;margin:4px 0 0;font-family:ui-monospace,monospace;">
                        <span class="difference_due"><i class="fas fa-sync fa-spin fa-fw"></i></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
@includeIf('report.partials.report_modern_css')
@endsection

@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection
