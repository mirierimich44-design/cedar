@extends('layouts.app')
@section('title', __('Daily Summary Report'))

@section('content')
<section class="content-header">
    <h1>Daily Summary Report
        <small>All activity for a selected day</small>
    </h1>
</section>

<section class="content">
    {{-- Filters --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Select Date</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" class="form-control" id="summary_date"
                               value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Location:</label>
                        {!! Form::select('location_id', $business_locations, null, [
                            'class' => 'form-control select2',
                            'id' => 'summary_location',
                            'placeholder' => 'All Locations'
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-primary btn-block" id="load_summary">
                            <i class="fa fa-search"></i> Load Report
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-success btn-block no-print" onclick="window.print()">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Content --}}
    <div id="summary_content" style="display:none;">

        <div style="text-align:center; margin-bottom: 15px; display:none;" class="print_section">
            <h2>{{ session()->get('business.name') }}</h2>
            <h3>Daily Summary Report &mdash; <span id="print_date"></span></h3>
        </div>

        {{-- KPI Cards Row --}}
        <div class="row" id="kpi_row">
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h4 id="kpi_sales_total">0.00</h4>
                        <p>Sales Total</p>
                    </div>
                    <div class="icon"><i class="fa fa-shopping-cart"></i></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h4 id="kpi_sales_count">0</h4>
                        <p>Sales Count</p>
                    </div>
                    <div class="icon"><i class="fa fa-ticket"></i></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h4 id="kpi_returns_total">0.00</h4>
                        <p>Returns</p>
                    </div>
                    <div class="icon"><i class="fa fa-undo"></i></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h4 id="kpi_expenses_total">0.00</h4>
                        <p>Expenses</p>
                    </div>
                    <div class="icon"><i class="fa fa-money"></i></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-purple" style="background-color: #605ca8 !important;">
                    <div class="inner">
                        <h4 id="kpi_purchases_total">0.00</h4>
                        <p>Purchases</p>
                    </div>
                    <div class="icon"><i class="fa fa-truck"></i></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-navy">
                    <div class="inner">
                        <h4 id="kpi_net">0.00</h4>
                        <p>Net (Sales &minus; Exp)</p>
                    </div>
                    <div class="icon"><i class="fa fa-line-chart"></i></div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Payment Methods --}}
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-credit-card"></i> Payment Collections</h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-condensed">
                            <thead>
                                <tr><th>Method</th><th class="text-right">Amount</th></tr>
                            </thead>
                            <tbody id="payment_methods_body">
                            </tbody>
                            <tfoot>
                                <tr class="info">
                                    <th>Total Collected</th>
                                    <th class="text-right" id="payment_total">0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Top Products --}}
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-star"></i> Top 10 Products Sold Today</h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th class="text-right">Qty Sold</th>
                                    <th class="text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody id="top_products_body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lost Sales + Follow-ups KPI row --}}
        <div class="row" id="kpi_row2">
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="small-box" style="background:#e74c3c;color:#fff;">
                    <div class="inner">
                        <h4 id="kpi_lost_count">0</h4>
                        <p>Lost Sales (Requests)</p>
                    </div>
                    <div class="icon"><i class="fa fa-times-circle"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="small-box" style="background:#c0392b;color:#fff;">
                    <div class="inner">
                        <h4 id="kpi_lost_revenue">0.00</h4>
                        <p>Potential Revenue Lost</p>
                    </div>
                    <div class="icon"><i class="fa fa-exclamation-triangle"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="small-box" style="background:#8e44ad;color:#fff;">
                    <div class="inner">
                        <h4 id="kpi_followup_count">0</h4>
                        <p>Follow-ups Created</p>
                    </div>
                    <div class="icon"><i class="fa fa-user-clock"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-6">
                <div class="small-box" style="background:#27ae60;color:#fff;">
                    <div class="inner">
                        <h4 id="kpi_followup_resolved">0</h4>
                        <p>Follow-ups Resolved</p>
                    </div>
                    <div class="icon"><i class="fa fa-check-circle"></i></div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Lost Sales Today --}}
            <div class="col-md-6">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-times-circle"></i> Top Lost Sales Today</h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-right">Qty Req.</th>
                                    <th class="text-right">Pot. Revenue</th>
                                    <th class="text-center">Times</th>
                                </tr>
                            </thead>
                            <tbody id="lost_sales_body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Follow-ups Today --}}
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-user-clock"></i> Follow-ups Created Today</h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody id="followups_body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- All Transactions --}}
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-list"></i> All Transactions for the Day</h3>
            </div>
            <div class="box-body no-padding">
                <table class="table table-bordered table-condensed table-striped" id="transactions_table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Ref / Invoice</th>
                            <th>Contact</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="transactions_body">
                    </tbody>
                    <tfoot>
                        <tr class="info">
                            <th colspan="6">Grand Total (Sales)</th>
                            <th class="text-right" id="grand_sales_total">0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div id="summary_loading" style="display:none; text-align:center; padding: 40px;">
        <i class="fa fa-spinner fa-spin fa-3x"></i>
        <p style="margin-top:10px;">Loading report...</p>
    </div>
</section>

<style>
    @media print {
        .no-print, .box-tools, .content-header, .main-sidebar, .main-header { display: none !important; }
        .print_section { display: block !important; }
        .content-wrapper { margin-left: 0 !important; }
        .small-box h4 { font-size: 14px; }
    }
    .small-box h4 { font-size: 18px; font-weight: bold; }
    .small-box > .inner { padding: 10px 15px; }
</style>
@endsection

@section('javascript')
<script>
$(document).ready(function() {

    // Auto-load on page open
    loadSummary();

    $('#load_summary').click(function() {
        loadSummary();
    });

    // Also load on Enter key in date field
    $('#summary_date').on('keypress', function(e) {
        if (e.which === 13) loadSummary();
    });

    function loadSummary() {
        var date = $('#summary_date').val();
        var location_id = $('#summary_location').val();

        if (!date) {
            toastr.warning('Please select a date.');
            return;
        }

        $('#summary_content').hide();
        $('#summary_loading').show();

        $.ajax({
            url: '{{ route("reports.daily_summary_data") }}',
            data: { date: date, location_id: location_id },
            success: function(data) {
                $('#summary_loading').hide();
                renderSummary(data);
                $('#summary_content').show();
            },
            error: function() {
                $('#summary_loading').hide();
                toastr.error('Failed to load report.');
            }
        });
    }

    function fmt(num) {
        return parseFloat(num || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    var typeLabels = {
        'sell': '<span class="label bg-green">Sale</span>',
        'sell_return': '<span class="label bg-red">Return</span>',
        'purchase': '<span class="label bg-navy">Purchase</span>',
        'expense': '<span class="label bg-yellow">Expense</span>',
        'stock_adjustment': '<span class="label bg-gray">Stock Adj.</span>'
    };

    var methodLabels = {
        'cash':          { label: 'Cash',          color: '#16a34a', bg: '#dcfce7' },
        'card':          { label: 'Card',           color: '#2563eb', bg: '#dbeafe' },
        'cheque':        { label: 'Cheque',         color: '#7c3aed', bg: '#ede9fe' },
        'bank_transfer': { label: 'Bank Transfer',  color: '#0891b2', bg: '#cffafe' },
        'custom_pay_1':  { label: 'M-Pesa',         color: '#059669', bg: '#a7f3d0' },
        'custom_pay_2':  { label: 'Custom Pay 2',   color: '#d97706', bg: '#fef3c7' },
        'custom_pay_3':  { label: 'Custom Pay 3',   color: '#dc2626', bg: '#fee2e2' },
        'advance':       { label: 'Advance',        color: '#9333ea', bg: '#f3e8ff' },
        'credit':        { label: 'Credit',         color: '#ea580c', bg: '#ffedd5' },
    };

    function renderPaymentBadges(methods) {
        if (!methods) {
            return '<span style="font-size:11px;padding:2px 7px;border-radius:10px;background:#ffedd5;color:#ea580c;font-weight:600;">Credit</span>';
        }
        return methods.split(',').map(function(m) {
            m = m.trim();
            var meta = methodLabels[m] || { label: m, color: '#475569', bg: '#f1f5f9' };
            return '<span style="font-size:11px;padding:2px 7px;border-radius:10px;background:' + meta.bg + ';color:' + meta.color + ';font-weight:600;margin-right:2px;">' + meta.label + '</span>';
        }).join('');
    }

    function renderSummary(data) {
        var salesTotal  = parseFloat(data.sales ? data.sales.total : 0) || 0;
        var salesCount  = parseInt(data.sales ? data.sales.count : 0) || 0;
        var salesTax    = parseFloat(data.sales ? data.sales.tax : 0) || 0;
        var returnsTotal = parseFloat(data.sell_returns ? data.sell_returns.total : 0) || 0;
        var expensesTotal = parseFloat(data.expenses ? data.expenses.total : 0) || 0;
        var purchasesTotal = parseFloat(data.purchases ? data.purchases.total : 0) || 0;
        var net = salesTotal - returnsTotal - expensesTotal;

        // KPI cards
        $('#print_date').text(data.date);
        $('#kpi_sales_total').text(fmt(salesTotal));
        $('#kpi_sales_count').text(salesCount);
        $('#kpi_returns_total').text(fmt(returnsTotal));
        $('#kpi_expenses_total').text(fmt(expensesTotal));
        $('#kpi_purchases_total').text(fmt(purchasesTotal));
        $('#kpi_net').text(fmt(net));

        // Payment methods
        var payHtml = '';
        var payTotal = 0;
        if (data.payments && data.payments.length > 0) {
            data.payments.forEach(function(p) {
                var amt = parseFloat(p.total) || 0;
                payTotal += amt;
                var meta = methodLabels[p.method] || { label: p.method, color: '#475569', bg: '#f1f5f9' };
                var badge = '<span style="font-size:11px;padding:2px 8px;border-radius:10px;background:' + meta.bg + ';color:' + meta.color + ';font-weight:600;">' + meta.label + '</span>';
                payHtml += '<tr><td>' + badge + '</td><td class="text-right"><strong>' + fmt(amt) + '</strong></td></tr>';
            });
        } else {
            payHtml = '<tr><td colspan="2" class="text-muted text-center">No payments</td></tr>';
        }
        $('#payment_methods_body').html(payHtml);
        $('#payment_total').text(fmt(payTotal));

        // Top products
        var prodHtml = '';
        if (data.top_products && data.top_products.length > 0) {
            data.top_products.forEach(function(p, i) {
                prodHtml += '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + p.product_name + '</td>' +
                    '<td><small>' + (p.sku || '-') + '</small></td>' +
                    '<td class="text-right">' + fmt(p.qty) + '</td>' +
                    '<td class="text-right">' + fmt(p.revenue) + '</td>' +
                    '</tr>';
            });
        } else {
            prodHtml = '<tr><td colspan="5" class="text-muted text-center">No sales today</td></tr>';
        }
        $('#top_products_body').html(prodHtml);

        // Transactions list
        var txHtml = '';
        var grandSales = 0;
        if (data.transactions && data.transactions.length > 0) {
            data.transactions.forEach(function(tx) {
                var ref = tx.invoice_no || tx.ref_no || tx.id;
                var typeLabel = typeLabels[tx.type] || tx.type;
                var amount = parseFloat(tx.final_total) || 0;
                var time = tx.transaction_date ? tx.transaction_date.substring(11, 16) : '-';
                if (tx.type === 'sell') grandSales += amount;

                var amtClass = tx.type === 'sell_return' || tx.type === 'expense' ? 'text-danger' : '';
                // Show payment only for sales and sell returns
                var paymentCell = (tx.type === 'sell' || tx.type === 'sell_return')
                    ? renderPaymentBadges(tx.payment_methods)
                    : '<span style="color:#cbd5e1;font-size:11px;">—</span>';

                txHtml += '<tr>' +
                    '<td>' + time + '</td>' +
                    '<td>' + typeLabel + '</td>' +
                    '<td>' + ref + '</td>' +
                    '<td>' + (tx.contact_name || '-') + '</td>' +
                    '<td>' + paymentCell + '</td>' +
                    '<td>' + (tx.status ? tx.status.charAt(0).toUpperCase() + tx.status.slice(1) : '-') + '</td>' +
                    '<td class="text-right ' + amtClass + '">' + fmt(amount) + '</td>' +
                    '</tr>';
            });
        } else {
            txHtml = '<tr><td colspan="7" class="text-muted text-center">No transactions for this day</td></tr>';
        }
        $('#transactions_body').html(txHtml);
        $('#grand_sales_total').text(fmt(grandSales));

        // Lost Sales KPIs
        var ls = data.lost_sales || {};
        $('#kpi_lost_count').text(parseInt(ls.count) || 0);
        $('#kpi_lost_revenue').text(fmt(ls.potential_revenue));

        // Lost Sales table
        var lsHtml = '';
        if (data.lost_sales_top && data.lost_sales_top.length > 0) {
            data.lost_sales_top.forEach(function(r) {
                lsHtml += '<tr>' +
                    '<td>' + (r.product_name || '-') + ' <small class="text-muted">' + (r.sku || '') + '</small></td>' +
                    '<td class="text-right">' + fmt(r.total_qty) + '</td>' +
                    '<td class="text-right text-danger">' + fmt(r.potential_revenue) + '</td>' +
                    '<td class="text-center"><span class="badge bg-red">' + (r.times_requested || 1) + 'x</span></td>' +
                    '</tr>';
            });
        } else {
            lsHtml = '<tr><td colspan="4" class="text-muted text-center">No lost sales today</td></tr>';
        }
        $('#lost_sales_body').html(lsHtml);

        // Follow-ups KPIs
        var fu = data.followups || {};
        $('#kpi_followup_count').text(parseInt(fu.count) || 0);
        $('#kpi_followup_resolved').text(parseInt(fu.resolved) || 0);

        // Follow-ups table
        var fuHtml = '';
        var statusColors = { pending: 'bg-yellow', contacted: 'bg-blue', resolved: 'bg-green' };
        if (data.followups_list && data.followups_list.length > 0) {
            data.followups_list.forEach(function(r) {
                var statusBadge = '<span class="badge ' + (statusColors[r.status] || 'bg-gray') + '">' + (r.status || '-') + '</span>';
                fuHtml += '<tr>' +
                    '<td>' + (r.customer_name || r.customer_phone || '-') + '</td>' +
                    '<td>' + (r.product_name || '-') + '</td>' +
                    '<td>' + fmt(r.quantity || 0) + '</td>' +
                    '<td>' + statusBadge + '</td>' +
                    '<td><small>' + (r.comment || '-') + '</small></td>' +
                    '</tr>';
            });
        } else {
            fuHtml = '<tr><td colspan="5" class="text-muted text-center">No follow-ups today</td></tr>';
        }
        $('#followups_body').html(fuHtml);
    }
});
</script>
@endsection
