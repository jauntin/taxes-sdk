<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

use Jauntin\TaxesSdk\TaxesFacadeV2;
use Jauntin\TaxesSdk\TaxesSdkServiceProvider;
use Jauntin\TaxesSdk\Tests\Mockable;
use Orchestra\Testbench\TestCase;

class TaxesFacadeV2Test extends TestCase
{
    use Mockable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient();
    }

    public function test_jurisdiction_lookup()
    {
        $result = TaxesFacadeV2::jurisdictionLookup(['city' => 'Adairville']);

        $this->assertSame('place_lookup', $result['lookup_mode']);
        $this->assertSame('ADAIRVILLE', $result['matches'][0]['jurisdiction']['canonical_key']);
    }

    public function test_lookup_by_lat_lng()
    {
        $result = TaxesFacadeV2::lookupByLatLng(36.67, -87.05);

        $this->assertSame('latlng_point_lookup', $result['lookup_mode']);
    }

    public function test_mock_self()
    {
        TaxesFacadeV2::shouldReceive('jurisdictionLookup')->once()->andReturn(['lookup_mode' => 'mocked']);

        $this->assertSame('mocked', TaxesFacadeV2::jurisdictionLookup(['city' => 'Anywhere'])['lookup_mode']);
    }

    protected function getPackageProviders($app): array
    {
        return [
            TaxesSdkServiceProvider::class,
        ];
    }
}
