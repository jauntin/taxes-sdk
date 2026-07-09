<?php

namespace Jauntin\TaxesSdk;

use Jauntin\TaxesSdk\Client\CacheableTaxesClientV2;
use Jauntin\TaxesSdk\Exception\ClientException;

class TaxesServiceV2
{
    public function __construct(private readonly CacheableTaxesClientV2 $client) {}

    /**
     * Look up the Kentucky LGPT jurisdiction for a location.
     *
     * @param  array<string, mixed>  $params  Any of: address, lat, lng, city, county, place_id, debug.
     * @return array<string, mixed>
     *
     * @throws ClientException
     */
    public function jurisdictionLookup(array $params): array
    {
        return $this->client->jurisdictionLookup($params);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ClientException
     */
    public function lookupByAddress(string $address, bool $debug = false): array
    {
        return $this->jurisdictionLookup($this->withDebug(['address' => $address], $debug));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ClientException
     */
    public function lookupByLatLng(float $lat, float $lng, bool $debug = false): array
    {
        return $this->jurisdictionLookup($this->withDebug(['lat' => $lat, 'lng' => $lng], $debug));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ClientException
     */
    public function lookupByCity(string $city, ?string $county = null, bool $debug = false): array
    {
        return $this->jurisdictionLookup($this->withDebug(['city' => $city, 'county' => $county], $debug));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ClientException
     */
    public function lookupByPlaceId(string $placeId, bool $debug = false): array
    {
        return $this->jurisdictionLookup($this->withDebug(['place_id' => $placeId], $debug));
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function withDebug(array $params, bool $debug): array
    {
        if ($debug) {
            $params['debug'] = true;
        }

        return $params;
    }
}
