<?php

namespace Jauntin\Taxes\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Jauntin\Taxes\Exception\ClientException as TaxesClientException;
use Jauntin\Taxes\Exception\Exception;
use Jauntin\Taxes\Exception\ServerException as TaxesServerException;

/**
 * @mixin Client
 */
class ClientDecorator
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     *
     * @throws Exception
     * @throws TaxesClientException
     * @throws TaxesServerException
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (!method_exists($this->client, $name)) {
            throw new \BadMethodCallException(sprintf(
                'Method %s::%s does not exist',
                get_class($this->client),
                $name
            ));
        }

        try {
            return $this->client->$name(...$arguments);
        } catch (ClientException $e) {
            throw new TaxesClientException($e->getMessage(), $e->getCode(), $e);
        } catch (ServerException $e) {
            throw new TaxesServerException($e->getMessage(), $e->getCode(), $e);
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
