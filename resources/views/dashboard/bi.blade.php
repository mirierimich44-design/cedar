<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Business Intelligence | Reenson</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bg: #0a0b0d;
            --surface: #111318;
            --card: #161b24;
            --border: #1e2530;
            --accent: #3b82f6;
            --accent-green: #06d6a0;
            --accent-amber: #f59e0b;
            --accent-red: #ef4444;
            --accent-purple: #8b5cf6;
            --text: #e8eaf0;
            --muted: #6b7280;
            --font-main: 'Syne', sans-serif;
            --font-mono: 'DM Mono', monospace;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            background-color: var(--bg); 
            color: var(--text); 
            font-family: var(--font-main); 
            padding: 20px;
            line-height: 1.5;
        }

        /* Layout */
        .container { max-width: 1400px; margin: 0 auto; }
        
        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .logo-area h1 { font-size: 24px; font-weight: 800; letter-spacing: -1px; }
        .logo-area span { color: var(--accent); }
        
        .status-pills { display: flex; gap: 12px; align-items: center; }
        .pill {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            border: 1px solid var(--border);
        }
        .pill-ai { background: rgba(59, 130, 246, 0.1); color: var(--accent); border-color: var(--accent); }
        .pill-ai .dot { width: 8px; height: 8px; background: var(--accent); border-radius: 50%; animation: pulse 2s infinite; }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 30px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 30px;
        }
        .tab {
            padding: 12px 0;
            color: var(--muted);
            cursor: pointer;
            font-weight: 600;
            position: relative;
            transition: 0.3s;
        }
        .tab.active { color: var(--text); }
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--accent);
        }

        /* Grid */
        .grid-kpis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Cards */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }
        .card-kpi { border-top-width: 3px; }
        .kpi-revenue { border-top-color: var(--accent); }
        .kpi-parcels { border-top-color: var(--accent-green); }
        .kpi-customers { border-top-color: var(--accent-amber); }
        .kpi-stock { border-top-color: var(--accent-red); }

        .kpi-label { font-size: 14px; color: var(--muted); margin-bottom: 8px; font-weight: 500; }
        .kpi-value { font-size: 28px; font-weight: 800; font-family: var(--font-mono); margin-bottom: 8px; }
        .kpi-meta { display: flex; align-items: center; gap: 8px; font-size: 13px; }
        .trend-up { color: var(--accent-green); }
        .trend-down { color: var(--accent-red); }

        .sparkline-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40px;
            opacity: 0.4;
        }

        /* Charts Grid */
        .grid-main {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .chart-title { font-size: 18px; font-weight: 700; }

        /* Heatmap */
        .heatmap-container {
            display: grid;
            grid-template-columns: 40px repeat(24, 1fr);
            gap: 4px;
            font-size: 10px;
        }
        .heatmap-cell {
            aspect-ratio: 1;
            border-radius: 2px;
            background: rgba(255,255,255,0.03);
            transition: 0.2s;
        }
        .heatmap-cell:hover { transform: scale(1.2); z-index: 10; cursor: pointer; }
        .day-label { display: flex; align-items: center; color: var(--muted); }

        /* AI Sections */
        .ai-section { margin-top: 40px; }
        .ai-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .insight-card {
            background: rgba(255,255,255,0.02);
            border-left: 4px solid var(--accent);
            padding: 16px;
            margin-bottom: 12px;
            border-radius: 4px 8px 8px 4px;
        }
        .badge {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 800;
            margin-bottom: 8px;
            display: inline-block;
        }
        .bg-OPPORTUNITY { background: var(--accent-green); color: #000; }
        .bg-WARNING { background: var(--accent-amber); color: #000; }
        .bg-ANOMALY { background: var(--accent); color: #fff; }
        .bg-GROWTH { background: var(--accent-purple); color: #fff; }
        .bg-ACTION { background: var(--accent-red); color: #fff; }

        /* Q&A */
        .qa-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .qa-input-wrapper {
            display: flex;
            gap: 10px;
        }
        input[type="text"] {
            flex: 1;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 16px;
            color: var(--text);
            font-family: var(--font-main);
        }
        .btn-ai {
            background: var(--accent);
            color: white;
            border: none;
            padding: 0 24px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }
        .qa-response {
            background: var(--surface);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
            min-height: 100px;
            font-size: 14px;
        }

        /* Predictions */
        .prediction-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        .confidence-bar {
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            margin: 10px 0;
            overflow: hidden;
        }
        .confidence-fill { height: 100%; background: var(--accent-green); }

        /* Skeleton Shimmer */
        .skeleton {
            background: linear-gradient(90deg, var(--card) 25%, var(--border) 50%, var(--card) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        @media (max-width: 768px) {
            .grid-main, .ai-grid, .prediction-cards { grid-template-columns: 1fr; }
            .heatmap-container { overflow-x: auto; padding-bottom: 10px; }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Top Bar -->
    <header class="top-bar">
        <div class="logo-area">
            <h1>REENSON <span>BI</span></h1>
        </div>
        <div class="status-pills">
            <div class="pill pill-ai">
                <div class="dot"></div>
                Gemini 2.0 Flash Connected
            </div>
            <button class="pill" id="refresh-ai" style="background: transparent; color: var(--text); cursor:pointer;">
                🔄 Refresh AI
            </button>
        </div>
    </header>

    <!-- Tab Nav -->
    <nav class="tabs">
        <div class="tab active" data-target="overview">OVERVIEW</div>
        <div class="tab" data-target="logistics">LOGISTICS</div>
        <div class="tab" data-target="ai-lab">AI INTELLIGENCE</div>
    </nav>

    <!-- KPI Row -->
    <div class="grid-kpis">
        <!-- Revenue -->
        <div class="card card-kpi kpi-revenue">
            <div class="kpi-label">Total Revenue (MTD)</div>
            <div class="kpi-value">KES {{ number_format($revenue_now, 0) }}</div>
            <div class="kpi-meta">
                @php $rev_change = $revenue_last > 0 ? (($revenue_now - $revenue_last) / $revenue_last) * 100 : 0; @endphp
                <span class="{{ $rev_change >= 0 ? 'trend-up' : 'trend-down' }}">
                    {{ $rev_change >= 0 ? '↑' : '↓' }} {{ abs(round($rev_change, 1)) }}%
                </span>
                <span style="color: var(--muted)">vs last month</span>
            </div>
            <div class="sparkline-container">
                <canvas id="rev-sparkline"></canvas>
            </div>
        </div>

        <!-- Parcels -->
        <div class="card card-kpi kpi-parcels">
            <div class="kpi-label">Parcels Dispatched</div>
            <div class="kpi-value">{{ number_format($parcels_now) }}</div>
            <div class="kpi-meta">
                @php $prc_change = $parcels_last > 0 ? (($parcels_now - $parcels_last) / $parcels_last) * 100 : 0; @endphp
                <span class="{{ $prc_change >= 0 ? 'trend-up' : 'trend-down' }}">
                    {{ $prc_change >= 0 ? '↑' : '↓' }} {{ abs(round($prc_change, 1)) }}%
                </span>
                <span style="color: var(--muted)">vs last month</span>
            </div>
        </div>

        <!-- New Customers -->
        <div class="card card-kpi kpi-customers">
            <div class="kpi-label">New Customers</div>
            <div class="kpi-value">{{ number_format($customers_now) }}</div>
            <div class="kpi-meta">
                @php $cus_change = $customers_last > 0 ? (($customers_now - $customers_last) / $customers_last) * 100 : 0; @endphp
                <span class="{{ $cus_change >= 0 ? 'trend-up' : 'trend-down' }}">
                    {{ $cus_change >= 0 ? '↑' : '↓' }} {{ abs(round($cus_change, 1)) }}%
                </span>
                <span style="color: var(--muted)">vs last month</span>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="card card-kpi kpi-stock">
            <div class="kpi-label">Low Stock Items</div>
            <div class="kpi-value" style="color: var(--accent-red)">{{ $low_stock_count }}</div>
            <div class="kpi-meta">
                <span style="color: var(--muted)">Requiring immediate action</span>
            </div>
        </div>
    </div>

    <!-- Overview Section -->
    <div id="section-overview" class="tab-content-item">
        <div class="grid-main">
            <div class="card">
                <div class="chart-header">
                    <div class="chart-title">Revenue vs Parcel Volume (12 Months)</div>
                </div>
                <canvas id="revenueParcelsChart" height="120"></canvas>
            </div>
            <div class="card">
                <div class="chart-header">
                    <div class="chart-title">Payment Methods</div>
                </div>
                <canvas id="paymentsChart"></canvas>
            </div>
        </div>

        <div class="grid-main">
            <div class="card">
                <div class="chart-header">
                    <div class="chart-title">Sales Heatmap (Transactions by Time)</div>
                </div>
                <div class="heatmap-container" id="sales-heatmap">
                    <!-- Labels -->
                    <div></div>
                    @for($i=0; $i<24; $i++) <div style="text-align:center; color: var(--muted)">{{$i}}h</div> @endfor
                    
                    @php $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; @endphp
                    @foreach($days as $idx => $day)
                        <div class="day-label">{{ $day }}</div>
                        @for($h=0; $h<24; $h++)
                            @php 
                                $match = $heatmap_raw->where('day', $idx+1)->where('hour', $h)->first();
                                $count = $match ? $match->count : 0;
                                $opacity = min($count / 10, 1);
                            @endphp
                            <div class="heatmap-cell" style="background: rgba(6, 214, 160, {{ $opacity }});" title="{{ $count }} transactions at {{ $h }}:00"></div>
                        @endfor
                    @endforeach
                </div>
            </div>
            <div class="card">
                <div class="chart-header">
                    <div class="chart-title">Top Revenue Routes</div>
                </div>
                <canvas id="routesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Logistics Section -->
    <div id="section-logistics" class="tab-content-item" style="display: none;">
        <div class="card">
            <h2>Detailed Logistics Analytics Coming Soon...</h2>
            <p style="color: var(--muted)">This section will integrate with the Parcel module tracking data.</p>
        </div>
    </div>

    <!-- AI Intelligence Section -->
    <div id="section-ai-lab" class="tab-content-item" style="display: none;">
        <div class="ai-grid">
            <!-- Automated Insights -->
            <div class="card">
                <div class="chart-header">
                    <div class="chart-title">Gemini Intelligence</div>
                    <button class="btn-ai" id="retry-insights" style="display:none">Retry</button>
                </div>
                <div id="insights-container">
                    <div class="skeleton" style="height: 60px; margin-bottom: 10px; border-radius: 8px;"></div>
                    <div class="skeleton" style="height: 60px; margin-bottom: 10px; border-radius: 8px;"></div>
                    <div class="skeleton" style="height: 60px; margin-bottom: 10px; border-radius: 8px;"></div>
                </div>
            </div>

            <!-- Q&A -->
            <div class="card">
                <div class="chart-header">
                    <div class="chart-title">AI Business Assistant</div>
                </div>
                <div class="qa-container">
                    <div class="qa-response" id="qa-output">
                        Ask a question like "What is my most profitable route?" or "How was last week's revenue compared to this week?"
                    </div>
                    <form id="qa-form" class="qa-input-wrapper">
                        <input type="text" id="qa-input" placeholder="Ask anything about your business..." required>
                        <button type="submit" class="btn-ai" id="qa-btn">Ask Gemini</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Predictions Row -->
        <div class="ai-section">
            <h2 style="margin-bottom: 20px;">Predictive Recommendations</h2>
            <div class="prediction-cards" id="predictions-container">
                <div class="card skeleton" style="height: 180px;"></div>
                <div class="card skeleton" style="height: 180px;"></div>
                <div class="card skeleton" style="height: 180px;"></div>
            </div>
        </div>
    </div>

</div>

<script>
    // --- Chart.js Data ---
    const chartConfig = {
        months: @json($months),
        revenue: @json($revenue_chart),
        parcels: @json($parcel_chart),
        payments: @json($payment_methods),
        routes: @json($top_routes),
        revTrend: @json($revenue_trend)
    };

    // Global Defaults
    Chart.defaults.color = '#6b7280';
    Chart.defaults.font.family = "'Syne', sans-serif";

    // 1. Revenue vs Parcels (Dual Axis)
    new Chart(document.getElementById('revenueParcelsChart'), {
        type: 'line',
        data: {
            labels: chartConfig.months,
            datasets: [{
                label: 'Revenue (KES)',
                data: chartConfig.revenue,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                yAxisID: 'y',
                tension: 0.4
            }, {
                label: 'Parcel Volume',
                data: chartConfig.parcels,
                borderColor: '#06d6a0',
                borderDash: [5, 5],
                yAxisID: 'y1',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { type: 'linear', display: true, position: 'left', grid: { color: '#1e2530' } },
                y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { position: 'top', align: 'end' } }
        }
    });

    // 2. Payments Doughnut
    new Chart(document.getElementById('paymentsChart'), {
        type: 'doughnut',
        data: {
            labels: chartConfig.payments.map(p => p.method.toUpperCase()),
            datasets: [{
                data: chartConfig.payments.map(p => p.total),
                backgroundColor: ['#3b82f6', '#06d6a0', '#f59e0b', '#ef4444', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 3. Routes Bar
    new Chart(document.getElementById('routesChart'), {
        type: 'bar',
        data: {
            labels: chartConfig.routes.map(r => r.route),
            datasets: [{
                label: 'Revenue',
                data: chartConfig.routes.map(r => r.total),
                backgroundColor: '#3b82f6',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { grid: { color: '#1e2530' } }, y: { grid: { display: false } } }
        }
    });

    // 4. KPI Sparkline
    new Chart(document.getElementById('rev-sparkline'), {
        type: 'line',
        data: {
            labels: chartConfig.revTrend.map((_, i) => i),
            datasets: [{
                data: chartConfig.revTrend,
                borderColor: '#3b82f6',
                borderWidth: 2,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: { x: { display: false }, y: { display: false } }
        }
    });

    // --- Tab Logic ---
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content-item').forEach(c => c.style.display = 'none');
            tab.classList.add('active');
            document.getElementById('section-' + tab.dataset.target).style.display = 'block';
        });
    });

    // --- AI Integration ---
    async function fetchInsights(refresh = false) {
        const container = document.getElementById('insights-container');
        if (refresh) container.innerHTML = '<div class="skeleton" style="height: 60px; margin-bottom: 10px; border-radius: 8px;"></div>'.repeat(3);
        
        try {
            const res = await fetch(`/api/bi/insights${refresh ? '?refresh=1' : ''}`);
            const data = await res.json();
            
            container.innerHTML = '';
            data.insights.forEach(text => {
                const tag = text.match(/\[(.*?)\]/)?.[1] || 'INFO';
                const cleanText = text.replace(/\[.*?\]/, '').trim();
                
                container.innerHTML += `
                    <div class="insight-card">
                        <span class="badge bg-${tag}">${tag}</span>
                        <p>${cleanText}</p>
                    </div>
                `;
            });
        } catch (e) {
            container.innerHTML = '<p style="color: var(--accent-red)">Failed to load AI insights.</p>';
            document.getElementById('retry-insights').style.display = 'block';
        }
    }

    async function fetchPredictions() {
        const container = document.getElementById('predictions-container');
        try {
            const res = await fetch('/api/bi/predictions');
            const data = await res.json();
            
            container.innerHTML = `
                <div class="card">
                    <div class="kpi-label">Revenue Forecast (Next Month)</div>
                    <div class="kpi-value">${data.revenue_forecast.value}</div>
                    <div class="confidence-bar"><div class="confidence-fill" style="width: ${data.revenue_forecast.confidence}%"></div></div>
                    <small style="color: var(--muted)">${data.revenue_forecast.reasoning}</small>
                </div>
                <div class="card">
                    <div class="kpi-label">Churn Risk Analysis</div>
                    <div class="kpi-value">${data.churn_risk.value}</div>
                    <div class="confidence-bar"><div class="confidence-fill" style="width: ${data.churn_risk.confidence}%; background: var(--accent-amber)"></div></div>
                    <small style="color: var(--muted)">${data.churn_risk.reasoning}</small>
                </div>
                <div class="card">
                    <div class="kpi-label">Inventory Recommendations</div>
                    <div class="kpi-value">${data.inventory_suggestions.value}</div>
                    <div class="confidence-bar"><div class="confidence-fill" style="width: ${data.inventory_suggestions.confidence}%; background: var(--accent-purple)"></div></div>
                    <small style="color: var(--muted)">${data.inventory_suggestions.reasoning}</small>
                </div>
            `;
        } catch (e) {
            container.innerHTML = '<p>Predictions unavailable.</p>';
        }
    }

    // AI Q&A
    document.getElementById('qa-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('qa-input');
        const output = document.getElementById('qa-output');
        const btn = document.getElementById('qa-btn');
        const q = input.value;

        output.innerHTML = '<i>Gemini is thinking...</i>';
        btn.disabled = true;

        try {
            const res = await fetch('/api/bi/ask', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ question: q })
            });
            const data = await res.json();
            output.innerHTML = data.answer;
        } catch (e) {
            output.innerHTML = 'Error communicating with AI.';
        } finally {
            btn.disabled = false;
            input.value = '';
        }
    });

    document.getElementById('refresh-ai').addEventListener('click', () => {
        fetchInsights(true);
        fetchPredictions();
    });

    // Initial Load
    window.addEventListener('DOMContentLoaded', () => {
        fetchInsights();
        fetchPredictions();
    });
</script>

</body>
</html>
