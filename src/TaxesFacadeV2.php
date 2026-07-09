<?php

namespace Jauntin\TaxesSdk;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin TaxesServiceV2
 *
 * @method static array jurisdictionLookup(array $params)
 * @method static array lookupByAddress(string $address, bool $debug = false)
 * @method static array lookupByLatLng(float $lat, float $lng, bool $debug = false)
 * @method static array lookupByCity(string $city, ?string $county = null, bool $debug = false)
 * @method static array lookupByPlaceId(string $placeId, bool $debug = false)
 */
class TaxesFacadeV2 extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TaxesServiceV2::class;
    }
}
