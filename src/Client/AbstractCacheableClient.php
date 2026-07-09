<?php

namespace Jauntin\TaxesSdk\Client;

use BadMethodCallException;
use Closure;
use Illuminate\Support\Facades\Cache;

abstract class AbstractCacheableClient
{
    /**
     * The underlying client whose calls are cached.
     */
    abstract protected function wrappedClient(): object;

    /**
     * @param  array<int, mixed>  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $client = $this->wrappedClient();

        if (! method_exists($client, $name)) {
            throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', get_class($client), $name));
        }

        $key = sprintf('taxes-sdk_%s_%s', $name, serialize($arguments));

        return $this->wrapCache($key, fn () => $client->$name(...$arguments));
    }

    private function wrapCache(string $key, Closure $closure): mixed
    {
        $driver = config('taxes-sdk.cache.driver');
        $ttl = config('taxes-sdk.cache.ttl');

        if ($driver && isset($ttl) && ($ttl = intval($ttl)) >= 0) {
            return Cache::driver($driver)->remember($key, $ttl === 0 ? null : $ttl, $closure); // @phpstan-ignore argument.templateType
        }

        return value($closure);
    }
}
