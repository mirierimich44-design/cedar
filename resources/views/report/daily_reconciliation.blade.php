@extends('layouts.app')
@section('title', __('Daily Cash Reconciliation'))

@php
    $themeAccentMap = [
        'primary' => ['solid' => '#4f46e5', 'dark' => '#3730a3'],
        'purple'  => ['solid' => '#7c3aed', 'dark' => '#5b21b6'],
        'green'   => ['solid' => '#059669', 'dark' => '#065f46'],
        'red'     => ['solid' => '#dc2626', 'dark' => '#991b1b'],
        'yellow'  => ['solid' => '#d97706', 'dark' => '#92400e'],
        'orange'  => ['solid' => '#ea580c', 'dark' => '#9a3412'],
        'sky'     => ['solid' => '#0284c7', 'dark' => '#075985'],
    ];
    $theme       = session('business.theme_color', 'primary');
    $themeColors = $themeAccentMap[$theme] ?? $themeAccentMap['primary'];
    $themeSolid  = $themeColors['solid'];
    $themeDark   = $themeColors['dark'];
@endphp

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    :root {
        --theme-solid: {{ $themeSolid }};
        --theme-dark:  {{ $themeDark }};
    }

    /* ── Page toolbar ─────────────────────────────────── */
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px; }
    .page-toolbar h1 { margin:0; font-size:22px; font-weight:700; color:#111827; }

    /* ── Filter bar ───────────────────────────────────── */
    .filter-bar {
        background:#fff; border-radius:12px; border:1px solid #e5e7eb;
        padding:14px 18px; margin-bottom:16px;
        display:flex; flex-wrap:wrap; align-items:flex-end; gap:12px;
    }
    .filter-bar .form-group { margin:0; min-width:160px; flex:1; }
    .filter-bar label { font-size:11px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.3px; margin-bottom:4px; display:block; }
    .filter-bar .form-control { border-radius:8px; border:1px solid #d1d5db; font-size:13px; }

    /* ── KPI grid ─────────────────────────────────────── */
    .kpi-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:16px; }
    @media(min-width:900px){ .kpi-grid{ grid-template-columns:repeat(6,1fr); } }

    .kpi-card {
        border-radius:14px; padding:18px 16px 14px;
        display:flex; flex-direction:column; gap:6px;
        box-shadow:0 2px 8px rgba(0,0,0,.13);
        transition:box-shadow .15s, transform .15s;
        position:relative; overflow:hidden; color:#fff;
    }
    .kpi-card::after {
        content:''; position:absolute; top:-30%; right:-20%;
        width:80px; height:130%; background:rgba(255,255,255,.08);
        transform:rotate(25deg); border-radius:50%;
    }
    .kpi-card:hover { box-shadow:0 6px 20px rgba(0,0,0,.18); transform:translateY(-2px); }
    .kpi-card .kpi-icon {
        width:38px; height:38px; border-radius:10px;
        background:rgba(255,255,255,.22);
        display:flex; align-items:center; justify-content:center;
        font-size:17px; color:#fff; margin-bottom:4px; flex-shrink:0;
    }
    .kpi-card .kpi-value { font-size:22px; font-weight:800; color:#fff; line-height:1.1; }
    .kpi-card .kpi-label { font-size:10px; font-weight:700; color:rgba(255,255,255,.82); text-transform:uppercase; letter-spacing:.6px; }

    /* Individual card colours */
    .kpi-gross   { background:linear-gradient(135deg,var(--theme-solid),var(--theme-dark)); }
    .kpi-net     { background:linear-gradient(135deg,#2563eb,#1e40af); }
    .kpi-profit  { background:linear-gradient(135deg,#10b981,#047857); }
    .kpi-margin  { background:linear-gradient(135deg,#8b5cf6,#6d28d9); }
    .kpi-txns    { background:linear-gradient(135deg,#f59e0b,#b45309); }
    .kpi-expenses{ background:linear-gradient(135deg,#ef4444,#b91c1c); }

    /* ── Section cards ────────────────────────────────── */
    .report-card {
        background:#fff; border-radius:12px; border:1px solid #e5e7eb;
        box-shadow:0 1px 3px rgba(0,0,0,.05); margin-bottom:16px; overflow:hidden;
    }
    .report-card-header {
        display:flex; align-items:center; gap:8px;
        padding:12px 18px; border-bottom:1px solid rgba(255,255,255,.15);
        font-size:13px; font-weight:700; color:#fff;
    }
    .report-card-header i { color:rgba(255,255,255,.85); }

    /* Per-section colours */
    .rch-sales    { background:linear-gradient(135deg,var(--theme-solid),var(--theme-dark)); }
    .rch-profit   { background:linear-gradient(135deg,#059669,#047857); }
    .rch-payments { background:linear-gradient(135deg,#f59e0b,#b45309); }
    .rch-volume   { background:linear-gradient(135deg,#2563eb,#1d4ed8); }
    .rch-register { background:linear-gradient(135deg,#475569,#334155); }

    /* ── Tables ───────────────────────────────────────── */
    .report-card table { width:100%; border-collapse:collapse; }
    .report-card table thead th {
        background:#f9fafb; font-size:11px; font-weight:700; text-transform:uppercase;
        letter-spacing:.4px; color:#6b7280; padding:9px 14px;
        border-bottom:1px solid #e5e7eb;
    }
    .report-card table tbody td { font-size:13px; color:#374151; padding:9px 14px; border-bottom:1px solid #f9fafb; vertical-align:middle; }
    .report-card table tbody tr:hover td { background:#f8faff; }
    .report-card table tfoot th, .report-card table tfoot td {
        background:#f0f4ff; font-size:12px; font-weight:700;
        padding:9px 14px; border-top:2px solid #e5e7eb;
    }

    /* register table small font */
    #recon_register_table td, #recon_register_table th { white-space:nowrap; font-size:12px; }

    @media print {
        .no-print, .main-sidebar, .main-header, .content-header { display:none !important; }
        .print_section { display:block !important; }
        .content-wrapper { margin-left:0 !important; }
    }
</style>
@endsection

@section('content')
<section class="content-header no-print">
    <div class="page-toolbar">
        <div>
            <h1>Daily Cash Reconciliation</h1>
            <small class="tw-text-sm tw-text-gray-500">Sales, payments &amp; profitability summary</small>
        </div>
        <button type="button" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-ghost no-print" onclick="window.print()">
            <i class="fa fa-print"></i> Print
        </button>
    </div>
</section>

<section class="content">

    {{-- Print header --}}
    <div style="display:none;text-align:center;margin-bottom:20px;" class="print_section">
        <h2>{{ session()->get('business.name') }}</h2>
        <h3>Daily Cash Reconciliation &mdash; <span id="print_date_label"></span></h3>
    </div>

    {{-- Filter bar --}}
    <div class="filter-bar no-print">

        {{-- Quick date presets --}}
        <div class="form-group" style="min-width:auto;flex:none;">
            <label>Quick Select</label>
            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                @foreach([
                    'today'      => 'Today',
                    'yesterday'  => 'Yesterday',
                    'this_week'  => 'This Week',
                    'last_week'  => 'Last Week',
                    'this_month' => 'This Month',
                    'last_month' => 'Last Month',
                ] as $key => $lbl)
                <button type="button" class="date-preset-btn"
                    data-preset="{{ $key }}"
                    style="padding:5px 11px;font-size:12px;font-weight:600;border-radius:7px;
                           border:1px solid #d1d5db;background:#fff;color:#374151;cursor:pointer;
                           white-space:nowrap;line-height:1.4;transition:all .15s;">
                    {{ $lbl }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Date --}}
        <div class="form-group" style="min-width:140px;max-width:180px;">
            <label>Date</label>
            <input type="text" class="form-control" id="reconciliation_date" value="{{ date('Y-m-d') }}" readonly>
        </div>

        {{-- Location --}}
        <div class="form-group">
            <label>Location</label>
            {!! Form::select('location_id', $business_locations, null,
                ['class' => 'form-control select2', 'id' => 'reconciliation_location',
                 'placeholder' => 'All Locations']) !!}
        </div>

        {{-- Load --}}
        <div style="padding-top:1px;">
            <label style="visibility:hidden;display:block;font-size:11px;">Go</label>
            <button type="button" id="load_reconciliation"
                    class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-border-none"
                    style="border-radius:8px;white-space:nowrap;background:var(--theme-solid);">
                <i class="fa fa-search"></i> Load Report
            </button>
        </div>
    </div>

    {{-- Loading --}}
    <div id="reconciliation_loading" style="display:none;text-align:center;padding:60px;">
        <i class="fa fa-spinner fa-spin fa-2x" style="color:var(--theme-solid);"></i>
        <p style="margin-top:12px;color:#6b7280;font-size:14px;">Loading report…</p>
    </div>

    {{-- Report output --}}
    <div id="reconciliation_report" style="display:none;">

        {{-- KPI cards --}}
        <div class="kpi-grid">
            <div class="kpi-card kpi-gross">
                <div class="kpi-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:19px;height:19px;" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
                </div>
                <div class="kpi-value" id="recon_kpi_gross">0.00</div>
                <div class="kpi-label">Gross Sales</div>
            </div>
            <div class="kpi-card kpi-net">
                <div class="kpi-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:19px;height:19px;" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 19l4 -4l4 4l4 -6l4 2"/></svg>
                </div>
                <div class="kpi-value" id="recon_kpi_net">0.00</div>
                <div class="kpi-label">Net Sales</div>
            </div>
            <div class="kpi-card kpi-profit">
                <div class="kpi-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:19px;height:19px;" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/></svg>
                </div>
                <div class="kpi-value" id="recon_kpi_profit">0.00</div>
                <div class="kpi-label">Gross Profit</div>
            </div>
            <div class="kpi-card kpi-margin">
                <div class="kpi-icon"><i class="fa fa-percent"></i></div>
                <div class="kpi-value" id="recon_kpi_margin">0%</div>
                <div class="kpi-label">Profit Margin</div>
            </div>
            <div class="kpi-card kpi-txns">
                <div class="kpi-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:19px;height:19px;" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 5l0 2"/><path d="M15 11l0 2"/><path d="M15 17l0 2"/><path d="M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-3a2 2 0 0 0 0 -4v-3a2 2 0 0 1 2 -2"/></svg>
                </div>
                <div class="kpi-value" id="recon_kpi_transactions">0</div>
                <div class="kpi-label">Transactions</div>
            </div>
            <div class="kpi-card kpi-expenses">
                <div class="kpi-icon"><i class="fa fa-minus-circle"></i></div>
                <div class="kpi-value" id="recon_kpi_expenses">0.00</div>
                <div class="kpi-label">Expenses</div>
            </div>
        </div>

        <div id="print_area">
        <div class="text-center print_section" style="display:none;margin-bottom:15px;">
            <p id="report_date_label" class="text-muted"></p>
        </div>

        <div class="row">
            {{-- Sales Summary --}}
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-card-header rch-sales">
                        <i class="fa fa-shopping-cart"></i> Sales Summary
                    </div>
                    <table>
                        <tbody>
                            <tr><td><strong>Gross Sales</strong></td><td style="text-align:right;font-weight:700;color:#059669;" id="r_gross_sales">—</td></tr>
                            <tr><td>Less: Discounts</td><td style="text-align:right;color:#d97706;" id="r_discount">—</td></tr>
                            <tr style="background:#f8faff;"><td><strong>Net Sales</strong></td><td style="text-align:right;font-weight:700;" id="r_net_sales">—</td></tr>
                            <tr><td>VAT / Tax Collected</td><td style="text-align:right;" id="r_tax">—</td></tr>
                            <tr><td>Sell Returns / Refunds</td><td style="text-align:right;color:#dc2626;" id="r_returns">—</td></tr>
                            <tr><td>Total Expenses</td><td style="text-align:right;color:#dc2626;" id="r_expenses">—</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Profitability --}}
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-card-header rch-profit">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 19l4 -4l4 4l4 -6l4 2"/></svg>
                        Profitability
                    </div>
                    <table>
                        <tbody>
                            <tr><td>Net Sales</td><td style="text-align:right;" id="r_net_sales_2">—</td></tr>
                            <tr><td>Less: Cost of Goods Sold (COGS)</td><td style="text-align:right;color:#dc2626;" id="r_cogs">—</td></tr>
                            <tr style="background:#f8faff;"><td><strong>Gross Profit</strong></td><td style="text-align:right;font-weight:700;" id="r_gross_profit">—</td></tr>
                            <tr><td><strong>Profit Margin</strong></td><td style="text-align:right;font-weight:700;" id="r_margin">—</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Payment Breakdown --}}
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-card-header rch-payments">
                        <i class="fa fa-credit-card"></i> Payment Method Breakdown
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th style="text-align:right;">Transactions</th>
                                <th style="text-align:right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="r_payments_body">
                            <tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:16px;">Loading…</td></tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total Collected</th>
                                <th style="text-align:right;" id="r_pay_total_count">—</th>
                                <th style="text-align:right;" id="r_pay_total_amount">—</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Transaction Volume --}}
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-card-header rch-volume">
                        <i class="fa fa-bar-chart"></i> Transaction Volume
                    </div>
                    <table>
                        <tbody>
                            <tr><td><strong>Total Transactions</strong></td><td style="text-align:right;font-weight:700;" id="r_tx_count">—</td></tr>
                            <tr><td>Average Basket Value</td><td style="text-align:right;" id="r_avg_basket">—</td></tr>
                            <tr><td>Gross Sales</td><td style="text-align:right;" id="r_gross_sales_2">—</td></tr>
                            <tr><td>Net Sales</td><td style="text-align:right;" id="r_net_sales_3">—</td></tr>
                            <tr><td>Gross Profit</td><td style="text-align:right;" id="r_gross_profit_2">—</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>{{-- /print_area --}}

        {{-- Register Sessions --}}
        <div class="report-card">
            <div class="report-card-header rch-register">
                <i class="fa fa-desktop"></i> Register Sessions for the Day
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-condensed" id="recon_register_table">
                    <thead>
                        <tr>
                            <th>Open Time</th>
                            <th>Close Time</th>
                            <th>Location</th>
                            <th>User</th>
                            <th>{{ $payment_types['custom_pay_1'] ?? 'M-Pesa' }}</th>
                            <th>Cash</th>
                            <th>Card</th>
                            <th>Cheque</th>
                            <th>Bank Transfer</th>
                            <th>{{ $payment_types['custom_pay_2'] ?? 'Custom Pay 2' }}</th>
                            <th>{{ $payment_types['custom_pay_3'] ?? 'Custom Pay 3' }}</th>
                            <th>Other</th>
                            <th>Advance</th>
                            <th><strong>Total</strong></th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="recon_register_body"></tbody>
                    <tfoot>
                        <tr style="background:#f0f4ff;font-weight:700;">
                            <td colspan="4"><strong>Total:</strong></td>
                            <td class="recon_reg_custom1">—</td>
                            <td class="recon_reg_cash">—</td>
                            <td class="recon_reg_card">—</td>
                            <td class="recon_reg_cheque">—</td>
                            <td class="recon_reg_bank">—</td>
                            <td class="recon_reg_custom2">—</td>
                            <td class="recon_reg_custom3">—</td>
                            <td class="recon_reg_other">—</td>
                            <td class="recon_reg_advance">—</td>
                            <td class="recon_reg_total">—</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>{{-- /reconciliation_report --}}
</section>

<div class="modal fade view_register" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {

    /* ── Flatpickr ─────────────────────────────────────── */
    var fp = flatpickr('#reconciliation_date', {
        dateFormat: 'Y-m-d',
        defaultDate: 'today',
        maxDate: 'today',
        disableMobile: false,
        onReady: function(_, __, inst) { inst.calendarContainer.style.zIndex = '99999'; }
    });

    /* ── Date presets ──────────────────────────────────── */
    function getPresetDate(preset) {
        var today = new Date();
        var fmt = function(d) { return d.toISOString().slice(0,10); };
        switch(preset) {
            case 'today':     return fmt(today);
            case 'yesterday':
                var y = new Date(today); y.setDate(y.getDate()-1); return fmt(y);
            case 'this_week':
                var dow = today.getDay();
                var mon = new Date(today); mon.setDate(today.getDate()-((dow+6)%7)); return fmt(mon);
            case 'last_week':
                var dow2 = today.getDay();
                var lmon = new Date(today); lmon.setDate(today.getDate()-((dow2+6)%7)-7); return fmt(lmon);
            case 'this_month':
                return fmt(new Date(today.getFullYear(), today.getMonth(), 1));
            case 'last_month':
                return fmt(new Date(today.getFullYear(), today.getMonth()-1, 1));
        }
    }

    $('.date-preset-btn').on('click', function() {
        $('.date-preset-btn').css({'background':'#fff','color':'#374151','border-color':'#d1d5db'});
        $(this).css({'background':'var(--theme-solid)','color':'#fff','border-color':'var(--theme-solid)'});
        var date = getPresetDate($(this).data('preset'));
        fp.setDate(date, false);
        $('#reconciliation_date').val(date);
        loadReport();
    });

    fp.config.onChange.push(function() {
        $('.date-preset-btn').css({'background':'#fff','color':'#374151','border-color':'#d1d5db'});
    });

    /* ── Formatters ────────────────────────────────────── */
    function fmt(num) {
        if (num === undefined || num === null) return '—';
        return parseFloat(num).toLocaleString('en-KE', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    /* ── Load report ───────────────────────────────────── */
    function loadReport() {
        var date     = $('#reconciliation_date').val();
        var location = $('#reconciliation_location').val();

        $('#reconciliation_report').hide();
        $('#reconciliation_loading').show();

        $.ajax({
            url: '{{ route("reports.daily_reconciliation") }}',
            data: { date: date, location_id: location },
            dataType: 'json',
            success: function(d) {
                $('#reconciliation_loading').hide();
                if (!d.success) { toastr.error('Failed to load report.'); return; }

                $('#report_date_label').text(d.date_label);
                $('#print_date_label').text(d.date_label);

                // KPI
                $('#recon_kpi_gross').text(fmt(d.gross_sales));
                $('#recon_kpi_net').text(fmt(d.net_sales));
                $('#recon_kpi_profit').text(fmt(d.gross_profit));
                $('#recon_kpi_margin').text(d.profit_margin + '%');
                $('#recon_kpi_transactions').text(d.total_transactions);
                $('#recon_kpi_expenses').text(fmt(d.total_expenses));

                // Sales summary
                $('#r_gross_sales').text(fmt(d.gross_sales));
                $('#r_gross_sales_2').text(fmt(d.gross_sales));
                $('#r_discount').text('− ' + fmt(d.total_discount));
                $('#r_net_sales').text(fmt(d.net_sales));
                $('#r_net_sales_2').text(fmt(d.net_sales));
                $('#r_net_sales_3').text(fmt(d.net_sales));
                $('#r_tax').text(fmt(d.total_tax));
                $('#r_returns').text('− ' + fmt(d.total_returns));
                $('#r_expenses').text('− ' + fmt(d.total_expenses));

                // Profitability
                $('#r_cogs').text('− ' + fmt(d.cogs));
                var gp = parseFloat(d.gross_profit) || 0;
                $('#r_gross_profit').text(fmt(gp)).css('color', gp >= 0 ? '#059669' : '#dc2626');
                $('#r_gross_profit_2').text(fmt(gp));
                $('#r_margin').text(d.profit_margin + '%');

                // Volume
                $('#r_tx_count').text(d.total_transactions);
                $('#r_avg_basket').text(fmt(d.avg_basket));

                // Payment breakdown
                var labels    = d.payment_labels;
                var breakdown = d.payment_breakdown;
                var rows      = '';
                var totalAmt  = 0, totalCnt = 0;
                $.each(labels, function(method, label) {
                    if (breakdown[method]) {
                        var row = breakdown[method];
                        rows += '<tr><td>' + label + '</td><td style="text-align:right;">' +
                            row.count + '</td><td style="text-align:right;font-weight:600;">' + fmt(row.total) + '</td></tr>';
                        totalAmt += parseFloat(row.total);
                        totalCnt += parseInt(row.count);
                    }
                });
                if (!rows) rows = '<tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:16px;">No payments recorded</td></tr>';
                $('#r_payments_body').html(rows);
                $('#r_pay_total_count').text(totalCnt);
                $('#r_pay_total_amount').text(fmt(totalAmt));

                $('#reconciliation_report').show();
                loadRegisterReport(date, location);
            },
            error: function() {
                $('#reconciliation_loading').hide();
                toastr.error('Server error loading report.');
            }
        });
    }

    /* ── Register DataTable ────────────────────────────── */
    var recon_register_table = null;

    function loadRegisterReport(date, location_id) {
        if (recon_register_table) {
            recon_register_table.destroy();
            $('#recon_register_body').empty();
        }
        recon_register_table = $('#recon_register_table').DataTable({
            processing: true, serverSide: true, paging: false, searching: false, info: false, scrollX: true,
            ajax: { url: '/reports/register-report', data: { start_date: date, end_date: date, location_id: location_id || '' } },
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'closed_at',  name: 'closed_at' },
                { data: 'location_name', name: 'bl.name' },
                { data: 'user_name', name: 'user_name', render: function(d){ return $('<div>').html(d).text(); } },
                { data: 'total_custom_pay_1', name: 'total_custom_pay_1', searchable: false },
                { data: 'total_cash_payment', name: 'total_cash_payment', searchable: false },
                { data: 'total_card_payment', name: 'total_card_payment', searchable: false },
                { data: 'total_cheque_payment', name: 'total_cheque_payment', searchable: false },
                { data: 'total_bank_transfer_payment', name: 'total_bank_transfer_payment', searchable: false },
                { data: 'total_custom_pay_2', name: 'total_custom_pay_2', searchable: false },
                { data: 'total_custom_pay_3', name: 'total_custom_pay_3', searchable: false },
                { data: 'total_other_payment', name: 'total_other_payment', searchable: false },
                { data: 'total_advance_payment', name: 'total_advance_payment', searchable: false },
                { data: 'total', name: 'total', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false,
                  render: function(d) {
                    var c = d === 'open'
                        ? 'background:#dcfce7;color:#16a34a;'
                        : 'background:#fee2e2;color:#dc2626;';
                    return '<span style="' + c + 'padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;">' + (d||'-') + '</span>';
                  }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            footerCallback: function(row, data) {
                var t = { c1:0, cash:0, card:0, cheque:0, bank:0, c2:0, c3:0, other:0, adv:0, total:0 };
                for (var r in data) {
                    t.c1    += parseFloat($(data[r].total_custom_pay_1).data('orig-value') || 0);
                    t.cash  += parseFloat($(data[r].total_cash_payment).data('orig-value') || 0);
                    t.card  += parseFloat($(data[r].total_card_payment).data('orig-value') || 0);
                    t.cheque+= parseFloat($(data[r].total_cheque_payment).data('orig-value') || 0);
                    t.bank  += parseFloat($(data[r].total_bank_transfer_payment).data('orig-value') || 0);
                    t.c2    += parseFloat($(data[r].total_custom_pay_2).data('orig-value') || 0);
                    t.c3    += parseFloat($(data[r].total_custom_pay_3).data('orig-value') || 0);
                    t.other += parseFloat($(data[r].total_other_payment).data('orig-value') || 0);
                    t.adv   += parseFloat($(data[r].total_advance_payment).data('orig-value') || 0);
                    t.total += parseFloat($(data[r].total).data('orig-value') || 0);
                }
                function f(v) { return parseFloat(v).toLocaleString('en-KE', {minimumFractionDigits:2, maximumFractionDigits:2}); }
                $('.recon_reg_custom1').text(f(t.c1));
                $('.recon_reg_cash').text(f(t.cash));
                $('.recon_reg_card').text(f(t.card));
                $('.recon_reg_cheque').text(f(t.cheque));
                $('.recon_reg_bank').text(f(t.bank));
                $('.recon_reg_custom2').text(f(t.c2));
                $('.recon_reg_custom3').text(f(t.c3));
                $('.recon_reg_other').text(f(t.other));
                $('.recon_reg_advance').text(f(t.adv));
                $('.recon_reg_total').html('<strong>' + f(t.total) + '</strong>');
            },
            drawCallback: function() {
                __currency_convert_recursively($('#recon_register_table'));
            }
        });
    }

    $('#load_reconciliation').on('click', loadReport);

    // Print button
    $('#print_reconciliation').on('click', function() {
        var w = window.open('', '_blank');
        w.document.write('<html><head><title>Daily Reconciliation</title>');
        w.document.write('<link rel="stylesheet" href="/css/app.css">');
        w.document.write('</head><body style="padding:20px;">');
        w.document.write(document.getElementById('print_area').innerHTML);
        w.document.write('</body></html>');
        w.document.close(); w.focus();
        setTimeout(function(){ w.print(); w.close(); }, 600);
    });

    // Auto-load today
    $('[data-preset="today"]').trigger('click');
});
</script>
@endsection
