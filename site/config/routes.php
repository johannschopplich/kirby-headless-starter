<?php

use Kirby\Data\Json;
use Kirby\Exception\NotFoundException;
use Kirby\Http\Response;
use Kirby\Kql\Kql;
use KirbyHeadless\Api\Api;
use KirbyHeadless\Api\Middlewares;

return [
    /**
     * Allow KQL to be used with bearer token authentication
     * (as opposed to `/api/query`)
     * It also caches the query results
     */
    [
        'pattern' => 'query',
        'method' => 'GET|POST',
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
    ],

    /**
     * Return JSON-encoded page data for each other request
     */
    [
        'pattern' => ['(:all)', '(:all).json'],
        'action' => Api::createHandler(
            // [Middlewares::class, 'hasAuthHeader'],
            [Middlewares::class, 'hasBearerToken'],
            function ($context, $args) {
                // The `$args` array contains the route parameters
                [$pageId] = $args;
                $kirby = kirby();

                // Fall back to homepage id
                if (empty($pageId)) {
                    $pageId = $kirby->site()->homePageId();
                }

                $page = $kirby->page($pageId);

                if (!$page) {
                    $page = $kirby->site()->errorPage();
                }

                $cache = $cacheKey = $data = null;

                // Try to get the page from cache
                if ($page->isCacheable()) {
                    $cache = $kirby->cache('pages');
                    $cacheKey = $page->id() . '.headless.json';
                    $data = $cache->get($cacheKey);
                }

                // Fetch the page regularly
                if ($data === null) {
                    $template = $page->template();

                    if (!$template->exists()) {
                        throw new NotFoundException([
                            'key' => 'template.default.notFound'
                        ]);
                    }

                    $kirby->data = $page->controller();
                    $data = $template->render($kirby->data);

                    // Cache the result
                    if ($cache !== null) {
                        $cache->set($cacheKey, $data);
                    }
                }

                return Response::json($data);
            }
        )
    ]
];
