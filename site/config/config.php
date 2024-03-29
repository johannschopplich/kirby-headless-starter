<?php

return [

    'debug' => env('KIRBY_DEBUG', false),

    'yaml' => [
        'handler' => 'symfony'
    ],
    'date' => [
        'handler' => 'intl'
    ],

    'languages' => env('KIRBY_MULTILANG', false),

    'panel' => [
        'install' => env('KIRBY_PANEL_INSTALL', false),
        'slug' => env('KIRBY_PANEL_SLUG', 'panel')
    ],

    'cache' => [
        'pages' => [
            'active' => env('KIRBY_CACHE', false),
            'ignore' => fn (\Kirby\Cms\Page $page) => $page->kirby()->user() !== null
        ]
    ],

    // Enable basic authentication for the Kirby API
    // Only needed, if you prefer basic auth over bearer tokens
    'api' => [
        'basicAuth' => true
    ],

    // Default to token-based authentication
    'kql' => [
        'auth' => 'bearer'
    ],

    // Kirby headless options
    'headless' => [
        // Enable returning Kirby templates as JSON
        'globalRoutes' => true,

        // Optional API token to use for authentication, also used
        // for for KQL endpoint
        'token' => env('KIRBY_HEADLESS_API_TOKEN'),

        'panel' => [
            // Preview URL for the Panel preview button
            'frontendUrl' => env('KIRBY_HEADLESS_FRONTEND_URL'),
            // Redirect to the Panel if no authorization header is sent,
            // useful for editors visiting the site directly
            'redirect' => false
        ],

        'cors' => [
            'allowOrigin' => env('KIRBY_HEADLESS_ALLOW_ORIGIN', '*'),
            'allowMethods' => env('KIRBY_HEADLESS_ALLOW_METHODS', 'GET, POST, OPTIONS'),
            'allowHeaders' => env('KIRBY_HEADLESS_ALLOW_HEADERS', 'Accept, Content-Type, Authorization, X-Language'),
            'maxAge' => env('KIRBY_HEADLESS_MAX_AGE', '86400')
        ]
    ]

];
