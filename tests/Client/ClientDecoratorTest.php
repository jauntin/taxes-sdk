<?php

namespace Jauntin\Taxes\Tests\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Jauntin\Taxes\Client\ClientDecorator;
use Jauntin\Taxes\Exception\ClientException as TaxesClientException;
use Jauntin\Taxes\Exception\Exception as TaxesException;
use Jauntin\Taxes\Exception\ServerException as TaxesServerException;
use PHPUnit\Framework\TestCase;

class ClientDecoratorTest extends TestCase
{
    public function testClientException()
    {
        $client = $this->createMock(Client::class);
        $client->method('get')->willThrowException($this->createMock(ClientException::class));

        $this->expectException(TaxesClientException::class);

        $decorator = new ClientDecorator($client);
        $decorator->get('/api/v1/taxes');
    }

    public function testServerException()
    {
        $client = $this->createMock(Client::class);
        $client->method('get')->willThrowException($this->createMock(ServerException::class));

        $this->expectException(TaxesServerException::class);

        $decorator = new ClientDecorator($client);
        $decorator->get('/api/v1/taxes');
    }

    public function testException()
    {
        $client = $this->createMock(Client::class);
        $client->method('get')->willThrowException($this->createMock(GuzzleException::class));

        $this->expectException(TaxesException::class);

        $decorator = new ClientDecorator($client);
        $decorator->get('/api/v1/taxes');
    }
}
