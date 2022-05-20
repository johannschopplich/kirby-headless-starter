<?php

load([
    'KirbyHeadless\\Api\\Api' => 'models/Api.php',
    'KirbyHeadless\\Api\\ApiResponse' => 'models/ApiResponse.php',
    'KirbyHeadless\\Api\\Middlewares' => 'models/Middlewares.php'
], __DIR__);

\Kirby\Cms\App::plugin('johannschopplich/kirby-headless', [
    'hooks' => [
        // Explicitly register catch-all routes only when Kirby and all plugins
        // have been loaded to ensure no other routes are overwritten
        'system.loadPlugins:after' => function () {
            kirby()->extend([
                'routes' => require __DIR__ . '/config/routes.php'
            ], kirby()->plugin('johannschopplich/kirby-headless'));
        }
    ]
]);

/**
 * Encode an array of data into a JSON string
 *
 * @param array $data
 * @return \string
 */
function toJson(array $data)
{
    return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
