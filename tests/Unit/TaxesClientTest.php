<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Jauntin\TaxesSdk\Client\TaxesClient;
use Jauntin\TaxesSdk\TaxesSdkServiceProvider;
use Jauntin\TaxesSdk\Tests\Mockable;
use Jauntin\TaxesSdk\Tests\TestCases;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TaxesClientTest extends TestCase
{
    use Mockable;
    use TestCases;

    private TaxesClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient();

        $this->client = $this->app->get(TaxesClient::class);
    }

    #[DataProvider('pricingTestCaseProvider')]
    public function test_calculate_many(array $input)
    {
        $calculated = $this->client->calculateTaxes($input);
        $this->assertArrayHasKey('taxes', $calculated);
        $this->assertArrayHasKey('total', $calculated);
        $this->assertArrayHasKey('amount', $calculated['taxes'][0]);
        $this->assertIsInt($calculated['taxes'][0]['amount']['amount']);
        $this->assertIsInt($calculated['total']['amount']);
    }

    public function test_requests_are_sent_with_bearer_token()
    {
        $this->client->shouldLookup('KY');

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-token'));
    }

    public function test_requests_are_sent_without_bearer_token_when_not_configured()
    {
        Config::set('taxes-sdk.token', null);
        $client = $this->app->make(TaxesClient::class);

        $client->shouldLookup('KY');

        Http::assertSent(fn ($request) => ! $request->hasHeader('Authorization'));
    }

    protected function getPackageProviders($app): array
    {
        return [
            TaxesSdkServiceProvider::class,
        ];
    }
}
