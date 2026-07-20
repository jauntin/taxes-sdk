<?php

namespace Jauntin\TaxesSdk;

use Illuminate\Support\ServiceProvider;

class TaxesSdkServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('taxes-sdk.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'taxes-sdk');
    }
}
