<?php

namespace Jauntin\TaxesSdk\Query;

use Illuminate\Validation\Factory as Validator;
use Jauntin\TaxesSdk\Client\CacheableTaxesClient;

class QueryFactory
{
    public function __construct(private readonly Validator $validator) {}

    public function make(CacheableTaxesClient $client): CalculateQuery
    {
        return new CalculateQuery($client, $this->validator);
    }
}
