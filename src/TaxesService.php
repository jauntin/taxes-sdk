<?php

namespace Jauntin\TaxesSdk;

use Jauntin\TaxesSdk\Client\TaxesClient;
use Jauntin\TaxesSdk\Exception\ClientException;
use Jauntin\TaxesSdk\Query\CalculateQuery;

class TaxesService
{
    public function __construct(private readonly TaxesClient $client) {
    }

    /**
     * @param array $taxes
     *
     * @return CalculateQuery
     */
    public function taxes(array $taxes): CalculateQuery
    {
         return (new CalculateQuery($this->client))->taxes($taxes);
    }

    /**
     * @throws ClientException
     */
    public function shouldLookup(string $state): bool
    {
        return $this->client->shouldLookup($state);
    }

    /**
     * @throws ClientException
     */
    public function lookupTaxLocations(string $state, string $search): array
    {
        return $this->client->lookupTaxLocations($state, $search);
    }
}
