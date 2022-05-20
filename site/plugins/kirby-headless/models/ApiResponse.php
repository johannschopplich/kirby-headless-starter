<?php

namespace KirbyHeadless\Api;

use Exception;
use Kirby\Http\Response;

/**
 * Enforces consistent JSON responses by
 * wrapping Kirby's `Response` class
 */
class ApiResponse
{
    /**
     * Create an API response
     *
     * @param int $code
     * @param null|array $data
     * @return \Kirby\Http\Response
     */
    public static function create(int $code, ?array $data = null): Response
    {
        $body = [
            'code' => $code,
            'status' => static::getStatusMessage($code),
        ];

        if ($data !== null) {
            $body['data'] = $data;
        }

        return Response::json($body, $code);
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
            500 => 'Internal Server Error',
        ];

        if (!isset($messages[$code])) {
            throw new Exception('Unknown status code: ' . $code);
        }

        return $messages[$code];
    }
}
