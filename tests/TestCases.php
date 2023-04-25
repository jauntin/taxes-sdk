<?php

namespace Jauntin\TaxesSdk\Tests;

trait TestCases
{
    /**
     * Pricing test cases.
     */
    public function pricingTestCaseProvider(): array
    {
        return [
            'kentuckyWithMunicipal' => [
                'input'           => [
                    'taxTypes'      => ['municipal', 'surplus'],
                    'state'         => 'KY',
                    'amount'        => 61900,
                    'municipalCode' => '0124'
                ],
                'surchargeAmount' => 6066,
            ],
            'kentuckyOnlyMunicipal' => [
                'input'           => [
                    'taxTypes'      => ['municipal'],
                    'state'         => 'KY',
                    'amount'        => 62400,
                    'municipalCode' => '0124'
                ],
                'surchargeAmount' => 3120,
            ],
            'kentuckyEntity'        => [
                'input'           => [
                    'taxTypes' => ['surplus'],
                    'state'    => 'KY',
                    'amount'   => 63100,
                ],
                'surchargeAmount' => 3029,
            ],
            'newYork'               => [
                'input'           => [
                    'taxTypes' => ['surplus'],
                    'state'    => 'NY',
                    'amount'   => 60100,
                ],
                'surchargeAmount' => 2254,
            ],
            'newYork2'              => [
                'input'           => [
                    'taxTypes' => ['surplus'],
                    'state'    => 'NY',
                    'amount'   => 41800,
                ],
                'surchargeAmount' => 1568,
            ],
            'testCase1'             => [
                'input'           => [
                    'taxTypes' => ['surplus'],
                    'state'    => 'NV',
                    'amount'   => 101200,
                ],
                'surchargeAmount' => 3947,
            ],
            'testCase2'             => [
                'input'           => [
                    'taxTypes' => ['surplus'],
                    'state'    => 'NJ',
                    'amount'   => 115300],
                'surchargeAmount' => 5765,
            ],
            'testCase3'             => [
                'input'           => [
                    'taxTypes' => ['surplus'],
                    'state'    => 'NY',
                    'amount'   => 58200,
                ],
                'surchargeAmount' => 2182,
            ],
            'testCase4'             => [
                'input'           => [
                    'taxTypes' => ['surplus'],
                    'state'    => 'FL',
                    'amount'   => 42500,
                ],
                'surchargeAmount' => 2126,
            ],
            'testCase5'             => [
                'input'           => [
                    'taxTypes' => ['surplus'],
                    'state'    => 'FL',
                    'amount'   => 47000,
                ],
                'surchargeAmount' => 2350,
            ],
            'testCase6'             => [
                'input'           => [
                    'taxTypes'      => ['municipal', 'surplus'],
                    'state'         => 'KY',
                    'amount'        => 64900,
                    'municipalCode' => '0124',
                ],
                'surchargeAmount' => 6360,
            ],
        ];
    }
}
