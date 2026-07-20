<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Jauntin\TaxesSdk\Client\TaxesClientV2;
use Jauntin\TaxesSdk\TaxesSdkServiceProvider;
use Jauntin\TaxesSdk\Tests\Mockable;
use Orchestra\Testbench\TestCase;

class TaxesClientV2Test extends TestCase
{
    use Mockable;

    private TaxesClientV2 $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient();

        $this->client = $this->app->get(TaxesClientV2::class);
    }

    public function test_jurisdiction_lookup_returns_match_shape()
    {
        $result = $this->client->jurisdictionLookup(['city' => 'Adairville']);

        $this->assertSame('place_lookup', $result['lookup_mode']);
        $this->assertSame('ADAIRVILLE', $result['matches'][0]['jurisdiction']['canonical_key']);
        $this->assertSame(0.1, $result['matches'][0]['jurisdiction']['rates']['fire_allied_perils']);
    }

    public function test_jurisdiction_lookup_filters_null_params()
    {
        $result = $this->client->jurisdictionLookup(['city' => 'Adairville', 'county' => null]);

        $this->assertArrayHasKey('city', $result['input']);
        $this->assertArrayNotHasKey('county', $result['input']);
    }

    public function test_jurisdiction_lookup_sends_lat_lng()
    {
        $result = $this->client->jurisdictionLookup(['lat' => 36.67, 'lng' => -87.05]);

        $this->assertSame('latlng_point_lookup', $result['lookup_mode']);
    }

    public function test_requests_are_sent_with_bearer_token()
    {
        $this->client->jurisdictionLookup(['city' => 'Adairville']);

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-token'));
    }

    protected function getPackageProviders($app): array
    {
        return [
            TaxesSdkServiceProvider::class,
        ];
    }
}
