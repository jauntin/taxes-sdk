<?php

namespace Jauntin\TaxesSdk\Client;

use Illuminate\Http\Client\PendingRequest;
use Jauntin\TaxesSdk\Exception\ClientException;

class TaxesClientV2 extends AbstractTaxesClient
{
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
        $url = sprintf('%s/api/v2/ky/lgpt/lookup', $this->serviceUrl);
        $response = $this->handleRequest(
            fn (PendingRequest $pendingRequest) => $pendingRequest->get($url, $this->prepareQuery($params))
        );

        return $response->json();
    }
}
