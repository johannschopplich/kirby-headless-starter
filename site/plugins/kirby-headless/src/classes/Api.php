<?php

namespace KirbyHeadless\Api;

use Exception;
use Kirby\Cms\File;
use Kirby\Http\Response;
use Kirby\Toolkit\A;

class Api
{
    /**
     * Create an API handler
     */
    public static function createHandler(callable ...$fns)
    {
        $context = [];

        return function (...$args) use ($fns, $context) {
            foreach ($fns as $fn) {
                $result = $fn($context, $args);

                if ($result instanceof Response || $result instanceof File) {
                    return $result;
                }

                if (is_array($result)) {
                    $context = A::merge($context, $result);
                }
            }
        };
    }

    /**
     * Create an API response
     * Enforces consistent JSON responses by wrapping Kirby's `Response` class
     */
    public static function createResponse(int $code, $data = null): Response
    {
        $body = [
            'code' => $code,
            'status' => static::getStatusMessage($code)
        ];

        if ($data !== null) {
            $body['result'] = $data;
        }

        return Response::json($body, $code, null, [
            'Access-Control-Allow-Origin' => kirby()->option('headless.cors.allowOrigin', '*')
        ]);
    }

    /**
     * Get the status message for the given code
     *
     * @throws \Exception
     */
    private static function getStatusMessage(int $code): string
    {
        $messages = [
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error'
        ];

        if (!isset($messages[$code])) {
            throw new Exception('Unknown status code: ' . $code);
        }

        return $messages[$code];
    }

    /**
     * Respond to CORS preflight requests
     */
    public static function createPreflightResponse(): Response
    {
        $kirby = kirby();

        return new Response('', null, 204, [
            'Access-Control-Allow-Origin' => $kirby->option('headless.cors.allowOrigin', '*'),
            'Access-Control-Allow-Methods' => $kirby->option('headless.cors.allowMethods', 'GET, POST, OPTIONS'),
            'Access-Control-Allow-Headers' => $kirby->option('headless.cors.allowHeaders', '*'),
            'Access-Control-Max-Age' => $kirby->option('headless.cors.maxAge', '86400'),
            // 204 responses **must not** have a `Content-Length` header
            // (https://www.rfc-editor.org/rfc/rfc7230#section-3.3.2)
        ]);
    }
}
