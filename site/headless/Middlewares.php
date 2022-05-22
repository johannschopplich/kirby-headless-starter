<?php

namespace KirbyHeadless\Api;

use Kirby\Exception\NotFoundException;
use Kirby\Http\Response;

class Middlewares
{
    /**
     * Redirect to the Kirby Panel if no
     * authorization header is provided
     *
     * @return void
     */
    public static function hasAuthHeader()
    {
        $authorization = kirby()->request()->header('Authorization');

        if (empty($authorization)) {
            go('panel');
        }
    }

    /**
     * Checks if a bearer token was sent with the request and
     * if it matches the one configured in `.env`
     *
     * @return \Kirby\Http\Response|void
     */
    public static function hasBearerToken()
    {
        $token = env('KIRBY_HEADLESS_API_TOKEN');
        $authorization = kirby()->request()->header('Authorization');

        if (
            !empty($token) &&
            (empty($authorization) || $authorization !== 'Bearer ' . $token)
        ) {
            return Api::createResponse(401);
        }
    }

    /**
     * Returns page data as a JSON response
     *
     * @param array $context
     * @param array $args
     * @return \Kirby\Http\Response|void
     */
    public static function templateToJson(array $context, array $args)
    {
        // The `$args` array contains the route parameters
        [$pageId] = $args;
        $kirby = kirby();

        // Fall back to homepage id
        if (empty($pageId)) {
            $pageId = $kirby->site()->homePageId();
        }

        $page = $kirby->page($pageId);

        if (!$page /* || !$page->isReadable() */) {
            $page = $kirby->site()->errorPage();
        }

        $cache = $cacheId = $data = null;

        // Try to get the page from cache
        if ($page->isCacheable()) {
            $cache = $kirby->cache('pages');
            $cacheId = $page->id() . '.headless.json';
            $data = $cache->get($cacheId);
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
                $cache->set($cacheId, $data);
            }
        }

        return Response::json($data);
    }

    /**
     * Checks if a body was sent with the request
     *
     * @param array $context
     * @return \Kirby\Http\Response|array
     */
    public static function hasBody(array $context)
    {
        $body = kirby()->request()->body();

        if (empty($body)) {
            return Api::createResponse(400, [
                'error' => 'No data was sent with the request'
            ]);
        }

        $context['body'] = $body;
        $context['query'] = kirby()->request()->query();

        return $context;
    }
}
