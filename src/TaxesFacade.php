<?php

namespace Jauntin\TaxesSdk;

use Illuminate\Support\Facades\Facade;

class TaxesFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TaxesService::class;
    }
}
