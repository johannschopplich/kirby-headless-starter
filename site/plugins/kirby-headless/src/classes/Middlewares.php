<?php

namespace KirbyHeadless\Api;

use Kirby\Exception\NotFoundException;
use Kirby\Filesystem\F;
use Kirby\Http\Response;
use Kirby\Toolkit\Str;

class Middlewares
{
    /**
     * Try to resolve page and site files
     */
    public static function tryResolveFiles(array $context, array $args)
    {
        // The `$args` array contains the route parameters
        [$path] = $args;
        $kirby = kirby();

        if (empty($path)) {
            return;
        }

        $extension = F::extension($path);

        if (empty($extension)) {
            return;
        }

        $id = dirname($path);
        $filename = basename($path);

        // Try to resolve image urls for pages and drafts
        if ($page = $kirby->site()->findPageOrDraft($id)) {
            return $page->file($filename);
        }

        // Try to resolve site files at least
        if ($file = $kirby->site()->file($filename)) {
            return $file;
        }
    }

    /**
     * Try to resolve global site data
     */
    public static function tryResolveSite(array $context, array $args)
    {
        // The `$args` array contains the route parameters
        [$path] = $args;

        if ($path !== '_site') {
            return;
        }

        $kirby = kirby();
        $cache = $cacheKey = $data = null;

        // Try to get the site data from cache
        $cache = $kirby->cache('pages');
        $cacheKey = '_site.headless.json';
        $data = $cache->get($cacheKey);

        // Fetch the site regularly
        if ($data === null) {
            $template = $kirby->template('_site');

            if (!$template->exists()) {
                throw new NotFoundException([
                    'key' => 'template.default.notFound'
                ]);
            }

            $data = $template->render([
                'kirby' => $kirby,
                'site'  => $kirby->site()
            ]);

            // Cache the result
            $cache?->set($cacheKey, $data);
        }

        return Response::json($data);
    }

    /**
     * Try to resolve the page id
     */
    public static function tryResolvePage(array $context, array $args)
    {
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

    /**
     * Checks if a bearer token was sent with the request and
     * if it matches the one configured in `.env`
     */
    public static function hasBearerToken()
    {
        $kirby = kirby();
        $token = env('KIRBY_HEADLESS_API_TOKEN');
        $authorization = $kirby->request()->header('Authorization');

        if ($kirby->option('headless.autoPanelRedirect', false) && empty($authorization)) {
            go(option('panel.slug'));
        }

        if (
            !empty($token) &&
            (empty($authorization) || $authorization !== 'Bearer ' . $token)
        ) {
            return Api::createResponse(401);
        }
    }

    /**
     * Checks if a body was sent with the request
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
