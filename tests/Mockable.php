<?php

namespace Jauntin\TaxesSdk\Tests;

use Faker\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Jauntin\TaxesSdk\Query\CalculateQuery;
use Jauntin\TaxesSdk\Query\Result\Calculated;
use Mockery;
use Mockery\MockInterface;

trait Mockable
{
    protected function mockClient(): void
    {
        $faker = Factory::create();

        Http::fake([
            Config::get('taxes-sdk.api_uri').'/api/v1/taxes/calculate*' => function (Request $request, array $options) use ($faker) {
                $tax = [
                    'state' => $options['laravel_data']['state'] ?? 'NY',
                    'type' => 'foo',
                    'code' => 'bar',
                    'rate' => 0.05,
                    'amount' => [
                        'amount' => $faker->randomNumber(4),
                        'currency' => 'USD',
                    ],
                    'municipalCode' => null,
                    'municipalName' => null,
                ];
                $municipal = array_merge($tax, [
                    'municipalCode' => $options['laravel_data']['municipalCode'] ?? null,
                    'municipalName' => isset($options['laravel_data']['municipalCode']) ? 'baz' : null,
                ]);

                return Http::response(json_encode([
                    'taxes' => isset($options['laravel_data']['municipalCode']) ? [$tax, $municipal] : [$tax],
                    'total' => [
                        'amount' => $faker->randomNumber(4),
                        'currency' => 'USD',
                    ],
                ]));
            },
            Config::get('taxes-sdk.api_uri').'/api/v1/taxes/lookup/locations*' => function (Request $request, array $options) {
                $state = $options['laravel_data']['state'] ?? 'NY';

                if ($state === 'KY') {
                    return Http::response(json_encode([
                        [
                            'state' => 'KY',
                            'type' => 'AdChrg',
                            'code' => 'AFKY1',
                            'rate' => 0.05,
                            'municipalCode' => '0905',
                            'municipalName' => 'JEFFERSON COUNTY',
                        ],
                        [
                            'state' => 'KY',
                            'type' => 'AdChrg',
                            'code' => 'AFKY1',
                            'rate' => 0.05,
                            'municipalCode' => '0072',
                            'municipalName' => 'JEFFERSONTOWN',
                        ],
                        [
                            'state' => 'KY',
                            'type' => 'AdChrg',
                            'code' => 'AFKY1',
                            'rate' => 0.05,
                            'municipalCode' => '0001',
                            'municipalName' => 'LOUISVILLE - JEFFERSON',
                        ],
                        [
                            'state' => 'KY',
                            'type' => 'AdChrg',
                            'code' => 'AFKY1',
                            'rate' => 0,
                            'municipalCode' => '0439',
                            'municipalName' => 'JEFFERSONVILLE',
                        ],
                    ]));
                }

                return Http::response(json_encode([]));
            },
            Config::get('taxes-sdk.api_uri').'/api/v1/taxes/lookup*' => function (Request $request, array $options) {
                $state = $options['laravel_data']['state'] ?? 'NY';

                return Http::response(json_encode([
                    'shouldLookupTax' => $state === 'KY',
                ]));
            },
            Config::get('taxes-sdk.api_uri').'/api/v2/ky/lgpt/lookup*' => function (Request $request) {
                parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

                $mode = match (true) {
                    isset($query['address']) => 'address_lookup',
                    isset($query['lat'], $query['lng']) => 'latlng_point_lookup',
                    isset($query['place_id']) => 'placeid_lookup',
                    default => 'place_lookup',
                };
                $canonicalKey = strtoupper($query['city'] ?? $query['address'] ?? 'ADAIRVILLE');

                return Http::response(json_encode([
                    'lookup_mode' => $mode,
                    'input' => $query,
                    'matches' => [
                        [
                            'rank' => 1,
                            'match_type' => 'rate_table_name',
                            'confidence' => 'medium',
                            'jurisdiction' => [
                                'canonical_key' => $canonicalKey,
                                'rates' => ['fire_allied_perils' => 0.1],
                            ],
                        ],
                    ],
                ]));
            },
        ]);
    }

    protected function mockQuery(array $result, bool $partial = true): MockInterface|CalculateQuery
    {
        /** @var MockInterface|CalculateQuery */
        $query = Mockery::mock(CalculateQuery::class);
        if ($partial) {
            $query->makePartial();
        }
        $query->shouldReceive('calculate')->andReturn(new Calculated($result));

        return $query;
    }
}
