<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

use Jauntin\TaxesSdk\TaxesFacade;
use Jauntin\TaxesSdk\TaxesSdkServiceProvider;
use Jauntin\TaxesSdk\Tests\MocksClient;
use Jauntin\TaxesSdk\Tests\TestCases;
use Money\Money;
use Orchestra\Testbench\TestCase;

class TaxesFacadeTest extends TestCase
{
    use TestCases, MocksClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient();
    }

    /**
     * @dataProvider pricingTestCaseProvider
     */
    public function testTaxes(array $input, int $preSurcharge)
    {
        $query = TaxesFacade::taxes($input['taxTypes'])->state($input['state']);
        if (isset($input['municipalCode'])) {
            $query->withMunicipal($input['municipalCode']);
        }
        $result = $query->calculate($input['amount']);

        $this->assertIsArray($result->getTaxes());
        $this->assertInstanceOf(Money::class, $result->getTotal());
    }

    public function testShouldLookup()
    {
        $this->assertTrue(TaxesFacade::shouldLookup('KY'));
        $this->assertFalse(TaxesFacade::shouldLookup('NY'));
    }

    public function testLookupTaxLocations()
    {
        $locations = TaxesFacade::lookupTaxLocations('KY', 'jefferson');
        $this->assertIsArray($locations);
        $this->assertCount(4, $locations);

        $locations = TaxesFacade::lookupTaxLocations('NY', 'brooklyn');
        $this->assertIsArray($locations);
        $this->assertEmpty($locations);
    }

    protected function getPackageProviders($app): array
    {
        return [
            TaxesSdkServiceProvider::class,
        ];
    }
}