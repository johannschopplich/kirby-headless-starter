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
                'action' => fn () => Api::createPreflightResponse()
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
                    // Middleware to validate the bearer token
                    function (array $context, array $args) use (&$kirby, &$authMethod) {
                        if ($authMethod !== 'bearer') {
                            return;
                        }

                        $authorization = $kirby->request()->header('Authorization');
                        $token = $kirby->option('headless.token');

                        if ($authorization !== 'Bearer ' . $token) {
                            return Api::createResponse(401);
                        }
                    },
                    // Middleware to run queries and cache their results
                    function (array $context, array $args) use (&$kirby) {
                        $input = get();
                        $cache = $cacheKey = $data = null;
                        $languageCode = $kirby->request()->header('X-Language');

                        // Set the Kirby language in multilanguage sites
                        if ($kirby->multilang() && $languageCode) {
                            $kirby->setCurrentLanguage($languageCode);
                        }

                        if (!empty($input)) {
                            $hash = sha1(Json::encode($input));
                            $cache = $kirby->cache('pages');
                            $cacheKey = 'query-' . $hash . (!empty($languageCode) ? '-' . $languageCode : '') . '.json';
                            $data = $cache->get($cacheKey);
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
