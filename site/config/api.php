<?php

use Kirby\Data\Json;
use Kirby\Kql\Kql;
use KirbyHeadless\Api\Api;
use KirbyHeadless\Api\Middlewares;

return [
    'routes' => function (\Kirby\Cms\App $kirby) {
        $kqlAuth = $kirby->option('kql.auth');

        return [
            /**
             * Allow KQL to be used with bearer token authentication and
             * cache query results
             */
            [
                'pattern' => 'query',
                'method' => 'GET|POST',
                'auth' => ($kqlAuth !== true && $kqlAuth !== 'bearer'),
                'action' => Api::createHandler(
                    [Middlewares::class, 'hasBearerToken'],
                    function ($context, $args) {
                        $input = get();
                        $cache = $cacheKey = $data = null;

                        if (!empty($input)) {
                            $hash = sha1(Json::encode($input));
                            $cache = kirby()->cache('pages');
                            $cacheKey = 'query-' . $hash . '.json';
                            $data = $cache->get($cacheKey);
                        }

                        if ($data === null) {
                            $data = Kql::run($input);

                            if ($cache !== null) {
                                $cache->set($cacheKey, $data);
                            }
                        }

                        return Api::createResponse(200, $data);
                    }
                )
            ]
        ];
    }
];
