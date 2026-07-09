<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

use Jauntin\TaxesSdk\TaxesSdkServiceProvider;
use Jauntin\TaxesSdk\TaxesServiceV2;
use Jauntin\TaxesSdk\Tests\Mockable;
use Orchestra\Testbench\TestCase;

class TaxesServiceV2Test extends TestCase
{
    use Mockable;

    private TaxesServiceV2 $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient();

        $this->service = $this->app->make(TaxesServiceV2::class);
    }

    public function test_jurisdiction_lookup_delegates()
    {
        $result = $this->service->jurisdictionLookup(['city' => 'Adairville']);

        $this->assertSame('place_lookup', $result['lookup_mode']);
        $this->assertSame('ADAIRVILLE', $result['matches'][0]['jurisdiction']['canonical_key']);
    }

    public function test_lookup_by_address()
    {
        $result = $this->service->lookupByAddress('123 Main St, Adairville KY');

        $this->assertSame('address_lookup', $result['lookup_mode']);
        $this->assertSame('123 Main St, Adairville KY', $result['input']['address']);
    }

    public function test_lookup_by_lat_lng()
    {
        $result = $this->service->lookupByLatLng(36.67, -87.05);

        $this->assertSame('latlng_point_lookup', $result['lookup_mode']);
        $this->assertSame('36.67', $result['input']['lat']);
        $this->assertSame('-87.05', $result['input']['lng']);
    }

    public function test_lookup_by_city_with_county()
    {
        $result = $this->service->lookupByCity('Adairville', 'Logan');

        $this->assertSame('place_lookup', $result['lookup_mode']);
        $this->assertSame('Adairville', $result['input']['city']);
        $this->assertSame('Logan', $result['input']['county']);
    }

    public function test_lookup_by_city_omits_null_county()
    {
        $result = $this->service->lookupByCity('Adairville');

        $this->assertArrayNotHasKey('county', $result['input']);
    }

    public function test_lookup_by_place_id()
    {
        $result = $this->service->lookupByPlaceId('ChIJ1234567890');

        $this->assertSame('placeid_lookup', $result['lookup_mode']);
        $this->assertSame('ChIJ1234567890', $result['input']['place_id']);
    }

    public function test_debug_flag_sent_only_when_true()
    {
        $withDebug = $this->service->lookupByCity('Adairville', null, true);
        $this->assertSame('1', $withDebug['input']['debug']);

        $withoutDebug = $this->service->lookupByCity('Adairville');
        $this->assertArrayNotHasKey('debug', $withoutDebug['input']);
    }

    protected function getPackageProviders($app): array
    {
        return [
            TaxesSdkServiceProvider::class,
        ];
    }
}
