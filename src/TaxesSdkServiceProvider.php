<?php

namespace Jauntin\TaxesSdk;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Jauntin\TaxesSdk\Client\CacheableTaxesClient;
use Jauntin\TaxesSdk\Client\TaxesClient;
use Jauntin\TaxesSdk\Query\QueryFactory;

class TaxesSdkServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('taxes-sdk.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'taxes-sdk');

        $this->app->singleton(TaxesClient::class, fn () => new TaxesClient(config('taxes-sdk.api_uri')));

        $this->app->singleton(CacheableTaxesClient::class, fn (Container $container) =>
            new CacheableTaxesClient($container->get(TaxesClient::class)));

        $this->app->singleton(TaxesService::class, fn (Container $container) =>
            new TaxesService($container->get(CacheableTaxesClient::class), $container->get(QueryFactory::class)));
    }
}
