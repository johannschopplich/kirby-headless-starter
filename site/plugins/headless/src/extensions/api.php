<?php

use Kirby\Data\Json;
use Kirby\Kql\Kql;
use KirbyHeadless\Api\Api;

return [
    'routes' => function (\Kirby\Cms\App $kirby) {
        $authMethod = $kirby->option('kql.auth', true);
        $auth = $authMethod !== false && $authMethod !== 'bearer';

        return [
            /**
             * Allow preflight requests, mainly for `fetch`
             */
            [
                'pattern' => '(:all)',
                'method' => 'OPTIONS',
                'auth' => $auth,
                'action' => fn () => Api::preflightResponse()
            ],

            /**
             * Allow KQL to be used with bearer token authentication and
             * cache query results
             */
            [
                'pattern' => 'kql',
                'method' => 'GET|POST',
                'auth' => $auth,
                'action' => Api::createHandler(
                    function (array $context, array $args) use ($authMethod) {
                        if ($authMethod !== 'bearer') {
                            return;
                        }

                        $token = env('KIRBY_HEADLESS_API_TOKEN');
                        $authorization = kirby()->request()->header('Authorization');

                        if (empty($authorization) || $authorization !== 'Bearer ' . $token) {
                            return Api::createResponse(401);
                        }
                    },
                    function (array $context, array $args) {
                        $input = get();
                        $kirby = kirby();
                        $cache = $cacheKey = $data = null;

                        if (!empty($input)) {
                            $hash = sha1(Json::encode($input));
                            $cache = $kirby->cache('pages');
                            $cacheKey = 'query-' . $hash . '.json';
                            $data = $cache->get($cacheKey);
                        }

                        if ($kirby->multilang()) {
                            $languageCode = kirby()->request()->header('X-Language');
                            $kirby->setCurrentLanguage($languageCode);
                        }

                        if ($data === null) {
                            $data = Kql::run($input);
                            $cache?->set($cacheKey, $data);
                        }

                        return Api::createResponse(200, $data);
                    }
                )
            ]
        ];
    }
];
