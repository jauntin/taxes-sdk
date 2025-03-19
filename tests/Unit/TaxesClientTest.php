<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

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

    protected function getPackageProviders($app): array
    {
        return [
            TaxesSdkServiceProvider::class,
        ];
    }
}
