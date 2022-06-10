<?php

namespace KirbyHeadless\Api;

use Exception;
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

                if (is_a($result, Response::class)) {
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
            'Access-Control-Allow-Origin' => env('KIRBY_HEADLESS_ALLOW_ORIGIN')
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
     * Add CORS headers to the response
     *
     * @return void
     */
    public static function addCorsAllowHeaders(): void
    {
        $headers = [
            'Access-Control-Allow-Origin' => env('KIRBY_HEADLESS_ALLOW_ORIGIN'),
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Authorization, Content-Type'
        ];

        foreach ($headers as $key => $value) {
            if ($value !== null) {
                kirby()->response()->header($key, $value);
            }
        }
    }
}
