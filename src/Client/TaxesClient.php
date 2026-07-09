<?php

namespace Jauntin\TaxesSdk\Client;

use Illuminate\Http\Client\PendingRequest;
use Jauntin\TaxesSdk\Exception\ClientException;
use Jauntin\TaxesSdk\TaxType;

class TaxesClient extends AbstractTaxesClient
{
    /**
     * @throws ClientException
     */
    public function getTaxes(array $params, TaxType $taxType): array
    {
        $url = sprintf('%s/api/v1/taxes/%s', $this->serviceUrl, $taxType->value);
        $response = $this->handleRequest(
            fn (PendingRequest $pendingRequest) => $pendingRequest->get($url, $this->prepareQuery($params))
        );

        return $response->json();
    }

    /**
     * @throws ClientException
     */
    public function calculateTaxes(array $params): array
    {
        $url = sprintf('%s/api/v1/taxes/calculate', $this->serviceUrl);
        $response = $this->handleRequest(
            fn (PendingRequest $pendingRequest) => $pendingRequest->get($url, $this->prepareQuery($params))
        );

        return $response->json();
    }

    /**
     * @throws ClientException
     */
    public function shouldLookup(string $state): bool
    {
        $url = sprintf('%s/api/v1/taxes/lookup', $this->serviceUrl);
        $response = $this->handleRequest(
            fn (PendingRequest $pendingRequest) => $pendingRequest->get($url, ['state' => $state])
        );

        return $response->json('shouldLookupTax', false);
    }

    /**
     * @throws ClientException
     */
    public function lookupTaxLocations(string $state, string $search, ?string $effectiveDate = null): array
    {
        $url = sprintf('%s/api/v1/taxes/lookup/locations', $this->serviceUrl);
        $params = ['state' => $state, 'searchString' => $search];
        if ($effectiveDate) {
            $params['effectiveDate'] = $effectiveDate;
        }
        $response = $this->handleRequest(
            fn (PendingRequest $pendingRequest) => $pendingRequest->get($url, $params)
        );

        return $response->json();
    }
}
