<?php

namespace Jauntin\TaxesSdk\Query;

use Illuminate\Validation\Factory as Validator;
use Jauntin\TaxesSdk\Client\CacheableTaxesClientDecorator;
use Jauntin\TaxesSdk\Client\TaxesClient;

class QueryFactory
{
    /**
     * @param Validator $validator
     */
    public function __construct(private readonly Validator $validator)
    {
    }

    /**
     * @param CacheableTaxesClientDecorator $client
     *
     * @return CalculateQuery
     */
    public function make(CacheableTaxesClientDecorator $client): CalculateQuery
    {
        return new CalculateQuery($client, $this->validator);
    }
}
