<?php

namespace Jauntin\TaxesSdk;

use Illuminate\Support\Facades\Facade;
use Jauntin\TaxesSdk\Query\CalculateQuery;

/**
 * @mixin TaxesService
 *
 * @method static CalculateQuery taxes(array $taxTypes)
 * @method static bool shouldLookup(string $state)
 * @method static array lookupTaxLocations(string $state, string $search)
 */
class TaxesFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TaxesService::class;
    }
}
