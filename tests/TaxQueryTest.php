<?php

namespace Jauntin\Taxes\Tests;

use Jauntin\Taxes\Exception\Exception;
use Jauntin\Taxes\TaxQuery;
use PHPUnit\Framework\TestCase;

class TaxQueryTest extends TestCase
{
    public function testShouldErrorWithoutAmount()
    {
        $query = TaxQuery::state('NY');

        $this->expectException(Exception::class);

        $query->get();
    }

    /**
     * @dataProvider testCases
     */
    public function testPositive(string $state, int $amount, string $municipalCode = null, array $excluded = [])
    {
        $query = TaxQuery::state($state)->amount($amount);
        if (isset($municipalCode)) {
            $query->withMunicipal($municipalCode);
        }
        if (isset($excluded)) {
            foreach ($excluded as $exclude) {
                $query->exclude($exclude);
            }
        }

        $result = $query->get();

        $this->assertIsArray($result);
        $this->assertEquals($state, $result['state']);
        $this->assertEquals($amount, $result['amount']);
        if (isset($municipalCode)) {
            $this->assertEquals($municipalCode, $result['municipalCode']);
        }
        if (!empty($excluded)) {
            $this->assertEquals($excluded, $result['exclude']);
        }
    }

    private function testCases()
    {
        return [
            'only state and amount'               => [
                'state'  => 'NY',
                'amount' => 1000,
            ],
            'with municipal code'                 => [
                'state'         => 'NY',
                'amount'        => 1000,
                'municipalCode' => '12345',
            ],
            'with excluded tax'                   => [
                'state'         => 'NY',
                'amount'        => 1000,
                'municipalCode' => null,
                'exclude'       => ['FOO'],
            ],
            'with 2 excluded taxes'               => [
                'state'         => 'NY',
                'amount'        => 1000,
                'municipalCode' => null,
                'exclude'       => ['FOO', 'BAR'],
            ],
            'with municipal and 2 excluded taxes' => [
                'state'         => 'NY',
                'amount'        => 1000,
                'municipalCode' => '12345',
                'exclude'       => ['FOO', 'BAR'],
            ],
        ];
    }
}
