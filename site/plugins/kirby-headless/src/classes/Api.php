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
     *
     * @param callable[] $fns
     * @return mixed
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
     *
     * @param int $code
     * @param mixed $data
     * @return \Kirby\Http\Response
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
            'Access-Control-Allow-Origin' => env('KIRBY_HEADLESS_ALLOW_ORIGIN', '*')
        ]);
    }

    /**
     * Get the status message for the given code
     *
     * @param int $code
     * @return string
     * @throws Exception
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
     *
     * @return \Kirby\Http\Response
     */
    public static function createPreflightResponse(): \Kirby\Http\Response
    {
        return new Response('', null, 204, [
            'Access-Control-Allow-Origin' => env('KIRBY_HEADLESS_ALLOW_ORIGIN', '*'),
            'Access-Control-Allow-Methods' => env('KIRBY_HEADLESS_ALLOW_METHODS', 'GET, POST, OPTIONS'),
            'Access-Control-Allow-Headers' => '*',
            'Access-Control-Max-Age' => '86400'
            // 204 responses **must not** have a `Content-Length` header
            // (https://www.rfc-editor.org/rfc/rfc7230#section-3.3.2)
        ]);
    }
}
