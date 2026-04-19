@extends('layouts.app')
@section('title', 'APEX BI | Artificial Intelligence Dashboard')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>APEX BI 
        <small>AI-Powered Business Intelligence <span class="label label-primary" style="font-size: 10px; margin-left: 10px;">Gemini 3.1 Pro</span></small>
    </h1>
</section>

<style>
    .kpi-card { border-radius: 8px; transition: transform 0.2s; border-top: 3px solid #ccc; }
    .kpi-card:hover { transform: translateY(-5px); }
    .kpi-val { font-size: 24px; font-weight: bold; display: block; margin: 5px 0; }
    .trend-pill { font-size: 11px; padding: 2px 6px; border-radius: 4px; }
    .heatmap-grid { display: grid; grid-template-columns: 40px repeat(24, 1fr); gap: 2px; }
    .heatmap-cell { aspect-ratio: 1; border-radius: 1px; background: #eee; }
    .badge-OPPORTUNITY { background-color: #00a65a !important; }
    .badge-WARNING { background-color: #f39c12 !important; }
    .badge-ANOMALY { background-color: #00c0ef !important; }
    .badge-GROWTH { background-color: #605ca8 !important; }
    .badge-ACTION { background-color: #dd4b39 !important; }
    .insight-item { border-left: 4px solid #3c8dbc; padding: 10px; background: #f9f9f9; margin-bottom: 10px; border-radius: 0 4px 4px 0; }
    .skeleton { background: #eee; height: 20px; margin-bottom: 5px; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
</style>

<!-- Main content -->
<section class="content">
    
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_overview" data-toggle="tab">Overview</a></li>
                    <li><a href="#tab_audit" data-toggle="tab">Audit & Risk Intelligence</a></li>
                    <li><a href="#tab_ai" data-toggle="tab">AI Assistant Lab</a></li>
                    <li class="pull-right">
                        <button class="btn btn-sm btn-default" id="refresh-bi" style="margin-top: 5px; margin-right: 10px;">
                            <i class="fa fa-refresh"></i> Refresh AI Insights
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" style="background: #f4f4f7;">
                    
                    <!-- 1. OVERVIEW TAB -->
                    <div class="tab-pane active" id="tab_overview">
                        <!-- KPI Row -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="box box-solid kpi-card" style="border-top-color: #3c8dbc;">
                                    <div class="box-body text-center">
                                        <span class="text-muted">Total Revenue (MTD)</span>
                                        <span class="kpi-val text-blue">KES {{ number_format($revenue_now, 0) }}</span>
                                        @php $rev_change = $revenue_last > 0 ? (($revenue_now - $revenue_last) / $revenue_last) * 100 : 0; @endphp
                                        <span class="trend-pill {{ $rev_change >= 0 ? 'bg-green' : 'bg-red' }}">
                                            <i class="fa fa-caret-{{ $rev_change >= 0 ? 'up' : 'down' }}"></i> {{ abs(round($rev_change, 1)) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="box box-solid kpi-card" style="border-top-color: #00a65a;">
                                    <div class="box-body text-center">
                                        <span class="text-muted">Parcels Dispatched</span>
                                        <span class="kpi-val text-green">{{ number_format($parcels_now) }}</span>
                                        @php $prc_change = $parcels_last > 0 ? (($parcels_now - $parcels_last) / $parcels_last) * 100 : 0; @endphp
                                        <span class="trend-pill {{ $prc_change >= 0 ? 'bg-green' : 'bg-red' }}">
                                            <i class="fa fa-caret-{{ $prc_change >= 0 ? 'up' : 'down' }}"></i> {{ abs(round($prc_change, 1)) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="box box-solid kpi-card" style="border-top-color: #f39c12;">
                                    <div class="box-body text-center">
                                        <span class="text-muted">New Customers</span>
                                        <span class="kpi-val text-yellow">{{ number_format($customers_now) }}</span>
                                        @php $cus_change = $customers_last > 0 ? (($customers_now - $customers_last) / $customers_last) * 100 : 0; @endphp
                                        <span class="trend-pill {{ $cus_change >= 0 ? 'bg-green' : 'bg-red' }}">
                                            <i class="fa fa-caret-{{ $cus_change >= 0 ? 'up' : 'down' }}"></i> {{ abs(round($cus_change, 1)) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="box box-solid kpi-card" style="border-top-color: #dd4b39;">
                                    <div class="box-body text-center">
                                        <span class="text-muted">Low Stock Alerts</span>
                                        <span class="kpi-val text-red">{{ $low_stock_count }}</span>
                                        <small class="text-muted">Requiring reorder</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Revenue vs Parcel Trends</h3>
                                    </div>
                                    <div class="box-body">
                                        <canvas id="mainTrendChart" style="height: 250px;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Payment Methods</h3>
                                    </div>
                                    <div class="box-body">
                                        <canvas id="paymentMethodChart" style="height: 250px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Sales Heatmap (7 Days × 24H)</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="heatmap-grid" id="sales-heatmap">
                                            <!-- Labels -->
                                            <div></div>
                                            @for($i=0; $i<24; $i++) <div style="text-align:center; font-size:9px; color:#999">{{$i}}h</div> @endfor
                                            
                                            @php $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; @endphp
                                            @foreach($days as $idx => $day)
                                                <div style="font-size:10px; color:#666">{{ $day }}</div>
                                                @for($h=0; $h<24; $h++)
                                                    @php 
                                                        $match = $heatmap_raw->where('day', $idx+1)->where('hour', $h)->first();
                                                        $count = $match ? $match->count : 0;
                                                        $opacity = min($count / 10, 1);
                                                    @endphp
                                                    <div class="heatmap-cell" style="background: rgba(60, 141, 188, {{ $opacity }});" title="{{ $count }} txns"></div>
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
                                        <canvas id="topRoutesChart" style="height: 180px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. AUDIT & RISK TAB -->
                    <div class="tab-pane" id="tab_audit">
                        <div class="row">
                            <!-- Revenue Leakage -->
                            <div class="col-md-4">
                                <div class="box box-danger">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-shield"></i> Leakage Audit</h3>
                                    </div>
                                    <div class="box-body" id="leakage-content">
                                        <div class="skeleton"></div>
                                        <div class="skeleton"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Hospital Efficiency -->
                            <div class="col-md-4">
                                <div class="box box-warning">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-hospital-o"></i> Hospital Ops</h3>
                                    </div>
                                    <div class="box-body" id="hospital-efficiency-content">
                                        <div class="skeleton"></div>
                                        <div class="skeleton"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- AI Settings -->
                            <div class="col-md-4">
                                <div class="box box-default">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-gears"></i> AI Connectivity</h3>
                                    </div>
                                    <div class="box-body">
                                        {!! Form::open(['url' => route('dashboard.bi.settings.save'), 'method' => 'post']) !!}
                                        <div class="form-group">
                                            <label>Gemini API Key:</label>
                                            <input type="password" name="gemini_api_key" class="form-control" placeholder="Paste your Gemini API Key here..." value="{{ session('business.common_settings.gemini_api_key') ?? '' }}">
                                            <p class="help-block" style="font-size: 11px;">Required for AI features. Get one from Google AI Studio.</p>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm btn-block">Save API Key</button>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Logistics Risk Analytics</h3>
                                    </div>
                                    <div class="box-body table-responsive" id="logistics-risk-content">
                                        <p class="text-center">Loading route reliability data...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. AI LAB TAB -->
                    <div class="tab-pane" id="tab_ai">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="box box-primary direct-chat direct-chat-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Apex AI Business Assistant</h3>
                                    </div>
                                    <div class="box-body" style="padding: 15px;">
                                        <div id="qa-response" style="min-height: 200px; background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
                                            <i>Ask me anything about your revenue, routes, or hospital operations.</i>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <form id="qa-form">
                                            <div class="input-group">
                                                <input type="text" id="qa-input" placeholder="e.g. Which route has the most failures?" class="form-control">
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
                                    </div>
                                    <div class="box-body" id="insights-container">
                                        <div class="skeleton" style="height: 80px;"></div>
                                        <div class="skeleton" style="height: 80px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h4 style="margin-left: 5px;">Predictive Recommendations</h4>
                                <div id="predictions-container" class="row">
                                    <div class="col-md-4"><div class="box box-solid skeleton" style="height: 150px;"></div></div>
                                    <div class="col-md-4"><div class="box box-solid skeleton" style="height: 150px;"></div></div>
                                    <div class="col-md-4"><div class="box box-solid skeleton" style="height: 150px;"></div></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</section>

@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // --- Chart Data ---
        const chartData = {
            labels: @json($months),
            revenue: @json($revenue_chart),
            parcels: @json($parcel_chart),
            payments: @json($payment_methods),
            routes: @json($top_routes)
        };

        // 1. Revenue vs Parcels Line Chart
        new Chart(document.getElementById('mainTrendChart'), {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Revenue (KES)',
                    data: chartData.revenue,
                    borderColor: '#3c8dbc',
                    backgroundColor: 'rgba(60, 141, 188, 0.1)',
                    yAxisID: 'y',
                    fill: true,
                    tension: 0.3
                }, {
                    label: 'Parcels',
                    data: chartData.parcels,
                    borderColor: '#00a65a',
                    borderDash: [5, 5],
                    yAxisID: 'y1',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { type: 'linear', position: 'left' },
                    y1: { type: 'linear', position: 'right', grid: { drawOnChartArea: false } }
                }
            }
        });

        // 2. Payments Pie
        new Chart(document.getElementById('paymentMethodChart'), {
            type: 'doughnut',
            data: {
                labels: chartData.payments.map(p => p.method.toUpperCase()),
                datasets: [{
                    data: chartData.payments.map(p => p.total),
                    backgroundColor: ['#3c8dbc', '#00a65a', '#f39c12', '#dd4b39', '#605ca8']
                }]
            },
            options: { cutout: '65%' }
        });

        // 3. Routes Horizontal Bar
        new Chart(document.getElementById('topRoutesChart'), {
            type: 'bar',
            data: {
                labels: chartData.routes.map(r => r.route),
                datasets: [{
                    label: 'Revenue',
                    data: chartData.routes.map(r => r.total),
                    backgroundColor: '#3c8dbc'
                }]
            },
            options: { indexAxis: 'y' }
        });

        // --- AI API Logic ---
        async function loadAI() {
            fetchInsights();
            fetchDeepIntelligence();
            fetchPredictions();
        }

        async function fetchInsights(refresh = false) {
            const container = $('#insights-container');
            try {
                const res = await fetch(`/api/bi/insights${refresh ? '?refresh=1' : ''}`);
                const data = await res.json();
                container.empty();
                data.insights.forEach(text => {
                    const tag = text.match(/\[(.*?)\]/)?.[1] || 'INFO';
                    const cleanText = text.replace(/\[.*?\]/, '').trim();
                    container.append(`
                        <div class="insight-item">
                            <span class="label badge-${tag}">${tag}</span>
                            <p style="margin-top:5px;">${cleanText}</p>
                        </div>
                    `);
                });
            } catch (e) { container.html('<p class="text-red">Error loading insights.</p>'); }
        }

        async function fetchDeepIntelligence() {
            try {
                const res = await fetch('/api/bi/deep-intelligence');
                const data = await res.json();

                $('#leakage-content').html(`
                    <h3 class="text-red">KES ${data.revenue_leakage.estimated_loss.toLocaleString()}</h3>
                    <p>Estimated loss from <b>${data.revenue_leakage.unbilled_labs}</b> unbilled labs and <b>${data.revenue_leakage.unbilled_imaging}</b> imaging orders.</p>
                `);

                $('#hospital-efficiency-content').html(`
                    <h3 class="text-orange">${data.hospital_efficiency.bed_occupancy_percent}%</h3>
                    <p>Current Bed Occupancy. Avg consultation time: <b>${data.hospital_efficiency.avg_consultation_mins} mins</b>.</p>
                `);

                let riskHtml = `<table class="table table-striped"><thead><tr><th>Route</th><th>Total</th><th>Failures</th><th>Risk Level</th></tr></thead><tbody>`;
                data.logistics_risk.route_risk.forEach(r => {
                    const level = r.failures > 0 ? 'High' : 'Low';
                    const color = r.failures > 0 ? 'text-red' : 'text-green';
                    riskHtml += `<tr><td>${r.route}</td><td>${r.total}</td><td class="${color}">${r.failures}</td><td><span class="label ${r.failures > 0 ? 'bg-red' : 'bg-green'}">${level}</span></td></tr>`;
                });
                riskHtml += `</tbody></table>`;
                $('#logistics-risk-content').html(riskHtml);

            } catch (e) { console.error(e); }
        }

        async function fetchPredictions() {
            const container = $('#predictions-container');
            try {
                const res = await fetch('/api/bi/predictions');
                const data = await res.json();
                container.empty();
                
                const cards = [
                    { title: 'Revenue Forecast', val: data.revenue_forecast.value, conf: data.revenue_forecast.confidence, reason: data.revenue_forecast.reasoning, color: 'aqua' },
                    { title: 'Churn Risk', val: data.churn_risk.value, conf: data.churn_risk.confidence, reason: data.churn_risk.reasoning, color: 'yellow' },
                    { title: 'Inventory Reorder', val: data.inventory_suggestions.value, conf: data.inventory_suggestions.confidence, reason: data.inventory_suggestions.reasoning, color: 'purple' }
                ];

                cards.forEach(c => {
                    container.append(`
                        <div class="col-md-4">
                            <div class="small-box bg-${c.color}">
                                <div class="inner">
                                    <h4>${c.title}</h4>
                                    <p style="font-size: 18px; font-weight: bold;">${c.val}</p>
                                    <div class="progress sm" style="background: rgba(0,0,0,0.1)">
                                        <div class="progress-bar progress-bar-white" style="width: ${c.conf}%"></div>
                                    </div>
                                    <small>${c.reason}</small>
                                </div>
                                <div class="icon"><i class="fa fa-line-chart"></i></div>
                            </div>
                        </div>
                    `);
                });
            } catch (e) { container.html('<p class="col-md-12">Predictions currently unavailable.</p>'); }
        }

        // Q&A Form
        $('#qa-form').submit(async function(e) {
            e.preventDefault();
            const input = $('#qa-input');
            const output = $('#qa-response');
            const btn = $('#qa-btn');
            
            output.html('<div class="text-center"><i class="fa fa-spin fa-refresh"></i> Apex AI is analyzing your data...</div>');
            btn.prop('disabled', true);

            try {
                const res = await $.post('/api/bi/ask', { 
                    question: input.val(),
                    _token: $('meta[name="csrf-token"]').attr('content') 
                });
                output.html(res.answer);
            } catch (e) { output.html('<p class="text-red">Communication error.</p>'); }
            finally { btn.prop('disabled', false); }
        });

        $('#refresh-bi').click(function() {
            loadAI();
        });

        loadAI();
    });
</script>
@endsection
