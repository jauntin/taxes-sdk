<?php

namespace Jauntin\Taxes;

use Jauntin\Taxes\Exception\BadQueryException;

class TaxQuery
{
    private string $municipalCode;
    /** @var array<string> */
    private array $excluded = [];
    private int $amount;

    private function __construct(private readonly string $state)
    {
    }

    public static function state(string $state): self
    {
        return new self($state);
    }

    /**
     * @param string $municipalCode
     *
     * @return $this
     */
    public function withMunicipal(string $municipalCode): self
    {
        $this->municipalCode = $municipalCode;

        return $this;
    }

    public function exclude(string $taxCode): self
    {
        $this->excluded[] = $taxCode;

        return $this;
    }

    public function amount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @throws BadQueryException
     */
    public function get(): array
    {
        if (!isset($this->amount)) {
            throw new BadQueryException('Amount must be set');
        }

        return array_filter([
            'state'         => $this->state,
            'amount'        => $this->amount,
            'municipalCode' => $this->municipalCode ?? null,
            'exclude'       => $this->excluded,
        ]);
    }
}
