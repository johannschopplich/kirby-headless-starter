<?php

use Kirby\Data\Json;
use Kirby\Kql\Kql;
use KirbyHeadless\Api\Api;

return [
    'routes' => function (\Kirby\Cms\App $kirby) {
        $kqlAuth = $kirby->option('kql.auth', true);

        return [
            /**
             * Allow KQL to be used with bearer token authentication and
             * cache query results
             */
            [
                'pattern' => 'kql',
                'method' => 'GET|POST',
                'auth' => $kqlAuth !== false && $kqlAuth !== 'bearer',
                'action' => Api::createHandler(
                    function ($context, $args) use ($kqlAuth) {
                        if ($kqlAuth !== 'bearer') {
                            return;
                        }

                        $token = env('KIRBY_HEADLESS_API_TOKEN');
                        $authorization = kirby()->request()->header('Authorization');

                        if (empty($authorization) || $authorization !== 'Bearer ' . $token) {
                            return Api::createResponse(401);
                        }
                    },
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
