<?php

use Kirby\Exception\NotFoundException;
use Kirby\Http\Response;
use Kirby\Toolkit\Str;
use KirbyHeadless\Api\Api;
use KirbyHeadless\Api\Middlewares;

return [
    /**
     * Allow for preflight requests, mainly for `fetch`
     */
    [
        'pattern' => '(:all)',
        'method' => 'OPTIONS',
        'action' => fn () => Api::preflightResponse()
    ],

    /**
     * Return JSON-encoded page data for each request
     */
    [
        'pattern' => '(:all)',
        'action' => Api::createHandler(
            [Middlewares::class, 'tryResolveFiles'],
            // [Middlewares::class, 'hasAuthHeaderOrRedirect'],
            [Middlewares::class, 'hasBearerToken'],
            function ($context, $args) {
                // The `$args` array contains the route parameters
                [$path] = $args;
                $kirby = kirby();

                // Fall back to homepage id
                if (empty($path)) {
                    $page = $kirby->site()->homePage();
                } else {
                    $path = Str::rtrim($path, '.json');
                    $page = $kirby->site()->find($path);

                    if (!$page) {
                        $page = $kirby->site()->errorPage();
                    }
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
