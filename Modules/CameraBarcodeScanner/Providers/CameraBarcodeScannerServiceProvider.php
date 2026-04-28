<?php

namespace Modules\CameraBarcodeScanner\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class CameraBarcodeScannerServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadAssets();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('camerabarcodescanner.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'camerabarcodescanner'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/camerabarcodescanner');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/camerabarcodescanner';
        }, \Config::get('view.paths')), [$sourcePath]), 'camerabarcodescanner');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/camerabarcodescanner');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'camerabarcodescanner');
        }
    }

    /**
     * Load assets (JS/CSS)
     *
     * @return void
     */
    protected function loadAssets()
    {
        $this->publishes([
            __DIR__.'/../Resources/assets' => public_path('modules/camerabarcodescanner'),
        ], 'assets');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}

