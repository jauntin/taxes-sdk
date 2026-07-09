<?php

namespace Jauntin\TaxesSdk\Client;

/**
 * @mixin TaxesClient
 */
class CacheableTaxesClient extends AbstractCacheableClient
{
    public function __construct(private readonly TaxesClient $client) {}

    protected function wrappedClient(): object
    {
        return $this->client;
    }
}
