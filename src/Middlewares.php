<?php

namespace KirbyHeadless\Api;

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
