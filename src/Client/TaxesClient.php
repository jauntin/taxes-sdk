<?php

namespace Jauntin\TaxesSdk\Client;

use Closure;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Jauntin\TaxesSdk\Exception\ClientException;
use Jauntin\TaxesSdk\TaxType;

class TaxesClient
{
    private const SLEEP = 1000;
    private const TIMEOUT = 5;
    private const TRIES = 3;

    public function __construct(private readonly string $serviceUrl)
    {
    }

    /**
     * @throws ClientException
     */
    public function getTaxes(array $params, TaxType $taxType): array
    {
        $url      = sprintf('%s/api/v1/taxes/%s', $this->serviceUrl, $taxType->value);
        $response = $this->handleRequest(
            fn(PendingRequest $pendingRequest) => $pendingRequest->get($url, $this->prepareQuery($params))
        );

        return $response->json();
    }

    /**
     * @throws ClientException
     */
    public function calculateTaxes(array $params): array
    {
        $url      = sprintf('%s/api/v1/taxes/calculate', $this->serviceUrl);
        $response = $this->handleRequest(
            fn(PendingRequest $pendingRequest) => $pendingRequest->get($url, $this->prepareQuery($params))
        );

        return $response->json();
    }

    /**
     * @param string $state
     *
     * @return bool
     *
     * @throws ClientException
     */
    public function shouldLookup(string $state): bool
    {
        $url      = sprintf('%s/api/v1/taxes/lookup', $this->serviceUrl);
        $response = $this->handleRequest(
            fn(PendingRequest $pendingRequest) => $pendingRequest->get($url, ['state' => $state])
        );

        return $response->json('shouldLookupTax', false);
    }

    /**
     * @param string $state
     * @param string $search
     * @param string|null $effectiveDate
     *
     * @return array
     *
     * @throws ClientException
     */
    public function lookupTaxLocations(string $state, string $search, ?string $effectiveDate = null): array
    {
        $url      = sprintf('%s/api/v1/taxes/lookup/locations', $this->serviceUrl);
        $params   = ['state' => $state, 'searchString' => $search];
        if ($effectiveDate) {
            $params['effectiveDate'] = $effectiveDate;
        }
        $response = $this->handleRequest(
            fn(PendingRequest $pendingRequest) => $pendingRequest->get($url, $params)
        );

        return $response->json();
    }

    /**
     * @return PendingRequest
     */
    private function prepareRequest(): PendingRequest
    {
        return Http::asJson()
            ->withHeaders(['Accept' => 'application/json'])
            ->timeout(self::TIMEOUT)
            ->retry(self::TRIES, self::SLEEP);
    }

    /**
     * @param array $query
     * @return int[]|null[]|string[]
     */
    private function prepareQuery(array $query): array
    {
        return array_filter($query, fn($a) => isset($a));
    }

    /**
     * @param Closure $handler
     *
     * @return Response
     *
     * @throws ClientException
     */
    private function handleRequest(Closure $handler): Response
    {
        try {
            /** @var Response $response */
            $response = $handler($this->prepareRequest());
        } catch (RequestException $e) {
            $response = $e->response;
        }

        if ($response->successful()) {
            return $response;
        }

        throw new ClientException($response->body(), $response->status(), $response->toException());
    }
}
