@extends('layouts.app')
@section('title', 'Close the day')

@section('css')
@include('layouts.partials.page_modern_css')
<style>
.dc-kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:16px}
@media(min-width:800px){.dc-kpi{grid-template-columns:repeat(4,1fr)}}
.dc-card{border-radius:12px;padding:14px 16px;color:#fff;box-shadow:0 2px 8px rgba(0,0,0,.12)}
.dc-card .v{font-size:22px;font-weight:800;line-height:1.1}
.dc-card .l{font-size:11px;font-weight:700;opacity:.9;text-transform:uppercase;letter-spacing:.04em;margin-top:4px}
.dc-sales{background:linear-gradient(135deg,#059669,#047857)}
.dc-count{background:linear-gradient(135deg,#2563eb,#1d4ed8)}
.dc-pay{background:linear-gradient(135deg,#0d9488,#0f766e)}
.dc-net{background:linear-gradient(135deg,#7c3aed,#6d28d9)}
.dc-section{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:14px;overflow:hidden}
.dc-section h3{margin:0;padding:12px 16px;font-size:14px;font-weight:800;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.dc-section .body{padding:12px 16px}
.dc-table{width:100%;font-size:13px;border-collapse:collapse}
.dc-table th,.dc-table td{padding:8px;border-bottom:1px solid #f1f5f9;text-align:left}
.dc-table th{font-size:11px;text-transform:uppercase;color:#64748b;font-weight:700}
.dc-help{font-size:13px;color:#64748b;margin:-4px 0 14px}
.dc-alert{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:10px;padding:12px 14px;margin-bottom:14px}
@media print{.no-print{display:none!important}.dc-card{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style>
@endsection

@section('content')
@php
    $data = $data ?? [];
    $salesTotal = (float) data_get($data, 'sales.total', 0);
    $salesCount = (int) data_get($data, 'sales.count', 0);
    $payTotal = (float) data_get($data, 'payment_total', 0);
    $netApprox = (float) data_get($data, 'net_approx', 0);
    $retCount = (int) data_get($data, 'returns.count', 0);
    $retTotal = (float) data_get($data, 'returns.total', 0);
    $expCount = (int) data_get($data, 'expenses.count', 0);
    $expTotal = (float) data_get($data, 'expenses.total', 0);
    $lostCount = (int) data_get($data, 'lost_sales_count', 0);
    $lostValue = (float) data_get($data, 'lost_sales_value', 0);
    $payments = data_get($data, 'payments', []);
    $cashiers = data_get($data, 'cashiers', []);
    $topProducts = data_get($data, 'top_products', []);
    $registers = data_get($data, 'registers', []);
@endphp
<div class="page-modern">
    <section class="content-header"></section>
    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <h1>Close the day</h1>
                    <p class="pg-subtitle">One page for end-of-shift: sales, payments, staff & top products &middot; {{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions no-print">
                <a href="{{ route('reports.hub') }}" class="pg-add-btn" style="background:rgba(255,255,255,.2)!important;color:#fff!important"><i class="fa fa-th"></i> All reports</a>
            </div>
        </div>
    </div>

    <section class="content">
        @if(!empty($error))
            <div class="dc-alert no-print">
                <strong>Some day-close data failed to load.</strong>
                <div style="margin-top:4px;font-size:12px;word-break:break-word">{{ $error }}</div>
                <div style="margin-top:6px;font-size:12px">Check <code>storage/logs</code> for “Day close”. Partial figures below may be zero.</div>
            </div>
        @endif

        <p class="dc-help"><i class="fa fa-info-circle"></i> Choose the date and location, then print or export for the owner.</p>

        <form method="get" action="{{ route('reports.day_close') }}" class="no-print" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px;align-items:flex-end">
            <div>
                <label style="font-size:12px;font-weight:700;display:block">Date</label>
                <input type="date" name="date" value="{{ $date }}" class="form-control" style="min-width:160px">
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;display:block">Location</label>
                <select name="location_id" class="form-control select2" style="min-width:200px">
                    <option value="">All locations</option>
                    @foreach(($business_locations ?? []) as $id => $name)
                        @if((string)$id !== '')
                            <option value="{{ $id }}" @if((string)$location_id === (string)$id) selected @endif>{{ $name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Load</button>
            <button type="button" class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
        </form>

        @includeIf('report.partials.export_toolbar', ['table' => '#day_close_export_table', 'title' => 'Day close '.$date])

        <div class="dc-kpi">
            <div class="dc-card dc-sales">
                <div class="v">@format_currency($salesTotal)</div>
                <div class="l">Sales total</div>
            </div>
            <div class="dc-card dc-count">
                <div class="v">{{ $salesCount }}</div>
                <div class="l">Invoices</div>
            </div>
            <div class="dc-card dc-pay">
                <div class="v">@format_currency($payTotal)</div>
                <div class="l">Payments recorded</div>
            </div>
            <div class="dc-card dc-net">
                <div class="v">@format_currency($netApprox)</div>
                <div class="l">Approx net (sales − returns − expenses)</div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="dc-section">
                    <h3><i class="fa fa-money"></i> Payments by method</h3>
                    <div class="body">
                        <table class="dc-table">
                            <thead><tr><th>Method</th><th>Amount</th></tr></thead>
                            <tbody>
                            @forelse($payments as $p)
                                @php $pTotal = (float) data_get($p, 'total', 0); @endphp
                                <tr>
                                    <td>{{ ucfirst(str_replace('_',' ', data_get($p, 'method', 'other'))) }}</td>
                                    <td>@format_currency($pTotal)</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted">No payments this day</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dc-section">
                    <h3><i class="fa fa-user"></i> By cashier</h3>
                    <div class="body">
                        <table class="dc-table">
                            <thead><tr><th>Cashier</th><th>Invoices</th><th>Total</th></tr></thead>
                            <tbody>
                            @forelse($cashiers as $c)
                                @php $cTotal = (float) data_get($c, 'total', 0); @endphp
                                <tr>
                                    <td>{{ data_get($c, 'cashier') ?: '—' }}</td>
                                    <td>{{ data_get($c, 'invoices', 0) }}</td>
                                    <td>@format_currency($cTotal)</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">No sales</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="dc-section">
                    <h3>Returns</h3>
                    <div class="body">
                        <p><strong>{{ $retCount }}</strong> returns · @format_currency($retTotal)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dc-section">
                    <h3>Expenses</h3>
                    <div class="body">
                        <p><strong>{{ $expCount }}</strong> · @format_currency($expTotal)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dc-section">
                    <h3>Lost sales</h3>
                    <div class="body">
                        <p><strong>{{ $lostCount }}</strong> logged
                        @if($lostValue > 0)
                            · ~@format_currency($lostValue)
                        @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="dc-section">
            <h3><i class="fa fa-star"></i> Top products sold</h3>
            <div class="body table-responsive">
                <table class="dc-table" id="day_close_export_table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Qty</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($topProducts as $p)
                        @php
                            $qty = (float) data_get($p, 'qty', 0);
                            $rev = (float) data_get($p, 'revenue', 0);
                        @endphp
                        <tr>
                            <td>{{ data_get($p, 'product_name') }}</td>
                            <td>{{ data_get($p, 'sku') }}</td>
                            <td>{{ @num_format($qty) }}</td>
                            <td>@format_currency($rev)</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">No product sales</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(!empty($registers) && count($registers))
        <div class="dc-section">
            <h3><i class="fa fa-cash-register"></i> Cash registers</h3>
            <div class="body table-responsive">
                <table class="dc-table">
                    <thead><tr><th>User</th><th>Status</th><th>Opened</th><th>Closed</th></tr></thead>
                    <tbody>
                    @foreach($registers as $r)
                        <tr>
                            <td>{{ data_get($r, 'user_name') ?: '—' }}</td>
                            <td>{{ data_get($r, 'status') ?: '—' }}</td>
                            <td>{{ data_get($r, 'created_at') ?: '—' }}</td>
                            <td>{{ data_get($r, 'closed_at') ?: '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </section>
</div>
@endsection
