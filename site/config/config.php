<?php

use Kirby\Cms\App;
use Kirby\Cms\Page;

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
        'slug' => env('KIRBY_PANEL_SLUG', 'panel'),
        'vue' => [
            'compiler' => false
        ]
    ],

    'thumbs' => [
        'format' => 'webp',
        'quality' => 80,
        'presets' => [
            'default' => ['format' => 'webp', 'quality' => 80],
        ],
        'srcsets' => [
            'default' => [360, 720, 1024, 1280, 1536]
        ]
    ],

    'cache' => [
        'pages' => [
            'active' => env('KIRBY_CACHE', false),
            'ignore' => fn(Page $page) => $page->kirby()->user() !== null
        ]
    ],

    // Blocks resolver configuration
    // See: https://kirby.tools/docs/headless/usage/field-methods#toresolvedblocks
    'blocksResolver' => require __DIR__ . '/blocks-resolver.php',

    // See: https://kirby.tools/docs/headless/usage/field-methods#resolvepermalinks
    'permalinksResolver' => [
        // Strip the origin from URLs
        'urlParser' => function (string $url, App $kirby) {
            $path = parse_url($url, PHP_URL_PATH);
            return $path;
        }
    ],

    // Default to token-based authentication
    'kql' => [
        'auth' => 'bearer'
    ],

    // Kirby 5 native CORS support
    'cors' => [
        'allowOrigin' => env('KIRBY_CORS_ALLOW_ORIGIN', '*'),
        'allowMethods' => ['GET', 'HEAD', 'POST', 'OPTIONS'],
        'allowHeaders' => true,
        'maxAge' => 86400
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
        ]
    ]

];
