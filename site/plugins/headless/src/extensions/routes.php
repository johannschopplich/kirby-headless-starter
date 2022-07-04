<?php

use KirbyHeadless\Api\Api;
use KirbyHeadless\Api\Middlewares;
use KirbyHeadless\Api\UserMiddlewares;

return [
    /**
     * Allow preflight requests, mainly for `fetch`
     */
    [
        'pattern' => '(:all)',
        'method' => 'OPTIONS',
        'action' => fn () => Api::preflightResponse()
    ],

    /**
     * Return JSON-encoded page data for each request
     */
    [
        'pattern' => '(:all)',
        'action' => Api::createHandler(
            [Middlewares::class, 'tryResolveFiles'],
            // [UserMiddlewares::class, 'hasAuthHeaderOrRedirect'],
            [Middlewares::class, 'hasBearerToken'],
            [Middlewares::class, 'tryResolvePage']
        )
    ]
];
