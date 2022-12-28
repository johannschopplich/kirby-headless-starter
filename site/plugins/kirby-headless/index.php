<?php

load([
    'KirbyHeadless\\Api\\Api' => 'src/classes/Api.php',
    'KirbyHeadless\\Api\\Middlewares' => 'src/classes/Middlewares.php'
], __DIR__);

\Kirby\Cms\App::plugin('johannschopplich/kirby-headless', [
    'hooks' => [
        // Explicitly register catch-all routes only when Kirby and all plugins
        // have been loaded to ensure no other routes are overwritten
        'system.loadPlugins:after' => function () {
            kirby()->extend(
                [
                    'api' => require __DIR__ . '/src/extensions/api.php',
                    'routes' => require __DIR__ . '/src/extensions/routes.php'
                ],
                kirby()->plugin('johannschopplich/kirby-headless')
            );
        }
    ],
    'fieldMethods' => require __DIR__ . '/src/extensions/fieldMethods.php',
    'pageMethods' => require __DIR__ . '/src/extensions/pageMethods.php',
    'siteMethods' => require __DIR__ . '/src/extensions/siteMethods.php'
]);
