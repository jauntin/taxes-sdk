<?php

namespace Jauntin\TaxesSdk\Query\Result;

use Money\Currency;
use Money\Money;

class Calculated
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private readonly array $taxes;

    private readonly Money $total;

    public function __construct(array $result)
    {
        $this->taxes = array_map(function (array $tax) {
            $tax['amount'] = new Money($tax['amount']['amount'], new Currency($tax['amount']['currency']));

            return $tax;
        }, $result['taxes']);

        $this->total = new Money($result['total']['amount'], new Currency($result['total']['currency']));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getTaxes(): array
    {
        return $this->taxes;
    }

    public function getTotal(): Money
    {
        return $this->total;
    }
}
