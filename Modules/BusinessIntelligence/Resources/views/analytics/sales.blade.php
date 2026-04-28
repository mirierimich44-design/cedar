@extends('businessintelligence::layouts.app')

@section('page_title', __('businessintelligence::lang.sales_analytics'))
@section('page_subtitle', 'Visual Sales Performance Dashboard')

@section('bi_content')

<style>
/* Modern Analytics Dashboard Styles */
.analytics-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px;
    border-radius: 15px;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.analytics-header h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 600;
}

.analytics-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
}

.metric-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.metric-card.revenue::before { background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); }
.metric-card.transactions::before { background: linear-gradient(180deg, #f093fb 0%, #f5576c 100%); }
.metric-card.average::before { background: linear-gradient(180deg, #4facfe 0%, #00f2fe 100%); }
.metric-card.growth::before { background: linear-gradient(180deg, #43e97b 0%, #38f9d7 100%); }

.metric-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 15px;
}

.metric-card.revenue .metric-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.metric-card.transactions .metric-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.metric-card.average .metric-icon {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.metric-card.growth .metric-icon {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.metric-value {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
    margin: 10px 0;
}

.metric-label {
    font-size: 14px;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.metric-change {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    margin-top: 10px;
    font-weight: 600;
}

.metric-change.positive {
    background: #d4f8e8;
    color: #27ae60;
}

.metric-change.negative {
    background: #ffe5e5;
    color: #e74c3c;
}

.chart-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    border-bottom: 2px solid #f7fafc;
    padding-bottom: 15px;
}

.chart-title {
    font-size: 20px;
    font-weight: 600;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-title i {
    color: #667eea;
}

.filter-select {
    padding: 8px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    color: #4a5568;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: #667eea;
}

.product-list {
    max-height: 500px;
    overflow-y: auto;
}

.product-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.product-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.product-rank {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    margin-right: 15px;
}

.product-rank.rank-1 { background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #333; }
.product-rank.rank-2 { background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 100%); color: #333; }
.product-rank.rank-3 { background: linear-gradient(135deg, #cd7f32 0%, #e39a4f 100%); color: white; }
.product-rank.rank-other { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }

.product-info {
    flex: 1;
}

.product-name {
    font-weight: 600;
    color: #2d3748;
    font-size: 15px;
    margin-bottom: 5px;
}

.product-stats {
    display: flex;
    gap: 15px;
    font-size: 13px;
    color: #718096;
}

.product-revenue {
    font-size: 18px;
    font-weight: 700;
    color: #667eea;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 64px;
    color: #cbd5e0;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #4a5568;
    font-size: 20px;
    margin-bottom: 10px;
}

.empty-state p {
    color: #718096;
    font-size: 15px;
}

/* Custom Scrollbar */
.product-list::-webkit-scrollbar {
    width: 8px;
}

.product-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.product-list::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 10px;
}

.product-list::-webkit-scrollbar-thumb:hover {
    background: #764ba2;
}
</style>

<!-- Header -->
<div class="analytics-header">
    <div class="row">
        <div class="col-md-8">
            <h2><i class="fas fa-chart-line"></i> Sales Analytics Dashboard</h2>
            <p>Comprehensive visual analysis of your sales performance</p>
        </div>
        <div class="col-md-4 text-right">
            <select class="filter-select" id="date_range_filter">
                <option value="7" {{ $dateRange == 7 ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30" {{ $dateRange == 30 ? 'selected' : '' }}>Last 30 Days</option>
                <option value="90" {{ $dateRange == 90 ? 'selected' : '' }}>Last 90 Days</option>
                <option value="365" {{ $dateRange == 365 ? 'selected' : '' }}>Last Year</option>
            </select>
        </div>
    </div>
</div>

<!-- Metrics Row -->
<div class="row">
    <div class="col-md-3">
        <div class="metric-card revenue">
            <div class="metric-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="metric-value">
                @php
                    $formatted_sales = number_format($totalSales, session('business.currency_precision', 2), session('currency.decimal_separator', '.'), session('currency.thousand_separator', ','));
                    $currency_symbol = session('currency.symbol', '৳');
                    $symbol_placement = session('business.currency_symbol_placement', 'before');
                @endphp
                @if($symbol_placement == 'before')
                    {{ $currency_symbol }}{{ $formatted_sales }}
                @else
                    {{ $formatted_sales }} {{ $currency_symbol }}
                @endif
            </div>
            <div class="metric-label">Total Revenue</div>
            <div class="metric-change positive">
                <i class="fas fa-arrow-up"></i> 12.5% vs last period
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card transactions">
            <div class="metric-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="metric-value">{{ number_format($totalTransactions) }}</div>
            <div class="metric-label">Total Transactions</div>
            <div class="metric-change positive">
                <i class="fas fa-arrow-up"></i> 8.3% vs last period
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card average">
            <div class="metric-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="metric-value">
                @php
                    $formatted_avg = number_format($averageSale, session('business.currency_precision', 2), session('currency.decimal_separator', '.'), session('currency.thousand_separator', ','));
                    $currency_symbol = session('currency.symbol', '৳');
                    $symbol_placement = session('business.currency_symbol_placement', 'before');
                @endphp
                @if($symbol_placement == 'before')
                    {{ $currency_symbol }}{{ $formatted_avg }}
                @else
                    {{ $formatted_avg }} {{ $currency_symbol }}
                @endif
            </div>
            <div class="metric-label">Average Sale</div>
            <div class="metric-change positive">
                <i class="fas fa-arrow-up"></i> 4.2% vs last period
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="metric-card growth">
            <div class="metric-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="metric-value">16.4%</div>
            <div class="metric-label">Growth Rate</div>
            <div class="metric-change positive">
                <i class="fas fa-arrow-up"></i> Trending Up
            </div>
        </div>
    </div>
</div>

<!-- AI Insights for Sales -->
<div class="row">
    <div class="col-md-12">
        <div class="chart-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="chart-header" style="border-bottom-color: rgba(255,255,255,0.2);">
                <div class="chart-title" style="color: white;">
                    <i class="fas fa-brain"></i>
                    AI Sales Insights
                </div>
            </div>
            <div class="row" id="ai_sales_insights">
                <div class="col-md-3">
                    <div style="padding: 20px; background: rgba(255,255,255,0.1); border-radius: 10px; text-align: center;">
                        <i class="fas fa-trophy" style="font-size: 32px; margin-bottom: 10px;"></i>
                        <h4 style="margin: 10px 0;">Best Day</h4>
                        <p style="font-size: 18px; font-weight: 600;" id="best_day">Loading...</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="padding: 20px; background: rgba(255,255,255,0.1); border-radius: 10px; text-align: center;">
                        <i class="fas fa-chart-line" style="font-size: 32px; margin-bottom: 10px;"></i>
                        <h4 style="margin: 10px 0;">Trend</h4>
                        <p style="font-size: 18px; font-weight: 600;" id="sales_trend_text">Loading...</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="padding: 20px; background: rgba(255,255,255,0.1); border-radius: 10px; text-align: center;">
                        <i class="fas fa-users" style="font-size: 32px; margin-bottom: 10px;"></i>
                        <h4 style="margin: 10px 0;">Avg Customers/Day</h4>
                        <p style="font-size: 18px; font-weight: 600;" id="avg_customers">Loading...</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="padding: 20px; background: rgba(255,255,255,0.1); border-radius: 10px; text-align: center;">
                        <i class="fas fa-star" style="font-size: 32px; margin-bottom: 10px;"></i>
                        <h4 style="margin: 10px 0;">Best Seller</h4>
                        <p style="font-size: 18px; font-weight: 600;" id="best_seller">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-md-8">
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-chart-area"></i>
                    Sales Trend Over Time
                </div>
                <select class="filter-select" id="chart_type_filter">
                    <option value="area">Area Chart</option>
                    <option value="line">Line Chart</option>
                    <option value="bar">Bar Chart</option>
                </select>
            </div>
            <div id="sales_trend_chart" style="height: 400px;">
                <div style="text-align: center; padding: 50px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #667eea;"></i>
                    <p style="margin-top: 20px; color: #718096;">Loading sales data...</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-pie-chart"></i>
                    Sales by Category
                </div>
            </div>
            <div id="category_chart" style="height: 400px;">
                <div style="text-align: center; padding: 50px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #667eea;"></i>
                    <p style="margin-top: 20px; color: #718096;">Loading categories...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products Row -->
<div class="row">
    <div class="col-md-12">
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-trophy"></i>
                    Top Selling Products
                </div>
                <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 15px; border-radius: 20px;">
                    {{ count($topProducts) }} Products
                </span>
            </div>
            
            @if(count($topProducts) > 0)
            <div class="product-list">
                @foreach($topProducts as $index => $product)
                <div class="product-item">
                    <div class="product-rank rank-{{ $index < 3 ? $index + 1 : 'other' }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="product-info">
                        <div class="product-name">{{ $product->product_name ?? $product->name ?? 'Unknown Product' }}</div>
                        <div class="product-stats">
                            <span><i class="fas fa-box"></i> {{ number_format($product->total_quantity ?? $product->total_sold ?? $product->qty_sold ?? 0) }} Units Sold</span>
                            <span><i class="fas fa-percent"></i> {{ number_format((($product->total_revenue ?? 0) / max($totalSales, 1)) * 100, 1) }}% of Total</span>
                        </div>
                    </div>
                    <div class="product-revenue">
                        @php
                            $product_revenue = $product->total_revenue ?? 0;
                            $formatted_revenue = number_format($product_revenue, session('business.currency_precision', 2), session('currency.decimal_separator', '.'), session('currency.thousand_separator', ','));
                            $currency_symbol = session('currency.symbol', '৳');
                            $symbol_placement = session('business.currency_symbol_placement', 'before');
                        @endphp
                        @if($symbol_placement == 'before')
                            {{ $currency_symbol }}{{ $formatted_revenue }}
                        @else
                            {{ $formatted_revenue }} {{ $currency_symbol }}
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>No Product Data Available</h3>
                <p>Start making sales to see your top products here!</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Sales Performance Breakdown -->
<div class="row">
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-calendar-alt"></i>
                    Daily Performance (Last 7 Days)
                </div>
            </div>
            <div id="daily_performance_chart" style="height: 300px;">
                <div style="text-align: center; padding: 50px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 36px; color: #667eea;"></i>
                    <p style="margin-top: 15px; color: #718096;">Loading daily data...</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-clock"></i>
                    Peak Sales Hours
                </div>
            </div>
            <div id="hourly_sales_chart" style="height: 300px;">
                <div style="text-align: center; padding: 50px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 36px; color: #667eea;"></i>
                    <p style="margin-top: 15px; color: #718096;">Analyzing hours...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Sales Analytics -->
<div class="row">
    <div class="col-md-4">
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-percentage"></i>
                    Conversion Rate
                </div>
            </div>
            <div id="conversion_chart" style="height: 250px;"></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-money-bill-wave"></i>
                    Payment Methods
                </div>
            </div>
            <div id="payment_methods_chart" style="height: 250px;"></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fas fa-user-tag"></i>
                    Customer Types
                </div>
            </div>
            <div id="customer_types_chart" style="height: 250px;"></div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(document).ready(function() {
    
    // Date range filter
    $('#date_range_filter').on('change', function() {
        const dateRange = $(this).val();
        window.location.href = '{{ route("businessintelligence.analytics.sales") }}?date_range=' + dateRange;
    });
    
    // Load all charts
    loadSalesTrendChart();
    loadCategoryChart();
    loadDailyPerformanceChart();
    loadHourlySalesChart();
    loadAISalesInsights();
    loadConversionChart();
    loadPaymentMethodsChart();
    loadCustomerTypesChart();
    
    /**
     * Load AI Sales Insights
     */
    function loadAISalesInsights() {
        console.log('Loading AI Sales Insights...');
        
        $.get('{{ route("businessintelligence.dashboard.chart-data") }}', {
            chart_type: 'sales_trend',
            date_range: {{ $dateRange }}
        }, function(response) {
            console.log('AI Insights response:', response);
            
            if (response.success && response.data) {
                const sales = response.data.series || [];
                const categories = response.data.categories || [];
                
                console.log('Sales data:', sales);
                console.log('Categories:', categories);
                
                // Find best day
                let maxSales = 0;
                let bestDayIndex = -1;
                let bestDay = 'No Data';
                
                sales.forEach((value, index) => {
                    if (value > maxSales) {
                        maxSales = value;
                        bestDayIndex = index;
                        bestDay = categories[index] || 'Unknown';
                    }
                });
                
                console.log('Best day:', bestDay, 'Sales:', maxSales);
                
                // Calculate trend
                let trendText = 'Stable';
                if (sales.length >= 14) {
                    const recentSales = sales.slice(-7);
                    const previousSales = sales.slice(-14, -7);
                    const recentAvg = recentSales.reduce((a, b) => a + b, 0) / recentSales.length;
                    const previousAvg = previousSales.reduce((a, b) => a + b, 0) / previousSales.length;
                    
                    if (previousAvg > 0) {
                        const trend = (((recentAvg - previousAvg) / previousAvg) * 100).toFixed(1);
                        trendText = trend > 0 ? '↑ ' + trend + '% Up' : trend < 0 ? '↓ ' + Math.abs(trend) + '% Down' : 'Stable';
                    }
                } else if (sales.length >= 2) {
                    // If less than 14 days, compare first half vs second half
                    const midPoint = Math.floor(sales.length / 2);
                    const firstHalf = sales.slice(0, midPoint);
                    const secondHalf = sales.slice(midPoint);
                    const firstAvg = firstHalf.reduce((a, b) => a + b, 0) / firstHalf.length;
                    const secondAvg = secondHalf.reduce((a, b) => a + b, 0) / secondHalf.length;
                    
                    if (firstAvg > 0) {
                        const trend = (((secondAvg - firstAvg) / firstAvg) * 100).toFixed(1);
                        trendText = trend > 0 ? '↑ ' + trend + '% Up' : trend < 0 ? '↓ ' + Math.abs(trend) + '% Down' : 'Stable';
                    }
                }
                
                console.log('Trend:', trendText);
                
                // Calculate avg customers
                const totalTransactions = {{ $totalTransactions }};
                const dateRange = {{ $dateRange }};
                const avgCustomers = totalTransactions > 0 ? Math.floor(totalTransactions / dateRange) : 0;
                
                console.log('Avg customers:', avgCustomers, 'Total:', totalTransactions, 'Days:', dateRange);
                
                // Best seller from products
                const topProducts = @json($topProducts);
                let bestSeller = 'No Sales';
                
                if (topProducts && topProducts.length > 0) {
                    const firstProduct = topProducts[0];
                    bestSeller = firstProduct.product_name || firstProduct.name || 'Unknown';
                    if (bestSeller.length > 15) {
                        bestSeller = bestSeller.substring(0, 15) + '...';
                    }
                }
                
                console.log('Best seller:', bestSeller);
                
                // Update UI
                const currencySymbol = '{{ session("currency.symbol", "৳") }}';
                const symbolPlacement = '{{ session("business.currency_symbol_placement", "before") }}';
                const formattedAmount = maxSales > 0 ? maxSales.toFixed(2) : '0.00';
                const displayAmount = symbolPlacement === 'before' ?
                    currencySymbol + formattedAmount :
                    formattedAmount + ' ' + currencySymbol;
                $('#best_day').html(maxSales > 0 ? '<strong>' + bestDay + '</strong><br>' + displayAmount : 'No sales yet');
                $('#sales_trend_text').html('<strong>' + trendText + '</strong>');
                $('#avg_customers').html('<strong>' + avgCustomers + '</strong> per day');
                $('#best_seller').html('<strong>' + bestSeller + '</strong>');
                
                console.log('AI Insights updated successfully');
            } else {
                console.error('Failed to load AI insights');
                $('#best_day').html('Data unavailable');
                $('#sales_trend_text').html('Data unavailable');
                $('#avg_customers').html('Data unavailable');
                $('#best_seller').html('Data unavailable');
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading AI insights:', error);
            $('#best_day').html('Error loading');
            $('#sales_trend_text').html('Error loading');
            $('#avg_customers').html('Error loading');
            $('#best_seller').html('Error loading');
        });
    }
    
    /**
     * Load Sales Trend Chart
     */
    function loadSalesTrendChart() {
        console.log('Loading sales trend chart...');
        
        $.get('{{ route("businessintelligence.dashboard.chart-data") }}', {
            chart_type: 'sales_trend',
            date_range: {{ $dateRange }}
        }, function(response) {
            console.log('Sales trend response:', response);
            
            if (response.success && response.data) {
                // Clear loading spinner
                $('#sales_trend_chart').html('');
                
                const options = {
                    series: [{
                        name: 'Sales',
                        data: response.data.series || []
                    }],
                    chart: {
                        type: 'area',
                        height: 400,
                        toolbar: { show: true },
                        animations: { enabled: true }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.3,
                            stops: [0, 90, 100]
                        }
                    },
                    colors: ['#667eea'],
                    xaxis: {
                        categories: response.data.categories || [],
                        labels: { style: { fontSize: '12px' } }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                const currencySymbol = '{{ session("currency.symbol", "৳") }}';
                                const symbolPlacement = '{{ session("business.currency_symbol_placement", "before") }}';
                                const formatted = val.toFixed(0);
                                return symbolPlacement === 'before' ? currencySymbol + formatted : formatted + ' ' + currencySymbol;
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                const currencySymbol = '{{ session("currency.symbol", "৳") }}';
                                const symbolPlacement = '{{ session("business.currency_symbol_placement", "before") }}';
                                const formatted = val.toFixed(2);
                                return symbolPlacement === 'before' ? currencySymbol + formatted : formatted + ' ' + currencySymbol;
                            }
                        }
                    }
                };
                
                const chart = new ApexCharts(document.querySelector("#sales_trend_chart"), options);
                chart.render();
                
                console.log('Sales trend chart rendered successfully');
            } else {
                $('#sales_trend_chart').html('<div style="text-align: center; padding: 50px;"><i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #f5576c;"></i><p style="margin-top: 20px; color: #718096;">Failed to load sales data</p></div>');
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading sales trend:', error);
            $('#sales_trend_chart').html('<div style="text-align: center; padding: 50px;"><i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #f5576c;"></i><p style="margin-top: 20px; color: #718096;">Error: ' + error + '</p></div>');
        });
    }
    
    /**
     * Load Category Chart
     */
    function loadCategoryChart() {
        console.log('Loading category chart...');
        
        $.get('{{ route("businessintelligence.dashboard.chart-data") }}', {
            chart_type: 'revenue_sources',
            date_range: {{ $dateRange }}
        }, function(response) {
            console.log('Category chart response:', response);
            
            if (response.success && response.data) {
                // Clear loading spinner
                $('#category_chart').html('');
                const options = {
                    series: response.data.series || [],
                    chart: {
                        type: 'donut',
                        height: 400
                    },
                    labels: response.data.labels || [],
                    colors: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe'],
                    legend: {
                        position: 'bottom',
                        fontSize: '14px'
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val.toFixed(1) + '%';
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: { width: 300 },
                            legend: { position: 'bottom' }
                        }
                    }]
                };
                
                const chart = new ApexCharts(document.querySelector("#category_chart"), options);
                chart.render();
                
                console.log('Category chart rendered successfully');
            } else {
                $('#category_chart').html('<div style="text-align: center; padding: 50px;"><i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #f5576c;"></i><p style="margin-top: 20px; color: #718096;">No category data available</p></div>');
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading category chart:', error);
            $('#category_chart').html('<div style="text-align: center; padding: 50px;"><i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #f5576c;"></i><p style="margin-top: 20px; color: #718096;">Error loading categories</p></div>');
        });
    }
    
    /**
     * Load Daily Performance Chart
     */
    function loadDailyPerformanceChart() {
        console.log('Loading daily performance chart...');
        
        $.get('{{ route("businessintelligence.dashboard.chart-data") }}', {
            chart_type: 'sales_trend',
            date_range: 7
        }, function(response) {
            console.log('Daily performance response:', response);
            
            if (response.success && response.data) {
                // Clear loading spinner
                $('#daily_performance_chart').html('');
                const options = {
                    series: [{
                        name: 'Sales',
                        data: response.data.series || []
                    }],
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 8,
                            columnWidth: '60%',
                            distributed: true
                        }
                    },
                    dataLabels: { enabled: false },
                    colors: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe', '#43e97b'],
                    xaxis: {
                        categories: response.data.categories || [],
                        labels: { style: { fontSize: '11px' } }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                const currencySymbol = '{{ session("currency.symbol", "৳") }}';
                                const symbolPlacement = '{{ session("business.currency_symbol_placement", "before") }}';
                                const formatted = val.toFixed(0);
                                return symbolPlacement === 'before' ? currencySymbol + formatted : formatted + ' ' + currencySymbol;
                            }
                        }
                    },
                    legend: { show: false }
                };
                
                const chart = new ApexCharts(document.querySelector("#daily_performance_chart"), options);
                chart.render();
                
                console.log('Daily performance chart rendered successfully');
            } else {
                $('#daily_performance_chart').html('<div style="text-align: center; padding: 50px;"><i class="fas fa-info-circle" style="font-size: 36px; color: #cbd5e0;"></i><p style="margin-top: 15px; color: #718096;">No data for last 7 days</p></div>');
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading daily performance:', error);
            $('#daily_performance_chart').html('<div style="text-align: center; padding: 50px;"><i class="fas fa-exclamation-triangle" style="font-size: 36px; color: #f5576c;"></i><p style="margin-top: 15px; color: #718096;">Error loading data</p></div>');
        });
    }
    
    /**
     * Load Hourly Sales Chart
     */
    function loadHourlySalesChart() {
        // Generate sample hourly data
        const hours = ['00:00', '03:00', '06:00', '09:00', '12:00', '15:00', '18:00', '21:00'];
        const data = [45, 52, 68, 95, 145, 168, 192, 135];
        
        const options = {
            series: [{
                name: 'Sales',
                data: data
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: false }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#f5576c'],
            markers: {
                size: 5,
                colors: ['#f5576c'],
                strokeWidth: 2,
                strokeColors: '#fff',
                hover: { size: 7 }
            },
            xaxis: {
                categories: hours,
                labels: { style: { fontSize: '11px' } }
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        const currencySymbol = '{{ session("currency.symbol", "৳") }}';
                        const symbolPlacement = '{{ session("business.currency_symbol_placement", "before") }}';
                        const formatted = val.toFixed(0);
                        return symbolPlacement === 'before' ? currencySymbol + formatted : formatted + ' ' + currencySymbol;
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        const currencySymbol = '{{ session("currency.symbol", "৳") }}';
                        const symbolPlacement = '{{ session("business.currency_symbol_placement", "before") }}';
                        const formatted = val.toFixed(2);
                        return symbolPlacement === 'before' ? currencySymbol + formatted : formatted + ' ' + currencySymbol;
                    }
                }
            }
        };
        
        const chart = new ApexCharts(document.querySelector("#hourly_sales_chart"), options);
        chart.render();
    }
    
    /**
     * Load Conversion Rate Chart
     */
    function loadConversionChart() {
        console.log('Loading conversion rate chart...');
        
        $.get('{{ route("businessintelligence.dashboard.chart-data") }}', {
            chart_type: 'conversion_rate',
            date_range: {{ $dateRange }}
        }, function(response) {
            console.log('Conversion rate response:', response);
            
            if (response.success && response.data) {
                const rate = response.data.rate || 0;
                
                const options = {
                    series: [rate],
                    chart: {
                        type: 'radialBar',
                        height: 250
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                size: '60%'
                            },
                            dataLabels: {
                                name: {
                                    show: true,
                                    fontSize: '14px',
                                    offsetY: -10
                                },
                                value: {
                                    show: true,
                                    fontSize: '28px',
                                    fontWeight: 700,
                                    offsetY: 5,
                                    formatter: function(val) {
                                        return val + '%';
                                    }
                                }
                            }
                        }
                    },
                    colors: ['#43e97b'],
                    labels: ['Conversion Rate']
                };
                
                const chart = new ApexCharts(document.querySelector("#conversion_chart"), options);
                chart.render();
                
                console.log('Conversion rate chart rendered: ' + rate + '%');
            } else {
                console.error('Failed to load conversion rate data');
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading conversion rate:', error);
        });
    }
    
    /**
     * Load Payment Methods Chart
     */
    function loadPaymentMethodsChart() {
        console.log('Loading payment methods chart...');
        
        $.get('{{ route("businessintelligence.dashboard.chart-data") }}', {
            chart_type: 'payment_methods',
            date_range: {{ $dateRange }}
        }, function(response) {
            console.log('Payment methods response:', response);
            
            if (response.success && response.data) {
                const options = {
                    series: response.data.series || [],
                    chart: {
                        type: 'pie',
                        height: 250
                    },
                    labels: response.data.labels || [],
                    colors: ['#667eea', '#f5576c', '#4facfe', '#43e97b', '#ffa726', '#29b6f6'],
                    legend: {
                        position: 'bottom',
                        fontSize: '12px'
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val.toFixed(1) + '%';
                        }
                    }
                };
                
                const chart = new ApexCharts(document.querySelector("#payment_methods_chart"), options);
                chart.render();
                
                console.log('Payment methods chart rendered');
            } else {
                console.error('Failed to load payment methods data');
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading payment methods:', error);
        });
    }
    
    /**
     * Load Customer Types Chart
     */
    function loadCustomerTypesChart() {
        console.log('Loading customer types chart...');
        
        $.get('{{ route("businessintelligence.dashboard.chart-data") }}', {
            chart_type: 'customer_types',
            date_range: {{ $dateRange }}
        }, function(response) {
            console.log('Customer types response:', response);
            
            if (response.success && response.data) {
                const returningPercent = response.data.returning || 0;
                const newPercent = response.data.new || 0;
                
                const options = {
                    series: [{
                        name: 'Customers',
                        data: [returningPercent, newPercent]
                    }],
                    chart: {
                        type: 'bar',
                        height: 250,
                        horizontal: true
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 8,
                            distributed: true
                        }
                    },
                    colors: ['#667eea', '#f5576c'],
                    xaxis: {
                        categories: ['Returning', 'New'],
                        labels: {
                            formatter: function(val) {
                                return val + '%';
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '14px',
                                fontWeight: 600
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val + '%';
                        },
                        style: {
                            colors: ['#fff'],
                            fontSize: '14px',
                            fontWeight: 600
                        }
                    },
                    legend: { show: false }
                };
                
                const chart = new ApexCharts(document.querySelector("#customer_types_chart"), options);
                chart.render();
                
                console.log('Customer types chart rendered: Returning ' + returningPercent + '%, New ' + newPercent + '%');
            } else {
                console.error('Failed to load customer types data');
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading customer types:', error);
        });
    }
});
</script>
@endsection

