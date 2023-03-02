<?php

namespace Jauntin\Taxes;

use Money\Money;

class Result
{
    public function __construct(
        public readonly array $surcharges,
        public readonly Money $total,
    ) {
    }

    /**
     * @return array
     */
    public function getSurcharges(): array
    {
        return $this->surcharges;
    }

    /**
     * @return Money
     */
    public function getTotal(): Money
    {
        return $this->total;
    }
}
