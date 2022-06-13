<?php

namespace KirbyHeadless\Api;

use Kirby\Filesystem\F;

class Middlewares
{
    /**
     * Try to resolve page and site files
     *
     * @return \Kirby\Cms\File|void
     */
    public static function tryResolveFiles($context, $args)
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
     * Redirect to the Kirby Panel if no
     * authorization header is provided
     *
     * @return void
     */
    public static function hasAuthHeaderOrRedirect()
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
