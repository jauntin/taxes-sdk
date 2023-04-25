<?php

namespace Jauntin\TaxesSdk\Tests;

use Faker\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

trait MocksClient
{

    /**
     * @return void
     */
    protected function mockClient(): void
    {
        $faker = Factory::create();

        Http::fake([
            'dev.taxes.jauntin.com/api/v1/taxes/calculate*' => function (Request $request, array $options) use ($faker) {
                $tax       = [
                    'state'         => $options['laravel_data']['state'] ?? 'NY',
                    'type'          => 'foo',
                    'code'          => 'bar',
                    'rate'          => 0.05,
                    'amount'        => [
                        'amount'   => $faker->randomNumber(4),
                        'currency' => 'USD',
                    ],
                    'municipalCode' => null,
                    'municipalName' => null,
                ];
                $municipal = array_merge($tax, [
                    'municipalCode' => $options['laravel_data']['municipalCode'] ?? null,
                    'municipalName' => isset($options['laravel_data']['municipalCode']) ? 'baz' : null
                ]);

                return Http::response(json_encode([
                        'taxes' => isset($options['laravel_data']['municipalCode']) ? [$tax, $municipal] : [$tax],
                        'total' => [
                            'amount'   => $faker->randomNumber(4),
                            'currency' => 'USD',
                        ],
                    ])
                );
            },
            'dev.taxes.jauntin.com/api/v1/taxes/lookup/locations*' => function (Request $request, array $options) use ($faker) {
                $state = $options['laravel_data']['state'] ?? 'NY';

                if ($state === 'KY') {
                    return Http::response(json_encode([
                        [
                            "state"         => "KY",
                            "type"          => "AdChrg",
                            "code"          => "AFKY1",
                            "rate"          => 0.05,
                            "municipalCode" => "0905",
                            "municipalName" => "JEFFERSON COUNTY"
                        ],
                        [
                            "state"         => "KY",
                            "type"          => "AdChrg",
                            "code"          => "AFKY1",
                            "rate"          => 0.05,
                            "municipalCode" => "0072",
                            "municipalName" => "JEFFERSONTOWN"
                        ],
                        [
                            "state"         => "KY",
                            "type"          => "AdChrg",
                            "code"          => "AFKY1",
                            "rate"          => 0.05,
                            "municipalCode" => "0001",
                            "municipalName" => "LOUISVILLE - JEFFERSON"
                        ],
                        [
                            "state"         => "KY",
                            "type"          => "AdChrg",
                            "code"          => "AFKY1",
                            "rate"          => 0,
                            "municipalCode" => "0439",
                            "municipalName" => "JEFFERSONVILLE"
                        ]
                    ]));
                }

                return Http::response(json_encode([]));
            },
            'dev.taxes.jauntin.com/api/v1/taxes/lookup*' => function (Request $request, array $options) use ($faker) {
                $state = $options['laravel_data']['state'] ?? 'NY';

                return Http::response(json_encode([
                    'shouldLookupTax' => $state === 'KY',
                ]));
            }
        ]);
    }
}