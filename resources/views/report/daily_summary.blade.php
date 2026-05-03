@extends('layouts.app')
@section('title', 'Daily Summary Report')

@php
    $themeAccentMap = [
        'primary' => ['solid' => '#4f46e5', 'light' => '#ede9fe', 'text' => '#3730a3'],
        'purple'  => ['solid' => '#7c3aed', 'light' => '#ede9fe', 'text' => '#5b21b6'],
        'green'   => ['solid' => '#059669', 'light' => '#d1fae5', 'text' => '#065f46'],
        'red'     => ['solid' => '#dc2626', 'light' => '#fee2e2', 'text' => '#991b1b'],
        'yellow'  => ['solid' => '#d97706', 'light' => '#fef3c7', 'text' => '#92400e'],
        'orange'  => ['solid' => '#ea580c', 'light' => '#ffedd5', 'text' => '#9a3412'],
        'sky'     => ['solid' => '#0284c7', 'light' => '#e0f2fe', 'text' => '#075985'],
    ];
    $theme       = session('business.theme_color', 'primary');
    $themeColors = $themeAccentMap[$theme] ?? $themeAccentMap['primary'];
    $themeSolid  = $themeColors['solid'];
    $themeLight  = $themeColors['light'];
    $themeText   = $themeColors['text'];
@endphp
@section('css')
<style>
    :root {
        --theme-solid: {{ $themeSolid }};
        --theme-light: {{ $themeLight }};
        --theme-text:  {{ $themeText }};
    }
    .page-toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px; }
    .page-toolbar h1 { margin:0; font-size:22px; font-weight:700; color:#111827; }

    /* KPI cards */
    .kpi-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px; }
    @media(min-width:768px){ .kpi-grid{ grid-template-columns:repeat(6,1fr); } }
    .kpi-card {
        background:#fff; border-radius:12px; border:1px solid #e5e7eb;
        padding:16px; display:flex; flex-direction:column; gap:8px;
        box-shadow:0 1px 4px rgba(0,0,0,.07);
        border-top: 3px solid transparent;
        transition: box-shadow .15s, transform .15s;
    }
    .kpi-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.10); transform:translateY(-1px); }
    .kpi-card.kpi-primary {
        background: linear-gradient(135deg, var(--theme-solid) 0%, var(--theme-text) 100%);
        border-top-color: transparent;
        color: #fff;
    }
    .kpi-card.kpi-primary .kpi-value { color:#fff; }
    .kpi-card.kpi-primary .kpi-label { color:rgba(255,255,255,.8); }
    .kpi-card.kpi-primary .kpi-icon  { background:rgba(255,255,255,.2); color:#fff; }
    .kpi-card .kpi-icon {
        width:36px; height:36px; border-radius:9px;
        display:flex; align-items:center; justify-content:center; font-size:16px;
        margin-bottom:2px; flex-shrink:0;
    }
    .kpi-card .kpi-value { font-size:20px; font-weight:800; color:#111827; line-height:1.1; }
    .kpi-card .kpi-label { font-size:10.5px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; }

    /* Section cards */
    .report-card {
        background:#fff; border-radius:12px; border:1px solid #e5e7eb;
        box-shadow:0 1px 3px rgba(0,0,0,.05); margin-bottom:16px; overflow:hidden;
    }
    .report-card-header {
        display:flex; align-items:center; gap:8px;
        padding:12px 18px; border-bottom:1px solid #f3f4f6; background:#fafafa;
        font-size:13px; font-weight:700; color:#374151;
    }
    .report-card-header i { color:#6b7280; }

    /* Tables inside report cards */
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
    .report-card table.table-transactions tbody tr.type-return td { color:#dc2626; }
    .report-card table.table-transactions tbody tr.type-expense td { color:#d97706; }

    /* badges */
    .tx-type-badge { display:inline-block; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:600; }
    .tx-sale     { background:#dcfce7; color:#16a34a; }
    .tx-return   { background:#fee2e2; color:#dc2626; }
    .tx-purchase { background:#dbeafe; color:#2563eb; }
    .tx-expense  { background:#fef3c7; color:#d97706; }
    .tx-adj      { background:#f3f4f6; color:#6b7280; }

    /* Filter bar */
    .filter-bar {
        background:#fff; border-radius:12px; border:1px solid #e5e7eb;
        padding:14px 18px; margin-bottom:16px;
        display:flex; flex-wrap:wrap; align-items:flex-end; gap:12px;
    }
    .filter-bar .form-group { margin:0; min-width:160px; flex:1; }
    .filter-bar label { font-size:11px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.3px; margin-bottom:4px; display:block; }
    .filter-bar .form-control { border-radius:8px; border:1px solid #d1d5db; font-size:13px; }

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
            <h1>Daily Summary Report</h1>
            <small class="tw-text-sm tw-text-gray-500">All activity for a selected day</small>
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
        <h3>Daily Summary &mdash; <span id="print_date"></span></h3>
    </div>

    {{-- Filter bar --}}
    <div class="filter-bar no-print">

        {{-- Quick date presets --}}
        <div class="form-group" style="min-width:auto;flex:none;">
            <label>Quick Select</label>
            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                @foreach([
                    'today'        => 'Today',
                    'yesterday'    => 'Yesterday',
                    'this_week'    => 'This Week',
                    'last_week'    => 'Last Week',
                    'this_month'   => 'This Month',
                    'last_month'   => 'Last Month',
                ] as $key => $label)
                <button type="button" class="date-preset-btn"
                    data-preset="{{ $key }}"
                    style="padding:5px 11px;font-size:12px;font-weight:600;border-radius:7px;
                           border:1px solid #d1d5db;background:#fff;color:#374151;cursor:pointer;
                           white-space:nowrap;line-height:1.4;transition:all .15s;">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Date picker --}}
        <div class="form-group" style="min-width:140px;max-width:180px;">
            <label>Date</label>
            <input type="date" class="form-control" id="summary_date" value="{{ date('Y-m-d') }}">
        </div>

        {{-- Location --}}
        <div class="form-group">
            <label>Location</label>
            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'id' => 'summary_location', 'placeholder' => 'All Locations']) !!}
        </div>

        {{-- Load button --}}
        <div style="padding-top:1px;">
            <label style="visibility:hidden;display:block;font-size:11px;">Go</label>
            <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-border-none" id="load_summary"
                    style="border-radius:8px;white-space:nowrap;background:var(--theme-solid);">
                <i class="fa fa-search"></i> Load Report
            </button>
        </div>
    </div>

    {{-- Loading spinner --}}
    <div id="summary_loading" style="display:none;text-align:center;padding:60px;">
        <i class="fa fa-spinner fa-spin fa-2x" style="color:#4f46e5;"></i>
        <p style="margin-top:12px;color:#6b7280;font-size:14px;">Loading report…</p>
    </div>

    {{-- Report body --}}
    <div id="summary_content" style="display:none;">

        {{-- KPI Row 1 --}}
        <div class="kpi-grid" id="kpi_row">

            {{-- Sales Total — theme gradient (hero card) --}}
            <div class="kpi-card kpi-primary">
                <div class="kpi-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
                </div>
                <div class="kpi-value" id="kpi_sales_total">0.00</div>
                <div class="kpi-label">Sales Total</div>
            </div>

            {{-- Sales Count — blue --}}
            <div class="kpi-card" style="border-top-color:#3b82f6;">
                <div class="kpi-icon" style="background:#dbeafe;color:#1d4ed8;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 5l0 2"/><path d="M15 11l0 2"/><path d="M15 17l0 2"/><path d="M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-3a2 2 0 0 0 0 -4v-3a2 2 0 0 1 2 -2"/></svg>
                </div>
                <div class="kpi-value" style="color:#1d4ed8;" id="kpi_sales_count">0</div>
                <div class="kpi-label">Sales Count</div>
            </div>

            {{-- Returns — red --}}
            <div class="kpi-card" style="border-top-color:#ef4444;">
                <div class="kpi-icon" style="background:#fee2e2;color:#b91c1c;"><i class="fa fa-undo"></i></div>
                <div class="kpi-value" style="color:#b91c1c;" id="kpi_returns_total">0.00</div>
                <div class="kpi-label">Returns</div>
            </div>

            {{-- Expenses — amber --}}
            <div class="kpi-card" style="border-top-color:#f59e0b;">
                <div class="kpi-icon" style="background:#fef3c7;color:#b45309;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/></svg>
                </div>
                <div class="kpi-value" style="color:#b45309;" id="kpi_expenses_total">0.00</div>
                <div class="kpi-label">Expenses</div>
            </div>

            {{-- Purchases — purple --}}
            <div class="kpi-card" style="border-top-color:#8b5cf6;">
                <div class="kpi-icon" style="background:#ede9fe;color:#6d28d9;"><i class="fa fa-truck"></i></div>
                <div class="kpi-value" style="color:#6d28d9;" id="kpi_purchases_total">0.00</div>
                <div class="kpi-label">Purchases</div>
            </div>

            {{-- Net — emerald --}}
            <div class="kpi-card" style="border-top-color:#10b981;">
                <div class="kpi-icon" style="background:#d1fae5;color:#047857;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 19l4 -4l4 4l4 -6l4 2"/></svg>
                </div>
                <div class="kpi-value" style="color:#047857;" id="kpi_net">0.00</div>
                <div class="kpi-label">Net (Sales−Exp)</div>
            </div>

        </div>

        {{-- KPI Row 2: Lost Sales + Follow-ups --}}
        <div class="kpi-grid" id="kpi_row2" style="grid-template-columns:repeat(2,1fr);">
            <div class="kpi-card" style="flex-direction:row;align-items:center;gap:14px;border-top-color:#ef4444;">
                <div class="kpi-icon" style="background:#fee2e2;color:#b91c1c;flex-shrink:0;"><i class="fa fa-times-circle"></i></div>
                <div>
                    <div class="kpi-value" style="color:#b91c1c;" id="kpi_lost_count">0</div>
                    <div class="kpi-label">Lost Sales</div>
                </div>
                <div style="margin-left:auto;text-align:right;">
                    <div class="kpi-value" id="kpi_lost_revenue" style="font-size:15px;color:#b91c1c;">0.00</div>
                    <div class="kpi-label">Pot. Revenue Lost</div>
                </div>
            </div>
            <div class="kpi-card" style="flex-direction:row;align-items:center;gap:14px;border-top-color:#8b5cf6;">
                <div class="kpi-icon" style="background:#ede9fe;color:#6d28d9;flex-shrink:0;"><i class="fa fa-users"></i></div>
                <div>
                    <div class="kpi-value" style="color:#6d28d9;" id="kpi_followup_count">0</div>
                    <div class="kpi-label">Follow-ups Created</div>
                </div>
                <div style="margin-left:auto;text-align:right;">
                    <div class="kpi-value" id="kpi_followup_resolved" style="font-size:15px;color:#047857;">0</div>
                    <div class="kpi-label">Resolved</div>
                </div>
            </div>
        </div>

        {{-- Payment Collections + Top Products --}}
        <div class="row">
            <div class="col-md-4">
                <div class="report-card">
                    <div class="report-card-header"><i class="fa fa-credit-card"></i> Payment Collections</div>
                    <table>
                        <thead><tr><th>Method</th><th style="text-align:right;">Amount</th></tr></thead>
                        <tbody id="payment_methods_body"></tbody>
                        <tfoot><tr><th>Total Collected</th><th style="text-align:right;" id="payment_total">0.00</th></tr></tfoot>
                    </table>
                </div>
            </div>
            <div class="col-md-8">
                <div class="report-card">
                    <div class="report-card-header"><i class="fa fa-star"></i> Top 10 Products Sold Today</div>
                    <table>
                        <thead><tr><th>#</th><th>Product</th><th>SKU</th><th style="text-align:right;">Qty</th><th style="text-align:right;">Revenue</th></tr></thead>
                        <tbody id="top_products_body"></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Lost Sales + Follow-ups tables --}}
        <div class="row">
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-card-header"><i class="fa fa-times-circle" style="color:#dc2626;"></i> Top Lost Sales Today</div>
                    <table>
                        <thead><tr><th>Product</th><th style="text-align:right;">Qty</th><th style="text-align:right;">Pot. Revenue</th><th style="text-align:center;">Times</th></tr></thead>
                        <tbody id="lost_sales_body"></tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card">
                    <div class="report-card-header"><i class="fa fa-users" style="color:#7c3aed;"></i> Follow-ups Created Today</div>
                    <table>
                        <thead><tr><th>Customer</th><th>Product</th><th>Qty</th><th>Status</th><th>Comment</th></tr></thead>
                        <tbody id="followups_body"></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- All Transactions --}}
        <div class="report-card">
            <div class="report-card-header"><i class="fa fa-list"></i> All Transactions for the Day</div>
            <div class="table-responsive">
                <table class="table-transactions">
                    <thead>
                        <tr>
                            <th>Time</th><th>Type</th><th>Ref / Invoice</th>
                            <th>Contact</th><th>Payment</th><th>Status</th>
                            <th style="text-align:right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="transactions_body"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6"><strong>Grand Total (Sales)</strong></td>
                            <td style="text-align:right;" id="grand_sales_total">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>{{-- /summary_content --}}
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {

    /* ── Date preset buttons ───────────────────────────── */
    function getPresetDate(preset) {
        var today = new Date();
        var fmt = function(d) { return d.toISOString().slice(0,10); };
        switch(preset) {
            case 'today':      return fmt(today);
            case 'yesterday':
                var y = new Date(today); y.setDate(y.getDate()-1); return fmt(y);
            case 'this_week':
                var dow = today.getDay(); // 0=Sun
                var mon = new Date(today); mon.setDate(today.getDate() - ((dow+6)%7));
                return fmt(mon);
            case 'last_week':
                var dow2 = today.getDay();
                var lmon = new Date(today); lmon.setDate(today.getDate() - ((dow2+6)%7) - 7);
                return fmt(lmon);
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
        $('#summary_date').val(date);
        loadSummary();
    });

    // Highlight Today preset on load
    $('[data-preset="today"]').trigger('click');

    $('#load_summary').click(function() { loadSummary(); });
    $('#summary_date').on('change', function() {
        $('.date-preset-btn').css({'background':'#fff','color':'#374151','border-color':'#d1d5db'});
    });
    $('#summary_date').on('keypress', function(e) { if (e.which === 13) loadSummary(); });

    function loadSummary() {
        var date = $('#summary_date').val();
        var location_id = $('#summary_location').val();
        if (!date) { toastr.warning('Please select a date.'); return; }
        $('#summary_content').hide();
        $('#summary_loading').show();
        $.ajax({
            url: '{{ route("reports.daily_summary_data") }}',
            data: { date: date, location_id: location_id },
            success: function(data) { $('#summary_loading').hide(); renderSummary(data); $('#summary_content').show(); },
            error: function() { $('#summary_loading').hide(); toastr.error('Failed to load report.'); }
        });
    }

    function fmt(num) {
        return parseFloat(num || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    var methodLabels = {
        cash:          { label:'Cash',         color:'#16a34a', bg:'#dcfce7' },
        card:          { label:'Card',          color:'#2563eb', bg:'#dbeafe' },
        cheque:        { label:'Cheque',        color:'#7c3aed', bg:'#ede9fe' },
        bank_transfer: { label:'Bank Transfer', color:'#0891b2', bg:'#cffafe' },
        custom_pay_1:  { label:'M-Pesa',        color:'#059669', bg:'#a7f3d0' },
        custom_pay_2:  { label:'Custom Pay 2',  color:'#d97706', bg:'#fef3c7' },
        custom_pay_3:  { label:'Custom Pay 3',  color:'#dc2626', bg:'#fee2e2' },
        advance:       { label:'Advance',       color:'#9333ea', bg:'#f3e8ff' },
        credit:        { label:'Credit',        color:'#ea580c', bg:'#ffedd5' },
    };

    function payBadge(methods) {
        if (!methods) return badge('Credit', '#ea580c', '#ffedd5');
        return methods.split(',').map(function(m) {
            m = m.trim();
            var meta = methodLabels[m] || { label: m, color:'#475569', bg:'#f1f5f9' };
            return badge(meta.label, meta.color, meta.bg);
        }).join(' ');
    }

    function badge(label, color, bg) {
        return '<span style="font-size:11px;padding:2px 8px;border-radius:20px;background:' + bg + ';color:' + color + ';font-weight:600;">' + label + '</span>';
    }

    var txBadges = {
        sell:             '<span class="tx-type-badge tx-sale">Sale</span>',
        sell_return:      '<span class="tx-type-badge tx-return">Return</span>',
        purchase:         '<span class="tx-type-badge tx-purchase">Purchase</span>',
        expense:          '<span class="tx-type-badge tx-expense">Expense</span>',
        stock_adjustment: '<span class="tx-type-badge tx-adj">Stock Adj.</span>',
    };

    var statusColors = { pending:'#d97706', contacted:'#2563eb', resolved:'#16a34a' };

    function renderSummary(data) {
        var salesTotal    = parseFloat(data.sales        ? data.sales.total        : 0) || 0;
        var salesCount    = parseInt  (data.sales        ? data.sales.count        : 0) || 0;
        var returnsTotal  = parseFloat(data.sell_returns ? data.sell_returns.total : 0) || 0;
        var expensesTotal = parseFloat(data.expenses     ? data.expenses.total     : 0) || 0;
        var purchasesTotal= parseFloat(data.purchases    ? data.purchases.total    : 0) || 0;
        var net = salesTotal - returnsTotal - expensesTotal;

        $('#print_date').text(data.date);
        $('#kpi_sales_total').text(fmt(salesTotal));
        $('#kpi_sales_count').text(salesCount);
        $('#kpi_returns_total').text(fmt(returnsTotal));
        $('#kpi_expenses_total').text(fmt(expensesTotal));
        $('#kpi_purchases_total').text(fmt(purchasesTotal));
        $('#kpi_net').text(fmt(net)).css('color', net >= 0 ? '#16a34a' : '#dc2626');

        // Payments
        var payHtml = '', payTotal = 0;
        if (data.payments && data.payments.length) {
            data.payments.forEach(function(p) {
                var amt = parseFloat(p.total) || 0; payTotal += amt;
                var meta = methodLabels[p.method] || { label:p.method, color:'#475569', bg:'#f1f5f9' };
                payHtml += '<tr><td>' + badge(meta.label,meta.color,meta.bg) + '</td><td style="text-align:right;font-weight:700;">' + fmt(amt) + '</td></tr>';
            });
        } else { payHtml = '<tr><td colspan="2" style="text-align:center;color:#9ca3af;padding:16px;">No payments</td></tr>'; }
        $('#payment_methods_body').html(payHtml);
        $('#payment_total').text(fmt(payTotal));

        // Top products
        var prodHtml = '';
        if (data.top_products && data.top_products.length) {
            data.top_products.forEach(function(p, i) {
                prodHtml += '<tr><td style="color:#9ca3af;">' + (i+1) + '</td><td>' + p.product_name + '</td><td style="color:#9ca3af;font-size:12px;">' + (p.sku||'-') + '</td><td style="text-align:right;">' + fmt(p.qty) + '</td><td style="text-align:right;font-weight:600;">' + fmt(p.revenue) + '</td></tr>';
            });
        } else { prodHtml = '<tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:16px;">No sales today</td></tr>'; }
        $('#top_products_body').html(prodHtml);

        // Transactions
        var txHtml = '', grandSales = 0;
        if (data.transactions && data.transactions.length) {
            data.transactions.forEach(function(tx) {
                var amt = parseFloat(tx.final_total) || 0;
                var time = tx.transaction_date ? tx.transaction_date.substring(11,16) : '-';
                if (tx.type === 'sell') grandSales += amt;
                var rowClass = tx.type === 'sell_return' ? 'type-return' : (tx.type === 'expense' ? 'type-expense' : '');
                var payCell = (tx.type==='sell'||tx.type==='sell_return') ? payBadge(tx.payment_methods) : '<span style="color:#e5e7eb;">—</span>';
                txHtml += '<tr class="' + rowClass + '"><td style="color:#9ca3af;">' + time + '</td><td>' + (txBadges[tx.type]||tx.type) + '</td><td style="font-size:12px;color:#6b7280;">' + (tx.invoice_no||tx.ref_no||tx.id) + '</td><td>' + (tx.contact_name||'-') + '</td><td>' + payCell + '</td><td style="text-transform:capitalize;">' + (tx.status||'-') + '</td><td style="text-align:right;font-weight:600;">' + fmt(amt) + '</td></tr>';
            });
        } else { txHtml = '<tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:24px;">No transactions for this day</td></tr>'; }
        $('#transactions_body').html(txHtml);
        $('#grand_sales_total').text(fmt(grandSales));

        // Lost sales
        var ls = data.lost_sales || {};
        $('#kpi_lost_count').text(parseInt(ls.count)||0);
        $('#kpi_lost_revenue').text(fmt(ls.potential_revenue));
        var lsHtml = '';
        if (data.lost_sales_top && data.lost_sales_top.length) {
            data.lost_sales_top.forEach(function(r) {
                lsHtml += '<tr><td>' + (r.product_name||'-') + ' <small style="color:#9ca3af;">' + (r.sku||'') + '</small></td><td style="text-align:right;">' + fmt(r.total_qty) + '</td><td style="text-align:right;color:#dc2626;">' + fmt(r.potential_revenue) + '</td><td style="text-align:center;">' + badge((r.times_requested||1)+'x','#dc2626','#fee2e2') + '</td></tr>';
            });
        } else { lsHtml = '<tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:16px;">No lost sales today</td></tr>'; }
        $('#lost_sales_body').html(lsHtml);

        // Follow-ups
        var fu = data.followups || {};
        $('#kpi_followup_count').text(parseInt(fu.count)||0);
        $('#kpi_followup_resolved').text(parseInt(fu.resolved)||0);
        var fuHtml = '';
        if (data.followups_list && data.followups_list.length) {
            data.followups_list.forEach(function(r) {
                var sc = statusColors[r.status] || '#6b7280';
                var sb = badge(r.status||'-', sc, sc+'1a');
                fuHtml += '<tr><td>' + (r.customer_name||r.customer_phone||'-') + '</td><td>' + (r.product_name||'-') + '</td><td>' + fmt(r.quantity||0) + '</td><td>' + sb + '</td><td><small style="color:#6b7280;">' + (r.comment||'-') + '</small></td></tr>';
            });
        } else { fuHtml = '<tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:16px;">No follow-ups today</td></tr>'; }
        $('#followups_body').html(fuHtml);
    }
});
</script>
@endsection
