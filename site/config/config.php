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

    // See: https://kirby.tools/docs/headless/usage/field-methods#toresolvedblocks
    'blocksResolver' => require __DIR__ . '/blocks-resolver.php',

    // See: https://kirby.tools/docs/headless/usage/field-methods#resolvepermalinks
    'permalinksResolver' => [
        // Strips the origin from URLs.
        'urlParser' => function (string $url, App $kirby) {
            $path = parse_url($url, PHP_URL_PATH);
            return $path;
        }
    ],

    'kql' => [
        'auth' => 'bearer'
    ],

    'cors' => [
        'allowOrigin' => env('KIRBY_CORS_ALLOW_ORIGIN', '*'),
        'allowHeaders' => true,
        'maxAge' => 86400
    ],

    // See: https://kirby.tools/docs/headless/configuration/authentication
    'headless' => [
        'globalRoutes' => true,

        'token' => env('KIRBY_HEADLESS_API_TOKEN'),

        'panel' => [
            // Preview URL for the Panel preview button.
            'frontendUrl' => env('KIRBY_HEADLESS_FRONTEND_URL'),
            'redirect' => true
        ]
    ]

];
