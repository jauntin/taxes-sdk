<?php

namespace Jauntin\TaxesSdk;

use Jauntin\TaxesSdk\Client\TaxesClient;
use Jauntin\TaxesSdk\Exception\ClientException;
use Jauntin\TaxesSdk\Query\CalculateQuery;
use Jauntin\TaxesSdk\Query\QueryFactory;

class TaxesService
{
    public function __construct(private readonly TaxesClient $client, private readonly QueryFactory $queryFactory)
    {
    }

    /**
     * @param array $taxes
     *
     * @return CalculateQuery
     */
    public function taxes(array $taxes): CalculateQuery
    {
         return $this->newQuery()->taxes($taxes);
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

    /**
     * @return CalculateQuery
     */
    private function newQuery(): CalculateQuery
    {
        return $this->queryFactory->make($this->client);
    }
}
