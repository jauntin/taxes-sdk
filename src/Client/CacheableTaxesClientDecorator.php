<?php

namespace Jauntin\TaxesSdk\Client;

use BadMethodCallException;
use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * @mixin TaxesClient
 */
class CacheableTaxesClientDecorator
{
    public function __construct(private readonly TaxesClient $client)
    {
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (!method_exists($this->client, $name)) {
            throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', get_class($this->client), $name));
        }

        $key = sprintf('%s_%s', $name, serialize($arguments));

        return $this->wrapCache($key, fn() => $this->client->$name(...$arguments));
    }

    /**
     * @param string $key
     * @param Closure $closure
     *
     * @return mixed
     */
    private function wrapCache(string $key, Closure $closure): mixed
    {
        $driver = config('taxes-sdk.cache.driver');
        $ttl    = config('taxes-sdk.cache.ttl');

        if ($driver && $ttl && intval($ttl) > 0) {
            return Cache::driver($driver)->remember($key, $ttl, $closure); // @phpstan-ignore-line
        }

        return value($closure);
    }
}
