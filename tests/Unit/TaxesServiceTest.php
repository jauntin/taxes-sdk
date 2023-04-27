<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

use Illuminate\Validation\ValidationException;
use Jauntin\TaxesSdk\TaxesSdkServiceProvider;
use Jauntin\TaxesSdk\TaxesService;
use Jauntin\TaxesSdk\Tests\Mockable;
use Jauntin\TaxesSdk\Tests\TestCases;
use Money\Money;
use Orchestra\Testbench\TestCase;

class TaxesServiceTest extends TestCase
{
    use TestCases;
    use Mockable;

    private TaxesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient();

        $this->service = $this->app->make(TaxesService::class);
    }

    /**
     * @dataProvider pricingTestCaseProvider
     */
    public function testTaxes(array $input, int $preSurcharge)
    {
        $query = $this->service->taxes($input['taxTypes'])->state($input['state']);
        if (isset($input['municipalCode'])) {
            $query->withMunicipal($input['municipalCode']);
        }
        $result = $query->calculate($input['amount']);

        $this->assertIsArray($result->getTaxes());
        $this->assertInstanceOf(Money::class, $result->getTotal());
    }

    public function testAtLeastOneTaxTypeIsRequired()
    {
        $this->expectException(ValidationException::class);
        try {
            $this->service->taxes([])->calculate(100);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('taxTypes', $e->errors());
            $this->assertArrayNotHasKey('municipalCode', $e->errors());
            throw $e;
        }
    }

    public function testStateIsRequired()
    {
        $this->expectException(ValidationException::class);
        try {
            $this->service->taxes(['surplus'])->calculate(100);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('state', $e->errors());
            $this->assertArrayNotHasKey('municipalCode', $e->errors());
            throw $e;
        }
    }

    public function testMunicipalCodeIsRequiredWithMunicipalTaxType()
    {
        $this->expectException(ValidationException::class);
        try {
            $this->service->taxes(['municipal'])->state('KY')->calculate(100);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('municipalCode', $e->errors());
            throw $e;
        }
    }

    public function testShouldLookup()
    {
        $this->assertTrue($this->service->shouldLookup('KY'));
        $this->assertFalse($this->service->shouldLookup('NY'));
    }

    public function testLookupTaxLocations()
    {
        $locations = $this->service->lookupTaxLocations('KY', 'jefferson');
        $this->assertIsArray($locations);
        $this->assertCount(4, $locations);

        $locations = $this->service->lookupTaxLocations('NY', 'brooklyn');
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
