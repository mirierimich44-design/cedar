@extends('layouts.app')
@section('title', 'eTIMS Reports & Analytics')


@section('css')
@parent
@include('layouts.partials.page_modern_css')
@endsection

@section('content')

{{-- ── Page Header ────────────────────────────────────────────────── --}}

<div class="page-modern">

    <section class="content-header"></section>

    <div class="pg-banner">
        <div class="pg-banner-inner">
            <div class="pg-banner-title">
                <div class="pg-banner-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div>
                    <h1>eTIMS Reports &amp; Analytics</h1>
                    <p class="pg-subtitle">{{ session('business.name') }}</p>
                </div>
            </div>
            <div class="pg-banner-actions"></div>
        </div>
    </div>

<section class="content">

{{-- ── KPI CARDS ────────────────────────────────────────────────────── --}}
@php
    $sell     = $stats['sell'];
    $purchase = $stats['purchase'];
    $sell_compliance = $sell->total > 0 ? round(($sell->synced / $sell->total) * 100, 1) : 0;
    $pur_compliance  = $purchase->total > 0 ? round(($purchase->synced / $purchase->total) * 100, 1) : 0;
@endphp

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:20px;">

    {{-- Total Sales --}}
    <div style="background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                border-left:4px solid #3b82f6;border:1px solid #e2e8f0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Sales Total</span>
            <span style="width:30px;height:30px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <i class="fa fa-shopping-cart" style="color:#3b82f6;font-size:12px;"></i>
            </span>
        </div>
        <div style="font-size:1.6rem;font-weight:700;color:#1e293b;">{{ number_format($sell->total ?? 0) }}</div>
        <div style="font-size:.72rem;color:#94a3b8;margin-top:2px;">Final invoices</div>
    </div>

    {{-- Synced Sales --}}
    <div style="background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                border:1px solid #e2e8f0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Sales Synced</span>
            <span style="width:30px;height:30px;background:#f0fdf4;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <i class="fa fa-check-circle" style="color:#22c55e;font-size:12px;"></i>
            </span>
        </div>
        <div style="font-size:1.6rem;font-weight:700;color:#16a34a;">{{ number_format($sell->synced ?? 0) }}</div>
        <div style="font-size:.72rem;color:#94a3b8;margin-top:2px;">
            <span style="color:#16a34a;font-weight:600;">{{ $sell_compliance }}%</span> compliance
        </div>
    </div>

    {{-- Failed Sales --}}
    <div style="background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                border:1px solid #e2e8f0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Sales Failed</span>
            <span style="width:30px;height:30px;background:#fff1f2;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <i class="fa fa-times-circle" style="color:#ef4444;font-size:12px;"></i>
            </span>
        </div>
        <div style="font-size:1.6rem;font-weight:700;color:#dc2626;">{{ number_format($sell->failed ?? 0) }}</div>
        <div style="font-size:.72rem;color:#94a3b8;margin-top:2px;">Need attention</div>
    </div>

    {{-- Pending Sales --}}
    <div style="background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                border:1px solid #e2e8f0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Sales Pending</span>
            <span style="width:30px;height:30px;background:#fffbeb;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <i class="fa fa-clock-o" style="color:#f59e0b;font-size:12px;"></i>
            </span>
        </div>
        <div style="font-size:1.6rem;font-weight:700;color:#d97706;">{{ number_format($sell->pending ?? 0) }}</div>
        <div style="font-size:.72rem;color:#94a3b8;margin-top:2px;">Awaiting sync</div>
    </div>

    {{-- Revenue Synced --}}
    <div style="background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                border:1px solid #e2e8f0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Revenue Synced</span>
            <span style="width:30px;height:30px;background:#f0fdf4;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <i class="fa fa-money" style="color:#059669;font-size:12px;"></i>
            </span>
        </div>
        <div style="font-size:1.25rem;font-weight:700;color:#059669;">
            <span class="display_currency" data-currency_symbol="true">{{ $sell->revenue_synced ?? 0 }}</span>
        </div>
        <div style="font-size:.72rem;color:#94a3b8;margin-top:2px;">
            of <span class="display_currency" data-currency_symbol="true">{{ $sell->total_revenue ?? 0 }}</span>
        </div>
    </div>

    {{-- Purchases Synced --}}
    <div style="background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                border:1px solid #e2e8f0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Purchases</span>
            <span style="width:30px;height:30px;background:#faf5ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <i class="fa fa-truck" style="color:#7c3aed;font-size:12px;"></i>
            </span>
        </div>
        <div style="font-size:1.6rem;font-weight:700;color:#7c3aed;">{{ number_format($purchase->synced ?? 0) }}<span style="font-size:.9rem;color:#94a3b8;">/{{ number_format($purchase->total ?? 0) }}</span></div>
        <div style="font-size:.72rem;color:#94a3b8;margin-top:2px;">
            <span style="color:#7c3aed;font-weight:600;">{{ $pur_compliance }}%</span> synced
            @if(($purchase->failed ?? 0) > 0)
                · <span style="color:#ef4444;">{{ $purchase->failed }} failed</span>
            @endif
        </div>
    </div>

</div>

{{-- ── CHARTS ROW ───────────────────────────────────────────────────── --}}
<div class="row" style="margin-bottom:20px;">

    {{-- Sync Rate Donut --}}
    <div class="col-md-4">
        <div style="background:#fff;border-radius:12px;padding:18px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                    border:1px solid #e2e8f0;height:100%;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h4 style="margin:0;font-size:.85rem;font-weight:600;color:#374151;">Sales Sync Rate</h4>
                <span style="font-size:.7rem;color:#94a3b8;">All time</span>
            </div>
            <div style="position:relative;display:flex;justify-content:center;">
                <canvas id="syncDonutChart" height="180"></canvas>
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                    <div style="font-size:1.5rem;font-weight:700;color:#1e293b;">{{ $sell_compliance }}%</div>
                    <div style="font-size:.65rem;color:#94a3b8;">synced</div>
                </div>
            </div>
            <div style="display:flex;justify-content:center;gap:16px;margin-top:10px;flex-wrap:wrap;">
                <span style="font-size:.72rem;color:#64748b;display:flex;align-items:center;gap:4px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;"></span> Synced
                </span>
                <span style="font-size:.72rem;color:#64748b;display:flex;align-items:center;gap:4px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;"></span> Failed
                </span>
                <span style="font-size:.72rem;color:#64748b;display:flex;align-items:center;gap:4px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;"></span> Pending
                </span>
            </div>
        </div>
    </div>

    {{-- 30-Day Trend Line --}}
    <div class="col-md-5">
        <div style="background:#fff;border-radius:12px;padding:18px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                    border:1px solid #e2e8f0;height:100%;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h4 style="margin:0;font-size:.85rem;font-weight:600;color:#374151;">Daily Sync Trend</h4>
                <div style="display:flex;gap:4px;">
                    <button class="trend-days-btn active" data-days="7"
                        style="font-size:.68rem;padding:3px 9px;border-radius:6px;border:1px solid #e2e8f0;
                               background:#eff6ff;color:#3b82f6;cursor:pointer;font-weight:600;">7d</button>
                    <button class="trend-days-btn" data-days="30"
                        style="font-size:.68rem;padding:3px 9px;border-radius:6px;border:1px solid #e2e8f0;
                               background:#fff;color:#64748b;cursor:pointer;">30d</button>
                    <button class="trend-days-btn" data-days="90"
                        style="font-size:.68rem;padding:3px 9px;border-radius:6px;border:1px solid #e2e8f0;
                               background:#fff;color:#64748b;cursor:pointer;">90d</button>
                </div>
            </div>
            <canvas id="trendLineChart" height="170"></canvas>
        </div>
    </div>

    {{-- Tax Category Breakdown --}}
    <div class="col-md-3">
        <div style="background:#fff;border-radius:12px;padding:18px;box-shadow:0 1px 6px rgba(0,0,0,.07);
                    border:1px solid #e2e8f0;height:100%;">
            <h4 style="margin:0 0 14px;font-size:.85rem;font-weight:600;color:#374151;">Tax Categories</h4>
            @forelse($tax_breakdown as $tb)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:7px 0;
                        border-bottom:1px solid #f1f5f9;">
                <div>
                    <div style="font-size:.8rem;font-weight:600;color:#374151;">
                        {{ $tb->etims_tax_category ?? 'Uncategorised' }}
                    </div>
                    <div style="font-size:.68rem;color:#94a3b8;">{{ number_format($tb->invoice_count) }} invoices</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:.78rem;font-weight:600;color:#059669;">
                        <span class="display_currency" data-currency_symbol="true">{{ $tb->taxable_amount }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:#94a3b8;padding:30px 0;font-size:.8rem;">
                <i class="fa fa-tag" style="font-size:1.5rem;margin-bottom:8px;display:block;"></i>
                No data yet
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── TABBED DATATABLES ────────────────────────────────────────────── --}}
<div style="background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(0,0,0,.07);
            border:1px solid #e2e8f0;overflow:hidden;">

    {{-- Tab Nav --}}
    <div style="border-bottom:1px solid #e2e8f0;padding:0 20px;background:#f8fafc;overflow-x:auto;">
        <ul class="nav" role="tablist" style="display:flex;gap:0;margin:0;padding:0;list-style:none;white-space:nowrap;">
            <li style="margin:0;">
                <a href="#tab-sales" data-toggle="tab" role="tab"
                   style="display:block;padding:13px 18px;font-size:.82rem;font-weight:600;color:#4f46e5;
                          border-bottom:3px solid #4f46e5;text-decoration:none;" class="etims-tab active-tab">
                    <i class="fa fa-arrow-up" style="margin-right:5px;color:#22c55e;"></i>
                    Sales Report
                    @if(($sell->failed ?? 0) > 0)
                        <span style="background:#fef2f2;color:#dc2626;border-radius:10px;padding:1px 7px;font-size:.65rem;margin-left:4px;">
                            {{ $sell->failed }} failed
                        </span>
                    @endif
                </a>
            </li>
            <li style="margin:0;">
                <a href="#tab-purchases" data-toggle="tab" role="tab"
                   style="display:block;padding:13px 18px;font-size:.82rem;font-weight:600;color:#64748b;
                          border-bottom:3px solid transparent;text-decoration:none;" class="etims-tab">
                    <i class="fa fa-arrow-down" style="margin-right:5px;color:#7c3aed;"></i>
                    Purchases Report
                    @if(($purchase->failed ?? 0) > 0)
                        <span style="background:#fef2f2;color:#dc2626;border-radius:10px;padding:1px 7px;font-size:.65rem;margin-left:4px;">
                            {{ $purchase->failed }} failed
                        </span>
                    @endif
                </a>
            </li>
            <li style="margin:0;">
                <a href="#tab-vat" data-toggle="tab" role="tab"
                   style="display:block;padding:13px 18px;font-size:.82rem;font-weight:600;color:#64748b;
                          border-bottom:3px solid transparent;text-decoration:none;" class="etims-tab">
                    <i class="fa fa-percent" style="margin-right:5px;color:#f59e0b;"></i>
                    VAT Report
                </a>
            </li>
            <li style="margin:0;">
                <a href="#tab-monthly" data-toggle="tab" role="tab"
                   style="display:block;padding:13px 18px;font-size:.82rem;font-weight:600;color:#64748b;
                          border-bottom:3px solid transparent;text-decoration:none;" class="etims-tab">
                    <i class="fa fa-calendar" style="margin-right:5px;color:#0ea5e9;"></i>
                    Monthly Compliance
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content" style="padding:0;">

        {{-- ── SALES TAB ── --}}
        <div role="tabpanel" class="tab-pane active" id="tab-sales">

            {{-- Filters --}}
            <div style="padding:14px 20px;background:#fafafa;border-bottom:1px solid #f1f5f9;">
                <div class="row" style="margin:0;align-items:flex-end;gap:0;">
                    <div class="col-md-2 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">Status</label>
                        <select id="sales_status_filter" class="form-control" style="font-size:.8rem;height:34px;">
                            <option value="">All Statuses</option>
                            <option value="success">Synced</option>
                            <option value="failed">Failed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">Location</label>
                        <select id="sales_location_filter" class="form-control" style="font-size:.8rem;height:34px;">
                            <option value="">All Locations</option>
                            @foreach($business_locations as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">From</label>
                        <input type="date" id="sales_start_date" class="form-control" style="font-size:.8rem;height:34px;">
                    </div>
                    <div class="col-md-2 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">To</label>
                        <input type="date" id="sales_end_date" class="form-control" style="font-size:.8rem;height:34px;">
                    </div>
                    <div class="col-md-2 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">&nbsp;</label>
                        <button id="sales_filter_btn"
                                style="width:100%;height:34px;background:#4f46e5;color:#fff;border:none;
                                       border-radius:6px;font-size:.78rem;font-weight:600;cursor:pointer;">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-1 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">&nbsp;</label>
                        <button id="sales_clear_btn"
                                style="width:100%;height:34px;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;
                                       border-radius:6px;font-size:.78rem;cursor:pointer;">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div style="padding:16px 20px;">
                <div class="table-responsive">
                    <table class="table table-hover" id="sales_table"
                           style="font-size:.8rem;border-collapse:separate;border-spacing:0;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Date</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Invoice #</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Customer</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Location</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;text-align:right;">Amount</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">eTIMS #</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Status</th>
                                <th style="border-top:none;padding:10px 12px;"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── PURCHASES TAB ── --}}
        <div role="tabpanel" class="tab-pane" id="tab-purchases">

            {{-- Filters --}}
            <div style="padding:14px 20px;background:#fafafa;border-bottom:1px solid #f1f5f9;">
                <div class="row" style="margin:0;align-items:flex-end;">
                    <div class="col-md-2 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">Status</label>
                        <select id="pur_status_filter" class="form-control" style="font-size:.8rem;height:34px;">
                            <option value="">All Statuses</option>
                            <option value="success">Synced</option>
                            <option value="failed">Failed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">Location</label>
                        <select id="pur_location_filter" class="form-control" style="font-size:.8rem;height:34px;">
                            <option value="">All Locations</option>
                            @foreach($business_locations as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">From</label>
                        <input type="date" id="pur_start_date" class="form-control" style="font-size:.8rem;height:34px;">
                    </div>
                    <div class="col-md-2 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">To</label>
                        <input type="date" id="pur_end_date" class="form-control" style="font-size:.8rem;height:34px;">
                    </div>
                    <div class="col-md-2 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">&nbsp;</label>
                        <button id="pur_filter_btn"
                                style="width:100%;height:34px;background:#7c3aed;color:#fff;border:none;
                                       border-radius:6px;font-size:.78rem;font-weight:600;cursor:pointer;">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-1 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">&nbsp;</label>
                        <button id="pur_clear_btn"
                                style="width:100%;height:34px;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;
                                       border-radius:6px;font-size:.78rem;cursor:pointer;">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div style="padding:16px 20px;">
                <div class="table-responsive">
                    <table class="table table-hover" id="purchases_table"
                           style="font-size:.8rem;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Date</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Ref #</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Type</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Supplier</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Location</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;text-align:right;">Amount</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">eTIMS #</th>
                                <th style="border-top:none;font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Status</th>
                                <th style="border-top:none;padding:10px 12px;"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── VAT REPORT TAB ── --}}
        <div role="tabpanel" class="tab-pane" id="tab-vat">

            {{-- Filters --}}
            <div style="padding:14px 20px;background:#fafafa;border-bottom:1px solid #f1f5f9;">
                <div class="row" style="margin:0;align-items:flex-end;gap:0;">
                    <div class="col-md-3 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">Location</label>
                        <select id="vat_location_filter" class="form-control" style="font-size:.8rem;height:34px;">
                            <option value="">All Locations</option>
                            @foreach($business_locations as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">From</label>
                        <input type="date" id="vat_start_date" class="form-control" style="font-size:.8rem;height:34px;"
                               value="{{ now()->startOfMonth()->toDateString() }}">
                    </div>
                    <div class="col-md-2 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">To</label>
                        <input type="date" id="vat_end_date" class="form-control" style="font-size:.8rem;height:34px;"
                               value="{{ now()->toDateString() }}">
                    </div>
                    <div class="col-md-2 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">&nbsp;</label>
                        <button id="vat_filter_btn"
                                style="width:100%;height:34px;background:#f59e0b;color:#fff;border:none;
                                       border-radius:6px;font-size:.78rem;font-weight:600;cursor:pointer;">
                            <i class="fa fa-refresh"></i> Load Report
                        </button>
                    </div>
                    <div class="col-md-3 col-sm-4" style="padding:0 6px;">
                        <label style="font-size:.72rem;font-weight:600;color:#64748b;margin-bottom:4px;display:block;">&nbsp;</label>
                        <a id="vat_export_btn" href="#"
                           style="display:inline-flex;align-items:center;gap:6px;height:34px;padding:0 14px;
                                  background:#16a34a;color:#fff;border-radius:6px;font-size:.78rem;
                                  font-weight:600;text-decoration:none;">
                            <i class="fa fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
            </div>

            {{-- VAT Summary Table --}}
            <div style="padding:20px;">
                <div id="vat_loading" style="text-align:center;padding:40px;color:#94a3b8;display:none;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading VAT Report…
                </div>
                <div id="vat_table_wrap">
                    <table class="table" id="vat_summary_table" style="font-size:.82rem;">
                        <thead>
                            <tr style="background:#fef3c7;">
                                <th style="font-weight:700;color:#92400e;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;">Tax Category</th>
                                <th style="font-weight:700;color:#92400e;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;">VAT Rate</th>
                                <th style="font-weight:700;color:#92400e;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;text-align:right;">Invoices</th>
                                <th style="font-weight:700;color:#92400e;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;text-align:right;">Taxable Amount (excl. VAT)</th>
                                <th style="font-weight:700;color:#92400e;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;text-align:right;">VAT Charged</th>
                                <th style="font-weight:700;color:#92400e;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;text-align:right;">Gross Amount (incl. VAT)</th>
                            </tr>
                        </thead>
                        <tbody id="vat_tbody">
                            <tr><td colspan="6" style="text-align:center;padding:30px;color:#94a3b8;">
                                Click <strong>Load Report</strong> to generate the VAT summary.
                            </td></tr>
                        </tbody>
                        <tfoot id="vat_tfoot" style="display:none;">
                            <tr style="background:#f0fdf4;font-weight:700;">
                                <td colspan="2" style="padding:10px 14px;color:#065f46;font-size:.82rem;">TOTALS</td>
                                <td id="vat_total_invoices" style="text-align:right;padding:10px 14px;color:#065f46;"></td>
                                <td id="vat_total_taxable" style="text-align:right;padding:10px 14px;color:#065f46;"></td>
                                <td id="vat_total_tax" style="text-align:right;padding:10px 14px;color:#065f46;"></td>
                                <td id="vat_total_gross" style="text-align:right;padding:10px 14px;color:#065f46;"></td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- KRA VAT Return Helper --}}
                    <div id="vat_return_helper" style="display:none;margin-top:20px;background:#eff6ff;border:1px solid #bfdbfe;
                                border-radius:10px;padding:16px 20px;">
                        <h5 style="margin:0 0 12px;font-size:.85rem;font-weight:700;color:#1d4ed8;">
                            <i class="fa fa-info-circle"></i> KRA VAT Return Reference
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div style="font-size:.75rem;color:#64748b;font-weight:600;margin-bottom:2px;">Box 11 — Standard Rated Sales (16%)</div>
                                <div id="box11" style="font-size:1.1rem;font-weight:700;color:#1e293b;">—</div>
                            </div>
                            <div class="col-md-4">
                                <div style="font-size:.75rem;color:#64748b;font-weight:600;margin-bottom:2px;">Box 12 — Output VAT (16%)</div>
                                <div id="box12" style="font-size:1.1rem;font-weight:700;color:#dc2626;">—</div>
                            </div>
                            <div class="col-md-4">
                                <div style="font-size:.75rem;color:#64748b;font-weight:600;margin-bottom:2px;">Box 15 — Zero-Rated / Exempt Sales</div>
                                <div id="box15" style="font-size:1.1rem;font-weight:700;color:#1e293b;">—</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── MONTHLY COMPLIANCE TAB ── --}}
        <div role="tabpanel" class="tab-pane" id="tab-monthly">
            <div style="padding:20px;">

                {{-- Controls --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
                    <h4 style="margin:0;font-size:.9rem;font-weight:700;color:#374151;">
                        Rolling Monthly Compliance — Sales invoices vs eTIMS sync status
                    </h4>
                    <div style="display:flex;gap:6px;">
                        <button class="monthly-range-btn active" data-months="6"
                            style="font-size:.72rem;padding:4px 12px;border-radius:6px;border:1px solid #e2e8f0;
                                   background:#eff6ff;color:#3b82f6;cursor:pointer;font-weight:600;">6 months</button>
                        <button class="monthly-range-btn" data-months="12"
                            style="font-size:.72rem;padding:4px 12px;border-radius:6px;border:1px solid #e2e8f0;
                                   background:#fff;color:#64748b;cursor:pointer;">12 months</button>
                        <button class="monthly-range-btn" data-months="24"
                            style="font-size:.72rem;padding:4px 12px;border-radius:6px;border:1px solid #e2e8f0;
                                   background:#fff;color:#64748b;cursor:pointer;">24 months</button>
                    </div>
                </div>

                {{-- Stacked Bar Chart --}}
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:16px;margin-bottom:20px;">
                    <canvas id="monthlyComplianceChart" height="100"></canvas>
                </div>

                {{-- Monthly Table --}}
                <div class="table-responsive">
                    <table class="table table-hover" style="font-size:.8rem;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;">Month</th>
                                <th style="font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;text-align:right;">Total Invoices</th>
                                <th style="font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;text-align:right;">Synced ✓</th>
                                <th style="font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;text-align:right;">Failed ✗</th>
                                <th style="font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;text-align:right;">Pending ⌛</th>
                                <th style="font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;text-align:right;">Revenue Synced</th>
                                <th style="font-weight:600;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:10px 12px;text-align:center;">Compliance</th>
                            </tr>
                        </thead>
                        <tbody id="monthly_tbody">
                            <tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8;">
                                <i class="fa fa-spinner fa-spin"></i> Loading…
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- /tab-content --}}
</div>{{-- /tabbed card --}}

</section>
@endsection

@section('javascript')
{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
$(function () {

    /* ── Helpers ─────────────────────────────────────────────── */
    var syncAllUrl   = '{{ route("etims.sync-all") }}';
    var analyticsUrl = '{{ action([\App\Http\Controllers\EtimsReportController::class, "analytics"]) }}';
    var salesUrl     = '{{ action([\App\Http\Controllers\EtimsReportController::class, "salesData"]) }}';
    var purchasesUrl = '{{ action([\App\Http\Controllers\EtimsReportController::class, "purchasesData"]) }}';
    var csrf         = '{{ csrf_token() }}';

    /* ── Tab switching style ─────────────────────────────────── */
    $('a.etims-tab').on('click', function (e) {
        e.preventDefault();
        $('a.etims-tab').css({'color':'#64748b','border-bottom-color':'transparent'});
        $(this).css({'color':'#4f46e5','border-bottom-color':'#4f46e5'});
        $($(this).attr('href')).tab('show');
        // lazy-init purchases table when tab first shown
        if ($(this).attr('href') === '#tab-purchases' && !purchasesTable) {
            initPurchasesTable();
        }
    });

    /* ── Donut Chart ─────────────────────────────────────────── */
    var synced  = {{ $sell->synced ?? 0 }};
    var failed  = {{ $sell->failed ?? 0 }};
    var pending = {{ $sell->pending ?? 0 }};
    var donutCtx = document.getElementById('syncDonutChart');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Synced', 'Failed', 'Pending'],
                datasets: [{
                    data: [synced, failed, pending],
                    backgroundColor: ['#22c55e', '#ef4444', '#f59e0b'],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverBorderWidth: 3
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false }, tooltip: { callbacks: {
                    label: function(ctx) { return ' ' + ctx.label + ': ' + ctx.parsed; }
                }}},
                animation: { animateScale: true }
            }
        });
    }

    /* ── Trend Line Chart ────────────────────────────────────── */
    var trendCtx  = document.getElementById('trendLineChart');
    var trendChart = null;

    function buildTrendChart(data) {
        var labels  = data.map(function(r){ return r.day; });
        var synced  = data.map(function(r){ return r.synced; });
        var failed  = data.map(function(r){ return r.failed; });
        var pending = data.map(function(r){ return r.pending; });

        if (trendChart) { trendChart.destroy(); }
        trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Synced',  data: synced,  borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,.08)',
                      tension:.4, fill:true, pointRadius:3, pointHoverRadius:5 },
                    { label: 'Failed',  data: failed,  borderColor: '#ef4444', backgroundColor: 'transparent',
                      tension:.4, fill:false, pointRadius:3, pointHoverRadius:5, borderDash:[4,3] },
                    { label: 'Pending', data: pending, borderColor: '#f59e0b', backgroundColor: 'transparent',
                      tension:.4, fill:false, pointRadius:3, pointHoverRadius:5, borderDash:[2,2] }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: { legend: { position:'bottom', labels:{ font:{size:11}, boxWidth:10, padding:12 } } },
                scales: {
                    x: { grid:{ display:false }, ticks:{ font:{size:10}, maxTicksLimit:7 } },
                    y: { grid:{ color:'#f1f5f9' }, ticks:{ font:{size:10}, precision:0 }, beginAtZero:true }
                }
            }
        });
    }

    // Initial 7-day trend from PHP
    var initialTrend = @json($trend);
    if (trendCtx && initialTrend.length) {
        buildTrendChart(initialTrend);
    }

    // Trend day buttons
    $('.trend-days-btn').on('click', function () {
        $('.trend-days-btn').css({'background':'#fff','color':'#64748b','borderColor':'#e2e8f0'});
        $(this).css({'background':'#eff6ff','color':'#3b82f6','borderColor':'#bfdbfe'});
        var days = $(this).data('days');
        $.getJSON(analyticsUrl + '?days=' + days, function (res) {
            if (res.trend && res.trend.length) { buildTrendChart(res.trend); }
        });
    });

    /* ── Sales DataTable ─────────────────────────────────────── */
    var salesTable = $('#sales_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        pageLength: 25,
        dom: '<"tw-flex tw-items-center tw-justify-between tw-mb-3"lf>rt<"tw-flex tw-items-center tw-justify-between tw-mt-3"ip>',
        language: { processing: '<div style="padding:10px;color:#64748b;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>' },
        ajax: {
            url: salesUrl,
            data: function (d) {
                d.sync_status  = $('#sales_status_filter').val();
                d.location_id  = $('#sales_location_filter').val();
                d.start_date   = $('#sales_start_date').val();
                d.end_date     = $('#sales_end_date').val();
            }
        },
        columns: [
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'invoice_no',       name: 'invoice_no' },
            { data: 'customer_name',    name: 'c.name' },
            { data: 'location_name',    name: 'bl.name' },
            { data: 'final_total',      name: 'final_total', className: 'text-right' },
            { data: 'etims_invoice_number', name: 'etims_invoice_number',
              render: function(d){ return d ? '<code style="font-size:.72rem;">'+d+'</code>' : '<span style="color:#cbd5e1;">—</span>'; } },
            { data: 'etims_sync_status', name: 'etims_sync_status' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
        ]
    });

    $('#sales_filter_btn').on('click', function () { salesTable.ajax.reload(); });
    $('#sales_clear_btn').on('click', function () {
        $('#sales_status_filter,#sales_location_filter').val('');
        $('#sales_start_date,#sales_end_date').val('');
        salesTable.ajax.reload();
    });

    /* ── Purchases DataTable (lazy) ─────────────────────────── */
    var purchasesTable = null;
    function initPurchasesTable() {
        purchasesTable = $('#purchases_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            pageLength: 25,
            dom: '<"tw-flex tw-items-center tw-justify-between tw-mb-3"lf>rt<"tw-flex tw-items-center tw-justify-between tw-mt-3"ip>',
            language: { processing: '<div style="padding:10px;color:#64748b;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>' },
            ajax: {
                url: purchasesUrl,
                data: function (d) {
                    d.sync_status  = $('#pur_status_filter').val();
                    d.location_id  = $('#pur_location_filter').val();
                    d.start_date   = $('#pur_start_date').val();
                    d.end_date     = $('#pur_end_date').val();
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'ref_no',           name: 'ref_no' },
                { data: 'type',             name: 'type' },
                { data: 'supplier_name',    name: 'c.name' },
                { data: 'location_name',    name: 'bl.name' },
                { data: 'final_total',      name: 'final_total', className: 'text-right' },
                { data: 'etims_invoice_number', name: 'etims_invoice_number',
                  render: function(d){ return d ? '<code style="font-size:.72rem;">'+d+'</code>' : '<span style="color:#cbd5e1;">—</span>'; } },
                { data: 'etims_sync_status', name: 'etims_sync_status' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ]
        });
        $('#pur_filter_btn').on('click', function () { purchasesTable.ajax.reload(); });
        $('#pur_clear_btn').on('click', function () {
            $('#pur_status_filter,#pur_location_filter').val('');
            $('#pur_start_date,#pur_end_date').val('');
            purchasesTable.ajax.reload();
        });
    }

    /* ── Sync Single Invoice ─────────────────────────────────── */
    $(document).on('click', '.sync-single-btn', function () {
        var btn = $(this);
        var url = btn.data('href');
        btn.prop('disabled', true).html('<i class="fa fa-refresh fa-spin"></i>');
        $.getJSON(url, function (res) {
            if (res.success) {
                toastr.success(res.msg);
                salesTable.ajax.reload(null, false);
                if (purchasesTable) purchasesTable.ajax.reload(null, false);
            } else {
                toastr.error(res.msg);
            }
            btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Retry');
        }).fail(function () {
            toastr.error('Request failed. Please try again.');
            btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Retry');
        });
    });

    /* ── Sync All ────────────────────────────────────────────── */
    function syncAll(btn, type) {
        var originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-refresh fa-spin"></i> Syncing…');
        $.ajax({
            method: 'POST', url: syncAllUrl,
            data: { _token: csrf, type: type },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    toastr.success(res.msg);
                    salesTable.ajax.reload(null, false);
                    if (purchasesTable) purchasesTable.ajax.reload(null, false);
                } else {
                    toastr.error(res.msg);
                }
            },
            error: function () { toastr.error('An error occurred. Please try again.'); },
            complete: function () { btn.prop('disabled', false).html(originalHtml); }
        });
    }

    $('#sync_all_sales_btn').on('click', function () { syncAll($(this), 'sell'); });
    $('#sync_all_purchases_btn').on('click', function () { syncAll($(this), 'purchase'); });

    /* ── VAT Report ──────────────────────────────────────────── */
    var vatDataUrl   = '{{ route("etims.vat-data") }}';
    var vatExportUrl = '{{ route("etims.export-vat") }}';

    function fmt(n) {
        return new Intl.NumberFormat('en-KE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n || 0);
    }

    function loadVatReport() {
        var start = $('#vat_start_date').val();
        var end   = $('#vat_end_date').val();
        var loc   = $('#vat_location_filter').val();

        $('#vat_loading').show();
        $('#vat_table_wrap').hide();

        $.getJSON(vatDataUrl, { start_date: start, end_date: end, location_id: loc }, function (res) {
            var tbody = '';
            var catColors = { A:'#fef9c3', B:'#fce7f3', C:'#dcfce7', D:'#f1f5f9', E:'#f0f9ff' };

            $.each(res.rows, function (i, r) {
                var bg = catColors[r.category] || '#fff';
                tbody += '<tr style="background:' + bg + ';">'
                    + '<td style="padding:10px 14px;font-weight:600;">' + r.label + '</td>'
                    + '<td style="padding:10px 14px;"><span style="background:#e0f2fe;color:#0369a1;padding:2px 8px;border-radius:8px;font-size:.75rem;font-weight:700;">' + r.rate + '</span></td>'
                    + '<td style="text-align:right;padding:10px 14px;">' + r.invoice_count.toLocaleString() + '</td>'
                    + '<td style="text-align:right;padding:10px 14px;font-family:monospace;">' + fmt(r.taxable_amount) + '</td>'
                    + '<td style="text-align:right;padding:10px 14px;font-family:monospace;color:#dc2626;font-weight:600;">' + fmt(r.tax_amount) + '</td>'
                    + '<td style="text-align:right;padding:10px 14px;font-family:monospace;font-weight:600;">' + fmt(r.gross_amount) + '</td>'
                    + '</tr>';
            });

            if (!tbody) {
                tbody = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#94a3b8;">No synced invoices found for the selected period.</td></tr>';
            }

            $('#vat_tbody').html(tbody);

            // Totals row
            var t = res.totals;
            $('#vat_total_invoices').text(parseInt(t.invoice_count).toLocaleString());
            $('#vat_total_taxable').text(fmt(t.taxable_amount));
            $('#vat_total_tax').text(fmt(t.tax_amount));
            $('#vat_total_gross').text(fmt(t.gross_amount));
            $('#vat_tfoot').show();

            // KRA VAT Return Helper
            var stdRow  = $.grep(res.rows, function(r){ return r.category === 'A'; })[0];
            var zeroRow = $.grep(res.rows, function(r){ return r.category === 'C' || r.category === 'D'; });
            var zeroTotal = 0;
            $.each(zeroRow, function(i, r){ zeroTotal += parseFloat(r.gross_amount || 0); });
            if (stdRow || zeroTotal > 0) {
                $('#box11').text(fmt(stdRow ? stdRow.taxable_amount : 0));
                $('#box12').text(fmt(stdRow ? stdRow.tax_amount : 0));
                $('#box15').text(fmt(zeroTotal));
                $('#vat_return_helper').show();
            }

        }).always(function () {
            $('#vat_loading').hide();
            $('#vat_table_wrap').show();
        });
    }

    $('#vat_filter_btn').on('click', loadVatReport);

    // Update export CSV link dynamically
    function updateExportLink() {
        var params = new URLSearchParams({
            start_date:  $('#vat_start_date').val(),
            end_date:    $('#vat_end_date').val(),
            location_id: $('#vat_location_filter').val(),
        });
        $('#vat_export_btn').attr('href', vatExportUrl + '?' + params.toString());
    }
    $('#vat_start_date, #vat_end_date, #vat_location_filter').on('change', updateExportLink);
    updateExportLink();

    // Auto-load when tab is opened
    $('a[href="#tab-vat"]').on('shown.bs.tab click', function() {
        if ($('#vat_tbody tr td[colspan]').length) { loadVatReport(); }
    });

    /* ── Monthly Compliance ──────────────────────────────────── */
    var monthlyUrl = '{{ route("etims.monthly-compliance") }}';
    var monthlyChart = null;

    function loadMonthlyCompliance(months) {
        $.getJSON(monthlyUrl, { months: months }, function (rows) {
            var labels   = rows.map(function(r){ return r.month; });
            var synced   = rows.map(function(r){ return r.synced; });
            var failed   = rows.map(function(r){ return r.failed; });
            var pending  = rows.map(function(r){ return r.pending; });
            var pcts     = rows.map(function(r){ return r.compliance_pct; });

            // Chart
            var ctx = document.getElementById('monthlyComplianceChart');
            if (monthlyChart) monthlyChart.destroy();
            monthlyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Synced',  data: synced,  backgroundColor: '#22c55e', stack: 's' },
                        { label: 'Failed',  data: failed,  backgroundColor: '#ef4444', stack: 's' },
                        { label: 'Pending', data: pending, backgroundColor: '#f59e0b', stack: 's' },
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: true,
                    plugins: {
                        legend: { position:'bottom', labels:{ font:{size:11}, boxWidth:12 } },
                        tooltip: { callbacks: {
                            afterBody: function(items) {
                                var idx = items[0].dataIndex;
                                return ['Compliance: ' + pcts[idx] + '%'];
                            }
                        }}
                    },
                    scales: {
                        x: { stacked:true, grid:{display:false}, ticks:{font:{size:10}} },
                        y: { stacked:true, grid:{color:'#f1f5f9'}, ticks:{font:{size:10}, precision:0}, beginAtZero:true }
                    }
                }
            });

            // Table
            var tbody = '';
            $.each(rows, function(i, r) {
                var pctColor = r.compliance_pct >= 90 ? '#16a34a' : r.compliance_pct >= 70 ? '#d97706' : '#dc2626';
                var bar = '<div style="background:#e2e8f0;border-radius:4px;height:6px;overflow:hidden;margin-top:4px;">'
                    + '<div style="width:' + r.compliance_pct + '%;background:' + pctColor + ';height:100%;border-radius:4px;"></div></div>';
                tbody += '<tr>'
                    + '<td style="padding:9px 12px;font-weight:600;">' + r.month + '</td>'
                    + '<td style="text-align:right;padding:9px 12px;">' + parseInt(r.total).toLocaleString() + '</td>'
                    + '<td style="text-align:right;padding:9px 12px;color:#16a34a;font-weight:600;">' + parseInt(r.synced).toLocaleString() + '</td>'
                    + '<td style="text-align:right;padding:9px 12px;color:#dc2626;">' + parseInt(r.failed).toLocaleString() + '</td>'
                    + '<td style="text-align:right;padding:9px 12px;color:#d97706;">' + parseInt(r.pending).toLocaleString() + '</td>'
                    + '<td style="text-align:right;padding:9px 12px;font-family:monospace;">' + fmt(r.revenue_synced) + '</td>'
                    + '<td style="text-align:center;padding:9px 12px;min-width:120px;">'
                    + '<span style="font-weight:700;color:' + pctColor + ';">' + r.compliance_pct + '%</span>' + bar
                    + '</td>'
                    + '</tr>';
            });

            $('#monthly_tbody').html(tbody || '<tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8;">No data available.</td></tr>');
        });
    }

    // Range buttons
    $('.monthly-range-btn').on('click', function() {
        $('.monthly-range-btn').css({'background':'#fff','color':'#64748b','borderColor':'#e2e8f0'});
        $(this).css({'background':'#eff6ff','color':'#3b82f6','borderColor':'#bfdbfe'});
        loadMonthlyCompliance($(this).data('months'));
    });

    // Auto-load when tab opened
    $('a[href="#tab-monthly"]').on('shown.bs.tab click', function() {
        if ($('#monthly_tbody tr td[colspan]').length || !monthlyChart) {
            loadMonthlyCompliance(6);
        }
    });

});
</script>

</div>{{-- .page-modern --}}
@endsection