<?php

use KirbyHeadless\Api\Api;
use KirbyHeadless\Api\Middlewares;

return [
    /**
     * Allow preflight requests, mainly for `fetch`
     */
    [
        'pattern' => '(:all)',
        'method' => 'OPTIONS',
        'action' => fn () => Api::createPreflightResponse()
    ],

    /**
     * Return JSON-encoded page data for each request
     */
    [
        'pattern' => '(:all)',
        'action' => Api::createHandler(
            [Middlewares::class, 'tryResolveFiles'],
            [Middlewares::class, 'hasBearerToken'],
            [Middlewares::class, 'tryResolveSite'],
            [Middlewares::class, 'tryResolvePage']
        )
    ]
];
