<?php

use Kirby\Exception\NotFoundException;
use Kirby\Http\Response;
use Kirby\Kql\Kql;

return [
    /**
     * Open KQL for usage with bearer token
     */
    [
        'pattern' => 'query',
        'method' => 'POST|GET',
        'action' => function () {
            $headers = kirby()->request()->headers();

            // Validate API bearer token
            if (
                env('KIRBY_HEADLESS_API_TOKEN') &&
                (!isset($headers['Authorization']) || $headers['Authorization'] !== 'Bearer ' . env('KIRBY_HEADLESS_API_TOKEN'))
            ) {
                return Response::json([
                    'code' => 401,
                    'status' => 'nok',
                    'message' => 'Unauthorized'
                ], 401);
            }

            $result = Kql::run([
                'query' => get('query'),
                'select' => get('select'),
                'pagination' => [
                    'page' => get('page', 1),
                    'limit' => get('limit', 100)
                ]
            ]);

            return [
                'code'   => 200,
                'status' => 'ok',
                'result' => $result
            ];
        }
    ],

    /**
     * Return JSON-encoded page data to be consumed by the frontend
     */
    [
        'pattern' => ['(:all)', '(:all).json'],
        'action' => function ($pageId) {
            $kirby = kirby();
            $headers = $kirby->request()->headers();

            // Validate API bearer token
            if (
                env('KIRBY_HEADLESS_API_TOKEN') &&
                (!isset($headers['Authorization']) || $headers['Authorization'] !== 'Bearer ' . env('KIRBY_HEADLESS_API_TOKEN'))
            ) {
                return Response::json([
                    'code' => 401,
                    'status' => 'nok',
                    'message' => 'Unauthorized'
                ], 401);
            }

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
                $cacheId  = $page->id() . '.headless';
                $result   = $cache->get($cacheId);
                $data     = $result['data'] ?? null;
                $response = $result['response'] ?? [];

                // Reconstruct the response configuration
                if (!empty($data) && !empty($response)) {
                    $kirby->response()->fromArray($response);
                }
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

                // Convert the response configuration to an array
                $response = $kirby->response()->toArray();

                // Cache the result
                if ($cache !== null) {
                    $cache->set($cacheId, [
                        'data' => $data,
                        'response' => $response
                    ]);
                }
            }

            return Response::json($data);
        }
    ]
];
