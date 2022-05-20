<?php

use Kirby\Exception\NotFoundException;
use Kirby\Http\Response;
use Kirby\Kql\Kql;
use KirbyHeadless\Api\Api;
use KirbyHeadless\Api\ApiResponse;
use KirbyHeadless\Api\Middlewares;

return [
    /**
     * Allow KQL to be used with bearer token authentication
     * (as opposed to `/api/query`)
     */
    [
        'pattern' => 'query',
        'method' => 'GET|POST',
        'action' => Api::createHandler(
            [Middlewares::class, 'hasBearerToken'],
            function ($context) {
                $result = Kql::run([
                    'query' => get('query'),
                    'select' => get('select'),
                    'pagination' => [
                        'page' => get('page', 1),
                        'limit' => get('limit', 100)
                    ]
                ]);

                return ApiResponse::create(200, $result);
            }
        )
    ],

    /**
     * Return JSON-encoded data for each other page
     */
    [
        'pattern' => ['(:all)', '(:all).json'],
        'action' => Api::createHandler(
            [Middlewares::class, 'hasBearerToken'],
            function ($context) {
                $kirby = kirby();

                // Fall back to homepage id
                if (empty($pageId)) {
                    $pageId = site()->homePageId();
                }

                $page = $kirby->page($pageId);

                if (!$page || !$page->isVerified(get('token'))) {
                    $page = $kirby->site()->errorPage();
                };

                $cache = $cacheId = $data = null;

                // Try to get the page from cache
                if ($page->isCacheable()) {
                    $cache    = $kirby->cache('pages');
                    $cacheId  = $page->id() . '.headless.json';
                    $result   = $cache->get($cacheId);
                    $data     = $result['data'] ?? null;
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
                        $cache->set($cacheId, compact('data'));
                    }
                }

                return Response::json($data);
            }
        )
    ]
];
