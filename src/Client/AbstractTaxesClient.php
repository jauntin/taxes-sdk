<?php

namespace Jauntin\TaxesSdk\Client;

use Closure;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Jauntin\TaxesSdk\Exception\ClientException;

abstract class AbstractTaxesClient
{
    private const SLEEP = 1000;

    private const TIMEOUT = 5;

    private const TRIES = 3;

    protected readonly string $serviceUrl;

    protected readonly ?string $token;

    public function __construct()
    {
        $this->serviceUrl = config('taxes-sdk.api_uri');
        $this->token = config('taxes-sdk.token');
    }

    protected function prepareRequest(): PendingRequest
    {
        $request = Http::asJson()
            ->withHeaders(['Accept' => 'application/json'])
            ->timeout(self::TIMEOUT)
            ->retry(self::TRIES, self::SLEEP);

        if ($this->token) {
            $request->withToken($this->token);
        }

        return $request;
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    protected function prepareQuery(array $query): array
    {
        return array_filter($query, fn ($value) => isset($value));
    }

    /**
     * @throws ClientException
     */
    protected function handleRequest(Closure $handler): Response
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
