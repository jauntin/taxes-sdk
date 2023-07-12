<?php

namespace Jauntin\TaxesSdk;

use Jauntin\TaxesSdk\Client\CacheableTaxesClient;
use Jauntin\TaxesSdk\Exception\ClientException;
use Jauntin\TaxesSdk\Query\CalculateQuery;
use Jauntin\TaxesSdk\Query\QueryFactory;

class TaxesService
{
    public function __construct(private readonly CacheableTaxesClient $client, private readonly QueryFactory $queryFactory)
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
     * @throws ClientException
     */
    public function getList(TaxType $taxType, array $params): array
    {
        return $this->client->getTaxes($params, $taxType);
    }

    /**
     * @return CalculateQuery
     */
    private function newQuery(): CalculateQuery
    {
        return $this->queryFactory->make($this->client);
    }
}
