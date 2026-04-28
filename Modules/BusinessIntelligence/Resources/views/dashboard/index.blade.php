@extends('businessintelligence::layouts.app')

@section('page_title', __('businessintelligence::lang.dashboard'))
@section('page_subtitle', __('businessintelligence::lang.ai_powered_insights'))

@section('bi_content')

<style>
/* Modern Dashboard Styles */
.bi-modern-dashboard {
    background: #f5f7fa;
    padding: 20px;
}

/* Modern KPI Cards with Gradients */
.bi-kpi-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 25px;
    color: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    margin-bottom: 25px;
    position: relative;
    overflow: hidden;
}

.bi-kpi-modern::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 120px;
    height: 120px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.bi-kpi-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

.bi-kpi-modern.revenue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.bi-kpi-modern.profit { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.bi-kpi-modern.expense { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.bi-kpi-modern.inventory { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.bi-kpi-modern.customers { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.bi-kpi-modern.orders { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
.bi-kpi-modern.products { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
.bi-kpi-modern.transactions { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }

.bi-kpi-icon-modern {
    font-size: 42px;
    opacity: 0.9;
    margin-bottom: 10px;
}

.bi-kpi-value-modern {
    font-size: 32px;
    font-weight: 700;
    margin: 10px 0;
}

.bi-kpi-label-modern {
    font-size: 14px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.bi-trend-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    margin-top: 10px;
    background: rgba(255,255,255,0.2);
}

.bi-trend-badge i {
    margin-right: 5px;
}

/* Modern Chart Container */
.bi-chart-modern {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    transition: all 0.3s ease;
}

.bi-chart-modern:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.bi-chart-title {
    font-size: 18px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.bi-chart-subtitle {
    font-size: 13px;
    color: #718096;
    margin-top: -15px;
    margin-bottom: 15px;
}

/* AI Insights Panel */
.bi-insights-panel {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 30px;
    color: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    margin-bottom: 25px;
}

.bi-insights-panel h3 {
    margin-top: 0;
    margin-bottom: 25px;
    font-size: 22px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.bi-insights-panel h3 i {
    font-size: 24px;
}

.bi-insight-item {
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    backdrop-filter: blur(10px);
    border-left: 5px solid transparent;
    transition: all 0.3s ease;
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.bi-insight-item:hover {
    background: rgba(255,255,255,0.15);
    transform: translateX(5px);
}

.bi-insight-item.priority-critical {
    border-left-color: #ff6b6b;
}

.bi-insight-item.priority-high {
    border-left-color: #ffd93d;
}

.bi-insight-item.priority-medium {
    border-left-color: #6bcfff;
}

.bi-insight-item.priority-low {
    border-left-color: #6ae792;
}

.bi-insight-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
    background: rgba(255,255,255,0.2);
}

.bi-insight-icon.icon-critical {
    background: rgba(255, 107, 107, 0.3);
}

.bi-insight-icon.icon-high {
    background: rgba(255, 217, 61, 0.3);
}

.bi-insight-icon.icon-medium {
    background: rgba(107, 207, 255, 0.3);
}

.bi-insight-icon.icon-low {
    background: rgba(106, 231, 146, 0.3);
}

.bi-insight-text {
    flex: 1;
}

.bi-insight-title {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 8px;
    line-height: 1.4;
}

.bi-insight-description {
    font-size: 14px;
    opacity: 0.95;
    line-height: 1.6;
}

/* Statistics Grid */
.bi-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.bi-stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    text-align: center;
    transition: all 0.3s ease;
}

.bi-stat-card:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 20px rgba(0,0,0,0.12);
}

.bi-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #667eea;
    margin: 10px 0;
}

.bi-stat-label {
    font-size: 13px;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Filter Section */
.bi-filter-section {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

/* Custom Date Range Input */
#custom_date_range {
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 14px;
    cursor: pointer;
}

#custom_date_range:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    outline: none;
}

/* Loading Overlay */
.bi-loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.bi-loader {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .bi-kpi-value-modern {
        font-size: 24px;
    }
    
    .bi-kpi-icon-modern {
        font-size: 32px;
    }
}
</style>

<div class="bi-modern-dashboard">
    <!-- Loading Overlay -->
    <div class="bi-loading-overlay" id="loading_overlay">
        <div class="bi-loader"></div>
    </div>

    <!-- Filter Section -->
    <div class="bi-filter-section">
        <div class="row">
            <div class="col-md-6">
                <label><i class="fa fa-calendar"></i> Date Range</label>
                <select class="form-control" id="date_range_filter">
                    <option value="1" {{ $dateRange == 1 ? 'selected' : '' }}>Today</option>
                    <option value="7" {{ $dateRange == 7 ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30" {{ $dateRange == 30 ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90" {{ $dateRange == 90 ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="365" {{ $dateRange == 365 ? 'selected' : '' }}>Last Year</option>
                    <option value="custom" {{ $dateRange === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
            </div>
            <div class="col-md-6">
                <label><i class="fa fa-calendar"></i> Custom Date Range</label>
                <input type="text" class="form-control" id="custom_date_range" placeholder="Select date range" style="display: none;">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <label>&nbsp;</label><br>
                <div class="btn-group" role="group">
                    <button class="btn btn-primary" id="refresh_dashboard">
                        <i class="fa fa-refresh"></i> Refresh Data
                    </button>
                    <button class="btn btn-success" id="generate_insights">
                        <i class="fa fa-magic"></i> Generate AI Insights
                    </button>
                    <button class="btn btn-info" id="export_dashboard">
                        <i class="fa fa-download"></i> Export Report
                    </button>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <small class="text-muted" id="date_range_info">
                    @if($dateRange == 1)
                        Showing data for today
                    @elseif($dateRange === 'custom')
                        Showing custom date range data
                    @else
                        Showing last {{ $dateRange }} days of data
                    @endif
                </small>
            </div>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="row">
        @foreach($kpis as $key => $kpi)
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="bi-kpi-modern {{ $key }}">
                <div class="bi-kpi-icon-modern">
                    <i class="{{ $kpi['icon'] }}"></i>
                </div>
                <div class="bi-kpi-value-modern" id="kpi_{{ $key }}">
                    @if(isset($kpi['percentage']))
                        {{ $kpi['percentage'] }}%
                    @else
                        @php
                            $formatted_value = number_format($kpi['value'], session('business.currency_precision', 2), session('currency.decimal_separator', '.'), session('currency.thousand_separator', ','));
                            $currency_symbol = session('currency.symbol', '৳');
                            $symbol_placement = session('business.currency_symbol_placement', 'before');
                        @endphp
                        @if($symbol_placement == 'before')
                            {{ $currency_symbol }}{{ $formatted_value }}
                        @else
                            {{ $formatted_value }} {{ $currency_symbol }}
                        @endif
                    @endif
                </div>
                <div class="bi-kpi-label-modern">{{ $kpi['label'] }}</div>
                @if(isset($kpi['trend']))
                    <div class="bi-trend-badge">
                        <i class="fa fa-arrow-{{ $kpi['trend']['direction'] }}"></i>
                        {{ number_format($kpi['trend']['value'], 1) }}% vs last period
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts Row 1: Sales & Revenue -->
    <div class="row">
        <div class="col-md-8">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-line-chart"></i> Sales Trend & Revenue</span>
                    <span class="badge bg-primary">
                        @if($dateRange == 1)
                            Today
                        @else
                            Last {{ $dateRange }} Days
                        @endif
                    </span>
                </div>
                <div class="bi-chart-subtitle">Daily sales performance and revenue trends</div>
                <div id="sales_trend_chart" style="height: 350px;"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-pie-chart"></i> Revenue Sources</span>
                </div>
                <div class="bi-chart-subtitle">Revenue distribution by source</div>
                <div id="revenue_sources_chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Profit & Expenses -->
    <div class="row">
        <div class="col-md-6">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-bar-chart"></i> Profit vs Expenses</span>
                </div>
                <div class="bi-chart-subtitle">Monthly comparison of profit and expenses</div>
                <div id="profit_expense_chart" style="height: 300px;"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-money"></i> Cash Flow Analysis</span>
                </div>
                <div class="bi-chart-subtitle">Income vs outgoing cash flow</div>
                <div id="cash_flow_chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 3: Sales, Purchase & Expense Analytics -->
    <div class="row">
        <div class="col-md-12">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-line-chart"></i> Sales, Purchase & Expense Analytics</span>
                    <span class="badge badge-info ml-2">Last 6 Months</span>
                </div>
                <div class="bi-chart-subtitle">Comprehensive monthly analysis of sales, purchases, and expenses</div>
                <div id="sales_purchase_expense_chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 3.5: Comprehensive Profit & Loss Analysis -->
    <div class="row">
        <div class="col-md-8">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-calculator"></i> Profit & Loss Statement</span>
                    <span class="badge badge-success ml-2">Complete Breakdown</span>
                </div>
                <div class="bi-chart-subtitle">Complete financial performance analysis with P&L breakdown</div>
                <div id="profit_loss_complete_chart" style="height: 400px;"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-pie-chart"></i> P&L Components</span>
                </div>
                <div class="bi-chart-subtitle">Revenue, COGS, Expenses & Profit breakdown</div>
                <div id="profit_loss_breakdown_chart" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 4: Products & Inventory -->
    <div class="row">
        <div class="col-md-6">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-trophy"></i> Top 10 Products</span>
                </div>
                <div class="bi-chart-subtitle">Best selling products by revenue</div>
                <div id="top_products_chart" style="height: 400px;"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-cubes"></i> Inventory Status</span>
                </div>
                <div class="bi-chart-subtitle">Stock levels and inventory health</div>
                <div id="inventory_status_chart" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    <!-- AI Insights & Performance Summary -->
    <div class="row">
        <div class="col-md-8">
            <div class="bi-insights-panel">
                <h3>
                    <i class="fas fa-brain"></i> AI-Powered Insights & Recommendations
                </h3>
                <div id="ai_insights_container">
                    @if(count($insights) > 0)
                        @foreach($insights as $insight)
                        <div class="bi-insight-item priority-{{ $insight->priority }}">
                            <div class="bi-insight-icon icon-{{ $insight->priority }}">
                                @if($insight->priority == 'critical')
                                    <i class="fas fa-exclamation-triangle"></i>
                                @elseif($insight->priority == 'high')
                                    <i class="fas fa-exclamation-circle"></i>
                                @elseif($insight->priority == 'medium')
                                    <i class="fas fa-info-circle"></i>
                                @else
                                    <i class="fas fa-check-circle"></i>
                                @endif
                            </div>
                            <div class="bi-insight-text">
                                <div class="bi-insight-title">{{ $insight->title }}</div>
                                <div class="bi-insight-description">{{ $insight->description }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center" style="padding: 40px 20px;">
                            <i class="fas fa-magic" style="font-size: 56px; opacity: 0.4; margin-bottom: 20px; display: block;"></i>
                            <p style="font-size: 15px; margin-bottom: 20px; opacity: 0.9;">No insights generated yet. Click "Generate AI Insights" to analyze your business data.</p>
                            <button class="btn btn-warning btn-lg" id="generate_first_insights" style="border-radius: 25px; padding: 12px 30px; font-weight: 600;">
                                <i class="fas fa-magic"></i> Generate Insights Now
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-bullseye"></i> Performance Summary</span>
                </div>
                <div style="padding: 15px;">
                    <div style="border-bottom: 1px solid #e2e8f0; padding: 15px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <span style="color: #718096; font-size: 14px;">
                                <i class="fa fa-chart-line"></i> Revenue Growth
                            </span>
                            <span style="color: #667eea; font-weight: 600; font-size: 16px;" id="revenue_growth">
                                {{ isset($kpis['revenue']['trend']) ? number_format($kpis['revenue']['trend']['value'], 1) : '0' }}%
                            </span>
                        </div>
                        <div style="font-size: 12px; color: #a0aec0;">vs previous period</div>
                    </div>
                    
                    <div style="border-bottom: 1px solid #e2e8f0; padding: 15px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <span style="color: #718096; font-size: 14px;">
                                <i class="fa fa-money"></i> Profit Margin
                            </span>
                            <span style="color: #f5576c; font-weight: 600; font-size: 16px;" id="profit_margin_pct">
                                {{ number_format($kpis['profit_margin']['value'] ?? 0, 1) }}%
                            </span>
                        </div>
                        <div style="font-size: 12px; color: #a0aec0;">current margin rate</div>
                    </div>
                    
                    <div style="border-bottom: 1px solid #e2e8f0; padding: 15px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <span style="color: #718096; font-size: 14px;">
                                <i class="fa fa-shopping-cart"></i> Total Orders
                            </span>
                            <span style="color: #43e97b; font-weight: 600; font-size: 16px;" id="order_count">
                                {{ number_format($kpis['orders']['value'] ?? 0) }}
                            </span>
                        </div>
                        <div style="font-size: 12px; color: #a0aec0;">in selected period</div>
                    </div>
                    
                    <div style="padding: 15px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <span style="color: #718096; font-size: 14px;">
                                <i class="fa fa-users"></i> Active Customers
                            </span>
                            <span style="color: #ffd700; font-weight: 600; font-size: 16px;" id="customer_count">
                                {{ number_format($kpis['customers']['value'] ?? 0) }}
                            </span>
                        </div>
                        <div style="font-size: 12px; color: #a0aec0;">total customer base</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Breakdown & Customer Analytics -->
    <div class="row">
        <div class="col-md-6">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-credit-card"></i> Expense Categories</span>
                </div>
                <div class="bi-chart-subtitle">Breakdown of business expenses</div>
                <div id="expense_breakdown_chart" style="height: 350px;"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="bi-chart-modern">
                <div class="bi-chart-title">
                    <span><i class="fa fa-user-plus"></i> Customer Growth</span>
                </div>
                <div class="bi-chart-subtitle">New customers over time</div>
                <div id="customer_growth_chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.1/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.1/daterangepicker.css">
<script src="{{ asset('modules/businessintelligence/js/bi-dashboard-dynamic.js') }}"></script>
<script type="text/javascript">
// Override with inline version for now

$(document).ready(function() {
    const BiDashboard = {
        dateRange: '{{ $dateRange }}',
        customStartDate: null,
        customEndDate: null,
        currencySymbol: '{{ $currencySymbol ?? "৳" }}',
        currencyPrecision: {{ $currencyPrecision ?? 2 }},
        currencyDecimalSeparator: '{{ $currencyDecimalSeparator ?? "." }}',
        currencyThousandSeparator: '{{ $currencyThousandSeparator ?? "," }}',
        currencySymbolPlacement: '{{ session("business.currency_symbol_placement", "before") }}',
        charts: {},

        init() {
            this.setupEventListeners();
            this.loadAllCharts();
        },

        setupEventListeners() {
            $('#date_range_filter').on('change', (e) => {
                const selectedValue = $(e.target).val();
                if (selectedValue === 'custom') {
                    $('#custom_date_range').show();
                    this.initializeDateRangePicker();
                } else {
                    $('#custom_date_range').hide();
                    this.dateRange = selectedValue;
                    this.customStartDate = null;
                    this.customEndDate = null;
                    this.updateDateRangeInfo(selectedValue);
                    this.refreshDashboard();
                }
            });

            $('#custom_date_range').on('apply.daterangepicker', (e, picker) => {
                const startDate = picker.startDate.format('YYYY-MM-DD');
                const endDate = picker.endDate.format('YYYY-MM-DD');
                this.dateRange = 'custom';
                this.customStartDate = startDate;
                this.customEndDate = endDate;
                this.updateDateRangeInfo('custom', startDate, endDate);
                this.refreshDashboard();
            });

            $('#refresh_dashboard').on('click', () => {
                this.showLoading();
                this.refreshDashboard();
            });

            $('#generate_insights, #generate_first_insights').on('click', () => {
                this.generateInsights();
            });

            $('#export_dashboard').on('click', () => {
                this.exportDashboard();
            });

            // Initialize date range picker if custom is selected
            if ($('#date_range_filter').val() === 'custom') {
                $('#custom_date_range').show();
                this.initializeDateRangePicker();
            }
        },

        initializeDateRangePicker() {
            const startDate = '{{ request()->get("start_date") ?? now()->subDays(29)->format("Y-m-d") }}';
            const endDate = '{{ request()->get("end_date") ?? now()->format("Y-m-d") }}';
            
            $('#custom_date_range').daterangepicker({
                startDate: moment(startDate),
                endDate: moment(endDate),
                locale: {
                    format: 'YYYY-MM-DD'
                },
                opens: 'left',
                autoUpdateInput: true
            });
        },

        buildAjaxData(baseData) {
            const data = { ...baseData, date_range: this.dateRange };
            
            // Add custom date parameters if using custom range
            if (this.dateRange === 'custom' && this.customStartDate && this.customEndDate) {
                data.start_date = this.customStartDate;
                data.end_date = this.customEndDate;
            }
            
            return data;
        },

        showLoading() {
            $('#loading_overlay').css('display', 'flex');
        },

        hideLoading() {
            $('#loading_overlay').hide();
        },

        loadAllCharts() {
            this.loadSalesTrendChart();
            this.loadRevenueSourcesChart();
            this.loadProfitExpenseChart();
            this.loadCashFlowChart();
            this.loadSalesPurchaseExpenseChart();
            this.loadProfitLossCompleteChart();
            this.loadTopProductsChart();
            this.loadInventoryStatusChart();
            this.loadExpenseBreakdownChart();
            this.loadCustomerGrowthChart();
        },

        loadSalesTrendChart() {
            this.showLoading();
            const ajaxData = this.buildAjaxData({
                chart_type: 'sales_trend'
            });

            $.ajax({
                url: '{{ route("businessintelligence.dashboard.chart-data") }}',
                method: 'GET',
                data: ajaxData,
                success: (response) => {
                    this.hideLoading();
                    if (response.success) {
                        const data = response.data;
                        console.log('Sales Trend Data:', data);
                        console.log('Categories:', data.categories);
                        console.log('Sales:', data.sales);
                        const options = {
                            series: [{
                                name: 'Sales',
                                data: data.sales || []
                            }],
                            chart: {
                                type: 'area',
                                height: 350,
                                zoom: { enabled: true },
                                toolbar: { show: true }
                            },
                            dataLabels: { enabled: false },
                            stroke: { curve: 'smooth', width: 3 },
                            colors: ['#667eea'],
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.7,
                                    opacityTo: 0.3,
                                }
                            },
                            xaxis: {
                                categories: data.categories || [],
                                labels: {
                                    rotate: -45,
                                    rotateAlways: false
                                }
                            },
                            tooltip: {
                                theme: 'dark',
                                y: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString(undefined, {
                                            minimumFractionDigits: BiDashboard.currencyPrecision,
                                            maximumFractionDigits: BiDashboard.currencyPrecision
                                        });
                                    }
                                }
                            },
                            yaxis: {
                                labels: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString(undefined, {
                                            minimumFractionDigits: BiDashboard.currencyPrecision,
                                            maximumFractionDigits: BiDashboard.currencyPrecision
                                        });
                                    }
                                }
                            }
                        };
                        if (this.charts.salesTrend) this.charts.salesTrend.destroy();
                        this.charts.salesTrend = new ApexCharts(document.querySelector("#sales_trend_chart"), options);
                        this.charts.salesTrend.render();
                    }
                },
                error: () => {
                    this.hideLoading();
                    console.error('Failed to load sales trend chart');
                }
            });
        },

        loadRevenueSourcesChart() {
            // Prevent multiple simultaneous requests
            if (this.loadingRevenueSources) {
                return;
            }
            this.loadingRevenueSources = true;

            const ajaxData = this.buildAjaxData({
                chart_type: 'revenue_sources'
            });

            $.ajax({
                url: '{{ route("businessintelligence.dashboard.chart-data") }}',
                method: 'GET',
                data: ajaxData,
                success: (response) => {
                    this.loadingRevenueSources = false;
                    if (response.success) {
                        const data = response.data;

                        // Clear the chart container first
                        const container = document.querySelector("#revenue_sources_chart");
                        if (container) {
                            container.innerHTML = '';
                        }

                        // Properly destroy existing chart
                        if (this.charts.revenueSources) {
                            try {
                                this.charts.revenueSources.destroy();
                            } catch (e) {
                                console.warn('Error destroying revenue sources chart:', e);
                            }
                            this.charts.revenueSources = null;
                        }

                        const options = {
                            series: data.series || [],
                            chart: {
                                type: 'donut',
                                height: 350,
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                }
                            },
                            labels: data.labels || [],
                            colors: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#43e97b'],
                            legend: { position: 'bottom' },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        labels: {
                                            show: true,
                                            total: {
                                                show: true,
                                                label: 'Total Revenue',
                                                formatter: (w) => {
                                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                                    return BiDashboard.currencySymbol + total.toLocaleString(undefined, {
                                                        minimumFractionDigits: BiDashboard.currencyPrecision,
                                                        maximumFractionDigits: BiDashboard.currencyPrecision
                                                    });
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            tooltip: {
                                theme: 'dark',
                                y: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString(undefined, {
                                            minimumFractionDigits: BiDashboard.currencyPrecision,
                                            maximumFractionDigits: BiDashboard.currencyPrecision
                                        });
                                    }
                                }
                            }
                        };

                        try {
                            this.charts.revenueSources = new ApexCharts(container, options);
                            this.charts.revenueSources.render();
                        } catch (error) {
                            console.error('Error rendering revenue sources chart:', error);
                        }
                    }
                },
                error: (xhr, status, error) => {
                    this.loadingRevenueSources = false;
                    console.error('Failed to load revenue sources chart:', error);
                }
            });
        },

        loadProfitExpenseChart() {
            // Prevent multiple simultaneous requests
            if (this.loadingProfitExpense) {
                return;
            }
            this.loadingProfitExpense = true;

            console.log('Loading Profit vs Expenses chart...');
            const ajaxData = this.buildAjaxData({
                chart_type: 'profit_expense'
            });

            $.ajax({
                url: '{{ route("businessintelligence.dashboard.chart-data") }}',
                method: 'GET',
                data: ajaxData,
                success: (response) => {
                    this.loadingProfitExpense = false;
                    console.log('Profit Expense AJAX Response:', response);
                    if (response.success) {
                        const data = response.data;
                        console.log('Profit vs Expenses Data:', data);
                        console.log('Categories:', data.categories);
                        console.log('Profit:', data.profit);
                        console.log('Expenses:', data.expenses);

                        // Clear the chart container first
                        const container = document.querySelector("#profit_expense_chart");
                        if (container) {
                            container.innerHTML = '';
                        }

                        // Properly destroy existing chart
                        if (this.charts.profitExpense) {
                            try {
                                this.charts.profitExpense.destroy();
                            } catch (e) {
                                console.warn('Error destroying profit expense chart:', e);
                            }
                            this.charts.profitExpense = null;
                        }

                        if (!container) {
                            console.error('Profit expense chart container not found!');
                            return;
                        }

                        const options = {
                            series: [{
                                name: 'Profit',
                                data: data.profit || []
                            }, {
                                name: 'Expenses',
                                data: data.expenses || []
                            }],
                            chart: {
                                type: 'bar',
                                height: 300,
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                }
                            },
                            colors: ['#43e97b', '#f5576c'],
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '55%',
                                    endingShape: 'rounded'
                                },
                            },
                            dataLabels: { enabled: false },
                            xaxis: {
                                categories: data.categories || [],
                            },
                            yaxis: {
                                labels: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString(undefined, {
                                            minimumFractionDigits: BiDashboard.currencyPrecision,
                                            maximumFractionDigits: BiDashboard.currencyPrecision
                                        });
                                    }
                                }
                            },
                            tooltip: {
                                theme: 'dark',
                                shared: true,
                                intersect: false,
                                y: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString(undefined, {
                                            minimumFractionDigits: BiDashboard.currencyPrecision,
                                            maximumFractionDigits: BiDashboard.currencyPrecision
                                        });
                                    }
                                }
                            }
                        };

                        console.log('Chart options:', options);

                        try {
                            console.log('Creating new chart...');
                            this.charts.profitExpense = new ApexCharts(container, options);
                            this.charts.profitExpense.render();
                            console.log('Chart rendered successfully!');
                        } catch (error) {
                            console.error('Error rendering chart:', error);
                        }
                    } else {
                        console.error('Response not successful:', response);
                    }
                },
                error: (xhr, status, error) => {
                    this.loadingProfitExpense = false;
                    console.error('Failed to load profit expense chart');
                    console.error('XHR:', xhr);
                    console.error('Status:', status);
                    console.error('Error:', error);
                }
            });
        },

        loadCashFlowChart() {
            // Prevent multiple simultaneous requests
            if (this.loadingCashFlow) {
                return;
            }
            this.loadingCashFlow = true;

            const ajaxData = this.buildAjaxData({
                chart_type: 'cash_flow'
            });

            $.ajax({
                url: '{{ route("businessintelligence.dashboard.chart-data") }}',
                method: 'GET',
                data: ajaxData,
                success: (response) => {
                    this.loadingCashFlow = false;
                    if (response.success) {
                        const data = response.data;

                        // Clear the chart container first
                        const container = document.querySelector("#cash_flow_chart");
                        if (container) {
                            container.innerHTML = '';
                        }

                        // Properly destroy existing chart
                        if (this.charts.cashFlow) {
                            try {
                                this.charts.cashFlow.destroy();
                            } catch (e) {
                                console.warn('Error destroying cash flow chart:', e);
                            }
                            this.charts.cashFlow = null;
                        }

                        const options = {
                            series: data.series || [],
                            chart: {
                                type: 'line',
                                height: 300,
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                }
                            },
                            colors: ['#43e97b', '#f5576c'],
                            stroke: { width: [4, 4], curve: 'smooth' },
                            xaxis: {
                                categories: data.categories || []
                            },
                            yaxis: {
                                labels: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString(undefined, {
                                            minimumFractionDigits: BiDashboard.currencyPrecision,
                                            maximumFractionDigits: BiDashboard.currencyPrecision
                                        });
                                    }
                                }
                            },
                            tooltip: {
                                theme: 'dark',
                                shared: true,
                                intersect: false,
                                y: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString(undefined, {
                                            minimumFractionDigits: BiDashboard.currencyPrecision,
                                            maximumFractionDigits: BiDashboard.currencyPrecision
                                        });
                                    }
                                }
                            }
                        };

                        try {
                            this.charts.cashFlow = new ApexCharts(container, options);
                            this.charts.cashFlow.render();
                        } catch (error) {
                            console.error('Error rendering cash flow chart:', error);
                        }
                    }
                },
                error: (xhr, status, error) => {
                    this.loadingCashFlow = false;
                    console.error('Failed to load cash flow chart:', error);
                }
            });
        },

        loadProfitLossCompleteChart() {
            console.log('Loading Comprehensive Profit & Loss chart...');
            const ajaxData = this.buildAjaxData({
                chart_type: 'profit_loss_complete'
            });

            $.ajax({
                url: '{{ route("businessintelligence.dashboard.chart-data") }}',
                method: 'GET',
                data: ajaxData,
                success: (response) => {
                    console.log('P&L Complete Response:', response);
                    if (response.success) {
                        const data = response.data;
                        console.log('P&L Data:', data);
                        
                        // Main P&L Chart (Column Chart)
                        const mainChartOptions = {
                            series: [{
                                name: 'Amount',
                                data: data.data.map(item => ({
                                    x: item.name,
                                    y: item.value,
                                    fillColor: item.color
                                }))
                            }],
                            chart: {
                                type: 'bar',
                                height: 400,
                                toolbar: {
                                    show: true,
                                    tools: {
                                        download: true
                                    }
                                },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 1000,
                                    animateGradually: {
                                        enabled: true,
                                        delay: 200
                                    }
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '60%',
                                    endingShape: 'rounded',
                                    borderRadius: 8,
                                    dataLabels: {
                                        position: 'top'
                                    },
                                    distributed: true
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function (val) {
                                    return BiDashboard.currencySymbol + val.toLocaleString();
                                },
                                offsetY: -25,
                                style: {
                                    fontSize: '13px',
                                    colors: ['#304758'],
                                    fontWeight: 'bold'
                                }
                            },
                            xaxis: {
                                labels: {
                                    style: {
                                        colors: data.data.map(item => item.color),
                                        fontSize: '13px',
                                        fontWeight: 600
                                    }
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Amount (' + BiDashboard.currencySymbol + ')',
                                    style: {
                                        fontSize: '14px',
                                        fontWeight: 600
                                    }
                                },
                                labels: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString();
                                    }
                                }
                            },
                            legend: {
                                show: false
                            },
                            tooltip: {
                                theme: 'dark',
                                y: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString(undefined, {
                                            minimumFractionDigits: BiDashboard.currencyPrecision,
                                            maximumFractionDigits: BiDashboard.currencyPrecision
                                        });
                                    }
                                }
                            },
                            grid: {
                                borderColor: '#f1f1f1',
                                strokeDashArray: 4
                            }
                        };
                        
                        // Breakdown Donut Chart
                        const breakdown = data.detailed_breakdown;
                        const breakdownOptions = {
                            series: [
                                breakdown.revenue.net_revenue,
                                breakdown.cogs.total_cogs,
                                breakdown.expenses.total_expenses,
                                Math.abs(breakdown.profit.net_profit)
                            ],
                            chart: {
                                type: 'donut',
                                height: 400,
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 1200
                                }
                            },
                            labels: ['Revenue', 'COGS', 'Expenses', 'Net Profit'],
                            colors: ['#00E396', '#FF4560', '#FEB019', breakdown.profit.net_profit >= 0 ? '#26de81' : '#fc5c65'],
                            dataLabels: {
                                enabled: true,
                                formatter: function (val, opts) {
                                    return val.toFixed(1) + '%';
                                },
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    colors: ['#fff']
                                },
                                dropShadow: {
                                    enabled: true,
                                    top: 1,
                                    left: 1,
                                    blur: 1,
                                    opacity: 0.45
                                }
                            },
                            legend: {
                                position: 'bottom',
                                fontSize: '13px',
                                fontWeight: 500,
                                markers: {
                                    width: 12,
                                    height: 12,
                                    radius: 3
                                }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '65%',
                                        labels: {
                                            show: true,
                                            name: {
                                                show: true,
                                                fontSize: '18px',
                                                fontWeight: 600,
                                                offsetY: -10
                                            },
                                            value: {
                                                show: true,
                                                fontSize: '22px',
                                                fontWeight: 'bold',
                                                offsetY: 10,
                                                formatter: function (val) {
                                                    return BiDashboard.currencySymbol + parseFloat(val).toLocaleString();
                                                }
                                            },
                                            total: {
                                                show: true,
                                                showAlways: true,
                                                label: 'Total Revenue',
                                                fontSize: '16px',
                                                fontWeight: 600,
                                                color: '#373d3f',
                                                formatter: function (w) {
                                                    return BiDashboard.currencySymbol + breakdown.revenue.net_revenue.toLocaleString();
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            tooltip: {
                                theme: 'dark',
                                y: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString(undefined, {
                                            minimumFractionDigits: BiDashboard.currencyPrecision,
                                            maximumFractionDigits: BiDashboard.currencyPrecision
                                        });
                                    }
                                }
                            },
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: 300
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }]
                        };
                        
                        // Destroy and render main chart
                        if (this.charts.profitLossComplete) {
                            this.charts.profitLossComplete.destroy();
                        }
                        this.charts.profitLossComplete = new ApexCharts(
                            document.querySelector("#profit_loss_complete_chart"),
                            mainChartOptions
                        );
                        this.charts.profitLossComplete.render();
                        
                        // Destroy and render breakdown chart
                        if (this.charts.profitLossBreakdown) {
                            this.charts.profitLossBreakdown.destroy();
                        }
                        this.charts.profitLossBreakdown = new ApexCharts(
                            document.querySelector("#profit_loss_breakdown_chart"),
                            breakdownOptions
                        );
                        this.charts.profitLossBreakdown.render();
                        
                        console.log('P&L charts rendered successfully!');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Failed to load profit & loss chart');
                    console.error('Error:', error);
                }
            });
        },

        loadSalesPurchaseExpenseChart() {
            console.log('Loading Sales, Purchase & Expense Analytics chart...');
            const ajaxData = this.buildAjaxData({
                chart_type: 'sales_purchase_expense_analytics'
            });

            $.ajax({
                url: '{{ route("businessintelligence.dashboard.chart-data") }}',
                method: 'GET',
                data: ajaxData,
                success: (response) => {
                    console.log('Sales Purchase Expense Response:', response);
                    if (response.success) {
                        const data = response.data;
                        console.log('Chart Data:', data);
                        
                        const options = {
                            series: [
                                {
                                    name: 'Sales',
                                    data: data.sales || []
                                },
                                {
                                    name: 'Purchases',
                                    data: data.purchases || []
                                },
                                {
                                    name: 'Expenses',
                                    data: data.expenses || []
                                }
                            ],
                            chart: {
                                type: 'bar',
                                height: 380,
                                toolbar: {
                                    show: true,
                                    tools: {
                                        download: true,
                                        selection: true,
                                        zoom: true,
                                        zoomin: true,
                                        zoomout: true,
                                        pan: true,
                                        reset: true
                                    },
                                    export: {
                                        csv: {
                                            filename: 'sales-purchase-expense-analytics',
                                        },
                                        svg: {
                                            filename: 'sales-purchase-expense-analytics',
                                        },
                                        png: {
                                            filename: 'sales-purchase-expense-analytics',
                                        }
                                    }
                                },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800,
                                    animateGradually: {
                                        enabled: true,
                                        delay: 150
                                    },
                                    dynamicAnimation: {
                                        enabled: true,
                                        speed: 350
                                    }
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '65%',
                                    endingShape: 'rounded',
                                    borderRadius: 8,
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            colors: ['#00E396', '#008FFB', '#FF4560'],
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            xaxis: {
                                categories: data.categories || [],
                                labels: {
                                    style: {
                                        colors: '#666',
                                        fontSize: '12px',
                                        fontFamily: 'Arial, sans-serif',
                                        fontWeight: 500,
                                    },
                                    rotate: -45,
                                    rotateAlways: false
                                },
                                axisBorder: {
                                    show: true,
                                    color: '#e0e0e0'
                                },
                                axisTicks: {
                                    show: true,
                                    color: '#e0e0e0'
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Amount (' + BiDashboard.currencySymbol + ')',
                                    style: {
                                        color: '#666',
                                        fontSize: '14px',
                                        fontFamily: 'Arial, sans-serif',
                                        fontWeight: 600,
                                    }
                                },
                                labels: {
                                    style: {
                                        colors: '#666',
                                        fontSize: '12px'
                                    },
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString();
                                    }
                                }
                            },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shade: 'light',
                                    type: 'vertical',
                                    shadeIntensity: 0.5,
                                    gradientToColors: ['#26de81', '#45aaf2', '#fc5c65'],
                                    inverseColors: false,
                                    opacityFrom: 0.95,
                                    opacityTo: 0.75,
                                    stops: [0, 100]
                                }
                            },
                            tooltip: {
                                theme: 'dark',
                                shared: true,
                                intersect: false,
                                style: {
                                    fontSize: '13px',
                                    fontFamily: 'Arial, sans-serif'
                                },
                                y: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString(undefined, {
                                            minimumFractionDigits: BiDashboard.currencyPrecision,
                                            maximumFractionDigits: BiDashboard.currencyPrecision
                                        });
                                    }
                                },
                                marker: {
                                    show: true
                                }
                            },
                            legend: {
                                position: 'top',
                                horizontalAlign: 'right',
                                floating: false,
                                offsetY: 0,
                                offsetX: 0,
                                fontSize: '14px',
                                fontFamily: 'Arial, sans-serif',
                                fontWeight: 500,
                                markers: {
                                    width: 14,
                                    height: 14,
                                    strokeWidth: 0,
                                    strokeColor: '#fff',
                                    radius: 3
                                },
                                itemMargin: {
                                    horizontal: 10,
                                    vertical: 0
                                }
                            },
                            grid: {
                                show: true,
                                borderColor: '#f1f1f1',
                                strokeDashArray: 4,
                                position: 'back',
                                xaxis: {
                                    lines: {
                                        show: false
                                    }
                                },   
                                yaxis: {
                                    lines: {
                                        show: true
                                    }
                                },
                                row: {
                                    colors: ['#fafafa', 'transparent'],
                                    opacity: 0.5
                                },
                                padding: {
                                    top: 0,
                                    right: 10,
                                    bottom: 0,
                                    left: 10
                                }
                            },
                            states: {
                                hover: {
                                    filter: {
                                        type: 'darken',
                                        value: 0.85
                                    }
                                },
                                active: {
                                    filter: {
                                        type: 'darken',
                                        value: 0.75
                                    }
                                }
                            }
                        };
                        
                        if (this.charts.salesPurchaseExpense) {
                            this.charts.salesPurchaseExpense.destroy();
                        }
                        this.charts.salesPurchaseExpense = new ApexCharts(
                            document.querySelector("#sales_purchase_expense_chart"), 
                            options
                        );
                        this.charts.salesPurchaseExpense.render();
                        console.log('Chart rendered successfully!');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Failed to load sales purchase expense chart');
                    console.error('Error:', error);
                }
            });
        },

        loadTopProductsChart() {
            // Prevent multiple simultaneous requests
            if (this.loadingTopProducts) {
                return;
            }
            this.loadingTopProducts = true;

            const ajaxData = this.buildAjaxData({
                chart_type: 'top_products'
            });

            $.ajax({
                url: '{{ route("businessintelligence.dashboard.chart-data") }}',
                method: 'GET',
                data: ajaxData,
                success: (response) => {
                    this.loadingTopProducts = false;
                    if (response.success) {
                        const data = response.data;

                        // Clear the chart container first
                        const container = document.querySelector("#top_products_chart");
                        if (container) {
                            container.innerHTML = '';
                        }

                        // Properly destroy existing chart
                        if (this.charts.topProducts) {
                            try {
                                this.charts.topProducts.destroy();
                            } catch (e) {
                                console.warn('Error destroying top products chart:', e);
                            }
                            this.charts.topProducts = null;
                        }

                        const options = {
                            series: [{
                                data: data.data || []
                            }],
                            chart: {
                                type: 'bar',
                                height: 400,
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                }
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 4,
                                    horizontal: true,
                                }
                            },
                            colors: ['#667eea'],
                            dataLabels: {
                                enabled: true,
                                formatter: function (value) {
                                    return BiDashboard.currencySymbol + value.toFixed(BiDashboard.currencyPrecision);
                                }
                            },
                            xaxis: {
                                categories: data.categories || []
                            },
                            tooltip: {
                                theme: 'dark',
                                y: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toFixed(BiDashboard.currencyPrecision);
                                    }
                                }
                            }
                        };

                        try {
                            this.charts.topProducts = new ApexCharts(container, options);
                            this.charts.topProducts.render();
                        } catch (error) {
                            console.error('Error rendering top products chart:', error);
                        }
                    }
                },
                error: (xhr, status, error) => {
                    this.loadingTopProducts = false;
                    console.error('Failed to load top products chart:', error);
                }
            });
        },

        loadInventoryStatusChart() {
            // Prevent multiple simultaneous requests
            if (this.loadingInventoryStatus) {
                return;
            }
            this.loadingInventoryStatus = true;

            const ajaxData = this.buildAjaxData({
                chart_type: 'inventory_status'
            });

            $.ajax({
                url: '{{ route("businessintelligence.dashboard.chart-data") }}',
                method: 'GET',
                data: ajaxData,
                success: (response) => {
                    this.loadingInventoryStatus = false;
                    if (response.success) {
                        const data = response.data;

                        // Clear the chart container first
                        const container = document.querySelector("#inventory_status_chart");
                        if (container) {
                            container.innerHTML = '';
                        }

                        // Properly destroy existing chart
                        if (this.charts.inventoryStatus) {
                            try {
                                this.charts.inventoryStatus.destroy();
                            } catch (e) {
                                console.warn('Error destroying inventory status chart:', e);
                            }
                            this.charts.inventoryStatus = null;
                        }

                        const options = {
                            series: data.series || [],
                            chart: {
                                type: 'donut',
                                height: 400,
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                }
                            },
                            labels: data.labels || [],
                            colors: ['#43e97b', '#ffd700', '#f5576c'],
                            legend: { position: 'bottom' },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        labels: {
                                            show: true,
                                            total: {
                                                show: true,
                                                label: 'Total Items',
                                                formatter: (w) => {
                                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                                    return total.toString();
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            tooltip: {
                                theme: 'dark',
                                y: {
                                    formatter: function (value) {
                                        return value.toString();
                                    }
                                }
                            }
                        };

                        try {
                            this.charts.inventoryStatus = new ApexCharts(container, options);
                            this.charts.inventoryStatus.render();
                        } catch (error) {
                            console.error('Error rendering inventory status chart:', error);
                        }
                    }
                },
                error: (xhr, status, error) => {
                    this.loadingInventoryStatus = false;
                    console.error('Failed to load inventory status chart:', error);
                }
            });
        },

        loadExpenseBreakdownChart() {
            // Prevent multiple simultaneous requests
            if (this.loadingExpenseBreakdown) {
                return;
            }
            this.loadingExpenseBreakdown = true;

            const ajaxData = this.buildAjaxData({
                chart_type: 'expense_breakdown'
            });

            $.ajax({
                url: '{{ route("businessintelligence.dashboard.chart-data") }}',
                method: 'GET',
                data: ajaxData,
                success: (response) => {
                    this.loadingExpenseBreakdown = false;
                    if (response.success) {
                        const data = response.data;

                        // Clear the chart container first
                        const container = document.querySelector("#expense_breakdown_chart");
                        if (container) {
                            container.innerHTML = '';
                        }

                        // Properly destroy existing chart
                        if (this.charts.expenseBreakdown) {
                            try {
                                this.charts.expenseBreakdown.destroy();
                            } catch (e) {
                                console.warn('Error destroying expense breakdown chart:', e);
                            }
                            this.charts.expenseBreakdown = null;
                        }

                        const options = {
                            series: data.series || [],
                            chart: {
                                type: 'pie',
                                height: 350,
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                }
                            },
                            labels: data.labels || [],
                            colors: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#43e97b'],
                            legend: { position: 'bottom' },
                            tooltip: {
                                theme: 'dark',
                                y: {
                                    formatter: function (value) {
                                        return BiDashboard.currencySymbol + value.toLocaleString(undefined, {
                                            minimumFractionDigits: BiDashboard.currencyPrecision,
                                            maximumFractionDigits: BiDashboard.currencyPrecision
                                        });
                                    }
                                }
                            }
                        };

                        try {
                            this.charts.expenseBreakdown = new ApexCharts(container, options);
                            this.charts.expenseBreakdown.render();
                        } catch (error) {
                            console.error('Error rendering expense breakdown chart:', error);
                        }
                    }
                },
                error: (xhr, status, error) => {
                    this.loadingExpenseBreakdown = false;
                    console.error('Failed to load expense breakdown chart:', error);
                }
            });
        },

        loadCustomerGrowthChart() {
            // Prevent multiple simultaneous requests
            if (this.loadingCustomerGrowth) {
                return;
            }
            this.loadingCustomerGrowth = true;

            const ajaxData = this.buildAjaxData({
                chart_type: 'customer_growth'
            });

            $.ajax({
                url: '{{ route("businessintelligence.dashboard.chart-data") }}',
                method: 'GET',
                data: ajaxData,
                success: (response) => {
                    this.loadingCustomerGrowth = false;
                    if (response.success) {
                        const data = response.data;

                        // Clear the chart container first
                        const container = document.querySelector("#customer_growth_chart");
                        if (container) {
                            container.innerHTML = '';
                        }

                        // Properly destroy existing chart
                        if (this.charts.customerGrowth) {
                            try {
                                this.charts.customerGrowth.destroy();
                            } catch (e) {
                                console.warn('Error destroying customer growth chart:', e);
                            }
                            this.charts.customerGrowth = null;
                        }

                        const options = {
                            series: [{
                                name: 'New Customers',
                                data: data.data || []
                            }],
                            chart: {
                                type: 'line',
                                height: 350,
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                }
                            },
                            stroke: { width: 5, curve: 'smooth' },
                            colors: ['#667eea'],
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shade: 'dark',
                                    gradientToColors: ['#764ba2'],
                                    shadeIntensity: 1,
                                    type: 'horizontal',
                                    opacityFrom: 1,
                                    opacityTo: 1,
                                }
                            },
                            xaxis: {
                                categories: data.categories || []
                            },
                            tooltip: {
                                theme: 'dark',
                                y: {
                                    formatter: function (value) {
                                        return value.toString();
                                    }
                                }
                            }
                        };

                        try {
                            this.charts.customerGrowth = new ApexCharts(container, options);
                            this.charts.customerGrowth.render();
                        } catch (error) {
                            console.error('Error rendering customer growth chart:', error);
                        }
                    }
                },
                error: (xhr, status, error) => {
                    this.loadingCustomerGrowth = false;
                    console.error('Failed to load customer growth chart:', error);
                }
            });
        },

        refreshDashboard() {
            this.showLoading();
            // Use AJAX to refresh data instead of full page reload
            const data = {
                _token: '{{ csrf_token() }}',
                date_range: this.dateRange
            };
            
            // Add custom date parameters if using custom range
            if (this.dateRange === 'custom' && this.customStartDate && this.customEndDate) {
                data.start_date = this.customStartDate;
                data.end_date = this.customEndDate;
            }
            
            $.ajax({
                url: '{{ route("businessintelligence.dashboard.refresh") }}',
                method: 'POST',
                data: data,
                success: (response) => {
                    this.hideLoading();
                    if (response.success) {
                        // Reload the page with parameters to show fresh data
                        const url = new URL(window.location);
                        url.searchParams.set('date_range', this.dateRange);
                        if (this.dateRange === 'custom') {
                            url.searchParams.set('start_date', this.customStartDate);
                            url.searchParams.set('end_date', this.customEndDate);
                        }
                        window.location.href = url.toString();
                    } else {
                        console.error('Refresh failed:', response.message);
                        this.hideLoading();
                    }
                },
                error: (xhr, status, error) => {
                    this.hideLoading();
                    console.error('Refresh error:', error);
                    // Fallback to page reload with parameters
                    const url = new URL(window.location);
                    url.searchParams.set('date_range', this.dateRange);
                    if (this.dateRange === 'custom') {
                        url.searchParams.set('start_date', this.customStartDate);
                        url.searchParams.set('end_date', this.customEndDate);
                    }
                    window.location.href = url.toString();
                }
            });
        },

        generateInsights() {
            this.showLoading();
            const data = {
                _token: '{{ csrf_token() }}',
                date_range: this.dateRange
            };
            
            // Add custom date parameters if using custom range
            if (this.dateRange === 'custom' && this.customStartDate && this.customEndDate) {
                data.start_date = this.customStartDate;
                data.end_date = this.customEndDate;
            }
            
            $.ajax({
                url: '{{ route("businessintelligence.insights.generate") }}',
                method: 'POST',
                data: data,
                success: (response) => {
                    this.hideLoading();
                    if (response.success) {
                        swal('Success', 'AI Insights generated successfully!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    }
                },
                error: () => {
                    this.hideLoading();
                    swal('Error', 'Failed to generate insights', 'error');
                }
            });
        },

        exportDashboard() {
            let url = '{{ route("businessintelligence.dashboard.export") }}?date_range=' + this.dateRange;
            if (this.dateRange === 'custom' && this.customStartDate && this.customEndDate) {
                url += '&start_date=' + this.customStartDate + '&end_date=' + this.customEndDate;
            }
            window.location.href = url;
        },

        updateDateRangeInfo(rangeType, startDate = null, endDate = null) {
            const infoElement = $('#date_range_info');
            if (rangeType == '1') {
                infoElement.text('Showing data for today');
            } else if (rangeType === 'custom' && startDate && endDate) {
                infoElement.text('Showing data from ' + startDate + ' to ' + endDate);
            } else {
                infoElement.text('Showing last ' + rangeType + ' days of data');
            }
        }
    };

    BiDashboard.init();
});
</script>
@endsection
