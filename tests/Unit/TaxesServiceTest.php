<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

use Illuminate\Validation\ValidationException;
use Jauntin\TaxesSdk\TaxesSdkServiceProvider;
use Jauntin\TaxesSdk\TaxesService;
use Jauntin\TaxesSdk\Tests\Mockable;
use Jauntin\TaxesSdk\Tests\TestCases;
use Money\Money;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TaxesServiceTest extends TestCase
{
    use Mockable;
    use TestCases;

    private TaxesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient();

        $this->service = $this->app->make(TaxesService::class);
    }

    #[DataProvider('pricingTestCaseProvider')]
    public function test_taxes(array $input)
    {
        $query = $this->service->taxes($input['taxTypes'])->state($input['state']);
        if (isset($input['municipalCode'])) {
            $query->withMunicipal($input['municipalCode']);
        }
        $result = $query->calculate($input['amount']);

        $this->assertIsArray($result->getTaxes());
        $this->assertInstanceOf(Money::class, $result->getTotal());
    }

    public function test_at_least_one_tax_type_is_required()
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

    public function test_state_is_required()
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

    public function test_municipal_code_is_required_with_municipal_tax_type()
    {
        $this->expectException(ValidationException::class);
        try {
            $this->service->taxes(['municipal'])->state('KY')->calculate(100);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('municipalCode', $e->errors());
            throw $e;
        }
    }

    public function test_should_lookup()
    {
        $this->assertTrue($this->service->shouldLookup('KY'));
        $this->assertFalse($this->service->shouldLookup('NY'));
    }

    public function test_lookup_tax_locations()
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
