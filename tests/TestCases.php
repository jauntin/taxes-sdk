<?php

namespace Jauntin\TaxesSdk\Tests;

trait TestCases
{
    /**
     * Pricing test cases.
     */
    public static function pricingTestCaseProvider(): array
    {
        return [
            'kentuckyWithMunicipal' => [
                'input' => [
                    'taxTypes' => ['municipal', 'surplus'],
                    'state' => 'KY',
                    'amount' => 61900,
                    'municipalCode' => '0124',
                ],
            ],
            'kentuckyOnlyMunicipal' => [
                'input' => [
                    'taxTypes' => ['municipal'],
                    'state' => 'KY',
                    'amount' => 62400,
                    'municipalCode' => '0124',
                ],
            ],
            'kentuckyEntity' => [
                'input' => [
                    'taxTypes' => ['surplus'],
                    'state' => 'KY',
                    'amount' => 63100,
                ],
            ],
            'newYork' => [
                'input' => [
                    'taxTypes' => ['surplus'],
                    'state' => 'NY',
                    'amount' => 60100,
                ],
            ],
            'newYork2' => [
                'input' => [
                    'taxTypes' => ['surplus'],
                    'state' => 'NY',
                    'amount' => 41800,
                ],
            ],
            'testCase1' => [
                'input' => [
                    'taxTypes' => ['surplus'],
                    'state' => 'NV',
                    'amount' => 101200,
                ],
            ],
            'testCase2' => [
                'input' => [
                    'taxTypes' => ['surplus'],
                    'state' => 'NJ',
                    'amount' => 115300,
                ],
            ],
            'testCase3' => [
                'input' => [
                    'taxTypes' => ['surplus'],
                    'state' => 'NY',
                    'amount' => 58200,
                ],
            ],
            'testCase4' => [
                'input' => [
                    'taxTypes' => ['surplus'],
                    'state' => 'FL',
                    'amount' => 42500,
                ],
            ],
            'testCase5' => [
                'input' => [
                    'taxTypes' => ['surplus'],
                    'state' => 'FL',
                    'amount' => 47000,
                ],
            ],
            'testCase6' => [
                'input' => [
                    'taxTypes' => ['municipal', 'surplus'],
                    'state' => 'KY',
                    'amount' => 64900,
                    'municipalCode' => '0124',
                ],
            ],
            'testCase7' => [
                'input' => [
                    'taxTypes' => ['admitted'],
                    'state' => 'FL',
                    'amount' => 64900,
                    'startDate' => '2023-01-01',
                ],
            ],
        ];
    }
}
