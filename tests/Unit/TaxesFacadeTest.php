<?php

namespace Jauntin\TaxesSdk\Tests\Unit;

use Jauntin\TaxesSdk\Query\CalculateQuery;
use Jauntin\TaxesSdk\Query\Result\Calculated;
use Jauntin\TaxesSdk\TaxesFacade;
use Jauntin\TaxesSdk\TaxesSdkServiceProvider;
use Jauntin\TaxesSdk\TaxType;
use Jauntin\TaxesSdk\Tests\MocksClient;
use Jauntin\TaxesSdk\Tests\TestCases;
use Money\Money;
use Orchestra\Testbench\TestCase;

class TaxesFacadeTest extends TestCase
{
    use TestCases;
    use MocksClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient();
    }

    /**
     * @dataProvider pricingTestCaseProvider
     */
    public function testTaxes(array $input)
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

    public function testMockSelf()
    {
        TaxesFacade::shouldReceive('shouldLookup')->once()->andReturn(false);
        TaxesFacade::shouldReceive('lookupTaxLocations')->once()->andReturn([]);
        $this->assertFalse(TaxesFacade::shouldLookup('NY'));
        $this->assertEmpty(TaxesFacade::lookupTaxLocations('NY', 'brooklyn'));
    }

    public function testMockCalculateQuery()
    {
        TaxesFacade::shouldReceive('taxes')->once()->andReturn(CalculateQuery::mock([
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
        ]));

        $calculated = TaxesFacade::taxes([TaxType::MUNICIPAL])
            ->state('KY')
            ->withMunicipal('0001')
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
