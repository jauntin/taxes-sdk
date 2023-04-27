<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Cache;
use Jauntin\TaxesSdk\Client\CacheableTaxesClient;
use Jauntin\TaxesSdk\TaxesSdkServiceProvider;
use Jauntin\TaxesSdk\Tests\Mockable;
use Orchestra\Testbench\TestCase;

class CacheableTaxesClientTest extends TestCase
{
    use Mockable;

    private CacheableTaxesClient $decorator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->decorator = $this->app->get(CacheableTaxesClient::class);
    }

    public function testGetTaxesFromCache()
    {
        Cache::shouldReceive('driver')->once()->andReturnSelf();
        Cache::shouldReceive('remember')->once()->andReturn([
            [
                'state'         => 'KY',
                'type'          => 'AdChrg',
                'code'          => 'AFKY1',
                'rate'          => 0.05,
                'municipalCode' => '0001',
                'municipalName' => 'LOUISVILLE - JEFFERSON'
            ],
        ]);
        $result = $this->decorator->getTaxes(['state' => 'KY', 'municipalCode' => '0001', 'taxTypes' => ['municipal']]);
        $this->assertCount(1, $result);
    }

    public function testCalculateTaxesFromCache()
    {
        Cache::shouldReceive('driver')->once()->andReturnSelf();
        Cache::shouldReceive('remember')->once()->andReturn([
            'taxes' => [
                [
                    'state'         => 'KY',
                    'type'          => 'AdChrg',
                    'code'          => 'AFKY1',
                    'rate'          => 0.05,
                    'municipalCode' => '0001',
                    'municipalName' => 'LOUISVILLE - JEFFERSON',
                    'amount'        => [
                        'amount'   => 500,
                        'currency' => 'USD',
                    ],
                ],
            ],
            'total' => [
                'amount'   => 500,
                'currency' => 'USD',
            ],
        ]);
        $result = $this->decorator->calculateTaxes(['state' => 'KY', 'municipalCode' => '0001', 'taxTypes' => ['municipal'], 'amount' => 10000]);

        $this->assertArrayHasKey('taxes', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertCount(1, $result['taxes']);
    }

    public function testShouldLookupFromCache()
    {
        Cache::shouldReceive('driver')->once()->andReturnSelf();
        Cache::shouldReceive('remember')->once()->andReturn(true);

        $result = $this->decorator->shouldLookup('KY');

        $this->assertTrue($result);
    }

    public function testLookupTaxLocationsFromCache()
    {
        Cache::shouldReceive('driver')->once()->andReturnSelf();
        Cache::shouldReceive('remember')->once()->andReturn([
            [
                "state" => "KY",
                "type" => "AdChrg",
                "code" => "AFKY1",
                "rate" => 0.05,
                "municipalCode" => "0905",
                "municipalName" => "JEFFERSON COUNTY"
            ],
            [
                "state" => "KY",
                "type" => "AdChrg",
                "code" => "AFKY1",
                "rate" => 0.05,
                "municipalCode" => "0072",
                "municipalName" => "JEFFERSONTOWN"
            ],
            [
                "state" => "KY",
                "type" => "AdChrg",
                "code" => "AFKY1",
                "rate" => 0.05,
                "municipalCode" => "0001",
                "municipalName" => "LOUISVILLE - JEFFERSON"
            ],
            [
                "state" => "KY",
                "type" => "AdChrg",
                "code" => "AFKY1",
                "rate" => 0,
                "municipalCode" => "0439",
                "municipalName" => "JEFFERSONVILLE"
            ]
        ]);

        $result = $this->decorator->lookupTaxLocations('KY', 'jefferson');

        $this->assertCount(4, $result);
    }

    public function testNoCacheWithoutTtl()
    {
        $this->mockClient();

        tap($this->app->make('config'), function (Repository $config) {
            $config->set('taxes-sdk.cache.ttl', null);
        });

        Cache::shouldReceive('driver')->never();
        Cache::shouldReceive('remember')->never();

        $result = $this->decorator->lookupTaxLocations('KY', 'LOUISVILLE');
        $this->assertIsArray($result);
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
