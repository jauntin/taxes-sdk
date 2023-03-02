<?php

namespace Jauntin\Taxes\Tests;

use GuzzleHttp\Client;
use Jauntin\Taxes\Client\ClientDecorator;
use Jauntin\Taxes\Result;
use Jauntin\Taxes\Taxes;
use Jauntin\Taxes\TaxQuery;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class TaxesTest extends TestCase
{
    public function testQuery()
    {
        $client = $this->createMock(Client::class);

        $client->method('get')->willReturnCallback(function () {
            $response = $this->createMock(ResponseInterface::class);
            $response->method('getBody')->willReturnCallback(function () {
                $stream = $this->createMock(StreamInterface::class);
                $stream->method('getContents')->willReturn('{"surcharges":[{"state":"KY","type":"State","code":"TAX","rate":0.03,"municipalCode":null,"municipalName":null,"amount":{"amount":"30","currency":"USD"}},{"state":"KY","type":"AdChrg","code":"SCKY","rate":0.018,"municipalCode":null,"municipalName":null,"amount":{"amount":"18","currency":"USD"}},{"state":"KY","type":"AdChrg","code":"AFKY1","rate":0.05,"municipalCode":"1017","municipalName":"LOUISVILLE - URBAN","amount":{"amount":"50","currency":"USD"}}],"total":{"amount":"98","currency":"USD"}}');

                return $stream;
            });

            return $response;
        });

        $clientDecorator = new ClientDecorator($client);
        $taxes           = new Taxes($clientDecorator);
        $result          = $taxes->query(TaxQuery::state('KY')
            ->withMunicipal('1017')
            ->exclude('FMT')
            ->amount(1000));

        $this->assertInstanceOf(Result::class, $result);
        $this->assertCount(3, $result->getSurcharges());
        $this->assertInstanceOf(Money::class, $result->getTotal());
    }
}
