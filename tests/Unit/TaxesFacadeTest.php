<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

use Illuminate\Support\Carbon;
use Jauntin\TaxesSdk\Query\Result\Calculated;
use Jauntin\TaxesSdk\TaxesFacade;
use Jauntin\TaxesSdk\TaxesSdkServiceProvider;
use Jauntin\TaxesSdk\TaxType;
use Jauntin\TaxesSdk\Tests\Mockable;
use Jauntin\TaxesSdk\Tests\TestCases;
use Money\Money;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class TaxesFacadeTest extends TestCase
{
    use Mockable;
    use TestCases;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient();
    }

    #[DataProvider('pricingTestCaseProvider')]
    public function test_taxes(array $input)
    {
        $query = TaxesFacade::taxes($input['taxTypes'])->state($input['state']);
        if (isset($input['municipalCode'])) {
            $query->withMunicipal($input['municipalCode']);
        }
        $result = $query->calculate($input['amount']);

        $this->assertIsArray($result->getTaxes());
        $this->assertInstanceOf(Money::class, $result->getTotal());
    }

    public function test_should_lookup()
    {
        $this->assertTrue(TaxesFacade::shouldLookup('KY'));
        $this->assertFalse(TaxesFacade::shouldLookup('NY'));
    }

    public function test_lookup_tax_locations()
    {
        $locations = TaxesFacade::lookupTaxLocations('KY', 'jefferson');
        $this->assertIsArray($locations);
        $this->assertCount(4, $locations);

        $locations = TaxesFacade::lookupTaxLocations('NY', 'brooklyn');
        $this->assertIsArray($locations);
        $this->assertEmpty($locations);
    }

    public function test_mock_self()
    {
        TaxesFacade::shouldReceive('shouldLookup')->once()->andReturn(false);
        TaxesFacade::shouldReceive('lookupTaxLocations')->once()->andReturn([]);
        $this->assertFalse(TaxesFacade::shouldLookup('NY'));
        $this->assertEmpty(TaxesFacade::lookupTaxLocations('NY', 'brooklyn'));
    }

    public function test_mock_calculate_query()
    {
        TaxesFacade::shouldReceive('taxes')->once()->andReturn($this->mockQuery([
            'taxes' => [
                [
                    'state' => 'KY',
                    'type' => 'AdChrg',
                    'code' => 'AFKY1',
                    'rate' => 0.05,
                    'municipalCode' => '0001',
                    'municipalName' => 'LOUISVILLE - JEFFERSON',
                    'amount' => [
                        'amount' => 500,
                        'currency' => 'USD',
                    ],
                ],
            ],
            'total' => [
                'amount' => 500,
                'currency' => 'USD',
            ],
        ]));

        $calculated = TaxesFacade::taxes([TaxType::MUNICIPAL])
            ->state('KY')
            ->withMunicipal('0001')
            ->setEffectiveDate(Carbon::parse('2022-12-01'))
            ->calculate(10000);

        $this->assertInstanceOf(Calculated::class, $calculated);
        $this->assertInstanceOf(Money::class, $calculated->getTotal());
        $this->assertCount(1, $calculated->getTaxes());
    }

    protected function getPackageProviders($app): array
    {
        return [
            TaxesSdkServiceProvider::class,
        ];
    }
}
