<?php

namespace Jauntin\Taxes;

use Jauntin\Taxes\Client\ClientDecorator;
use Jauntin\Taxes\Exception\Exception;
use Money\Currency;
use Money\Money;

class Taxes
{
    public function __construct(private readonly ClientDecorator $client,)
    {
    }

    /**
     * @param TaxQuery $query
     *
     * @return Result
     *
     * @throws Exception
     */
    public function query(TaxQuery $query): Result
    {
        $response = $this->client->get('/api/v1/taxes/calculate', [
            'query' => $query->get(),
        ]);
        $result   = json_decode($response->getBody()->getContents(), true);

        $this->validateResult($result);

        return $this->formatResult($result);
    }

    private function formatResult(array $result): Result
    {
        return new Result(
            array_map(function (array $surcharge) {
                $surcharge['amount'] = new Money($surcharge['amount']['amount'], new Currency($surcharge['amount']['currency']));

                return $surcharge;
            }, $result['surcharges']),
            new Money($result['total']['amount'], new Currency($result['total']['currency'])),
        );
    }

    /**
     * @throws Exception
     */
    private function validateResult(mixed $result): void
    {
        if (!$result || !is_array($result) || !isset($result['surcharges']) || !isset($result['total'])) {
            throw new Exception('Invalid response from Taxes API');
        }
    }
}
