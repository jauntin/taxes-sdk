<?php

namespace Jauntin\TaxesSdk;

enum TaxType: string
{
    case ADMITTED = 'admitted';
    case SURPLUS = 'surplus';
    case MUNICIPAL = 'municipal';

    /**
     * @return array<int, string>
     */
    public static function types(): array
    {
        return array_map(fn (TaxType $taxType) => $taxType->value, self::cases());
    }
}
