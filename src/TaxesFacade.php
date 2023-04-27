<?php

namespace Jauntin\TaxesSdk;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin TaxesService
 */
class TaxesFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TaxesService::class;
    }
}
