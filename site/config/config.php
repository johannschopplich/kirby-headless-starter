<?php

return [

    'debug' => env('KIRBY_DEBUG', false),

    'languages' => env('KIRBY_MULTILANG', false),
    'languages.detect' => env('KIRBY_MULTILANG_DETECT', false),

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

    // Enable basic authentication for the API and thus KQL
    'api' => [
        'basicAuth' => true
    ],

    // Default to token-based authentication
    'kql' => [
        'auth' => 'bearer'
    ]

];
