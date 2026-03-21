@extends('layouts.app')
@section('title', __('Daily Cash Reconciliation'))

@section('content')
<section class="content-header">
    <h1>Daily Cash Reconciliation
        <small>Sales, Payments &amp; Profitability Summary</small>
    </h1>
</section>

<section class="content">
    {{-- Filters --}}
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="text" id="reconciliation_date" class="form-control"
                               value="{{ now()->toDateString() }}" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Location:</label>
                        {!! Form::select('location_id', $business_locations, null,
                            ['class' => 'form-control select2', 'id' => 'reconciliation_location',
                             'placeholder' => 'All Locations']) !!}
                    </div>
                </div>
                <div class="col-md-2" style="padding-top:25px;">
                    <button id="load_reconciliation" class="btn btn-primary btn-block">
                        <i class="fa fa-search"></i> Load Report
                    </button>
                </div>
                <div class="col-md-2" style="padding-top:25px;">
                    <button id="print_reconciliation" class="btn btn-default btn-block">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Output --}}
    <div id="reconciliation_report" style="display:none;">

        {{-- KPI Cards Row --}}
        <div class="row" id="recon_kpi_row">
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h4 id="recon_kpi_gross">0.00</h4>
                        <p>Gross Sales</p>
                    </div>
                    <div class="icon"><i class="fa fa-shopping-cart"></i></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h4 id="recon_kpi_net">0.00</h4>
                        <p>Net Sales</p>
                    </div>
                    <div class="icon"><i class="fa fa-line-chart"></i></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-navy">
                    <div class="inner">
                        <h4 id="recon_kpi_profit">0.00</h4>
                        <p>Gross Profit</p>
                    </div>
                    <div class="icon"><i class="fa fa-money"></i></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-purple" style="background-color:#605ca8 !important;">
                    <div class="inner">
                        <h4 id="recon_kpi_margin">0%</h4>
                        <p>Profit Margin</p>
                    </div>
                    <div class="icon"><i class="fa fa-percent"></i></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h4 id="recon_kpi_transactions">0</h4>
                        <p>Transactions</p>
                    </div>
                    <div class="icon"><i class="fa fa-ticket"></i></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h4 id="recon_kpi_expenses">0.00</h4>
                        <p>Expenses</p>
                    </div>
                    <div class="icon"><i class="fa fa-minus-circle"></i></div>
                </div>
            </div>
        </div>

        <div id="print_area">
        <div class="text-center" style="margin-bottom:15px;">
            <h3 id="report_title">Daily Cash Reconciliation</h3>
            <p id="report_date_label" class="text-muted"></p>
        </div>

        <div class="row">
            {{-- Sales Summary --}}
            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-shopping-cart"></i> Sales Summary</h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped table-condensed">
                            <tr>
                                <td><strong>Gross Sales</strong></td>
                                <td class="text-right text-success"><strong id="r_gross_sales">—</strong></td>
                            </tr>
                            <tr>
                                <td>Less: Discounts</td>
                                <td class="text-right text-warning" id="r_discount">—</td>
                            </tr>
                            <tr class="active">
                                <td><strong>Net Sales</strong></td>
                                <td class="text-right"><strong id="r_net_sales">—</strong></td>
                            </tr>
                            <tr>
                                <td>VAT / Tax Collected</td>
                                <td class="text-right" id="r_tax">—</td>
                            </tr>
                            <tr>
                                <td>Sell Returns / Refunds</td>
                                <td class="text-right text-danger" id="r_returns">—</td>
                            </tr>
                            <tr>
                                <td>Total Expenses</td>
                                <td class="text-right text-danger" id="r_expenses">—</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Profitability --}}
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-line-chart"></i> Profitability</h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped table-condensed">
                            <tr>
                                <td>Net Sales</td>
                                <td class="text-right" id="r_net_sales_2">—</td>
                            </tr>
                            <tr>
                                <td>Less: Cost of Goods Sold (COGS)</td>
                                <td class="text-right text-danger" id="r_cogs">—</td>
                            </tr>
                            <tr class="active">
                                <td><strong>Gross Profit</strong></td>
                                <td class="text-right"><strong id="r_gross_profit">—</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Profit Margin</strong></td>
                                <td class="text-right"><strong id="r_margin">—</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Payment Breakdown --}}
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-credit-card"></i> Payment Method Breakdown</h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th class="text-right">Transactions</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="r_payments_body">
                                <tr><td colspan="3" class="text-center text-muted">Loading…</td></tr>
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th>Total Collected</th>
                                    <th class="text-right" id="r_pay_total_count">—</th>
                                    <th class="text-right" id="r_pay_total_amount">—</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Transaction Volume --}}
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Transaction Volume</h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped table-condensed">
                            <tr>
                                <td><strong>Total Transactions</strong></td>
                                <td class="text-right"><strong id="r_tx_count">—</strong></td>
                            </tr>
                            <tr>
                                <td>Average Basket Value</td>
                                <td class="text-right" id="r_avg_basket">—</td>
                            </tr>
                            <tr>
                                <td>Gross Sales</td>
                                <td class="text-right" id="r_gross_sales_2">—</td>
                            </tr>
                            <tr>
                                <td>Net Sales</td>
                                <td class="text-right" id="r_net_sales_3">—</td>
                            </tr>
                            <tr>
                                <td>Gross Profit</td>
                                <td class="text-right" id="r_gross_profit_2">—</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>{{-- end print_area --}}

        {{-- Register Report Section --}}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-cash-register"></i> Register Sessions for the Day</h3>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condensed" id="recon_register_table">
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
                        <tbody id="recon_register_body">
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray font-17 text-center">
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
        </div>

    </div>{{-- end reconciliation_report --}}

    <div id="reconciliation_loading" style="display:none;" class="text-center tw-py-10">
        <i class="fa fa-spinner fa-spin fa-2x"></i>
        <p class="text-muted">Loading report…</p>
    </div>
</section>
<div class="modal fade view_register" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {

    // Date picker
    $('#reconciliation_date').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });

    function fmt(num) {
        if (num === undefined || num === null) return '—';
        return parseFloat(num).toLocaleString('en-KE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

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

                // KPI Cards
                $('#recon_kpi_gross').text(fmt(d.gross_sales));
                $('#recon_kpi_net').text(fmt(d.net_sales));
                $('#recon_kpi_profit').text(fmt(d.gross_profit));
                $('#recon_kpi_margin').text(d.profit_margin + '%');
                $('#recon_kpi_transactions').text(d.total_transactions);
                $('#recon_kpi_expenses').text(fmt(d.total_expenses));

                // Sales
                $('#r_gross_sales').text(fmt(d.gross_sales));
                $('#r_gross_sales_2').text(fmt(d.gross_sales));
                $('#r_discount').text('— ' + fmt(d.total_discount));
                $('#r_net_sales').text(fmt(d.net_sales));
                $('#r_net_sales_2').text(fmt(d.net_sales));
                $('#r_net_sales_3').text(fmt(d.net_sales));
                $('#r_tax').text(fmt(d.total_tax));
                $('#r_returns').text('— ' + fmt(d.total_returns));
                $('#r_expenses').text('— ' + fmt(d.total_expenses));

                // Profitability
                $('#r_cogs').text('— ' + fmt(d.cogs));
                $('#r_gross_profit').text(fmt(d.gross_profit)).removeClass('text-danger text-success')
                    .addClass(d.gross_profit >= 0 ? 'text-success' : 'text-danger');
                $('#r_gross_profit_2').text(fmt(d.gross_profit));
                $('#r_margin').text(d.profit_margin + '%');

                // Volume
                $('#r_tx_count').text(d.total_transactions);
                $('#r_avg_basket').text(fmt(d.avg_basket));

                // Payment breakdown
                var labels   = d.payment_labels;
                var breakdown = d.payment_breakdown;
                var rows     = '';
                var totalAmt = 0;
                var totalCnt = 0;
                $.each(labels, function(method, label) {
                    if (breakdown[method]) {
                        var row = breakdown[method];
                        rows += '<tr><td>' + label + '</td><td class="text-right">' +
                            row.count + '</td><td class="text-right">' + fmt(row.total) + '</td></tr>';
                        totalAmt += parseFloat(row.total);
                        totalCnt += parseInt(row.count);
                    }
                });
                if (!rows) rows = '<tr><td colspan="3" class="text-center text-muted">No payments recorded</td></tr>';
                $('#r_payments_body').html(rows);
                $('#r_pay_total_count').text(totalCnt);
                $('#r_pay_total_amount').text(fmt(totalAmt));

                $('#reconciliation_report').show();

                // Load register report for this date
                loadRegisterReport(date, location);
            },
            error: function() {
                $('#reconciliation_loading').hide();
                toastr.error('Server error loading report.');
            }
        });
    }

    // Register report for reconciliation date
    var recon_register_table = null;

    function loadRegisterReport(date, location_id) {
        if (recon_register_table) {
            recon_register_table.destroy();
            $('#recon_register_body').empty();
        }

        recon_register_table = $('#recon_register_table').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            info: false,
            scrollX: true,
            ajax: {
                url: '/reports/register-report',
                data: { start_date: date, end_date: date, location_id: location_id || '' }
            },
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'closed_at', name: 'closed_at' },
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
                    var cls = d === 'open' ? 'bg-green' : 'bg-red';
                    return '<span class="label ' + cls + '">' + (d || '-') + '</span>';
                  }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            footerCallback: function(row, data) {
                var totals = { c1: 0, cash: 0, card: 0, cheque: 0, bank: 0, c2: 0, c3: 0, other: 0, adv: 0, total: 0 };
                for (var r in data) {
                    totals.c1    += parseFloat($(data[r].total_custom_pay_1).data('orig-value') || 0);
                    totals.cash  += parseFloat($(data[r].total_cash_payment).data('orig-value') || 0);
                    totals.card  += parseFloat($(data[r].total_card_payment).data('orig-value') || 0);
                    totals.cheque += parseFloat($(data[r].total_cheque_payment).data('orig-value') || 0);
                    totals.bank  += parseFloat($(data[r].total_bank_transfer_payment).data('orig-value') || 0);
                    totals.c2    += parseFloat($(data[r].total_custom_pay_2).data('orig-value') || 0);
                    totals.c3    += parseFloat($(data[r].total_custom_pay_3).data('orig-value') || 0);
                    totals.other += parseFloat($(data[r].total_other_payment).data('orig-value') || 0);
                    totals.adv   += parseFloat($(data[r].total_advance_payment).data('orig-value') || 0);
                    totals.total += parseFloat($(data[r].total).data('orig-value') || 0);
                }
                function f(v) { return parseFloat(v).toLocaleString('en-KE', {minimumFractionDigits:2, maximumFractionDigits:2}); }
                $('.recon_reg_custom1').text(f(totals.c1));
                $('.recon_reg_cash').text(f(totals.cash));
                $('.recon_reg_card').text(f(totals.card));
                $('.recon_reg_cheque').text(f(totals.cheque));
                $('.recon_reg_bank').text(f(totals.bank));
                $('.recon_reg_custom2').text(f(totals.c2));
                $('.recon_reg_custom3').text(f(totals.c3));
                $('.recon_reg_other').text(f(totals.other));
                $('.recon_reg_advance').text(f(totals.adv));
                $('.recon_reg_total').html('<strong>' + f(totals.total) + '</strong>');
            },
            drawCallback: function() {
                __currency_convert_recursively($('#recon_register_table'));
            }
        });
    }

    $('#load_reconciliation').on('click', loadReport);

    // Print
    $('#print_reconciliation').on('click', function() {
        var w = window.open('', '_blank');
        w.document.write('<html><head><title>Daily Reconciliation</title>');
        w.document.write('<link rel="stylesheet" href="/css/app.css">');
        w.document.write('</head><body style="padding:20px;">');
        w.document.write(document.getElementById('print_area').innerHTML);
        w.document.write('</body></html>');
        w.document.close();
        w.focus();
        setTimeout(function(){ w.print(); w.close(); }, 600);
    });

    // Auto-load today on page open
    loadReport();
});
</script>
<style>
    .small-box h4 { font-size: 18px; font-weight: bold; }
    .small-box > .inner { padding: 10px 15px; }
    #recon_register_table td, #recon_register_table th { white-space: nowrap; font-size: 12px; }
</style>
@endsection
