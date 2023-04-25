<?php

namespace Jauntin\TaxesSdk\Query;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Jauntin\TaxesSdk\Client\TaxesClient;
use Jauntin\TaxesSdk\Exception\ClientException;
use Jauntin\TaxesSdk\Query\Result\Calculated;
use Money\Money;

class CalculateQuery
{
    private array $params = [
        'taxTypes' => [],
    ];

    public function __construct(private readonly TaxesClient $client)
    {
    }

    public function taxes(array $taxes): self
    {
        $this->params['taxTypes'] = $taxes;

        return $this;
    }

    public function state(string $state): self
    {
        $this->params['state'] = $state;

        return $this;
    }

    public function withMunicipal(string $municipalCode): self
    {
        $this->params['municipalCode'] = $municipalCode;
        $this->params['taxTypes'] = array_unique([...$this->params['taxTypes'], 'municipal']);

        return $this;
    }

    /**
     * @throws ClientException|ValidationException
     */
    public function calculate(Money|int $preSurcharge): Calculated
    {
        $this->params['amount'] = $preSurcharge instanceof Money ? $preSurcharge->getAmount() : $preSurcharge;
        $this->validate();
        $result = $this->client->calculateTaxes($this->params);

        return new Calculated($result);
    }

    /**
     * @throws ValidationException
     */
    private function validate(): void
    {
        Validator::validate($this->params, [
            'taxTypes' => ['required', 'array', 'min:1'],
            'state'    => ['required', 'string'],
            'amount'   => ['required', 'int', 'min:1'],
            'municipalCode' => [Rule::requiredIf(in_array('municipal', $this->params['taxTypes'])), 'string'],
        ]);
    }
}
