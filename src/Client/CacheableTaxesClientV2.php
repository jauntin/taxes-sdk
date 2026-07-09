<?php

namespace Jauntin\TaxesSdk\Client;

/**
 * @mixin TaxesClientV2
 */
class CacheableTaxesClientV2 extends AbstractCacheableClient
{
    public function __construct(private readonly TaxesClientV2 $client) {}

    protected function wrappedClient(): object
    {
        return $this->client;
    }
}
