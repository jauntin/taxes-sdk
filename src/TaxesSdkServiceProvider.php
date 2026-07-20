<?php

namespace Jauntin\TaxesSdk;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Jauntin\TaxesSdk\Client\CacheableTaxesClient;
use Jauntin\TaxesSdk\Client\CacheableTaxesClientV2;
use Jauntin\TaxesSdk\Client\TaxesClient;
use Jauntin\TaxesSdk\Client\TaxesClientV2;
use Jauntin\TaxesSdk\Query\QueryFactory;

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

        $this->app->bind(TaxesClient::class, fn () => new TaxesClient(config('taxes-sdk.api_uri'), config('taxes-sdk.token')));

        $this->app->bind(CacheableTaxesClient::class, fn (Container $container) => new CacheableTaxesClient($container->get(TaxesClient::class)));

        $this->app->bind(TaxesService::class, fn (Container $container) => new TaxesService($container->get(CacheableTaxesClient::class), $container->get(QueryFactory::class)));

        $this->app->bind(TaxesClientV2::class, fn () => new TaxesClientV2(config('taxes-sdk.api_uri'), config('taxes-sdk.token')));

        $this->app->bind(CacheableTaxesClientV2::class, fn (Container $container) => new CacheableTaxesClientV2($container->get(TaxesClientV2::class)));

        $this->app->bind(TaxesServiceV2::class, fn (Container $container) => new TaxesServiceV2($container->get(CacheableTaxesClientV2::class)));
    }
}
