@extends('layouts.app')
@section('title', 'eTIMS Reports & Analytics')

@section('content')

{{-- ── Page Header ────────────────────────────────────────────────── --}}
<section class="content-header" style="padding-bottom:0;">
    <div style="background:linear-gradient(135deg,#064e3b 0%,#065f46 50%,#047857 100%);
                border-radius:14px;padding:20px 24px 18px;margin-bottom:20px;
                box-shadow:0 4px 20px rgba(6,78,59,.25);">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:46px;height:46px;background:rgba(255,255,255,.15);border-radius:12px;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa fa-bar-chart" style="color:#fff;font-size:20px;"></i>
                </div>
                <div>
                    <h1 style="color:#fff;font-size:1.35rem;font-weight:700;margin:0;line-height:1.2;">
                        eTIMS Reports &amp; Analytics
                    </h1>
                    <p style="color:rgba(255,255,255,.75);margin:2px 0 0;font-size:.8rem;">
                        Kenya Revenue Authority · Electronic Tax Invoice Management System
                    </p>
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('etims.settings') }}"
                   style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);
                          border-radius:8px;padding:7px 14px;font-size:.8rem;text-decoration:none;
                          display:inline-flex;align-items:center;gap:6px;">
                    <i class="fa fa-cog"></i> Settings
                </a>
                <button id="sync_all_sales_btn" data-type="sell"
                        style="background:#fff;color:#065f46;border:none;border-radius:8px;
                               padding:7px 14px;font-size:.8rem;font-weight:600;cursor:pointer;
                               display:inline-flex;align-items:center;gap:6px;">
                    <i class="fa fa-refresh"></i> Sync Pending Sales
                </button>
                <button id="sync_all_purchases_btn" data-type="purchase"
                        style="background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.3);
                               border-radius:8px;padding:7px 14px;font-size:.8rem;font-weight:600;cursor:pointer;
                               display:inline-flex;align-items:center;gap:6px;">
                    <i class="fa fa-refresh"></i> Sync Pending Purchases
                </button>
            </div>
        </div>
    </div>
</section>

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
    <div style="border-bottom:1px solid #e2e8f0;padding:0 20px;background:#f8fafc;">
        <ul class="nav" role="tablist" style="display:flex;gap:0;margin:0;padding:0;list-style:none;">
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

});
</script>
@endsection
