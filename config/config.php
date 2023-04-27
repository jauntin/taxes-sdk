<?php

return [
    'api_uri' => env('TAXES_MICROSERVICE_URL', 'https://dev.taxes.jauntin.com'),
    'cache'   => [
        'driver' => env('CACHE_DRIVER', 'file'),
        'ttl'    => env('CACHE_TTL', 0), // cache ttl in seconds, use 0 or -1 to disable cache
    ],
];