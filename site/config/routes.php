<?php

use Kirby\Exception\NotFoundException;
use Kirby\Http\Response;
use KirbyHeadless\Api\Api;
use KirbyHeadless\Api\Middlewares;

return [
    /**
     * Allow for preflight requests, mainly for `fetch`
     */
    [
        'pattern' => '(:all)',
        'method' => 'OPTIONS',
        'action' => function () {
            Api::addCorsAllowHeaders();
            return true;
        }
    ],

    /**
     * Return JSON-encoded page data for each request
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
                    $cache?->set($cacheKey, $data);
                }

                return Response::json($data);
            }
        )
    ]
];
