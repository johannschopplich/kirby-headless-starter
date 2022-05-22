<?php

use Kirby\Kql\Kql;
use KirbyHeadless\Api\Api;
use KirbyHeadless\Api\Middlewares;

return [
    /**
     * Allow KQL to be used with bearer token authentication
     * (as opposed to `/api/query`)
     */
    [
        'pattern' => 'query',
        'method' => 'GET|POST',
        'action' => Api::createHandler(
            [Middlewares::class, 'hasBearerToken'],
            function ($context, $args) {
                $result = Kql::run([
                    'query' => get('query'),
                    'select' => get('select'),
                    'pagination' => [
                        'page' => get('page', 1),
                        'limit' => get('limit', 100)
                    ]
                ]);

                return Api::createResponse(200, $result);
            }
        )
    ],

    /**
     * Return JSON-encoded data for each other page
     */
    [
        'pattern' => ['(:all)', '(:all).json'],
        'action' => Api::createHandler(
            [Middlewares::class, 'hasBearerToken'],
            [Middlewares::class, 'templateToJson']
        )
    ]
];
