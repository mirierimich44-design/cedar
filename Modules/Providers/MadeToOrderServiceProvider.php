<?php

namespace Modules\MadeToOrder\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Lang;

class MadeToOrderServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        // Load views (namespace: made_to_order)
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'made_to_order');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'made_to_order');
    }
}
