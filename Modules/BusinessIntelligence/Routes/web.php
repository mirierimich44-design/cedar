<?php

/*
|--------------------------------------------------------------------------
| Web Routes - Business Intelligence Module
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Business Intelligence module.
|
*/

Route::middleware(['web', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu'])->prefix('business-intelligence')->group(function () {
    
    // Dashboard Routes
    Route::get('/dashboard', 'DashboardController@index')->name('businessintelligence.dashboard');
    Route::get('/dashboard/kpis', 'DashboardController@getKPIs')->name('businessintelligence.dashboard.kpis');
    Route::get('/dashboard/chart-data', 'DashboardController@getChartData')->name('businessintelligence.dashboard.chart-data');
    Route::get('/dashboard/performance', 'DashboardController@getPerformanceSummary')->name('businessintelligence.dashboard.performance');
    Route::post('/dashboard/refresh', 'DashboardController@refreshData')->name('businessintelligence.dashboard.refresh');
    Route::get('/dashboard/export', 'DashboardController@exportDashboard')->name('businessintelligence.dashboard.export');

    // Analytics Routes
    Route::prefix('analytics')->group(function () {
        Route::get('/sales', 'AnalyticsController@getSalesAnalytics')->name('businessintelligence.analytics.sales');
        Route::get('/inventory', 'AnalyticsController@getInventoryAnalytics')->name('businessintelligence.analytics.inventory');
        Route::get('/financial', 'AnalyticsController@getFinancialAnalytics')->name('businessintelligence.analytics.financial');
        Route::get('/customer', 'AnalyticsController@getCustomerAnalytics')->name('businessintelligence.analytics.customer');
        Route::get('/supplier', 'AnalyticsController@getSupplierAnalytics')->name('businessintelligence.analytics.supplier');
        Route::get('/comprehensive', 'AnalyticsController@getComprehensiveAnalytics')->name('businessintelligence.analytics.comprehensive');
        Route::match(['get', 'post'], '/export', 'AnalyticsController@exportAnalytics')->name('businessintelligence.analytics.export');
    });

    // Insights Routes
    Route::prefix('insights')->group(function () {
        Route::get('/', 'InsightsController@index')->name('businessintelligence.insights.index');
        Route::get('/data', 'InsightsController@getInsights')->name('businessintelligence.insights.data');
        Route::post('/generate', 'InsightsController@generateInsights')->name('businessintelligence.insights.generate');
        Route::get('/type/{type}', 'InsightsController@getByType')->name('businessintelligence.insights.by-type');
        Route::get('/critical', 'InsightsController@getCritical')->name('businessintelligence.insights.critical');
        Route::get('/{id}', 'InsightsController@show')->name('businessintelligence.insights.show');
        Route::post('/{id}/acknowledge', 'InsightsController@acknowledge')->name('businessintelligence.insights.acknowledge');
        Route::post('/{id}/dismiss', 'InsightsController@dismiss')->name('businessintelligence.insights.dismiss');
        Route::post('/{id}/resolve', 'InsightsController@resolve')->name('businessintelligence.insights.resolve');
    });

    // Configuration Routes
    Route::prefix('configuration')->group(function () {
        Route::get('/', 'ConfigurationController@index')->name('businessintelligence.configuration.index');
        Route::get('/data', 'ConfigurationController@getConfigurations')->name('businessintelligence.configuration.data');
        Route::get('/{key}', 'ConfigurationController@getConfiguration')->name('businessintelligence.configuration.show');
        Route::post('/update', 'ConfigurationController@updateConfiguration')->name('businessintelligence.configuration.update');
        Route::post('/update-multiple', 'ConfigurationController@updateMultiple')->name('businessintelligence.configuration.update-multiple');
        Route::delete('/{key}', 'ConfigurationController@deleteConfiguration')->name('businessintelligence.configuration.delete');
        Route::post('/reset-defaults', 'ConfigurationController@resetToDefaults')->name('businessintelligence.configuration.reset');
    });

    // Installation & Update Routes
    Route::get('/install', 'InstallController@index')->name('businessintelligence.install');
    Route::post('/install', 'InstallController@install')->name('businessintelligence.install.process');
    Route::post('/install-direct', 'AlternativeInstallController@installDirect')->name('businessintelligence.install.direct');
    Route::get('/update', 'InstallController@update')->name('businessintelligence.update');
    Route::get('/uninstall', 'InstallController@uninstall')->name('businessintelligence.uninstall');
    Route::get('/status', 'InstallController@status')->name('businessintelligence.status');
});

