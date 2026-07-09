<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

use BadMethodCallException;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Cache;
use Jauntin\TaxesSdk\Client\CacheableTaxesClientV2;
use Jauntin\TaxesSdk\TaxesSdkServiceProvider;
use Jauntin\TaxesSdk\Tests\Mockable;
use Orchestra\Testbench\TestCase;

class CacheableTaxesClientV2Test extends TestCase
{
    use Mockable;

    private CacheableTaxesClientV2 $decorator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->decorator = $this->app->get(CacheableTaxesClientV2::class);
    }

    public function test_jurisdiction_lookup_from_cache()
    {
        Cache::shouldReceive('driver')->once()->andReturnSelf();
        Cache::shouldReceive('remember')->once()->andReturn([
            'lookup_mode' => 'place_lookup',
            'matches' => [
                ['jurisdiction' => ['canonical_key' => 'ADAIRVILLE']],
            ],
        ]);

        $result = $this->decorator->jurisdictionLookup(['city' => 'Adairville']);

        $this->assertSame('ADAIRVILLE', $result['matches'][0]['jurisdiction']['canonical_key']);
    }

    public function test_no_cache_without_ttl()
    {
        $this->mockClient();

        tap($this->app->make('config'), function (Repository $config) {
            $config->set('taxes-sdk.cache.ttl', null);
        });

        Cache::shouldReceive('driver')->never();
        Cache::shouldReceive('remember')->never();

        $result = $this->decorator->jurisdictionLookup(['city' => 'Adairville']);
        $this->assertIsArray($result);
    }

    public function test_undefined_method_throws()
    {
        $this->expectException(BadMethodCallException::class);

        $this->decorator->nonExistentMethod();
    }

    protected function getPackageProviders($app): array
    {
        return [
            TaxesSdkServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        tap($app->make('config'), function (Repository $config) {
            $config->set('taxes-sdk.cache.store', 'array');
            $config->set('taxes-sdk.cache.ttl', 3600);
        });
    }
}
