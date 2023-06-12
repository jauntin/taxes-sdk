<?php

return [
    'api_uri' => env('TAXES_MICROSERVICE_URL', 'https://dev.taxes.jauntin.com'),
    'cache'   => [
        'driver' => env('TAXES_CACHE_DRIVER', env('CACHE_DRIVER', 'file')),
        'ttl'    => env('TAXES_CACHE_TTL', -1), // cache ttl in seconds, use 0 to cache forever or -1 to disable cache
    ],
];
