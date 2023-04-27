<?php

namespace Jauntin\TaxesSdk\Query;

use Illuminate\Validation\Factory;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Jauntin\TaxesSdk\Client\CacheableTaxesClient;
use Jauntin\TaxesSdk\Exception\ClientException;
use Jauntin\TaxesSdk\Query\Result\Calculated;
use Jauntin\TaxesSdk\TaxType;
use Mockery;
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
     * @param array $result
     *
     * @return CalculateQuery|Mockery\MockInterface
     */
    public static function mock(array $result): self|Mockery\MockInterface
    {
        $mock = Mockery::mock(self::class)->makePartial();
        /** @var Mockery\ExpectationInterface $expectation */
        $expectation = $mock->shouldReceive('calculate');
        $expectation->andReturn(new Calculated($result));

        return $mock;
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
        ]);
    }
}
