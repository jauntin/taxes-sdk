<?php

return [
    'api_uri' => env('TAXES_MICROSERVICE_URL', 'https://dev.taxes.jauntin.com'),
    'cache'   => [
        'driver' => env('CACHE_DRIVER', 'file'),
        'ttl'    => env('CACHE_TTL'),
    ],
];