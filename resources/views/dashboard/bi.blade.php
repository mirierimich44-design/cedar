@extends('layouts.app')
@section('title', 'Business Intelligence Dashboard')

@section('content')

<section class="content-header">
    <h1>Business Intelligence
        <small>Data-Driven Insights &amp; Analytics
            <span class="label label-primary" style="font-size:10px;margin-left:8px;">Gemini 2.0 Flash</span>
        </small>
        <button class="btn btn-sm btn-default pull-right" id="refresh-bi" style="margin-top:2px;">
            <i class="fa fa-refresh"></i> Refresh AI
        </button>
    </h1>
</section>

<style>
    .kpi-card { border-radius:8px; transition:transform .2s; border-top:3px solid #ccc; }
    .kpi-card:hover { transform:translateY(-4px); }
    .kpi-val { font-size:24px; font-weight:bold; display:block; margin:5px 0; }
    .trend-pill { font-size:11px; padding:2px 6px; border-radius:4px; }
    .heatmap-grid { display:grid; grid-template-columns:40px repeat(24,1fr); gap:2px; }
    .heatmap-cell { aspect-ratio:1; border-radius:1px; background:#eee; }
    .badge-OPPORTUNITY { background-color:#00a65a!important; }
    .badge-WARNING     { background-color:#f39c12!important; }
    .badge-ANOMALY     { background-color:#00c0ef!important; }
    .badge-GROWTH      { background-color:#605ca8!important; }
    .badge-ACTION      { background-color:#dd4b39!important; }
    .insight-item { border-left:4px solid #3c8dbc; padding:10px; background:#f9f9f9; margin-bottom:10px; border-radius:0 4px 4px 0; }
    .skeleton { background:#eee; height:20px; margin-bottom:5px; animation:pulse 1.5s infinite; }
    @keyframes pulse { 0%{opacity:1} 50%{opacity:.5} 100%{opacity:1} }
    .bi-tab-icon { margin-right:4px; }
    .period-btn.active { font-weight:bold; }
    #bi-kpi-strip .info-box { margin-bottom:8px; }
    /* AI Intelligence Panels */
    .ai-panel { border-radius:6px; margin-bottom:16px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    .ai-panel-header { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; font-weight:600; font-size:14px; color:#fff; }
    .ai-panel-header .ai-title { display:flex; align-items:center; gap:8px; }
    .ai-panel-body { background:#fff; padding:14px 16px; min-height:56px; }
    .ai-panel-sales   .ai-panel-header { background:linear-gradient(135deg,#605ca8,#8e7de8); }
    .ai-panel-customers .ai-panel-header { background:linear-gradient(135deg,#00a65a,#00d673); }
    .ai-panel-financial .ai-panel-header { background:linear-gradient(135deg,#e67e22,#f1a64e); }
    .ai-panel-procurement .ai-panel-header { background:linear-gradient(135deg,#dd4b39,#e8715e); }
    .ai-panel-invhealth .ai-panel-header { background:linear-gradient(135deg,#3c8dbc,#5ba7d4); }
    .ai-insight-row { display:flex; flex-wrap:wrap; gap:10px; margin-top:8px; }
    .ai-insight-card { flex:1; min-width:220px; border-left:4px solid #3c8dbc; padding:10px 12px; background:#f8f9fa; border-radius:0 4px 4px 0; }
    .ai-insight-card.danger  { border-color:#dd4b39; background:#fff5f5; }
    .ai-insight-card.success { border-color:#00a65a; background:#f0fff4; }
    .ai-insight-card.warning { border-color:#f39c12; background:#fffbf0; }
    .ai-insight-card.info    { border-color:#3c8dbc; background:#f0f8ff; }
    .ai-insight-card h6 { margin:0 0 4px; font-size:13px; font-weight:700; }
    .ai-insight-card p  { margin:0; font-size:12px; color:#555; }
    .ai-placeholder { text-align:center; padding:8px 0; color:#999; font-size:13px; }
</style>

<section class="content">

    {{-- ── TOP KPI STRIP ──────────────────────────────────────────── --}}
    <div class="row" id="bi-kpi-strip">
        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-teal">
                <span class="info-box-icon"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Revenue (MTD)</span>
                    <span class="info-box-number">KES {{ number_format($revenue_now,0) }}</span>
                    @php $rev_ch = $revenue_last > 0 ? round((($revenue_now-$revenue_last)/$revenue_last)*100,1) : 0; @endphp
                    <span class="progress-description">
                        <i class="fa fa-caret-{{ $rev_ch>=0?'up':'down' }}"></i> {{ abs($rev_ch) }}% vs last month
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-blue">
                <span class="info-box-icon"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">New Customers (MTD)</span>
                    <span class="info-box-number">{{ number_format($customers_now) }}</span>
                    @php $cus_ch = $customers_last > 0 ? round((($customers_now-$customers_last)/$customers_last)*100,1) : 0; @endphp
                    <span class="progress-description">
                        <i class="fa fa-caret-{{ $cus_ch>=0?'up':'down' }}"></i> {{ abs($cus_ch) }}% vs last month
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Low Stock Alerts</span>
                    <span class="info-box-number">{{ $low_stock_count }}</span>
                    <span class="progress-description">Requiring reorder</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-truck"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Parcels Dispatched</span>
                    <span class="info-box-number">{{ number_format($parcels_now) }}</span>
                    @php $prc_ch = $parcels_last > 0 ? round((($parcels_now-$parcels_last)/$parcels_last)*100,1) : 0; @endphp
                    <span class="progress-description">
                        <i class="fa fa-caret-{{ $prc_ch>=0?'up':'down' }}"></i> {{ abs($prc_ch) }}% vs last month
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TABS ────────────────────────────────────────────────────── --}}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_overview"    data-toggle="tab"><i class="fa fa-dashboard bi-tab-icon"></i>Overview</a></li>
                    <li><a href="#tab_sales"       data-toggle="tab"><i class="fa fa-bar-chart bi-tab-icon"></i>Sales Analytics</a></li>
                    <li><a href="#tab_customers"   data-toggle="tab"><i class="fa fa-users bi-tab-icon"></i>Customer Intelligence</a></li>
                    <li><a href="#tab_financial"   data-toggle="tab"><i class="fa fa-line-chart bi-tab-icon"></i>Financial KPIs</a></li>
                    <li><a href="#tab_inventory"   data-toggle="tab"><i class="fa fa-cubes bi-tab-icon"></i>Inventory Intelligence</a></li>
                    <li><a href="#tab_ai"          data-toggle="tab"><i class="fa fa-robot bi-tab-icon"></i>AI Advisor</a></li>
                    <li><a href="#tab_digest"      data-toggle="tab"><i class="fa fa-coffee bi-tab-icon"></i>Morning Digest</a></li>
                    <li><a href="#tab_audit"       data-toggle="tab"><i class="fa fa-shield bi-tab-icon"></i>Audit &amp; Risk</a></li>
                </ul>

                <div class="tab-content" style="background:#f4f4f7;">

                    {{-- ══ 1. OVERVIEW ══════════════════════════════════════════ --}}
                    <div class="tab-pane active" id="tab_overview">

                        <div class="row" style="margin-top:10px;">
                            <div class="col-md-8">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Revenue vs Parcel Trends (12 Months)</h3>
                                    </div>
                                    <div class="box-body">
                                        <canvas id="mainTrendChart" style="height:250px;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Payment Methods</h3>
                                    </div>
                                    <div class="box-body">
                                        <canvas id="paymentMethodChart" style="height:250px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Sales Heatmap (7 Days × 24 Hours)</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="heatmap-grid" id="sales-heatmap">
                                            <div></div>
                                            @for($i=0;$i<24;$i++)<div style="text-align:center;font-size:9px;color:#999">{{$i}}h</div>@endfor
                                            @php $days=['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; @endphp
                                            @foreach($days as $idx=>$day)
                                                <div style="font-size:10px;color:#666">{{$day}}</div>
                                                @for($h=0;$h<24;$h++)
                                                    @php
                                                        $match=$heatmap_raw->where('day',$idx+1)->where('hour',$h)->first();
                                                        $count=$match?$match->count:0;
                                                        $opacity=min($count/10,1);
                                                    @endphp
                                                    <div class="heatmap-cell" style="background:rgba(60,141,188,{{$opacity}});" title="{{$count}} txns"></div>
                                                @endfor
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Top Revenue Routes (Logistics)</h3>
                                    </div>
                                    <div class="box-body">
                                        <canvas id="topRoutesChart" style="height:180px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /overview --}}

                    {{-- ══ 2. SALES ANALYTICS ═══════════════════════════════════ --}}
                    <div class="tab-pane" id="tab_sales">

                        {{-- AI Sales Intelligence — TOP --}}
                        <div class="ai-panel ai-panel-sales" style="margin-top:12px;">
                            <div class="ai-panel-header">
                                <span class="ai-title"><i class="fa fa-magic"></i> AI Sales Intelligence</span>
                                <div>
                                    <span id="ai-sales-spinner" style="display:none;margin-right:8px;"><i class="fa fa-spinner fa-spin"></i> Analysing…</span>
                                    <button class="btn btn-xs btn-light ai-refresh-btn" data-target="sales" style="display:none;background:rgba(255,255,255,.2);color:#fff;border-color:rgba(255,255,255,.4);"><i class="fa fa-refresh"></i> Refresh</button>
                                </div>
                            </div>
                            <div class="ai-panel-body" id="ai-sales-panel">
                                <p class="ai-placeholder"><i class="fa fa-spinner fa-spin"></i> Loading AI insights…</p>
                            </div>
                        </div>

                        <div style="padding:5px 0 5px 5px;">
                            <span class="text-muted" style="margin-right:8px;">Period:</span>
                            @foreach([7=>'7 Days',30=>'30 Days',90=>'90 Days',365=>'1 Year'] as $d=>$label)
                                <button class="btn btn-xs btn-default period-btn {{ $d==30?'active':'' }}" data-days="{{$d}}">{{$label}}</button>
                            @endforeach
                            <span id="sales-loading" style="display:none;margin-left:10px;"><i class="fa fa-spinner fa-spin"></i></span>
                        </div>

                        <div class="row" id="sales-kpis" style="margin-top:8px;">
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-teal"><span class="info-box-icon"><i class="fa fa-money"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Total Revenue</span><span class="info-box-number" id="sa_total_revenue">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-blue"><span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Total Orders</span><span class="info-box-number" id="sa_total_orders">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-yellow"><span class="info-box-icon"><i class="fa fa-calculator"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Avg Basket Size</span><span class="info-box-number" id="sa_avg_basket">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-purple" style="background:#7c3aed!important"><span class="info-box-icon"><i class="fa fa-star"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Top Category</span><span class="info-box-number" id="sa_top_category" style="font-size:14px;">—</span></div></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="box box-primary">
                                    <div class="box-header with-border"><h3 class="box-title">Daily Sales Trend</h3></div>
                                    <div class="box-body"><canvas id="salesTrendChart" style="height:240px;"></canvas></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box box-info">
                                    <div class="box-header with-border"><h3 class="box-title">Sales by Hour of Day</h3></div>
                                    <div class="box-body"><canvas id="hourlyChart" style="height:240px;"></canvas></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="box box-success">
                                    <div class="box-header with-border"><h3 class="box-title">Top 10 Products by Revenue</h3></div>
                                    <div class="box-body"><canvas id="topProductsChart" style="height:280px;"></canvas></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="box box-warning">
                                    <div class="box-header with-border"><h3 class="box-title">Revenue by Category</h3></div>
                                    <div class="box-body"><canvas id="categoryChart" style="height:280px;"></canvas></div>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /sales --}}

                    {{-- ══ 3. CUSTOMER INTELLIGENCE ════════════════════════════ --}}
                    <div class="tab-pane" id="tab_customers">

                        {{-- AI Customer Intelligence — TOP --}}
                        <div class="ai-panel ai-panel-customers" style="margin-top:12px;">
                            <div class="ai-panel-header">
                                <span class="ai-title"><i class="fa fa-magic"></i> AI Customer Intelligence</span>
                                <div>
                                    <span id="ai-customers-spinner" style="display:none;margin-right:8px;"><i class="fa fa-spinner fa-spin"></i> Analysing…</span>
                                    <button class="btn btn-xs ai-refresh-btn" data-target="customers" style="display:none;background:rgba(255,255,255,.2);color:#fff;border-color:rgba(255,255,255,.4);"><i class="fa fa-refresh"></i> Refresh</button>
                                </div>
                            </div>
                            <div class="ai-panel-body" id="ai-customers-panel">
                                <p class="ai-placeholder"><i class="fa fa-spinner fa-spin"></i> Loading AI insights…</p>
                            </div>
                        </div>

                        <div class="row" id="cust-kpis" style="margin-top:10px;">
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-teal"><span class="info-box-icon"><i class="fa fa-user-plus"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">New Customers (30d)</span><span class="info-box-number" id="ci_new">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-blue"><span class="info-box-icon"><i class="fa fa-repeat"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Returning Customers</span><span class="info-box-number" id="ci_returning">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-yellow"><span class="info-box-icon"><i class="fa fa-users"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Total Buying (30d)</span><span class="info-box-number" id="ci_total">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-green"><span class="info-box-icon"><i class="fa fa-percent"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Retention Rate</span><span class="info-box-number" id="ci_retention">—</span></div></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-7">
                                <div class="box box-primary">
                                    <div class="box-header with-border"><h3 class="box-title">Top 10 Customers by Spend (30 Days)</h3></div>
                                    <div class="box-body"><canvas id="topCustomersChart" style="height:280px;"></canvas></div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="box box-info">
                                    <div class="box-header with-border"><h3 class="box-title">Customer Growth (6 Months)</h3></div>
                                    <div class="box-body"><canvas id="custGrowthChart" style="height:280px;"></canvas></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-default">
                                    <div class="box-header with-border"><h3 class="box-title">Top Customers — Detail</h3></div>
                                    <div class="box-body table-responsive">
                                        <table class="table table-bordered table-hover table-condensed" id="top-customers-table">
                                            <thead><tr><th>#</th><th>Customer</th><th>Orders</th><th>Total Spend</th><th>Avg Order</th></tr></thead>
                                            <tbody id="top-customers-tbody"><tr><td colspan="5" class="text-center text-muted">Loading…</td></tr></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /customers --}}

                    {{-- ══ 4. FINANCIAL KPIs ═══════════════════════════════════ --}}
                    <div class="tab-pane" id="tab_financial">

                        {{-- AI Financial Advisor — TOP --}}
                        <div class="ai-panel ai-panel-financial" style="margin-top:12px;">
                            <div class="ai-panel-header">
                                <span class="ai-title"><i class="fa fa-magic"></i> AI Financial Advisor</span>
                                <div>
                                    <span id="ai-financial-spinner" style="display:none;margin-right:8px;"><i class="fa fa-spinner fa-spin"></i> Analysing…</span>
                                    <button class="btn btn-xs ai-refresh-btn" data-target="financial" style="display:none;background:rgba(255,255,255,.2);color:#fff;border-color:rgba(255,255,255,.4);"><i class="fa fa-refresh"></i> Refresh</button>
                                </div>
                            </div>
                            <div class="ai-panel-body" id="ai-financial-panel">
                                <p class="ai-placeholder"><i class="fa fa-spinner fa-spin"></i> Loading AI insights…</p>
                            </div>
                        </div>

                        <div class="row" id="fin-kpis" style="margin-top:10px;">
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-teal"><span class="info-box-icon"><i class="fa fa-money"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">YTD Revenue</span><span class="info-box-number" id="fk_revenue">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-green"><span class="info-box-icon"><i class="fa fa-plus-circle"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Gross Profit</span><span class="info-box-number" id="fk_gp">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-blue"><span class="info-box-icon"><i class="fa fa-percent"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">GP Margin</span><span class="info-box-number" id="fk_gp_margin">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-yellow"><span class="info-box-icon"><i class="fa fa-bar-chart"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Net Margin</span><span class="info-box-number" id="fk_net_margin">—</span></div></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="box box-primary">
                                    <div class="box-header with-border"><h3 class="box-title">Revenue / Gross Profit / Net Profit — This Year</h3></div>
                                    <div class="box-body"><canvas id="finTrendChart" style="height:260px;"></canvas></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box box-danger">
                                    <div class="box-header with-border"><h3 class="box-title">Expense Breakdown</h3></div>
                                    <div class="box-body"><canvas id="expenseChart" style="height:260px;"></canvas></div>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /financial --}}

                    {{-- ══ 5. INVENTORY INTELLIGENCE ════════════════════════════ --}}
                    <div class="tab-pane" id="tab_inventory">

                        {{-- AI Inventory Intelligence — TOP --}}
                        <div class="row" style="margin-top:12px;">
                            <div class="col-md-6">
                                <div class="ai-panel ai-panel-procurement">
                                    <div class="ai-panel-header">
                                        <span class="ai-title"><i class="fa fa-magic"></i> AI Restocking &amp; Procurement</span>
                                        <div>
                                            <span id="ai-procurement-spinner" style="display:none;margin-right:8px;"><i class="fa fa-spinner fa-spin"></i> Analysing…</span>
                                            <button class="btn btn-xs ai-refresh-btn" data-target="procurement" style="display:none;background:rgba(255,255,255,.2);color:#fff;border-color:rgba(255,255,255,.4);"><i class="fa fa-refresh"></i> Refresh</button>
                                        </div>
                                    </div>
                                    <div class="ai-panel-body" id="ai-procurement-panel">
                                        <p class="ai-placeholder"><i class="fa fa-spinner fa-spin"></i> Loading AI insights…</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ai-panel ai-panel-invhealth">
                                    <div class="ai-panel-header">
                                        <span class="ai-title"><i class="fa fa-magic"></i> AI Inventory Health</span>
                                        <div>
                                            <span id="ai-inv-health-spinner" style="display:none;margin-right:8px;"><i class="fa fa-spinner fa-spin"></i> Analysing…</span>
                                            <button class="btn btn-xs ai-refresh-btn" data-target="inv-health" style="display:none;background:rgba(255,255,255,.2);color:#fff;border-color:rgba(255,255,255,.4);"><i class="fa fa-refresh"></i> Refresh</button>
                                        </div>
                                    </div>
                                    <div class="ai-panel-body" id="ai-inv-health-panel">
                                        <p class="ai-placeholder"><i class="fa fa-spinner fa-spin"></i> Loading AI insights…</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="inv-kpis" style="margin-top:10px;">
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-red"><span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Reorder Alerts</span><span class="info-box-number" id="iv_reorder">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-yellow"><span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Slow Movers (30d)</span><span class="info-box-number" id="iv_slow">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-orange" style="background:#e67e22!important"><span class="info-box-icon"><i class="fa fa-times-circle"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Dead Stock (90d)</span><span class="info-box-number" id="iv_dead">—</span></div></div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box bg-teal"><span class="info-box-icon"><i class="fa fa-refresh"></i></span>
                                    <div class="info-box-content"><span class="info-box-text">Top Turnover Rate</span><span class="info-box-number" id="iv_top_turnover">—</span></div></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="box box-primary">
                                    <div class="box-header with-border"><h3 class="box-title">Top 10 Products by Turnover Rate</h3></div>
                                    <div class="box-body"><canvas id="turnoverChart" style="height:260px;"></canvas></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="box box-danger">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Reorder Alerts</h3>
                                    </div>
                                    <div class="box-body table-responsive">
                                        <table class="table table-bordered table-condensed table-hover">
                                            <thead><tr><th>Product</th><th>In Stock</th><th>Alert Qty</th><th>Unit Cost</th></tr></thead>
                                            <tbody id="reorder-tbody"><tr><td colspan="4" class="text-center text-muted">Loading…</td></tr></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="box box-warning">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Slow Movers <small>(no sales 30 days, has stock)</small></h3>
                                        <div class="box-tools pull-right">
                                            <button class="btn btn-xs btn-default" onclick="runAutoProcure()"><i class="fa fa-cog"></i> Auto-Procurement</button>
                                        </div>
                                    </div>
                                    <div class="box-body table-responsive" style="max-height:260px;overflow-y:auto;">
                                        <table class="table table-condensed table-hover">
                                            <thead><tr><th>Product</th><th>Category</th><th>Stock</th></tr></thead>
                                            <tbody id="slow-movers-tbody"><tr><td colspan="3" class="text-center text-muted">Loading…</td></tr></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="box box-danger">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Dead Stock <small>(no sales 90 days)</small></h3>
                                    </div>
                                    <div class="box-body table-responsive" style="max-height:260px;overflow-y:auto;">
                                        <table class="table table-condensed table-hover">
                                            <thead><tr><th>Product</th><th>Category</th><th>Stock</th></tr></thead>
                                            <tbody id="dead-stock-tbody"><tr><td colspan="3" class="text-center text-muted">Loading…</td></tr></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /inventory --}}

                    {{-- ══ 6. AI ADVISOR ════════════════════════════════════════ --}}
                    <div class="tab-pane" id="tab_ai">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="box box-primary direct-chat direct-chat-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-comments"></i> Apex AI Business Assistant</h3>
                                    </div>
                                    <div class="box-body" style="padding:15px;">
                                        <div id="qa-response" style="min-height:200px;background:#fff;padding:15px;border:1px solid #ddd;border-radius:4px;">
                                            <i>Ask me anything about your revenue, customers, inventory, or operations.</i>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <form id="qa-form">
                                            <div class="input-group">
                                                <input type="text" id="qa-input" placeholder="e.g. Which product has highest margin?" class="form-control">
                                                <span class="input-group-btn">
                                                    <button type="submit" class="btn btn-primary btn-flat" id="qa-btn">Ask AI</button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Automated Business Insights</h3>
                                        <div class="box-tools pull-right">
                                            <button class="btn btn-xs btn-default" onclick="fetchInsights(true)"><i class="fa fa-refresh"></i> Refresh</button>
                                        </div>
                                    </div>
                                    <div class="box-body" id="insights-container">
                                        <div class="skeleton" style="height:80px;"></div>
                                        <div class="skeleton" style="height:80px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h4 style="margin-left:5px;">Predictive Recommendations</h4>
                                <div id="predictions-container" class="row">
                                    <div class="col-md-4"><div class="box box-solid skeleton" style="height:150px;"></div></div>
                                    <div class="col-md-4"><div class="box box-solid skeleton" style="height:150px;"></div></div>
                                    <div class="col-md-4"><div class="box box-solid skeleton" style="height:150px;"></div></div>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /ai --}}

                    {{-- ══ 7. MORNING DIGEST ════════════════════════════════════ --}}
                    <div class="tab-pane" id="tab_digest">
                        <div class="row" style="padding:15px;">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border" style="display:flex;align-items:center;justify-content:space-between;">
                                        <h3 class="box-title"><i class="fa fa-coffee"></i> Morning Digest <small>(Yesterday &amp; Today)</small></h3>
                                        <button class="btn btn-sm btn-default" id="refresh_morning_digest"><i class="fa fa-refresh"></i> Refresh</button>
                                    </div>
                                    <div class="box-body">
                                        <div id="morning_digest_loader" class="text-center" style="padding:30px 0;">
                                            <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                                            <p class="text-muted" style="margin-top:10px;">Brewing your morning digest…</p>
                                        </div>
                                        <div id="morning_digest_data" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-3 col-sm-6"><div class="info-box"><span class="info-box-icon bg-green"><i class="fa fa-dollar"></i></span><div class="info-box-content"><span class="info-box-text">Today Sales</span><span class="info-box-number display_currency" data-currency_symbol="true" id="md_today_sales">0</span></div></div></div>
                                                <div class="col-md-3 col-sm-6"><div class="info-box"><span class="info-box-icon bg-yellow"><i class="fa fa-calendar"></i></span><div class="info-box-content"><span class="info-box-text">Yesterday Sales</span><span class="info-box-number display_currency" data-currency_symbol="true" id="md_yesterday_sales">0</span></div></div></div>
                                                <div class="col-md-3 col-sm-6"><div class="info-box"><span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span><div class="info-box-content"><span class="info-box-text">Customers Today</span><span class="info-box-number" id="md_total_customers">0</span></div></div></div>
                                                <div class="col-md-3 col-sm-6"><div class="info-box"><span class="info-box-icon bg-aqua"><i class="fa fa-money"></i></span><div class="info-box-content"><span class="info-box-text">Cash in Register</span><span class="info-box-number display_currency" data-currency_symbol="true" id="md_cash_in_register">0</span></div></div></div>
                                                <div class="col-md-3 col-sm-6"><div class="info-box"><span class="info-box-icon bg-red"><i class="fa fa-file-text-o"></i></span><div class="info-box-content"><span class="info-box-text">Unpaid Invoices</span><span class="info-box-number" id="md_unpaid_invoices">0</span></div></div></div>
                                                <div class="col-md-3 col-sm-6"><div class="info-box"><span class="info-box-icon bg-red"><i class="fa fa-exclamation-triangle"></i></span><div class="info-box-content"><span class="info-box-text">Low Stock Alerts</span><span class="info-box-number" id="md_low_stock">0</span></div></div></div>
                                                <div class="col-md-3 col-sm-6"><div class="info-box"><span class="info-box-icon bg-orange"><i class="fa fa-clock-o"></i></span><div class="info-box-content"><span class="info-box-text">Expiry Risk (&lt;30d)</span><span class="info-box-number display_currency" data-currency_symbol="true" id="md_expiry_value">0</span><span class="progress-description" id="md_expiry_count">0 items</span></div></div></div>
                                                <div class="col-md-3 col-sm-6"><div class="info-box"><span class="info-box-icon bg-purple"><i class="fa fa-star"></i></span><div class="info-box-content"><span class="info-box-text">Top Product</span><span class="info-box-number" id="md_top_product" style="font-size:14px;">N/A</span></div></div></div>
                                            </div>
                                            <div class="callout callout-info" style="margin-top:10px;">
                                                <i class="fa fa-lightbulb-o"></i>
                                                <strong>Insights:</strong> Check <a href="{{ action([\App\Http\Controllers\ReportController::class, 'getDeadStockReport']) }}">Dead Stock Report</a> to clear tied-up capital.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /digest --}}

                    {{-- ══ 8. AUDIT & RISK ══════════════════════════════════════ --}}
                    <div class="tab-pane" id="tab_audit">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="box box-danger">
                                    <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-shield"></i> Leakage Audit</h3></div>
                                    <div class="box-body" id="leakage-content">
                                        <div class="skeleton"></div><div class="skeleton"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box box-warning">
                                    <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-hospital-o"></i> Hospital Ops</h3></div>
                                    <div class="box-body" id="hospital-efficiency-content">
                                        <div class="skeleton"></div><div class="skeleton"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box box-default">
                                    <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-gears"></i> AI Connectivity</h3></div>
                                    <div class="box-body">
                                        {!! Form::open(['url' => route('dashboard.bi.settings.save'), 'method' => 'post']) !!}
                                        <div class="form-group">
                                            <label>Gemini API Key:</label>
                                            <input type="password" name="gemini_api_key" class="form-control" placeholder="Paste your Gemini API Key here…" value="{{ session('business.common_settings.gemini_api_key') ?? '' }}">
                                            <p class="help-block" style="font-size:11px;">Required for AI features. Get one free from Google AI Studio.</p>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm btn-block">Save API Key</button>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="box box-solid">
                                    <div class="box-header with-border"><h3 class="box-title">Procurement &amp; Inventory Actions</h3></div>
                                    <div class="box-body">
                                        <button class="btn btn-primary" onclick="runAutoProcure()"><i class="fa fa-cog"></i> Run Auto-Procurement Scan</button>
                                        <p class="text-muted" style="margin-top:10px;">Generates draft Purchase Orders for all products below alert thresholds.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="box box-solid">
                                    <div class="box-header with-border"><h3 class="box-title">Procurement Log</h3></div>
                                    <div class="box-body" id="audit-log"><p class="text-muted">Click 'Run Scan' to process pending alerts.</p></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-solid">
                                    <div class="box-header with-border"><h3 class="box-title">Logistics Risk Analytics</h3></div>
                                    <div class="box-body table-responsive" id="logistics-risk-content">
                                        <p class="text-center text-muted">Loading route reliability data…</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /audit --}}

                </div>{{-- tab-content --}}
            </div>{{-- nav-tabs-custom --}}
        </div>
    </div>

</section>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(function() {

    /* ── helpers ──────────────────────────────────────────────────── */
    const COLORS = ['#3c8dbc','#00a65a','#f39c12','#dd4b39','#605ca8','#00c0ef','#001F3F','#39CCCC','#FF851B','#85144b'];
    function kes(n){ return 'KES '+Number(n).toLocaleString('en-KE',{maximumFractionDigits:0}); }

    /* ── Overview charts (static, from PHP) ───────────────────────── */
    const chartData = {
        labels:   @json($months),
        revenue:  @json($revenue_chart),
        parcels:  @json($parcel_chart),
        payments: @json($payment_methods),
        routes:   @json($top_routes)
    };

    new Chart(document.getElementById('mainTrendChart'), {
        type:'line',
        data:{
            labels:chartData.labels,
            datasets:[
                {label:'Revenue (KES)',data:chartData.revenue,borderColor:'#3c8dbc',backgroundColor:'rgba(60,141,188,.1)',yAxisID:'y',fill:true,tension:.3},
                {label:'Parcels',data:chartData.parcels,borderColor:'#00a65a',borderDash:[5,5],yAxisID:'y1',tension:.3}
            ]
        },
        options:{responsive:true,maintainAspectRatio:false,scales:{y:{type:'linear',position:'left'},y1:{type:'linear',position:'right',grid:{drawOnChartArea:false}}}}
    });

    new Chart(document.getElementById('paymentMethodChart'), {
        type:'doughnut',
        data:{labels:chartData.payments.map(p=>p.method.toUpperCase()),datasets:[{data:chartData.payments.map(p=>p.total),backgroundColor:COLORS}]},
        options:{cutout:'65%'}
    });

    new Chart(document.getElementById('topRoutesChart'), {
        type:'bar',
        data:{labels:chartData.routes.map(r=>r.route),datasets:[{label:'Revenue',data:chartData.routes.map(r=>r.total),backgroundColor:'#3c8dbc'}]},
        options:{indexAxis:'y',responsive:true,maintainAspectRatio:false}
    });

    /* ── Chart instances (lazy) ────────────────────────────────────── */
    let salesTrendChart, hourlyChart, topProductsChart, categoryChart;
    let topCustomersChart, custGrowthChart;
    let finTrendChart, expenseChart;
    let turnoverChart;

    /* ────────────────────────────────────────────────────────────────
       SALES ANALYTICS
    ──────────────────────────────────────────────────────────────── */
    let salesLoaded = false;
    let currentDays = 30;

    function loadSales(days) {
        currentDays = days;
        $('#sales-loading').show();
        $.getJSON('/bi-api/sales?days='+days, function(d) {
            $('#sales-loading').hide();

            // KPIs
            $('#sa_total_revenue').text(kes(d.total_revenue));
            $('#sa_total_orders').text(Number(d.total_orders).toLocaleString());
            $('#sa_avg_basket').text(kes(d.avg_basket));
            $('#sa_top_category').text(d.top_category || '—');

            // Daily trend
            const tLabels = d.daily_trend.map(r=>r.day);
            const tData   = d.daily_trend.map(r=>r.total);
            if (salesTrendChart) salesTrendChart.destroy();
            salesTrendChart = new Chart(document.getElementById('salesTrendChart'), {
                type:'line',
                data:{labels:tLabels,datasets:[{label:'Daily Revenue',data:tData,borderColor:'#3c8dbc',backgroundColor:'rgba(60,141,188,.1)',fill:true,tension:.3}]},
                options:{responsive:true,maintainAspectRatio:false}
            });

            // Hourly
            const hLabels = d.hourly.map(r=>r.hour+'h');
            const hData   = d.hourly.map(r=>r.total);
            if (hourlyChart) hourlyChart.destroy();
            hourlyChart = new Chart(document.getElementById('hourlyChart'), {
                type:'bar',
                data:{labels:hLabels,datasets:[{label:'Revenue',data:hData,backgroundColor:'#605ca8'}]},
                options:{responsive:true,maintainAspectRatio:false}
            });

            // Top products
            if (topProductsChart) topProductsChart.destroy();
            topProductsChart = new Chart(document.getElementById('topProductsChart'), {
                type:'bar',
                data:{labels:d.top_products.map(p=>p.name),datasets:[{label:'Revenue',data:d.top_products.map(p=>p.total),backgroundColor:COLORS}]},
                options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}
            });

            // Categories
            if (categoryChart) categoryChart.destroy();
            categoryChart = new Chart(document.getElementById('categoryChart'), {
                type:'doughnut',
                data:{labels:d.categories.map(c=>c.name),datasets:[{data:d.categories.map(c=>c.total),backgroundColor:COLORS}]},
                options:{cutout:'55%',responsive:true,maintainAspectRatio:false}
            });
        }).fail(function(){ $('#sales-loading').hide(); });
    }

    $('a[href="#tab_sales"]').on('shown.bs.tab', function(){
        if (!salesLoaded) { loadSales(30); salesLoaded=true; }
    });

    $(document).on('click','.period-btn', function(){
        $('.period-btn').removeClass('active');
        $(this).addClass('active');
        salesLoaded = true;
        loadSales($(this).data('days'));
    });

    /* ────────────────────────────────────────────────────────────────
       CUSTOMER INTELLIGENCE
    ──────────────────────────────────────────────────────────────── */
    let custLoaded = false;

    function loadCustomers() {
        $.getJSON('/bi-api/customers', function(d) {
            $('#ci_new').text(d.new_customers);
            $('#ci_returning').text(d.returning_customers);
            $('#ci_total').text(d.total_buying);
            const ret = d.total_buying > 0 ? Math.round((d.returning_customers/d.total_buying)*100) : 0;
            $('#ci_retention').text(ret+'%');

            // Top customers bar
            if (topCustomersChart) topCustomersChart.destroy();
            topCustomersChart = new Chart(document.getElementById('topCustomersChart'), {
                type:'bar',
                data:{labels:d.top_customers.map(c=>c.name||'Walk-in'),datasets:[{label:'Total Spend',data:d.top_customers.map(c=>c.total),backgroundColor:'#3c8dbc'}]},
                options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}
            });

            // Growth line
            if (custGrowthChart) custGrowthChart.destroy();
            custGrowthChart = new Chart(document.getElementById('custGrowthChart'), {
                type:'line',
                data:{labels:d.growth.map(g=>g.month),datasets:[{label:'New Customers',data:d.growth.map(g=>g.count),borderColor:'#00a65a',backgroundColor:'rgba(0,166,90,.1)',fill:true,tension:.3}]},
                options:{responsive:true,maintainAspectRatio:false}
            });

            // Detail table
            let rows = '';
            d.top_customers.forEach(function(c,i){
                const avg = c.orders > 0 ? kes(c.total/c.orders) : '—';
                rows += `<tr><td>${i+1}</td><td>${c.name||'Walk-in'}</td><td>${c.orders}</td><td>${kes(c.total)}</td><td>${avg}</td></tr>`;
            });
            $('#top-customers-tbody').html(rows || '<tr><td colspan="5" class="text-center text-muted">No data</td></tr>');
        });
    }

    $('a[href="#tab_customers"]').on('shown.bs.tab', function(){
        if (!custLoaded) { loadCustomers(); custLoaded=true; }
    });

    /* ────────────────────────────────────────────────────────────────
       FINANCIAL KPIs
    ──────────────────────────────────────────────────────────────── */
    let finLoaded = false;

    function loadFinancial() {
        $.getJSON('/bi-api/financial', function(d) {
            $('#fk_revenue').text(kes(d.ytd_revenue));
            $('#fk_gp').text(kes(d.ytd_gross_profit));
            $('#fk_gp_margin').text(d.gp_margin+'%');
            $('#fk_net_margin').text(d.net_margin+'%');

            if (finTrendChart) finTrendChart.destroy();
            finTrendChart = new Chart(document.getElementById('finTrendChart'), {
                type:'line',
                data:{
                    labels:d.monthly.map(m=>m.month),
                    datasets:[
                        {label:'Revenue',data:d.monthly.map(m=>m.revenue),borderColor:'#3c8dbc',tension:.3,fill:false},
                        {label:'Gross Profit',data:d.monthly.map(m=>m.gross_profit),borderColor:'#00a65a',tension:.3,fill:false},
                        {label:'Net Profit',data:d.monthly.map(m=>m.net_profit),borderColor:'#f39c12',tension:.3,fill:false}
                    ]
                },
                options:{responsive:true,maintainAspectRatio:false}
            });

            if (expenseChart) expenseChart.destroy();
            expenseChart = new Chart(document.getElementById('expenseChart'), {
                type:'doughnut',
                data:{labels:d.expenses.map(e=>e.category),datasets:[{data:d.expenses.map(e=>e.total),backgroundColor:COLORS}]},
                options:{cutout:'55%',responsive:true,maintainAspectRatio:false}
            });
        });
    }

    $('a[href="#tab_financial"]').on('shown.bs.tab', function(){
        if (!finLoaded) { loadFinancial(); finLoaded=true; }
    });

    /* ────────────────────────────────────────────────────────────────
       INVENTORY INTELLIGENCE
    ──────────────────────────────────────────────────────────────── */
    let invLoaded = false;

    function loadInventory() {
        $.getJSON('/bi-api/inventory', function(d) {
            $('#iv_reorder').text(d.reorder_alerts.length);
            $('#iv_slow').text(d.slow_movers.length);
            $('#iv_dead').text(d.dead_stock.length);
            const topT = d.top_turnover[0];
            $('#iv_top_turnover').text(topT ? topT.name.substring(0,18)+'…' : '—');

            // Turnover chart
            if (turnoverChart) turnoverChart.destroy();
            turnoverChart = new Chart(document.getElementById('turnoverChart'), {
                type:'bar',
                data:{labels:d.top_turnover.map(p=>p.name),datasets:[{label:'Turnover Rate',data:d.top_turnover.map(p=>p.turnover_rate),backgroundColor:'#3c8dbc'}]},
                options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}}}
            });

            // Reorder table
            let rRows = '';
            d.reorder_alerts.forEach(function(p){
                rRows += `<tr><td>${p.name}</td><td class="text-red"><strong>${p.qty_available}</strong></td><td>${p.alert_quantity}</td><td>${kes(p.unit_price)}</td></tr>`;
            });
            $('#reorder-tbody').html(rRows || '<tr><td colspan="4" class="text-center text-muted">None — stock levels OK</td></tr>');

            // Slow movers
            let sRows = '';
            d.slow_movers.forEach(function(p){ sRows += `<tr><td>${p.name}</td><td>${p.category||'—'}</td><td>${p.qty_available}</td></tr>`; });
            $('#slow-movers-tbody').html(sRows || '<tr><td colspan="3" class="text-center text-muted">None</td></tr>');

            // Dead stock
            let dRows = '';
            d.dead_stock.forEach(function(p){ dRows += `<tr><td>${p.name}</td><td>${p.category||'—'}</td><td>${p.qty_available}</td></tr>`; });
            $('#dead-stock-tbody').html(dRows || '<tr><td colspan="3" class="text-center text-muted">None</td></tr>');
        });
    }

    $('a[href="#tab_inventory"]').on('shown.bs.tab', function(){
        if (!invLoaded) { loadInventory(); invLoaded=true; }
    });

    /* ────────────────────────────────────────────────────────────────
       AI ADVISOR
    ──────────────────────────────────────────────────────────────── */
    async function fetchInsights(refresh) {
        const container = $('#insights-container');
        container.html('<div class="skeleton" style="height:80px;"></div><div class="skeleton" style="height:80px;"></div>');
        try {
            const res  = await fetch('/bi-api/insights'+(refresh?'?refresh=1':''));
            const data = await res.json();
            container.empty();
            data.insights.forEach(function(text){
                const tag       = (text.match(/\[(.*?)\]/)||['','INFO'])[1];
                const cleanText = text.replace(/\[.*?\]/,'').trim();
                container.append(
                    `<div class="insight-item">
                        <span class="label badge-${tag}">${tag}</span>
                        <p style="margin-top:5px;">${cleanText}</p>
                        <button class="btn btn-xs btn-default" onclick="generateCampaign('${cleanText.replace(/'/g,"\\'")}')">
                            <i class="fa fa-whatsapp"></i> Draft Campaign
                        </button>
                    </div>`
                );
            });
        } catch(e){ container.html('<p class="text-red">Error loading insights.</p>'); }
    }

    async function fetchPredictions() {
        const container = $('#predictions-container');
        try {
            const res  = await fetch('/bi-api/predictions');
            const data = await res.json();
            container.empty();
            [
                {title:'Revenue Forecast',  ...data.revenue_forecast,    color:'aqua'},
                {title:'Churn Risk',        ...data.churn_risk,           color:'yellow'},
                {title:'Inventory Reorder', ...data.inventory_suggestions,color:'purple'}
            ].forEach(function(c){
                container.append(
                    `<div class="col-md-4">
                        <div class="small-box bg-${c.color}">
                            <div class="inner">
                                <h4>${c.title}</h4>
                                <p style="font-size:18px;font-weight:bold;">${c.value}</p>
                                <div class="progress sm" style="background:rgba(0,0,0,.1)">
                                    <div class="progress-bar progress-bar-white" style="width:${c.confidence}%"></div>
                                </div>
                                <small>${c.reasoning}</small>
                            </div>
                            <div class="icon"><i class="fa fa-line-chart"></i></div>
                        </div>
                    </div>`
                );
            });
        } catch(e){ container.html('<p class="col-md-12">Predictions currently unavailable.</p>'); }
    }

    $('a[href="#tab_ai"]').on('shown.bs.tab', function(){
        fetchInsights(false);
        fetchPredictions();
    });

    /* ────────────────────────────────────────────────────────────────
       AUDIT & RISK (Deep Intelligence)
    ──────────────────────────────────────────────────────────────── */
    let auditLoaded = false;

    async function fetchDeepIntelligence() {
        try {
            const res  = await fetch('/bi-api/deep-intelligence');
            const data = await res.json();
            $('#leakage-content').html(
                `<h3 class="text-red">KES ${data.revenue_leakage.estimated_loss.toLocaleString()}</h3>
                 <p>Estimated loss from <b>${data.revenue_leakage.unbilled_labs}</b> unbilled labs and <b>${data.revenue_leakage.unbilled_imaging}</b> imaging orders.</p>`
            );
            $('#hospital-efficiency-content').html(
                `<h3 class="text-orange">${data.hospital_efficiency.bed_occupancy_percent}%</h3>
                 <p>Current Bed Occupancy. Avg consultation time: <b>${data.hospital_efficiency.avg_consultation_mins} mins</b>.</p>`
            );
            let riskHtml = `<table class="table table-striped"><thead><tr><th>Route</th><th>Total</th><th>Failures</th><th>Risk</th></tr></thead><tbody>`;
            data.logistics_risk.route_risk.forEach(function(r){
                riskHtml += `<tr><td>${r.route}</td><td>${r.total}</td><td class="${r.failures>0?'text-red':'text-green'}">${r.failures}</td>
                              <td><span class="label ${r.failures>0?'bg-red':'bg-green'}">${r.failures>0?'High':'Low'}</span></td></tr>`;
            });
            riskHtml += `</tbody></table>`;
            $('#logistics-risk-content').html(riskHtml);
        } catch(e){ console.error(e); }
    }

    $('a[href="#tab_audit"]').on('shown.bs.tab', function(){
        if (!auditLoaded) { fetchDeepIntelligence(); auditLoaded=true; }
    });

    async function runAutoProcure() {
        $('#audit-log').html('<i class="fa fa-spin fa-spinner"></i> Scanning stock levels…');
        try {
            const res = await $.post('/bi-api/run-procurement',{_token:$('meta[name="csrf-token"]').attr('content')});
            let html = `<div class="alert alert-${res.success?'success':'danger'}">${res.msg}</div>`;
            if (res.items && res.items.length) {
                html += `<table class="table table-condensed table-bordered"><thead><tr><th>Product</th><th>In Stock</th><th>Alert At</th><th>Reorder Qty</th><th>Est. Cost</th></tr></thead><tbody>`;
                res.items.forEach(function(i){ html += `<tr><td>${i.name}</td><td class="text-red">${i.in_stock}</td><td>${i.alert_at}</td><td><strong>${i.reorder_qty}</strong></td><td>KES ${i.line_value.toLocaleString()}</td></tr>`; });
                html += `</tbody></table>`;
            }
            $('#audit-log').html(html);
        } catch(e){ $('#audit-log').html('<div class="alert alert-danger">Procurement scan failed.</div>'); }
    }
    window.runAutoProcure = runAutoProcure;

    /* ────────────────────────────────────────────────────────────────
       MORNING DIGEST
    ──────────────────────────────────────────────────────────────── */
    function loadMorningDigest() {
        $('#morning_digest_data').hide();
        $('#morning_digest_loader').show();
        $.ajax({
            url: '{{ route("home.morning_digest") }}',
            success: function(data){
                $('#morning_digest_loader').hide();
                $('#morning_digest_data').show();
                $('#md_today_sales').text(parseFloat(data.today_sales).toFixed(2));
                $('#md_yesterday_sales').text(parseFloat(data.yesterday_sales).toFixed(2));
                $('#md_total_customers').text(data.total_customers_today||0);
                $('#md_cash_in_register').text(parseFloat(data.current_cash_in_register).toFixed(2));
                $('#md_unpaid_invoices').text(data.unpaid_invoices_count||0);
                $('#md_low_stock').text(data.low_stock_alerts_count||0);
                $('#md_expiry_value').text(parseFloat(data.expiry_risk_value).toFixed(2));
                $('#md_expiry_count').text('('+data.expiry_item_count+' items)');
                $('#md_top_product').text(data.top_product_name||'N/A');
                __currency_convert_recursively($('#morning_digest_data'));
            },
            error: function(){ $('#morning_digest_loader').html('<span class="text-danger">Failed to load morning digest.</span>'); }
        });
    }
    $('a[href="#tab_digest"]').on('shown.bs.tab', loadMorningDigest);
    $('#refresh_morning_digest').click(loadMorningDigest);

    /* ────────────────────────────────────────────────────────────────
       Q&A FORM
    ──────────────────────────────────────────────────────────────── */
    $('#qa-form').submit(async function(e){
        e.preventDefault();
        const btn = $('#qa-btn'), out = $('#qa-response');
        out.html('<div class="text-center"><i class="fa fa-spin fa-refresh"></i> Apex AI is analysing your data…</div>');
        btn.prop('disabled',true);
        try {
            const res = await $.post('/bi-api/ask',{question:$('#qa-input').val(),_token:$('meta[name="csrf-token"]').attr('content')});
            out.html(res.answer);
        } catch(e){ out.html('<p class="text-red">Communication error. Please try again.</p>'); }
        finally { btn.prop('disabled',false); }
    });

    /* ────────────────────────────────────────────────────────────────
       GLOBAL REFRESH
    ──────────────────────────────────────────────────────────────── */
    $('#refresh-bi').click(function(){
        salesLoaded=false; custLoaded=false; finLoaded=false; invLoaded=false; auditLoaded=false;
        // re-trigger whichever tab is active
        const active = $('.nav-tabs .active a').attr('href');
        if (active==='#tab_sales')     { loadSales(currentDays); salesLoaded=true; }
        if (active==='#tab_customers') { loadCustomers(); custLoaded=true; }
        if (active==='#tab_financial') { loadFinancial(); finLoaded=true; }
        if (active==='#tab_inventory') { loadInventory(); invLoaded=true; }
        if (active==='#tab_ai')        { fetchInsights(true); fetchPredictions(); }
        if (active==='#tab_digest')    { loadMorningDigest(); }
        if (active==='#tab_audit')     { fetchDeepIntelligence(); auditLoaded=true; }
    });

    /* Campaign generator exposed globally for inline onclick */
    window.generateCampaign = function(text){ alert('Campaign draft for: '+text.substring(0,60)+'…\n(hook to WhatsApp/SMS module)'); };

    /* ────────────────────────────────────────────────────────────────
       AI INTELLIGENCE PANELS
    ──────────────────────────────────────────────────────────────── */
    const AI_ENDPOINTS = {
        sales:        '/bi-api/ai/sales',
        customers:    '/bi-api/ai/customers',
        financial:    '/bi-api/ai/financial',
        procurement:  '/bi-api/ai/procurement',
        'inv-health': '/bi-api/ai/inventory',
    };
    const AI_LOADED = { sales:false, customers:false, financial:false, procurement:false, 'inv-health':false };

    function aiLoadingHTML(){ return '<p class="ai-placeholder"><i class="fa fa-spinner fa-spin"></i> Gemini AI is analysing your data…</p>'; }

    function triggerAI(target, refresh){
        const panel   = $('#ai-' + (target==='inv-health'?'inv-health':target) + '-panel');
        const spinner = $('#ai-' + (target==='inv-health'?'inv-health':target) + '-spinner');
        const refBtn  = $('[data-target="'+target+'"].ai-refresh-btn');
        const url     = AI_ENDPOINTS[target] + (refresh ? '?refresh=1' : '');

        if (!refresh && AI_LOADED[target]) return;

        panel.html(aiLoadingHTML());
        spinner.show();
        refBtn.hide().prop('disabled', true);

        $.ajax({
            url: url,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(d){
                spinner.hide();
                refBtn.show().prop('disabled', false);
                AI_LOADED[target] = true;
                if (d && d.error) {
                    panel.html('<div class="alert alert-warning" style="margin:0;"><strong>AI returned an error:</strong> '+d.error+'<br><small>'+( d.raw||'' )+'</small></div>');
                } else {
                    AI_RENDERERS[target](d, panel);
                }
            },
            error: function(xhr){
                spinner.hide();
                refBtn.show().prop('disabled', false);
                let msg = 'HTTP '+xhr.status+' — ';
                try { const j=JSON.parse(xhr.responseText); msg += j.message||j.error||xhr.responseText.substring(0,200); }
                catch(e){ msg += xhr.responseText.substring(0,300)||'No response body'; }
                panel.html('<div class="alert alert-danger" style="margin:0;"><strong>AI endpoint error:</strong><br><small>'+msg+'</small></div>');
            }
        });
    }

    // Auto-load AI when each tab is opened
    $('a[href="#tab_sales"]').on('shown.bs.tab',     function(){ triggerAI('sales', false); });
    $('a[href="#tab_customers"]').on('shown.bs.tab', function(){ triggerAI('customers', false); });
    $('a[href="#tab_financial"]').on('shown.bs.tab', function(){ triggerAI('financial', false); });
    $('a[href="#tab_inventory"]').on('shown.bs.tab', function(){ triggerAI('procurement', false); triggerAI('inv-health', false); });

    // Refresh buttons
    $(document).on('click', '.ai-refresh-btn', function(){
        triggerAI($(this).data('target'), true);
    });


    function renderAISales(d, panel){
        let html = '';
        // Summary
        if (d.sales_summary) html += `<div class="callout callout-info" style="margin-bottom:12px;"><i class="fa fa-lightbulb-o"></i> ${d.sales_summary}</div>`;
        // Anomalies
        if (d.anomalies && d.anomalies.length) {
            html += `<h5><i class="fa fa-exclamation-triangle text-orange"></i> Sales Anomalies Detected</h5><div class="row">`;
            d.anomalies.forEach(function(a){
                html += `<div class="col-md-4"><div class="callout callout-warning">
                    <strong>${a.date}</strong><br>Revenue: KES ${Number(a.revenue).toLocaleString()}
                    <br><span class="text-red">${a.deviation_pct}% deviation</span>
                    <br><small><em>${a.likely_cause}</em></small>
                    <br><span class="label label-warning">${a.recommendation}</span></div></div>`;
            });
            html += `</div>`;
        }
        // Margin insights
        if (d.top_margin_products && d.top_margin_products.length) {
            html += `<h5 style="margin-top:12px;"><i class="fa fa-trophy text-green"></i> High-Margin Stars</h5><div class="row">`;
            d.top_margin_products.slice(0,3).forEach(function(p){
                html += `<div class="col-md-4"><div class="callout callout-success">
                    <strong>${p.product}</strong>
                    <br><span class="label label-success">${p.margin_pct}% margin</span>
                    <br><small>${p.insight}</small>
                    <br><i class="fa fa-arrow-right"></i> ${p.pricing_suggestion}</div></div>`;
            });
            html += `</div>`;
        }
        if (d.low_margin_warnings && d.low_margin_warnings.length) {
            html += `<h5 style="margin-top:8px;"><i class="fa fa-warning text-red"></i> Low-Margin Warnings</h5><div class="row">`;
            d.low_margin_warnings.slice(0,3).forEach(function(p){
                html += `<div class="col-md-4"><div class="callout callout-danger">
                    <strong>${p.product}</strong> &mdash; <span class="text-red">${p.margin_pct}% margin</span>
                    <br><small>${p.risk}</small>
                    <br><i class="fa fa-arrow-right"></i> ${p.action}</div></div>`;
            });
            html += `</div>`;
        }
        if (d.best_selling_day) html += `<p><i class="fa fa-calendar text-blue"></i> <strong>Best day to push sales:</strong> ${d.best_selling_day}</p>`;
        panel.html(html || '<p class="text-muted">No insights available — add more sales data.</p>');
    }

    function renderAICustomers(d, panel){
        let html = '';
        if (d.retention_summary) html += `<div class="callout callout-info"><i class="fa fa-lightbulb-o"></i> ${d.retention_summary}</div>`;
        // Segments
        if (d.segments) {
            html += `<h5><i class="fa fa-users text-blue"></i> Customer Segments</h5><div class="row">`;
            [['vip','success','crown'],['regular','info','user'],['at_risk','warning','exclamation'],['lost','danger','times']].forEach(function(s){
                const seg = d.segments[s[0]];
                if (seg) html += `<div class="col-md-3"><div class="callout callout-${s[1]}"><i class="fa fa-${s[2]}"></i> <strong>${s[0].replace('_',' ').toUpperCase()}</strong><br><small>${typeof seg==='object'?seg.description||'':seg}</small></div></div>`;
            });
            html += `</div>`;
        }
        // Churn risk
        if (d.churn_actions && d.churn_actions.length) {
            html += `<h5 style="margin-top:10px;"><i class="fa fa-exclamation-circle text-red"></i> Churn Risk — Take Action Now</h5>
            <table class="table table-condensed table-bordered table-hover">
            <thead><tr><th>Customer</th><th>Last Purchase</th><th>Lifetime Value</th><th>Recommended Action</th></tr></thead><tbody>`;
            d.churn_actions.slice(0,8).forEach(function(c){
                html += `<tr>
                    <td><strong>${c.customer_name}</strong></td>
                    <td><span class="text-red">${c.days_since_purchase} days ago</span></td>
                    <td>KES ${Number(c.lifetime_value||0).toLocaleString()}</td>
                    <td>${c.recommended_action}
                        ${c.message_template?`<br><small class="text-muted"><em>"${c.message_template}"</em></small>`:''}
                    </td></tr>`;
            });
            html += `</tbody></table>`;
        }
        // Upsell
        if (d.upsell_opportunities && d.upsell_opportunities.length) {
            html += `<h5 style="margin-top:10px;"><i class="fa fa-arrow-up text-green"></i> Upsell Opportunities</h5><div class="row">`;
            d.upsell_opportunities.slice(0,3).forEach(function(u){
                html += `<div class="col-md-4"><div class="callout callout-success"><strong>${u.product}</strong><br><small>${u.insight}</small><br><i class="fa fa-arrow-right"></i> ${u.suggested_bundle_or_action}</div></div>`;
            });
            html += `</div>`;
        }
        panel.html(html || '<p class="text-muted">No insights available.</p>');
    }

    function renderAIFinancial(d, panel){
        let html = '';
        if (d.financial_summary) html += `<div class="callout callout-info"><i class="fa fa-lightbulb-o"></i> ${d.financial_summary}</div>`;
        // Cash flow forecast
        if (d.cash_flow_forecast) {
            const cf = d.cash_flow_forecast;
            html += `<div class="row"><div class="col-md-6">
                <div class="callout callout-${cf.risk_level==='high'?'danger':cf.risk_level==='medium'?'warning':'success'}">
                    <h5><i class="fa fa-line-chart"></i> 30-Day Cash Flow Forecast</h5>
                    <h4>KES ${Number(cf.next_30_days_estimate||0).toLocaleString()}</h4>
                    <span class="label label-default">Confidence: ${cf.confidence||'—'}</span>
                    <p style="margin-top:6px;"><small>${cf.key_assumptions||''}</small></p>
                    ${cf.risks?`<p class="text-red"><small><strong>Risk:</strong> ${cf.risks}</small></p>`:''}
                </div></div>`;
            // Margin trend
            if (d.margin_trend) html += `<div class="col-md-6"><div class="callout callout-${d.margin_trend.direction==='improving'?'success':d.margin_trend.direction==='declining'?'danger':'warning'}">
                <h5><i class="fa fa-percent"></i> Margin Trend: <strong>${(d.margin_trend.direction||'').toUpperCase()}</strong></h5>
                <p><small>${d.margin_trend.insight||''}</small></p>
                <p><i class="fa fa-arrow-right"></i> ${d.margin_trend.action||''}</p>
            </div></div>`;
            html += `</div>`;
        }
        // Expense alerts
        if (d.expense_alerts && d.expense_alerts.length) {
            html += `<h5><i class="fa fa-warning text-orange"></i> Expense Alerts</h5><div class="row">`;
            d.expense_alerts.forEach(function(e){
                html += `<div class="col-md-4"><div class="callout callout-warning"><strong>${e.category}</strong><br><small>${e.observation}</small><br><i class="fa fa-arrow-right"></i> ${e.recommended_action}</div></div>`;
            });
            html += `</div>`;
        }
        // Receivables
        if (d.receivables_risk) {
            const r = d.receivables_risk;
            html += `<div class="callout callout-${r.risk_level==='high'?'danger':'warning'}">
                <i class="fa fa-file-text-o"></i> <strong>Unpaid Receivables:</strong> KES ${Number(r.outstanding_amount||0).toLocaleString()}
                &nbsp; <span class="label label-${r.risk_level==='high'?'danger':'warning'}">${(r.risk_level||'').toUpperCase()} RISK</span>
                <br><small>${r.recommended_action||''}</small></div>`;
        }
        panel.html(html || '<p class="text-muted">No financial insights available.</p>');
    }

    function renderAIProcurement(d, panel){
        let html = '';
        if (d.procurement_summary) html += `<div class="callout callout-info"><i class="fa fa-lightbulb-o"></i> ${d.procurement_summary}</div>`;
        if (d.reorder_recommendations && d.reorder_recommendations.length) {
            html += `<h5><i class="fa fa-shopping-cart text-blue"></i> Reorder Recommendations</h5>
            <table class="table table-condensed table-bordered">
            <thead><tr><th>Product</th><th>Stock</th><th>Order Qty</th><th>Urgency</th><th>Reason</th></tr></thead><tbody>`;
            d.reorder_recommendations.forEach(function(r){
                const uc = r.urgency==='high'?'danger':r.urgency==='medium'?'warning':'info';
                html += `<tr><td><strong>${r.product}</strong></td><td class="text-red">${r.current_stock}</td>
                    <td><strong>${r.suggested_order_qty}</strong></td>
                    <td><span class="label label-${uc}">${(r.urgency||'').toUpperCase()}</span></td>
                    <td><small>${r.reason}</small></td></tr>`;
            });
            html += `</tbody></table>`;
        }
        if (d.dead_stock_actions && d.dead_stock_actions.length) {
            html += `<h5 style="margin-top:10px;"><i class="fa fa-times-circle text-orange"></i> Dead Stock Actions</h5><div class="row">`;
            d.dead_stock_actions.slice(0,4).forEach(function(s){
                html += `<div class="col-md-6"><div class="callout callout-warning">
                    <strong>${s.product}</strong> &mdash; Stock: ${s.stock}
                    <br>Tied Capital: KES ${Number(s.tied_capital||0).toLocaleString()}
                    <br><i class="fa fa-arrow-right"></i> <em>${s.suggested_action}</em></div></div>`;
            });
            html += `</div>`;
        }
        panel.html(html || '<p class="text-muted">All stock levels healthy — no urgent reorders needed.</p>');
    }

    function renderAIInventoryHealth(d, panel){
        let html = '';
        // Health score badge
        if (d.inventory_health_score !== undefined) {
            const score = d.inventory_health_score;
            const sc = score>=75?'success':score>=50?'warning':'danger';
            html += `<div class="callout callout-${sc}" style="display:flex;align-items:center;gap:16px;">
                <div style="font-size:36px;font-weight:bold;color:${score>=75?'#00a65a':score>=50?'#f39c12':'#dd4b39'};">${score}<small style="font-size:14px;">/100</small></div>
                <div><strong>Inventory Health Score</strong><br><small>${d.inventory_summary||''}</small></div>
            </div>`;
        } else if (d.inventory_summary) {
            html += `<div class="callout callout-info">${d.inventory_summary}</div>`;
        }
        // Capital recovery
        if (d.capital_recovery_estimate) html += `<p><i class="fa fa-money text-green"></i> <strong>Estimated Capital Recoverable:</strong> KES ${Number(d.capital_recovery_estimate).toLocaleString()}</p>`;
        // Clearance plan
        if (d.clearance_plan && d.clearance_plan.length) {
            html += `<h5><i class="fa fa-tag text-orange"></i> Clearance Plan</h5>
            <table class="table table-condensed table-bordered">
            <thead><tr><th>Product</th><th>Stock</th><th>Tied Capital</th><th>Discount %</th><th>Recovery Est.</th><th>Timeline</th></tr></thead><tbody>`;
            d.clearance_plan.forEach(function(c){
                html += `<tr><td><strong>${c.product}</strong></td><td>${c.stock}</td>
                    <td>KES ${Number(c.tied_capital||0).toLocaleString()}</td>
                    <td><span class="label label-warning">${c.suggested_discount_pct}% OFF</span></td>
                    <td class="text-green">KES ${Number(c.expected_recovery||0).toLocaleString()}</td>
                    <td><small>${c.timeline}</small></td></tr>`;
            });
            html += `</tbody></table>`;
        }
        // Expiry actions
        if (d.expiry_actions && d.expiry_actions.length) {
            html += `<h5 style="margin-top:10px;"><i class="fa fa-clock-o text-red"></i> Expiry Urgent Actions</h5><div class="row">`;
            d.expiry_actions.forEach(function(e){
                html += `<div class="col-md-4"><div class="callout callout-danger">
                    <strong>${e.product}</strong>
                    <br><span class="text-red"><i class="fa fa-warning"></i> ${e.days_left} days left</span> &mdash; Qty: ${e.qty}
                    <br><i class="fa fa-arrow-right"></i> <em>${e.urgent_action}</em></div></div>`;
            });
            html += `</div>`;
        }
        panel.html(html || '<p class="text-muted">Inventory is healthy — no urgent actions needed.</p>');
    }

    const AI_RENDERERS = {
        sales:       renderAISales,
        customers:   renderAICustomers,
        financial:   renderAIFinancial,
        procurement: renderAIProcurement,
        'inv-health':renderAIInventoryHealth,
    };

});
</script>
@endsection
