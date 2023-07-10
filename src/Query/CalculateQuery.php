<?php

namespace Jauntin\TaxesSdk\Query;

use DateTime;
use Illuminate\Validation\Factory;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Jauntin\TaxesSdk\Client\CacheableTaxesClient;
use Jauntin\TaxesSdk\Exception\ClientException;
use Jauntin\TaxesSdk\Query\Result\Calculated;
use Jauntin\TaxesSdk\TaxType;
use Money\Money;

class CalculateQuery
{
    private array $params = [
        'taxTypes' => [],
    ];

    /**
     * @param CacheableTaxesClient $client
     * @param Factory $validator
     */
    public function __construct(private readonly CacheableTaxesClient $client, private readonly Factory $validator)
    {
    }

    /**
     * @param array<int, TaxType|string> $taxes
     *
     * @return $this
     */
    public function taxes(array $taxes): self
    {
        $this->params['taxTypes'] = array_map(fn(TaxType|string $t) => $t instanceof TaxType ? $t->value : $t, $taxes);

        return $this;
    }

    /**
     * @param string $state
     *
     * @return $this
     */
    public function state(string $state): self
    {
        $this->params['state'] = $state;

        return $this;
    }

    /**
     * @param array|string[] $codes
     *
     * @return $this
     */
    public function include(array $codes): self
    {
        $this->params['include'] = $codes;

        return $this;
    }

    /**
     * @param array|string[] $codes
     *
     * @return $this
     */
    public function exclude(array $codes): self
    {
        $this->params['exclude'] = $codes;

        return $this;
    }

    /**
     * @param string $municipalCode
     *
     * @return $this
     */
    public function withMunicipal(string $municipalCode): self
    {
        $this->params['municipalCode'] = $municipalCode;
        $this->params['taxTypes'][] = TaxType::MUNICIPAL->value;
        $this->params['taxTypes'] = array_unique($this->params['taxTypes']);

        return $this;
    }

    /**
     * @param DateTime|string $startDate
     *
     * @return $this
     */
    public function setStartDate(DateTime|string $startDate): self
    {
        $this->params['startDate'] = $startDate instanceof DateTime ? $startDate->format('Y-m-d') : $startDate;

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

    public function get(TaxType $taxType): array
    {
        try {
            return $this->client->getTaxes($this->params, $taxType);
        } catch (ClientException $e) {
            return [];
        }
    }

    /**
     * @throws ValidationException
     */
    private function validate(): void
    {
        $requiredWithMunicipal = Rule::requiredIf(in_array(TaxType::MUNICIPAL->value, $this->params['taxTypes']));

        $this->validator->validate($this->params, [
            'taxTypes'      => ['required', 'array', 'min:1'],
            'taxTypes.*'    => ['required', Rule::in(TaxType::types())],
            'state'         => ['required', 'string'],
            'amount'        => ['required', 'int', 'min:1'],
            'municipalCode' => [$requiredWithMunicipal, 'string'],
            'include'       => ['array'],
            'include.*'     => ['string'],
            'exclude'       => ['array'],
            'exclude.*'     => ['string'],
            'startDate'     => ['date'],
        ]);
    }
}
